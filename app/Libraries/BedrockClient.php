<?php

namespace App\Libraries;

/**
 * Lightweight AWS Bedrock client using raw curl + SigV4 signing.
 * Zero dependency on aws/aws-sdk-php.
 */
class BedrockClient
{
    private string $accessKey;
    private string $secretKey;
    private string $region;
    private string $modelId;

    public function __construct()
    {
        $this->accessKey = env('AWS_ACCESS_KEY_ID', '');
        $this->secretKey = env('AWS_SECRET_ACCESS_KEY', '');
        $this->region    = env('AWS_REGION', 'eu-central-1');
        $this->modelId   = env('BEDROCK_MODEL_ID', 'amazon.nova-pro-v1:0');
    }

    /**
     * Invoke Bedrock model (non-streaming) and return full response text.
     * Returns array with 'text', 'error', 'debug' keys.
     */
    public function chat(string $systemPrompt, array $messages): string
    {
        $body = $this->buildRequestBody($systemPrompt, $messages);
        $host = "bedrock-runtime.{$this->region}.amazonaws.com";
        $path = '/model/' . rawurlencode($this->modelId) . '/invoke';

        $result = $this->signedRequest('POST', $host, $path, $body);

        if ($result['error']) {
            log_message('error', 'Bedrock chat error: ' . $result['error']);
            return 'Fehler: ' . $result['error'];
        }

        $data = json_decode($result['body'], true);
        return $data['output']['message']['content'][0]['text']
            ?? 'Entschuldigung, ich konnte keine Antwort generieren.';
    }

    /**
     * Debug method — returns full diagnostic info.
     */
    public function debugChat(string $systemPrompt, array $messages): array
    {
        $body = $this->buildRequestBody($systemPrompt, $messages);
        $host = "bedrock-runtime.{$this->region}.amazonaws.com";
        $path = '/model/' . rawurlencode($this->modelId) . '/invoke';

        $result = $this->signedRequest('POST', $host, $path, $body);

        return [
            'credentials' => [
                'accessKey' => substr($this->accessKey, 0, 8) . '...',
                'region'    => $this->region,
                'modelId'   => $this->modelId,
                'hasSecret' => !empty($this->secretKey),
            ],
            'request' => [
                'host' => $host,
                'path' => $path,
                'bodyLength' => strlen(json_encode($body)),
            ],
            'response' => [
                'httpCode' => $result['httpCode'],
                'error'    => $result['error'],
                'bodyLength' => strlen($result['body'] ?? ''),
                'body' => substr($result['body'] ?? '', 0, 2000),
            ],
        ];
    }

    /**
     * Test connection to Bedrock.
     */
    public function testConnection(): bool
    {
        $host = "bedrock.{$this->region}.amazonaws.com";
        $path = '/foundation-models';
        $result = $this->signedRequest('GET', $host, $path);
        return !$result['error'] && $result['httpCode'] < 400;
    }

    // ─── Private ───

    private function buildRequestBody(string $systemPrompt, array $messages): array
    {
        $formatted = [];
        foreach ($messages as $msg) {
            $formatted[] = [
                'role' => $msg['role'] === 'assistant' ? 'assistant' : 'user',
                'content' => [['text' => $msg['content']]],
            ];
        }

        return [
            'schemaVersion' => 'messages-v1',
            'system' => [['text' => $systemPrompt]],
            'messages' => $formatted,
            'inferenceConfig' => [
                'temperature' => 0.1,
                'topP' => 0.8,
                'maxTokens' => 1024,
            ],
        ];
    }

    /**
     * Signed HTTP request. Returns array with body, httpCode, error.
     */
    private function signedRequest(string $method, string $host, string $path, ?array $body = null): array
    {
        $bodyJson = $body ? json_encode($body) : '';
        $headers = $this->signHeaders($method, $host, $path, $bodyJson);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => "https://{$host}{$path}",
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyJson);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        $error = null;
        if ($curlErrno) {
            $error = "curl({$curlErrno}): {$curlError}";
        } elseif ($httpCode >= 400) {
            $error = "HTTP {$httpCode}: " . substr($response, 0, 500);
        }

        return [
            'body'     => $response,
            'httpCode' => $httpCode,
            'error'    => $error,
        ];
    }

    /**
     * AWS Signature Version 4 signing.
     */
    private function signHeaders(string $method, string $host, string $path, string $body = ''): array
    {
        $now = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        $payloadHash = hash('sha256', $body);

        // Service name from host
        $service = 'bedrock';

        $credentialScope = "{$date}/{$this->region}/{$service}/aws4_request";

        // Canonical headers (must be sorted alphabetically)
        $canonicalHeaders = "content-type:application/json\nhost:{$host}\nx-amz-date:{$now}\n";
        $signedHeaders = 'content-type;host;x-amz-date';

        // Split path and query
        $pathParts = explode('?', $path, 2);
        // AWS SigV4 requires each path segment to be URI-encoded
        // Since path already contains %3A (from rawurlencode of model ID),
        // we need to encode each segment again so %3A → %253A
        $segments = explode('/', $pathParts[0]);
        $encodedSegments = array_map(fn($s) => rawurlencode($s), $segments);
        $canonicalUri = implode('/', $encodedSegments) ?: '/';
        $canonicalQueryString = $pathParts[1] ?? '';

        $canonicalRequest = implode("\n", [
            $method,
            $canonicalUri,
            $canonicalQueryString,
            $canonicalHeaders,
            $signedHeaders,
            $payloadHash,
        ]);

        $stringToSign = implode("\n", [
            'AWS4-HMAC-SHA256',
            $now,
            $credentialScope,
            hash('sha256', $canonicalRequest),
        ]);

        // Derive signing key
        $kDate    = hash_hmac('sha256', $date, "AWS4{$this->secretKey}", true);
        $kRegion  = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', $service, $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

        $signature = hash_hmac('sha256', $stringToSign, $kSigning);

        $authorization = "AWS4-HMAC-SHA256 Credential={$this->accessKey}/{$credentialScope}, "
                       . "SignedHeaders={$signedHeaders}, Signature={$signature}";

        return [
            "Content-Type: application/json",
            "Host: {$host}",
            "X-Amz-Date: {$now}",
            "Authorization: {$authorization}",
            "X-Amz-Content-Sha256: {$payloadHash}",
        ];
    }
}

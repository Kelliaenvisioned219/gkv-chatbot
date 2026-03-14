<?php

namespace App\Controllers;

use App\Libraries\BedrockClient;
use App\Libraries\WissenBank;
use CodeIgniter\HTTP\ResponseInterface;

class Chat extends BaseController
{
    private const SYSTEM_BASE = <<<'PROMPT'
Du bist ein neutraler und kompetenter Ratgeber zur gesetzlichen Krankenversicherung (GKV) in Deutschland.

STRIKTE REGELN:
- Antworte AUSSCHLIESSLICH basierend auf der bereitgestellten WISSENSBASIS.
- Erfinde KEINE Informationen, Zahlen, URLs, Tarife oder Leistungen, die nicht in der WISSENSBASIS stehen.
- Generiere KEINE Links oder URLs.
- Wenn die WISSENSBASIS keine Antwort auf die Frage enthält, antworte ehrlich:
  "Zu dieser Frage habe ich leider keine gesicherten Informationen. Bitte kontaktieren Sie Ihre Krankenkasse direkt oder wenden Sie sich an die Unabhängige Patientenberatung Deutschland (UPD)."
- Gib konkrete Zahlen und Beträge NUR an, wenn sie in der WISSENSBASIS stehen.
- Weise darauf hin, wenn Leistungen kassenindividuell variieren können (Satzungsleistungen).
- Sei freundlich, empathisch und professionell.
- Antworte immer auf Deutsch.
- Halte deine Antworten kurz und präzise (max. 3-4 Absätze).
- Du vertrittst KEINE bestimmte Krankenkasse. Du bist ein allgemeiner GKV-Berater.
PROMPT;

    /**
     * POST /api/chat
     * Uses non-streaming InvokeModel for reliability on shared hosting,
     * then streams the response text character-by-character to simulate typing.
     */
    public function stream()
    {
        $input = $this->request->getJSON(true);
        $userMessage = trim($input['message'] ?? '');
        $history = $input['history'] ?? [];

        if (empty($userMessage)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Keine Nachricht erhalten.']);
        }

        // Select relevant knowledge context
        $wissenBank = new WissenBank();
        $ctx = $wissenBank->selectContext($userMessage);
        $systemPrompt = self::SYSTEM_BASE . "\n\nWISSENSBASIS:\n" . $ctx['context'];

        // Build messages (last 4 exchanges)
        $messages = [];
        $recentHistory = array_slice($history, -8);
        foreach ($recentHistory as $msg) {
            $messages[] = [
                'role'    => $msg['role'],
                'content' => $msg['content'],
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        log_message('info', "[CHAT] Frage: {$userMessage} | Kontext: {$ctx['fileCount']} Dateien, {$ctx['chars']} Zeichen");

        // Disable ALL output buffering for SSE
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        flush();

        try {
            $bedrock = new BedrockClient();

            // Use non-streaming invoke for reliability
            $fullResponse = $bedrock->chat($systemPrompt, $messages);

            log_message('info', "[CHAT] Antwort: " . strlen($fullResponse) . " Zeichen");

            if (empty($fullResponse)) {
                echo "data: " . json_encode(['error' => 'Keine Antwort vom Modell erhalten.']) . "\n\n";
                flush();
                exit;
            }

            // Simulate streaming by sending chunks of text
            $chunks = str_split($fullResponse, 8);
            foreach ($chunks as $chunk) {
                echo "data: " . json_encode(['token' => $chunk]) . "\n\n";
                flush();
                usleep(10000); // 10ms delay for typing effect
            }

            echo "data: " . json_encode(['done' => true]) . "\n\n";
            flush();

        } catch (\Exception $e) {
            log_message('error', 'Chat error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            echo "data: " . json_encode(['error' => 'Fehler: ' . $e->getMessage()]) . "\n\n";
            flush();
        }

        exit;
    }

    /**
     * GET /api/health
     */
    public function health(): ResponseInterface
    {
        $wissenBank = new WissenBank();

        try {
            $bedrock = new BedrockClient();
            $connected = $bedrock->testConnection();
        } catch (\Exception $e) {
            $connected = false;
        }

        return $this->response->setJSON([
            'bedrock'         => $connected,
            'model'           => env('BEDROCK_MODEL_ID', 'amazon.nova-pro-v1:0'),
            'knowledge_files' => $wissenBank->getFileCount(),
        ]);
    }

    /**
     * GET /api/wissen/{filename}
     */
    public function wissen(string $filename): ResponseInterface
    {
        $safe = basename($filename);
        $path = ROOTPATH . 'wissenBank/' . $safe;

        if (!file_exists($path)) {
            return $this->response->setStatusCode(404)->setBody('Not found');
        }

        return $this->response
            ->setContentType('text/plain; charset=UTF-8')
            ->setBody(file_get_contents($path));
    }
}

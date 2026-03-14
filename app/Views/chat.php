<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>GKV Assistent — Ihr Ratgeber zur gesetzlichen Krankenversicherung</title>
<meta name="description" content="Digitaler Ratgeber zur gesetzlichen Krankenversicherung (GKV) in Deutschland. Fragen zu Leistungen, Beiträgen, Mitgliedschaft und Pflege.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #ffffff;
    --fg: #000000;
    --muted: #888888;
    --border: #e0e0e0;
    --hover: #f5f5f5;
    --user-bg: #f0f0f0;
    --bot-bg: #ffffff;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Inter', -apple-system, sans-serif;
    background: var(--bg);
    color: var(--fg);
    height: 100vh;
    height: 100dvh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  header {
    border-bottom: 1px solid var(--border);
    padding: 16px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
  }

  .logo {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .logo-mark {
    width: 32px;
    height: 32px;
    background: var(--fg);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--bg);
    font-weight: 700;
    font-size: 14px;
  }

  .logo-text { font-size: 15px; font-weight: 600; letter-spacing: -0.01em; }
  .logo-sub { font-size: 11px; color: var(--muted); font-weight: 400; }

  .header-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .info-link {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    color: var(--muted);
    transition: color 0.15s, background 0.15s;
    text-decoration: none;
    font-size: 11px;
    font-weight: 500;
  }
  .info-link:hover { color: var(--fg); background: var(--hover); }
  .info-link svg { width: 14px; height: 14px; flex-shrink: 0; }

  .header-nav { display: flex; align-items: center; gap: 4px; }
  @media (max-width: 600px) { .header-nav { flex-direction: column; align-items: flex-end; } }

  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #ccc;
    transition: background 0.3s;
  }
  .status-dot.online { background: #000; }

  .chat-area {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .welcome {
    text-align: center;
    padding: 60px 24px 40px;
    max-width: 520px;
    margin: auto;
  }

  .welcome h2 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
  }

  .welcome p {
    font-size: 14px;
    color: var(--muted);
    line-height: 1.6;
    margin-bottom: 24px;
  }

  .suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
  }

  .suggestion {
    font-size: 12px;
    padding: 8px 14px;
    border: 1px solid var(--border);
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.15s;
    background: var(--bg);
    color: var(--fg);
    font-family: inherit;
  }

  .suggestion:hover {
    background: var(--fg);
    color: var(--bg);
  }

  .message {
    display: flex;
    gap: 12px;
    max-width: 720px;
    width: 100%;
    margin: 0 auto;
    animation: fadeIn 0.2s ease;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
  }

  .message.user { flex-direction: row-reverse; }

  .avatar {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .message.bot .avatar { background: var(--fg); color: var(--bg); }
  .message.user .avatar { background: var(--user-bg); color: var(--fg); }

  .bubble {
    position: relative;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.65;
    max-width: 85%;
  }

  .message.bot .bubble { background: var(--bot-bg); border: 1px solid var(--border); }
  .message.user .bubble { background: var(--fg); color: var(--bg); }

  .bubble p { margin-bottom: 8px; }
  .bubble p:last-child { margin-bottom: 0; }
  .bubble strong { font-weight: 600; }
  .bubble ul, .bubble ol { padding-left: 18px; margin-bottom: 8px; }
  .bubble li { margin-bottom: 4px; }

  .speak-btn {
    position: absolute;
    right: 8px;
    bottom: 6px;
    background: none;
    border: none;
    cursor: pointer;
    color: var(--muted);
    padding: 4px;
    border-radius: 4px;
    transition: color 0.15s, background 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .speak-btn:hover { color: var(--fg); background: var(--hover); }
  .speak-btn.speaking { color: var(--fg); }
  .speak-btn svg { width: 14px; height: 14px; }

  .typing-indicator {
    display: flex;
    gap: 4px;
    padding: 4px 0;
  }

  .typing-indicator span {
    width: 6px;
    height: 6px;
    background: var(--muted);
    border-radius: 50%;
    animation: blink 1.2s infinite;
  }
  .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
  .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

  @keyframes blink {
    0%, 60%, 100% { opacity: 0.3; }
    30% { opacity: 1; }
  }

  .input-area {
    border-top: 1px solid var(--border);
    padding: 16px 24px;
    flex-shrink: 0;
  }

  .input-wrap {
    max-width: 720px;
    margin: 0 auto;
    display: flex;
    gap: 8px;
    align-items: flex-end;
  }

  .input-wrap textarea {
    flex: 1;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 12px 16px;
    font-family: inherit;
    font-size: 14px;
    resize: none;
    outline: none;
    background: var(--bg);
    color: var(--fg);
    line-height: 1.5;
    max-height: 120px;
    min-height: 44px;
    transition: border-color 0.15s;
  }

  .input-wrap textarea:focus { border-color: var(--fg); }
  .input-wrap textarea::placeholder { color: var(--muted); }

  .send-btn, .mic-btn {
    width: 44px;
    height: 44px;
    border: none;
    border-radius: 12px;
    background: var(--fg);
    color: var(--bg);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.15s, background 0.2s;
    flex-shrink: 0;
  }

  .send-btn:disabled { opacity: 0.3; cursor: not-allowed; }
  .send-btn svg, .mic-btn svg { width: 18px; height: 18px; }

  .mic-btn.recording {
    background: #d00;
    animation: pulse-mic 1.2s ease-in-out infinite;
  }

  .mic-btn.hidden { display: none; }

  @keyframes pulse-mic {
    0%, 100% { box-shadow: 0 0 0 0 rgba(208,0,0,0.4); }
    50% { box-shadow: 0 0 0 8px rgba(208,0,0,0); }
  }



  footer {
    text-align: center;
    padding: 8px;
    font-size: 10px;
    color: var(--muted);
    flex-shrink: 0;
  }

  .test-banner {
    background: var(--fg);
    color: var(--bg);
    text-align: center;
    font-size: 11px;
    padding: 4px 12px;
    letter-spacing: 0.02em;
    flex-shrink: 0;
  }

  .error-banner {
    display: none;
    background: var(--fg);
    color: var(--bg);
    padding: 10px 24px;
    font-size: 12px;
    text-align: center;
  }
  .error-banner.visible { display: block; }
</style>
</head>
<body>

<div class="test-banner">⚠ Diese Webseite dient ausschließlich zu Testzwecken.</div>
<div class="error-banner" id="errorBanner"></div>

<header>
  <div class="logo">
    <div class="logo-mark">G</div>
    <div>
      <div class="logo-text">GKV Assistent</div>
      <div class="logo-sub">Ratgeber zur gesetzlichen Krankenversicherung</div>
    </div>
  </div>
  <div class="header-nav">
    <a href="/wissenbank.html" class="info-link" title="Wissenbank">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Wissenbank
    </a>
    <a href="/gov_safety.html" class="info-link" title="Security &amp; Governance">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      Security &amp; Governance
    </a>

  </div>
</header>

<div class="chat-area" id="chatArea">
  <div class="welcome" id="welcome">
    <h2>Willkommen beim GKV Assistenten</h2>
    <p>Ich beantworte Ihre Fragen rund um die gesetzliche Krankenversicherung in Deutschland — Leistungen, Beiträge, Pflege, Zahngesundheit, Schwangerschaft und mehr.</p>
    <div class="suggestions">
      <button class="suggestion" onclick="askSuggestion(this)">Was zahlt die gesetzliche Krankenversicherung?</button>
      <button class="suggestion" onclick="askSuggestion(this)">Wie wechsle ich meine Krankenkasse?</button>
      <button class="suggestion" onclick="askSuggestion(this)">Wie hoch ist das Krankengeld?</button>
      <button class="suggestion" onclick="askSuggestion(this)">Wie beantrage ich einen Pflegegrad?</button>
      <button class="suggestion" onclick="askSuggestion(this)">Was ist die elektronische Patientenakte?</button>
      <button class="suggestion" onclick="askSuggestion(this)">Welche Vorsorgeuntersuchungen stehen mir zu?</button>
      <button class="suggestion" onclick="askSuggestion(this)">Wie lege ich Widerspruch ein?</button>
    </div>
  </div>
</div>

<div class="input-area">
  <div class="input-wrap">
    <textarea id="userInput" rows="1" placeholder="Ihre Frage zur GKV eingeben…" onkeydown="handleKey(event)" oninput="autoGrow(this)"></textarea>
    <button class="mic-btn hidden" id="micBtn" title="Spracheingabe">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
    </button>
    <button class="send-btn" id="sendBtn" onclick="sendMessage()" disabled>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
    </button>
  </div>
</div>

<footer>GKV Assistent · Alle Angaben ohne Gewähr</footer>

<script>
const chatArea = document.getElementById('chatArea');
const userInput = document.getElementById('userInput');
const sendBtn = document.getElementById('sendBtn');
const welcome = document.getElementById('welcome');
const statusDot = document.getElementById('statusDot');
const errorBanner = document.getElementById('errorBanner');
const micBtn = document.getElementById('micBtn');

let history = [];
let isStreaming = false;

// ─── Voice: Speech Recognition (STT) ───
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = null;

if (SpeechRecognition) {
  recognition = new SpeechRecognition();
  recognition.lang = 'de-DE';
  recognition.interimResults = false;
  recognition.maxAlternatives = 1;
  recognition.continuous = false;

  micBtn.classList.remove('hidden');

  micBtn.addEventListener('click', () => {
    if (micBtn.classList.contains('recording')) {
      recognition.stop();
      return;
    }
    micBtn.classList.add('recording');
    recognition.start();
  });

  recognition.addEventListener('result', (e) => {
    const transcript = e.results[0][0].transcript;
    if (transcript.trim()) {
      userInput.value = transcript;
      sendBtn.disabled = false;
      sendMessage();
    }
  });

  recognition.addEventListener('end', () => {
    micBtn.classList.remove('recording');
  });

  recognition.addEventListener('error', (e) => {
    micBtn.classList.remove('recording');
    if (e.error !== 'aborted' && e.error !== 'no-speech') {
      console.warn('Speech recognition error:', e.error);
    }
  });
}

// ─── Voice: Speech Synthesis (TTS) ───
function speakText(text) {
  if (!window.speechSynthesis) return;
  window.speechSynthesis.cancel();

  // Strip markdown artifacts for cleaner speech
  const clean = text
    .replace(/\*\*(.+?)\*\*/g, '$1')
    .replace(/\*(.+?)\*/g, '$1')
    .replace(/`(.+?)`/g, '$1')
    .replace(/^[-•] /gm, '')
    .replace(/\n+/g, '. ')
    .trim();

  if (!clean) return;

  const utter = new SpeechSynthesisUtterance(clean);
  utter.lang = 'de-DE';
  utter.rate = 1.05;

  // Prefer a German voice
  const voices = speechSynthesis.getVoices();
  const deVoice = voices.find(v => v.lang.startsWith('de'));
  if (deVoice) utter.voice = deVoice;

  speechSynthesis.speak(utter);
  return utter;
}

function addSpeakButton(bubble, text) {
  const btn = document.createElement('button');
  btn.className = 'speak-btn';
  btn.title = 'Vorlesen';
  btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>';
  btn.addEventListener('click', () => {
    if (btn.classList.contains('speaking')) {
      speechSynthesis.cancel();
      btn.classList.remove('speaking');
      return;
    }
    // Stop any other speaking buttons
    document.querySelectorAll('.speak-btn.speaking').forEach(b => b.classList.remove('speaking'));
    const utter = speakText(text);
    if (utter) {
      btn.classList.add('speaking');
      utter.addEventListener('end', () => btn.classList.remove('speaking'));
      utter.addEventListener('error', () => btn.classList.remove('speaking'));
    }
  });
  bubble.style.paddingBottom = '28px';
  bubble.appendChild(btn);
}


// Health check
async function checkHealth() {
  try {
    const resp = await fetch('/api/health');
    const data = await resp.json();
    if (data.bedrock) {
      if (statusDot) statusDot.classList.add('online');
      if (statusDot) statusDot.title = `Verbunden · ${data.model} · ${data.knowledge_files} Wissensdateien`;
      errorBanner.classList.remove('visible');
    } else {
      if (statusDot) statusDot.classList.remove('online');
      if (statusDot) statusDot.title = 'Verbindungsproblem';
    }
  } catch {
    if (statusDot) statusDot.classList.remove('online');
  }
}
checkHealth();
setInterval(checkHealth, 60000);

// Input
userInput.addEventListener('input', () => {
  sendBtn.disabled = !userInput.value.trim() || isStreaming;
});

function autoGrow(el) {
  el.style.height = 'auto';
  el.style.height = Math.min(el.scrollHeight, 120) + 'px';
}

function handleKey(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    if (!sendBtn.disabled) sendMessage();
  }
}

function askSuggestion(btn) {
  userInput.value = btn.textContent;
  sendBtn.disabled = false;
  sendMessage();
}

// Send message
async function sendMessage() {
  const text = userInput.value.trim();
  if (!text || isStreaming) return;

  if (welcome) welcome.style.display = 'none';
  addMessage('user', text);
  history.push({ role: 'user', content: text });

  userInput.value = '';
  userInput.style.height = 'auto';
  sendBtn.disabled = true;
  isStreaming = true;

  const botBubble = addMessage('bot', '', true);

  try {
    const resp = await fetch('/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: text, history: history.slice(0, -1) })
    });

    const reader = resp.body.getReader();
    const decoder = new TextDecoder();
    let fullResponse = '';

    while (true) {
      const { done, value } = await reader.read();
      if (done) break;

      const chunk = decoder.decode(value);
      const lines = chunk.split('\n');

      for (const line of lines) {
        if (line.startsWith('data: ')) {
          try {
            const data = JSON.parse(line.slice(6));
            if (data.error) {
              botBubble.innerHTML = `<p style="color:#999">${data.error}</p>`;
              break;
            }
            if (data.token) {
              fullResponse += data.token;
              botBubble.innerHTML = formatMarkdown(fullResponse);
              chatArea.scrollTop = chatArea.scrollHeight;
            }
            if (data.done) {
              history.push({ role: 'assistant', content: fullResponse });
              addSpeakButton(botBubble, fullResponse);
            }
          } catch {}
        }
      }
    }

    const typing = botBubble.querySelector('.typing-indicator');
    if (typing) typing.remove();

  } catch (err) {
    botBubble.innerHTML = `<p style="color:#999">Verbindungsfehler. Bitte versuchen Sie es später erneut.</p>`;
  }

  isStreaming = false;
  sendBtn.disabled = !userInput.value.trim();
  chatArea.scrollTop = chatArea.scrollHeight;
}

function addMessage(role, text, streaming = false) {
  const msg = document.createElement('div');
  msg.className = `message ${role}`;

  const avatar = document.createElement('div');
  avatar.className = 'avatar';
  avatar.textContent = role === 'bot' ? 'G' : 'Du';

  const bubble = document.createElement('div');
  bubble.className = 'bubble';

  if (streaming) {
    bubble.innerHTML = '<div class="typing-indicator"><span></span><span></span><span></span></div>';
  } else {
    bubble.innerHTML = role === 'user' ? `<p>${escapeHtml(text)}</p>` : formatMarkdown(text);
  }

  msg.appendChild(avatar);
  msg.appendChild(bubble);
  chatArea.appendChild(msg);
  chatArea.scrollTop = chatArea.scrollHeight;
  return bubble;
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

function formatMarkdown(text) {
  return text
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.+?)\*/g, '<em>$1</em>')
    .replace(/`(.+?)`/g, '<code style="background:#f0f0f0;padding:1px 4px;border-radius:3px;font-size:13px">$1</code>')
    .replace(/^[-•] (.+)/gm, '<li>$1</li>')
    .replace(/(<li>.*<\/li>)/gs, '<ul>$1</ul>')
    .replace(/<\/ul>\s*<ul>/g, '')
    .replace(/\n\n/g, '</p><p>')
    .replace(/\n/g, '<br>')
    .replace(/^/, '<p>').replace(/$/, '</p>')
    .replace(/<p><\/p>/g, '');
}
</script>
</body>
</html>

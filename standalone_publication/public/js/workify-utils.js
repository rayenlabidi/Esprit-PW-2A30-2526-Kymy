/* ============================================================
   workify-utils.js  –  Shared utilities: normalization,
                        bad-word filter, Gemini chatbot
   ============================================================ */

'use strict';

// ==================== 1. TEXT NORMALIZATION ====================

/**
 * Normalizes text for consistent comparison:
 * - Converts to lowercase
 * - Removes diacritics/accents  (é → e, ç → c, etc.)
 * - Reduces repeated characters (bonjouuuurrrrr → bonjour)
 */
function normalizeText(text) {
  if (!text) return '';
  // Lowercase
  let result = text.toLowerCase();
  // Remove accents via NFD + strip combining marks
  result = result.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  // Reduce 3+ repeated chars down to the single char
  result = result.replace(/(.)\1{2,}/g, '$1');
  return result;
}


// ==================== 2. BAD WORDS FILTER (HYBRID) ====================

// --- Local bad word arrays (multi-language) ---
// PurgoMalum API handles English profanity well.
// These local arrays cover languages the API does NOT handle.

const TUNISIAN_BAD_WORDS = [
  // Tunisian Derja
  'zebi', 'nayek', '9ahba', 'tfeh', 'kahba', 'khra', 'miboun',
  'zamel', 'nik', 'nikomok', 'nikoumouk', 'barra', 'kelb',
  // Standard Arabic
  'sharmouta', 'sharmout', 'kos', 'omak', 'ayr', 'kuss',
  'ibn el sharmouta', 'manyak', 'khara',
];

const EXTRA_BAD_WORDS = [
  // Spanish
  'puta', 'mierda', 'cabron', 'pendejo', 'chinga', 'coño', 'joder', 'maricon', 'hijo de puta',
  // German
  'scheiße', 'scheisse', 'arschloch', 'hurensohn', 'wichser', 'fotze', 'missgeburt',
  // Italian
  'cazzo', 'stronzo', 'vaffanculo', 'puttana', 'minchia', 'coglione',
  // Portuguese
  'caralho', 'porra', 'foda', 'merda', 'viado', 'filho da puta',
];

/**
 * Hybrid bad-word detection.
 * 1. Normalize the text
 * 2. Check local Tunisian / Arabic bad words
 * 3. Check extra multi-language bad words (Spanish, German, Italian, Portuguese)
 * 4. If clean locally → call PurgoMalum API (handles English profanity)
 * Returns { isBad: true/false, source: 'local'|'api'|null, word: string|null }
 */
async function isBadMessage(text) {
  const normalized = normalizeText(text);

  // --- Local Tunisian / Arabic check ---
  for (const word of TUNISIAN_BAD_WORDS) {
    if (normalized.includes(word)) {
      return { isBad: true, source: 'local', word: word };
    }
  }

  // --- Multi-language check (Spanish, German, Italian, Portuguese) ---
  for (const word of EXTRA_BAD_WORDS) {
    if (normalized.includes(normalizeText(word))) {
      return { isBad: true, source: 'local', word: word };
    }
  }

  // --- External PurgoMalum API (English profanity) ---
  try {
    const apiUrl = 'https://www.purgomalum.com/service/containsprofanity?text=' +
      encodeURIComponent(normalized);
    const response = await fetch(apiUrl);
    const result = await response.text();
    if (result.trim() === 'true') {
      return { isBad: true, source: 'api', word: null };
    }
  } catch (err) {
    console.warn('PurgoMalum API unreachable, skipping external check:', err);
    // Fail open – if API is down, don't block the user
  }

  return { isBad: false, source: null, word: null };
}


// ==================== 3. GEMINI CHATBOT API (server-side proxy) ====================

// API key is now stored securely in controllers/ChatbotAPI.php
const CHATBOT_PROXY_URL = '../../controllers/ChatbotAPI.php';

/**
 * Sends a user message to the server-side PHP proxy which forwards it
 * to the Gemini API.  The API key never leaves the server.
 * Input is normalized before sending.
 */
async function askChatbot(message) {
  const normalized = normalizeText(message);

  try {
    const res = await fetch(CHATBOT_PROXY_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ message: normalized })
    });
    const data = await res.json();

    if (!res.ok) {
      console.error('Chatbot API Error:', data);
      return data.error ? `Error: ${data.error}` : 'Sorry, the server returned an error.';
    }

    if (data.reply) {
      return data.reply;
    }
    return 'Sorry, I could not generate a response right now.';
  } catch (err) {
    console.error('Chatbot proxy error:', err);
    return 'Sorry, I am having trouble connecting. Please try again later.';
  }
}


// ==================== 4. CHATBOT UI ====================

/**
 * Injects the floating chatbot button + modal into the page.
 * Safe to call multiple times – it checks for existing elements.
 */
function initChatbotUI() {
  if (document.getElementById('wf-chatbot-fab')) return; // already initialised

  // --- Floating Action Button ---
  const fab = document.createElement('button');
  fab.id = 'wf-chatbot-fab';
  fab.title = 'Chat with Workify AI';
  fab.innerHTML = `
    <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>`;
  fab.addEventListener('click', toggleChatbotModal);
  document.body.appendChild(fab);

  // --- Modal ---
  const modal = document.createElement('div');
  modal.id = 'wf-chatbot-modal';
  modal.innerHTML = `
    <div class="wf-chatbot-container">
      <div class="wf-chatbot-header">
        <div class="wf-chatbot-header-left">
          <div class="wf-chatbot-avatar">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="3"/>
              <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
            </svg>
          </div>
          <div>
            <div class="wf-chatbot-title">Workify AI</div>
            <div class="wf-chatbot-subtitle">Online · Ready to help</div>
          </div>
        </div>
        <button class="wf-chatbot-close" onclick="toggleChatbotModal()" title="Close">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="18" y1="6" x2="6" y2="18"/>
            <line x1="6" y1="6" x2="18" y2="18"/>
          </svg>
        </button>
      </div>
      <div class="wf-chatbot-messages" id="wf-chatbot-messages">
        <div class="wf-chatbot-msg bot">
          <div class="wf-chatbot-msg-bubble">
            👋 Hi there! I'm the Workify AI assistant. How can I help you today?
          </div>
        </div>
      </div>
      <div class="wf-chatbot-input-bar">
        <input type="text" id="wf-chatbot-input" placeholder="Type a message…"
               autocomplete="off" />
        <button id="wf-chatbot-send" title="Send">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <line x1="22" y1="2" x2="11" y2="13"/>
            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
          </svg>
        </button>
      </div>
    </div>`;
  document.body.appendChild(modal);

  // --- Event listeners ---
  const input = document.getElementById('wf-chatbot-input');
  const sendBtn = document.getElementById('wf-chatbot-send');

  sendBtn.addEventListener('click', handleChatbotSend);
  input.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleChatbotSend();
    }
  });

  // Close on backdrop click
  modal.addEventListener('click', function (e) {
    if (e.target === modal) toggleChatbotModal();
  });
}

function toggleChatbotModal() {
  const modal = document.getElementById('wf-chatbot-modal');
  if (!modal) return;
  const isOpen = modal.classList.contains('open');
  modal.classList.toggle('open');
  if (!isOpen) {
    setTimeout(function () {
      const inp = document.getElementById('wf-chatbot-input');
      if (inp) inp.focus();
    }, 200);
  }
}

async function handleChatbotSend() {
  const input = document.getElementById('wf-chatbot-input');
  const msgBox = document.getElementById('wf-chatbot-messages');
  const raw = input.value.trim();
  if (!raw) return;

  // Show user bubble
  appendChatbotMessage(raw, 'user');
  input.value = '';

  // Show typing indicator
  const typing = document.createElement('div');
  typing.className = 'wf-chatbot-msg bot typing-indicator';
  typing.innerHTML = '<div class="wf-chatbot-msg-bubble"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>';
  msgBox.appendChild(typing);
  msgBox.scrollTop = msgBox.scrollHeight;

  // Get bot response
  const reply = await askChatbot(raw);

  // Remove typing indicator
  if (typing.parentNode) typing.remove();

  // Show bot reply
  appendChatbotMessage(reply, 'bot');
}

function appendChatbotMessage(text, sender) {
  const msgBox = document.getElementById('wf-chatbot-messages');
  if (!msgBox) return;
  const div = document.createElement('div');
  div.className = 'wf-chatbot-msg ' + sender;
  div.innerHTML = '<div class="wf-chatbot-msg-bubble">' + escChatHtml(text) + '</div>';
  msgBox.appendChild(div);
  msgBox.scrollTop = msgBox.scrollHeight;
}

function escChatHtml(text) {
  if (text == null) return '';
  const d = document.createElement('div');
  d.textContent = String(text);
  return d.innerHTML.replace(/\n/g, '<br>');
}

// Auto-init when DOM is ready
document.addEventListener('DOMContentLoaded', initChatbotUI);

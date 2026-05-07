            </section>
        </main>
    </div>
    <script src="../assets/js/validation.js"></script>
    
    <!-- Chatbot Widget -->
    <style>
        #chatbot-widget {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 320px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            font-family: sans-serif;
            border: 1px solid #e0e0e0;
        }
        #chatbot-header {
            background: #2563eb;
            color: #fff;
            padding: 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }
        #chatbot-messages {
            height: 300px;
            overflow-y: auto;
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 14px;
            background: #f8fafc;
        }
        .chat-msg { max-width: 80%; line-height: 1.4; word-wrap: break-word; }
        .chat-msg.user { align-self: flex-end; background: #2563eb; color: white; padding: 10px; border-radius: 12px 12px 0 12px; }
        .chat-msg.bot { align-self: flex-start; background: #e2e8f0; color: #1e293b; padding: 10px; border-radius: 12px 12px 12px 0; }
        #chatbot-input-container {
            display: flex;
            padding: 10px;
            background: #fff;
            border-top: 1px solid #e2e8f0;
        }
        #chatbot-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
        }
        #chatbot-input:focus { border-color: #2563eb; }
        #chatbot-send {
            margin-left: 8px;
            padding: 10px 15px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        #chatbot-send:hover { background: #1d4ed8; }
        #chatbot-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #2563eb;
            color: #fff;
            padding: 15px 20px;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            z-index: 999;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.2s;
        }
        #chatbot-toggle:hover { transform: scale(1.05); }
        .chatbot-typing { align-self: flex-start; background: transparent; color: #64748b; font-size: 12px; font-style: italic; }
    </style>

    <div id="chatbot-toggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
        Assistant Formation
    </div>

    <div id="chatbot-widget">
        <div id="chatbot-header">
            <span>Assistant Formation</span>
            <span id="chatbot-close" style="cursor:pointer; font-size: 18px;">✖</span>
        </div>
        <div id="chatbot-messages">
            <div class="chat-msg bot">Bonjour ! Je suis l'assistant expert en gestion des formations. Comment puis-je vous aider aujourd'hui ?</div>
        </div>
        <div id="chatbot-input-container">
            <input type="text" id="chatbot-input" placeholder="Posez une question...">
            <button id="chatbot-send">Envoyer</button>
        </div>
    </div>

    <script>
    document.getElementById('chatbot-toggle').addEventListener('click', () => {
        document.getElementById('chatbot-widget').style.display = 'flex';
        document.getElementById('chatbot-toggle').style.display = 'none';
    });
    
    document.getElementById('chatbot-close').addEventListener('click', () => {
        document.getElementById('chatbot-widget').style.display = 'none';
        document.getElementById('chatbot-toggle').style.display = 'flex';
    });

    async function sendChatMessage() {
        const input = document.getElementById('chatbot-input');
        const messagesDiv = document.getElementById('chatbot-messages');
        const msg = input.value.trim();
        if (!msg) return;

        addMessage(msg, 'user');
        input.value = '';

        // Add typing indicator
        const typingId = 'typing-' + Date.now();
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chatbot-typing';
        typingDiv.id = typingId;
        typingDiv.textContent = 'En train d\'écrire...';
        messagesDiv.appendChild(typingDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        try {
            const basePath = window.location.pathname.includes('/controller/') ? 'ChatbotC.php' : '../controller/ChatbotC.php';
            
            const response = await fetch(basePath, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: msg })
            });
            const data = await response.json();
            
            const typingEl = document.getElementById(typingId);
            if (typingEl) typingEl.remove();
            
            if (data.choices && data.choices[0] && data.choices[0].message) {
                addMessage(data.choices[0].message.content, 'bot');
            } else if (data.error) {
                addMessage('Erreur: ' + data.error, 'bot');
            } else {
                addMessage('Erreur: Format de réponse inattendu.', 'bot');
            }
        } catch (err) {
            const typingEl = document.getElementById(typingId);
            if (typingEl) typingEl.remove();
            addMessage('Erreur de connexion au chatbot.', 'bot');
        }
    }

    document.getElementById('chatbot-send').addEventListener('click', sendChatMessage);
    document.getElementById('chatbot-input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendChatMessage();
    });

    function addMessage(text, sender) {
        const messages = document.getElementById('chatbot-messages');
        const div = document.createElement('div');
        div.className = `chat-msg ${sender}`;
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }
    </script>
</body>
</html>

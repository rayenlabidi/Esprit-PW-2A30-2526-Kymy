            </section>
        </main>
    </div>
    <script src="../assets/js/validation.js"></script>

    <?php if (isset($activeModule) && $activeModule === 'formations') { ?>
        <div id="chatbot-toggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            Assistant Formation
        </div>

        <div id="chatbot-widget">
            <div id="chatbot-header">
                <span>Assistant Formation</span>
                <button id="chatbot-close" type="button" aria-label="Fermer">x</button>
            </div>
            <div id="chatbot-messages">
                <div class="chat-msg bot">Bonjour ! Je suis l'assistant expert en gestion des formations. Comment puis-je vous aider aujourd'hui ?</div>
            </div>
            <div id="chatbot-input-container">
                <input type="text" id="chatbot-input" placeholder="Posez une question...">
                <button id="chatbot-send" type="button">Envoyer</button>
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

            const typingId = 'typing-' + Date.now();
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chatbot-typing';
            typingDiv.id = typingId;
            typingDiv.textContent = 'En train d ecrire...';
            messagesDiv.appendChild(typingDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;

            try {
                const response = await fetch('../controller/ChatbotC.php', {
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
                    addMessage('Erreur: format de reponse inattendu.', 'bot');
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
    <?php } ?>
</body>
</html>

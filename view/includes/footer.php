            </section>
            <?php if (!isset($isBackOffice) || !$isBackOffice) { ?>
                <footer class="site-footer">
                    <div class="footer-grid">
                        <div>
                            <a class="public-brand footer-brand" href="../controller/HomeC.php">
                                <span class="brand-mark brand-briefcase" aria-hidden="true">
                                    <svg viewBox="0 0 24 24"><path d="M10 5h4a2 2 0 0 1 2 2v2h4v10H4V9h4V7a2 2 0 0 1 2-2zm4 4V7h-4v2h4zm-8 4v4h12v-4h-3v2H9v-2H6z"/></svg>
                                </span>
                                <span class="brand-text">Workify</span>
                            </a>
                            <p class="muted">Une plateforme professionnelle pour apprendre, recruter, postuler et collaborer avec une experience fluide.</p>
                        </div>
                        <div>
                            <h3>Navigation</h3>
                            <a href="../controller/HomeC.php#services">Services</a>
                            <a href="../controller/HomeC.php#about">A propos</a>
                            <a href="../controller/HomeC.php#projects">Projets</a>
                            <a href="../controller/HomeC.php#contact">Contact</a>
                        </div>
                        <div>
                            <h3>Modules</h3>
                            <a href="../controller/JobC.php?office=front&action=list">Jobs</a>
                            <a href="../controller/FormationC.php?office=front&action=list">Formations</a>
                            <a href="../controller/EventC.php?office=front&action=list">Evenements</a>
                            <a href="../controller/PublicationC.php?office=front&action=list">Publications</a>
                            <a href="../controller/MessageC.php?office=front&action=list">Messages</a>
                            <a href="../controller/AuthController.php?action=signup">Inscription</a>
                        </div>
                        <div>
                            <h3>Contact</h3>
                            <span>workifytn@gmail.com</span>
                            <span>Tunis, Tunisie</span>
                            <div class="social-links">
                                <a href="#contact">LinkedIn</a>
                                <a href="#contact">Instagram</a>
                                <a href="#contact">Facebook</a>
                            </div>
                        </div>
                    </div>
                    <div class="footer-bottom">
                        <span>&copy; <?= date('Y'); ?> Workify. Tous droits reserves.</span>
                        <a href="../controller/AuthController.php?action=login">Connexion</a>
                    </div>
                </footer>
            <?php } ?>
        </main>
    </div>
    <script src="../assets/js/validation.js"></script>
    <script src="../assets/js/mui-motion.js"></script>

    <?php
    $chatbotModules = [
        'formations' => [
            'title' => 'Assistant Formation',
            'intro' => 'Bonjour ! Je peux vous aider avec les formations, plans, categories et inscriptions.'
        ],
        'publications' => [
            'title' => 'Assistant Publication',
            'intro' => 'Bonjour ! Je peux vous aider a rediger, ameliorer ou structurer une publication Workify.'
        ],
        'messages' => [
            'title' => 'Assistant Message',
            'intro' => 'Bonjour ! Je peux vous aider a ecrire une reponse claire et professionnelle.'
        ]
    ];
    $chatbotConfig = (isset($activeModule) && isset($chatbotModules[$activeModule])) ? $chatbotModules[$activeModule] : null;
    ?>

    <?php if ($chatbotConfig) { ?>
        <div id="chatbot-toggle">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <?= htmlspecialchars($chatbotConfig['title'], ENT_QUOTES); ?>
        </div>

        <div id="chatbot-widget">
            <div id="chatbot-header">
                <span><?= htmlspecialchars($chatbotConfig['title'], ENT_QUOTES); ?></span>
                <button id="chatbot-close" type="button" aria-label="Fermer">x</button>
            </div>
            <div id="chatbot-messages">
                <div class="chat-msg bot"><?= htmlspecialchars($chatbotConfig['intro'], ENT_QUOTES); ?></div>
            </div>
            <div id="chatbot-input-container">
                <input type="text" id="chatbot-input" placeholder="Posez une question...">
                <button id="chatbot-send" type="button">Envoyer</button>
            </div>
        </div>

        <script>
        const workifyChatbotModule = <?= json_encode($activeModule); ?>;

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
                    body: JSON.stringify({ message: msg, module: workifyChatbotModule })
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

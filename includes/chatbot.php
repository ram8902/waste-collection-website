<!-- Chatbot Widget -->
<div id="chatbot-widget" class="chatbot-widget">
    <!-- Chat Button -->
    <button id="chatbot-toggle" class="chatbot-toggle">
        <img src="assets/images/chatbot_logo.png" alt="Chatbot" class="chatbot-toggle-logo">
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window">
        <div class="chatbot-header">
            <div class="d-flex align-items-center">
                <div class="chatbot-avatar">
                    <i class="bi bi-robot"></i>
                </div>
                <div class="ms-2">
                    <h6 class="mb-0">WasteBot</h6>
                    <small class="text-white-50">Online</small>
                </div>
            </div>
            <button id="chatbot-close" class="chatbot-close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="chatbot-messages" class="chatbot-messages">
            <div class="message bot-message">
                Hello! 👋 I'm WasteBot. How can I help you with your waste collection today?
            </div>
        </div>
        <div class="chatbot-input-area">
            <form id="chatbot-form" class="d-flex gap-2">
                <input type="text" id="chatbot-input" class="form-control" placeholder="Type a message..." autocomplete="off">
                <button type="submit" class="btn btn-primary btn-send">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Chatbot Styles */
    .chatbot-widget {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 9999;
        font-family: 'Poppins', sans-serif;
    }

    /* Toggle Button */
    .chatbot-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color, #0d6efd), var(--info-color, #0dcaf0));
        color: white;
        border: none;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .chatbot-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
    }

    .chatbot-toggle-logo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Chat Window */
    .chatbot-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 350px;
        height: 500px;
        background: var(--card-bg, #ffffff);
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform-origin: bottom right;
        transform: scale(0);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid var(--border-color, rgba(0,0,0,0.1));
    }

    .chatbot-window.active {
        transform: scale(1);
        opacity: 1;
    }

    /* Dark Mode Support */
    body.dark-mode .chatbot-window {
        background: rgba(19, 26, 42, 0.95);
        backdrop-filter: blur(10px);
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Header */
    .chatbot-header {
        background: linear-gradient(135deg, var(--primary-color, #0d6efd), var(--info-color, #0dcaf0));
        padding: 1rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chatbot-avatar {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .chatbot-close {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .chatbot-close:hover {
        opacity: 1;
    }

    /* Messages Area */
    .chatbot-messages {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        max-width: 80%;
        padding: 0.75rem 1rem;
        border-radius: 15px;
        font-size: 0.95rem;
        line-height: 1.4;
        animation: fadeIn 0.3s ease;
    }

    .bot-message {
        background: var(--bg-color, #f8f9fa);
        color: var(--text-color, #212529);
        align-self: flex-start;
        border-bottom-left-radius: 5px;
        border: 1px solid var(--border-color, #dee2e6);
    }

    body.dark-mode .bot-message {
        background: rgba(255, 255, 255, 0.05);
        color: #e0e0e0;
        border-color: rgba(255, 255, 255, 0.1);
    }

    .user-message {
        background: linear-gradient(135deg, var(--primary-color, #0d6efd), var(--info-color, #0dcaf0));
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 5px;
        box-shadow: 0 2px 5px rgba(13, 110, 253, 0.2);
    }

    /* Input Area */
    .chatbot-input-area {
        padding: 1rem;
        border-top: 1px solid var(--border-color, #dee2e6);
        background: var(--card-bg, #ffffff);
    }

    body.dark-mode .chatbot-input-area {
        background: transparent;
        border-color: rgba(255, 255, 255, 0.1);
    }

    #chatbot-input {
        border-radius: 25px;
        padding-left: 1.2rem;
        border: 1px solid var(--border-color, #dee2e6);
        background: var(--bg-color, #f8f9fa);
        color: var(--text-color, #212529);
    }

    body.dark-mode #chatbot-input {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
        color: white;
    }

    body.dark-mode #chatbot-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .btn-send {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Typing Indicator */
    .typing-indicator {
        display: flex;
        gap: 5px;
        padding: 10px 15px;
        background: var(--bg-color, #f8f9fa);
        border-radius: 15px;
        border-bottom-left-radius: 5px;
        width: fit-content;
    }
    
    body.dark-mode .typing-indicator {
        background: rgba(255, 255, 255, 0.05);
    }

    .dot {
        width: 8px;
        height: 8px;
        background: #adb5bd;
        border-radius: 50%;
        animation: bounce 1.4s infinite ease-in-out both;
    }

    .dot:nth-child(1) { animation-delay: -0.32s; }
    .dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes bounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const chatWindow = document.getElementById('chatbot-window');
    const chatForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chatbot-input');
    const messagesContainer = document.getElementById('chatbot-messages');

    // Toggle Chat
    function toggleChat() {
        chatWindow.classList.toggle('active');
        if (chatWindow.classList.contains('active')) {
            setTimeout(() => chatInput.focus(), 300);
        }
    }

    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    // Send Message
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        const typingId = showTyping();

        try {
            // Call API
            const response = await fetch('api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            
            // Remove typing indicator
            removeTyping(typingId);

            // Add bot response
            if (data.response) {
                addMessage(data.response, 'bot');
            } else if (data.error) {
                addMessage("Sorry, I encountered an error: " + data.error, 'bot');
            }
        } catch (error) {
            removeTyping(typingId);
            addMessage("Sorry, I'm having trouble connecting to the server.", 'bot');
            console.error('Chat Error:', error);
        }
    });

    function addMessage(text, sender) {
        const div = document.createElement('div');
        div.className = `message ${sender}-message`;
        div.textContent = text;
        messagesContainer.appendChild(div);
        scrollToBottom();
    }

    function showTyping() {
        const id = 'typing-' + Date.now();
        const div = document.createElement('div');
        div.className = 'typing-indicator';
        div.id = id;
        div.innerHTML = `
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        `;
        messagesContainer.appendChild(div);
        scrollToBottom();
        return id;
    }

    function removeTyping(id) {
        const element = document.getElementById(id);
        if (element) element.remove();
    }

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>

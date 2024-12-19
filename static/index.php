<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .chat-container {
            width: 100%;
            max-width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }
        .chat-history {
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 20px;
            padding-right: 10px; /* To avoid overlap with scrollbar */
        }
        .chat-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .chat-message {
            margin: 10px 0;
        }
        .bot-message {
            background-color: #d4f7e1;
            padding: 10px;
            border-radius: 5px;
        }
        .user-message {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            text-align: right;
        }
        .loading {
            font-style: italic;
            color: #888;
        }

        @media (max-width: 600px) {
            .chat-container {
                width: 90%;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-history" id="chat-history"></div>
        <input type="text" id="user-message" class="chat-input" placeholder="Type a message..." />
    </div>

    <script>
        // Open WebSocket connection to the Python backend
        const socket = new WebSocket("ws://127.0.0.1:5000");

        // When a message is received from Python, display it in the chat history
        socket.onmessage = function(event) {
            const chatHistory = document.getElementById('chat-history');
            const message = document.createElement('div');
            message.classList.add('chat-message', 'bot-message');
            message.textContent = "Bot: " + event.data;

            // Remove loading message
            const loadingMessage = document.querySelector('.loading');
            if (loadingMessage) {
                loadingMessage.remove();
            }

            chatHistory.appendChild(message);
            chatHistory.scrollTop = chatHistory.scrollHeight;  // Scroll to the latest message
        };

        // Handle WebSocket connection errors
        socket.onerror = function(error) {
            console.error("WebSocket Error: ", error);
            alert("Connection failed! Please try again later.");
        };

        // Send message to Python backend when user presses Enter
        document.getElementById('user-message').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                const message = event.target.value;
                if (message.trim() !== '') {
                    // Display user's message
                    const chatHistory = document.getElementById('chat-history');
                    const userMessage = document.createElement('div');
                    userMessage.classList.add('chat-message', 'user-message');
                    userMessage.textContent = "You: " + message;
                    chatHistory.appendChild(userMessage);

                    // Show loading message while waiting for response
                    const loadingMessage = document.createElement('div');
                    loadingMessage.classList.add('chat-message', 'loading');
                    loadingMessage.textContent = "Bot is typing...";
                    chatHistory.appendChild(loadingMessage);
                    
                    chatHistory.scrollTop = chatHistory.scrollHeight;  // Scroll to the latest message
                    socket.send(message);  // Send message to Python
                    event.target.value = '';  // Clear input field
                }
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat System with SSE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
        }
        #messages {
            width: 100%;
            height: 300px;
            overflow-y: auto;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            padding: 10px;
        }
        input[type="text"] {
            padding: 10px;
            width: 70%;
        }
        button {
            padding: 10px;
        }
    </style>
</head>
<body>

    <h1>Chat System</h1>
    <div id="messages"></div>
    <input type="text" id="username" placeholder="Enter your name" />
    <input type="text" id="message" placeholder="Enter your message" />
    <button onclick="sendMessage()">Send Message</button>

    <script>
        let lastMessageTimestamp = 0; // Keep track of the last message received
        let isPolling = false; // Prevent concurrent polling
        const pollingInterval = 2000; // Poll every 2 seconds

        // Function to fetch new messages
        async function fetchMessages() {
            if (isPolling) return; // Avoid overlapping polling
            isPolling = true;

            try {
                const response = await fetch(`/functions/chat.php?room=${roomId}&timestamp=${lastMessageTimestamp}`);
                const messages = await response.json();

                // Process only new messages
                if (messages.length > 0) {
                    messages.forEach(message => appendMessage(message));
                    lastMessageTimestamp = messages[messages.length - 1].timestamp; // Update the timestamp
                }
            } catch (error) {
                console.error('Error fetching messages:', error);
            } finally {
                isPolling = false;
            }
        }

        // Append new message to the chat box
        function appendMessage(message) {
            const messagesDiv = document.getElementById('messages');
            const messageElement = document.createElement('div');
            messageElement.textContent = `${message.sender}: ${message.text}`;
            messagesDiv.appendChild(messageElement);

            // Optional: Scroll to the bottom of the chat box
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // Start polling for messages
        setInterval(fetchMessages, pollingInterval);

        // Send a new message
        async function sendMessage(text) {
            const data = { room: roomId, sender: 'User', text };

            try {
                await fetch('/functions/send_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data),
                });
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }

        // Example: Sending a message
        document.getElementById('sendButton').addEventListener('click', () => {
            const textInput = document.getElementById('messageInput');
            const messageText = textInput.value.trim();

            if (messageText) {
                sendMessage(messageText);
                textInput.value = '';
            }
        });

    </script>

</body>
</html>

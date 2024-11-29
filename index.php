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
        const messagesContainer = document.getElementById('messages');
        const usernameInput = document.getElementById('username');
        const messageInput = document.getElementById('message');

        // Function to send a message to the server
        function sendMessage() {
            const username = usernameInput.value;
            const message = messageInput.value;

            if (username && message) {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('message', message);

                fetch('functions/send_message.php', {
                    method: 'POST',
                    body: formData
                }).then(() => {
                    messageInput.value = '';  // Clear input after sending
                }).catch(error => {
                    console.error('Error sending message:', error);
                });
            }
        }

        // Function to listen for new messages via SSE
        function listenForMessages() {
            const eventSource = new EventSource('functions/chat.php');

            eventSource.onmessage = function(event) {
                const messageData = JSON.parse(event.data);
                const messageElement = document.createElement('div');
                messageElement.textContent = `${messageData.username}: ${messageData.message}`;
                messagesContainer.appendChild(messageElement);

                // Scroll to the latest message
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            };

            eventSource.onerror = function() {
                console.error('Error with SSE connection');
                eventSource.close();
            };
        }

        // Start listening for messages as soon as the page loads
        listenForMessages();
    </script>

</body>
</html>

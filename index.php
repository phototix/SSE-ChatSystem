<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Chat</title>
</head>
<body>
    <div id="video-container">
        <video id="host-video" autoplay></video>
    </div>
    <div id="chat-container">
        <div id="chat"></div>
        <input type="text" id="message" placeholder="Enter message">
        <button id="send">Send</button>
    </div>

    <script>
        // Check if room_id exists in the URL, if not, generate a new one and update the URL
        let roomId = new URLSearchParams(window.location.search).get('room_id');
        if (!roomId) {
            roomId = generateRoomId();
            const newUrl = window.location.href.split('?')[0] + '?room_id=' + roomId;
            window.history.replaceState(null, '', newUrl);
        }

        const socket = new WebSocket('wss://chatserver.brandon.my/ws/');
        const messageInput = document.getElementById('message');
        const sendButton = document.getElementById('send');
        const chatContainer = document.getElementById('chat');
        const hostVideo = document.getElementById('host-video');

        let peerConnection;

        socket.onopen = () => {
            console.log('Connected to WebSocket server');
            socket.send(JSON.stringify({type: 'join', room_id: roomId}));
        };

        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (data.type === 'chat') {
                const chatMessage = document.createElement('p');
                chatMessage.textContent = data.message;
                chatContainer.appendChild(chatMessage);
            }
        };

        sendButton.addEventListener('click', () => {
            const message = messageInput.value;
            socket.send(JSON.stringify({type: 'chat', room_id: roomId, message: message}));
            messageInput.value = '';
        });

        // Video stream
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                hostVideo.srcObject = stream;
                peerConnection = new RTCPeerConnection();
                stream.getTracks().forEach(track => peerConnection.addTrack(track, stream));
            })
            .catch(error => console.log('Error accessing webcam: ', error));

        // Function to generate a random room ID
        function generateRoomId() {
            return Math.random().toString(36).substr(2, 9); // Generates a random 9-character string
        }
    </script>
</body>
</html>

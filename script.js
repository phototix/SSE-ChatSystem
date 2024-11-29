const roomId = new URLSearchParams(window.location.search).get('room');
if (!roomId) {
    alert('Room ID is required in the URL (e.g., ?room=123)');
    throw new Error('Missing Room ID');
}

const signalingUrl = '/functions/signaling.php';
let isHost = false;
let peerConnections = {};
let localStream = null;
const videoContainer = document.getElementById('video-container');

// Initialize WebRTC and signaling
(async function init() {
    // Check if the current user is the host
    isHost = await checkIfHost();

    // Get local media stream
    localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
    addVideoStream('localVideo', localStream);

    if (isHost) {
        console.log('You are the host. Waiting for clients...');
        pollForClients();
    } else {
        console.log('You are a client. Connecting to the host...');
        connectToHost();
    }
})();

// Add video stream to the DOM
function addVideoStream(videoId, stream) {
    const videoElement = document.createElement('video');
    videoElement.id = videoId;
    videoElement.srcObject = stream;
    videoElement.autoplay = true;
    videoElement.playsInline = true;
    videoContainer.appendChild(videoElement);
}

// Check if the user is the host
async function checkIfHost() {
    const response = await fetch(`${signalingUrl}?action=check_host&room=${roomId}`);
    const data = await response.json();
    if (data.is_host === false) {
        await fetch(`${signalingUrl}?action=set_host&room=${roomId}`);
        return true;
    }
    return false;
}

// Poll for clients (host)
async function pollForClients() {
    setInterval(async () => {
        const response = await fetch(`${signalingUrl}?action=get_clients&room=${roomId}`);
        const clients = await response.json();
        clients.forEach(clientId => {
            if (!peerConnections[clientId]) {
                createPeerConnection(clientId, true);
            }
        });
    }, 3000);
}

// Connect to the host (client)
async function connectToHost() {
    const response = await fetch(`${signalingUrl}?action=get_host&room=${roomId}`);
    const hostId = await response.text();
    if (hostId) {
        createPeerConnection(hostId, false);
    }
}

// Create Peer Connection
async function createPeerConnection(peerId, isInitiator) {
    const peerConnection = new RTCPeerConnection();
    peerConnections[peerId] = peerConnection;

    // Add local stream to the connection
    localStream.getTracks().forEach(track => peerConnection.addTrack(track, localStream));

    // Handle remote stream
    peerConnection.ontrack = event => {
        if (!document.getElementById(peerId)) {
            addVideoStream(peerId, event.streams[0]);
        }
    };

    // Handle ICE candidates
    peerConnection.onicecandidate = async event => {
        if (event.candidate) {
            await sendSignalingData(peerId, { candidate: event.candidate });
        }
    };

    // Create Offer/Answer
    if (isInitiator) {
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);
        await sendSignalingData(peerId, { offer });
    } else {
        pollForSignalingData(peerId);
    }
}

// Poll for signaling data (Client)
async function pollForSignalingData(peerId) {
    setInterval(async () => {
        const response = await fetch(`${signalingUrl}?action=get_signaling&peer=${peerId}&room=${roomId}`);
        const signalingData = await response.json();

        if (signalingData.offer) {
            const peerConnection = peerConnections[peerId];
            await peerConnection.setRemoteDescription(new RTCSessionDescription(signalingData.offer));
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);
            await sendSignalingData(peerId, { answer });
        }

        if (signalingData.candidate) {
            const peerConnection = peerConnections[peerId];
            await peerConnection.addIceCandidate(new RTCIceCandidate(signalingData.candidate));
        }
    }, 3000);
}

// Send signaling data
async function sendSignalingData(peerId, data) {
    await fetch(signalingUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ room: roomId, peer: peerId, data })
    });
}

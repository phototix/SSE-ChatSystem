<?php
session_start();

$room = $_GET['room'] ?? '';
$action = $_GET['action'] ?? '';
$data = json_decode(file_get_contents('php://input'), true);

$signalingFile = 'signaling.json';
$rooms = file_exists($signalingFile) ? json_decode(file_get_contents($signalingFile), true) : [];

if (!isset($rooms[$room])) {
    $rooms[$room] = ['host' => null, 'clients' => [], 'signaling' => []];
}

switch ($action) {
    case 'check_host':
        echo json_encode(['is_host' => $rooms[$room]['host'] !== null]);
        break;

    case 'set_host':
        $rooms[$room]['host'] = session_id();
        saveRooms();
        break;

    case 'get_host':
        echo $rooms[$room]['host'];
        break;

    case 'get_clients':
        echo json_encode($rooms[$room]['clients']);
        break;

    case 'get_signaling':
        $peer = $_GET['peer'] ?? '';
        echo json_encode($rooms[$room]['signaling'][$peer] ?? []);
        break;

    case 'send_signaling':
        $peer = $data['peer'];
        $rooms[$room]['signaling'][$peer][] = $data['data'];
        saveRooms();
        break;

    case 'add_client':
        $rooms[$room]['clients'][] = session_id();
        saveRooms();
        break;
}

function saveRooms() {
    global $rooms, $signalingFile;
    file_put_contents($signalingFile, json_encode($rooms));
}

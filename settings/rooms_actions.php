<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../classes/Room.php';

$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
$room = new Room();

function jsonResponse($success, $data = null, $message = '') {
    echo json_encode(array_filter([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ], function ($value) {
        return $value !== null;
    }));
    exit;
}

switch ($action) {
    case 'list':
        $data = $room->getAllRooms();
        jsonResponse(true, $data);
        break;

    case 'get':
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid room ID.');
        }
        $data = $room->getRoomById($id);
        if (!$data) {
            jsonResponse(false, null, 'Room not found.');
        }
        jsonResponse(true, $data);
        break;

    case 'add':
        $roomName = isset($_POST['room_name']) ? trim($_POST['room_name']) : '';
        $capacity = isset($_POST['capacity']) ? (int) $_POST['capacity'] : 0;

        if (empty($roomName) || $capacity <= 0) {
            jsonResponse(false, null, 'Please provide valid room information.');
        }

        if ($room->roomExists($roomName)) {
            jsonResponse(false, null, 'This room name already exists.');
        }

        $room->room_name = $roomName;
        $room->capacity = $capacity;

        $result = $room->addRoom();
        if ($result) {
            jsonResponse(true, null, 'Room added successfully.');
        }
        jsonResponse(false, null, 'Failed to add room.');
        break;

    case 'update':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $roomName = isset($_POST['room_name']) ? trim($_POST['room_name']) : '';
        $capacity = isset($_POST['capacity']) ? (int) $_POST['capacity'] : 0;

        if ($id <= 0 || empty($roomName) || $capacity <= 0) {
            jsonResponse(false, null, 'Please provide valid room information.');
        }

        if ($room->roomExists($roomName, $id)) {
            jsonResponse(false, null, 'This room name already exists.');
        }

        $room->id = $id;
        $room->room_name = $roomName;
        $room->capacity = $capacity;

        if ($room->updateRoom()) {
            jsonResponse(true, null, 'Room updated successfully.');
        }
        jsonResponse(false, null, 'Failed to update room.');
        break;

    case 'delete':
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($id <= 0) {
            jsonResponse(false, null, 'Invalid room ID.');
        }

        if ($room->deleteRoom($id)) {
            jsonResponse(true, null, 'Room deleted successfully.');
        }
        jsonResponse(false, null, 'Cannot delete room. It may have scheduled classes.');
        break;

    default:
        jsonResponse(false, null, 'Invalid action.');
}

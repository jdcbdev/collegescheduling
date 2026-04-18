<?php
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/SchoolClass.php';
require_once __DIR__ . '/../classes/Instructor.php';
require_once __DIR__ . '/../classes/Room.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $db = new Database();
    if ($action === 'getSchedule') {
        $type = $_GET['type'] ?? 'class';
        $id = $_GET['id'] ?? null;
        $schoolyear_id = $_GET['schoolyear_id'] ?? null;
        // Fetch schedule data based on type
        // TODO: Implement fetching logic
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }
    if ($action === 'getPrograms') {
        $stmt = $db->connect()->query("SELECT id, program_code, program_name FROM programs ORDER BY program_code");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }
    if ($action === 'getClassSections') {
        $program_id = (int)($_GET['program_id'] ?? 0);
        $stmt = $db->connect()->prepare("SELECT c.id, c.section_name FROM class c JOIN curriculum cu ON c.curriculum_id = cu.id WHERE cu.program_id = ? ORDER BY c.section_name");
        $stmt->execute([$program_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }
    if ($action === 'getInstructors') {
        $stmt = $db->connect()->query("SELECT id, instructor_code, firstname, lastname FROM instructors WHERE active_status = 1 ORDER BY lastname, firstname");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }
    if ($action === 'getRooms') {
        $stmt = $db->connect()->query("SELECT id, room_name FROM rooms ORDER BY room_name");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    exit;
}

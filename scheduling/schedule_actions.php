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
        $type = strtolower(trim($_GET['type'] ?? 'class'));
        $id = (int)($_GET['id'] ?? 0);
        $schoolyear_id = (int)($_GET['schoolyear_id'] ?? 0);

        if (!in_array($type, ['class', 'instructor', 'room'], true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid schedule type']);
            exit;
        }

        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid id']);
            exit;
        }

        $conn = $db->connect();

        if ($schoolyear_id <= 0) {
            $activeStmt = $conn->query("SELECT id FROM schoolyear WHERE is_active = TRUE LIMIT 1");
            $activeRow = $activeStmt->fetch(PDO::FETCH_ASSOC);
            $schoolyear_id = $activeRow ? (int)$activeRow['id'] : 0;
        }

        if ($schoolyear_id <= 0) {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }

        $filterColumn = 's.class_id';
        if ($type === 'instructor') {
            $filterColumn = 's.instructor_id';
        } elseif ($type === 'room') {
            $filterColumn = 's.room_id';
        }

        $sql = "SELECT
                    s.id,
                    s.day_of_week,
                    s.start_time,
                    s.end_time,
                    s.class_id,
                    s.instructor_id,
                    s.room_id,
                    sub.subject_code,
                    sub.subject_name,
                    c.section_name AS class_section,
                    CONCAT(i.lastname, ', ', i.firstname) AS instructor_name,
                    r.room_name
                FROM schedules s
                LEFT JOIN subjects sub ON sub.id = s.subject_id
                LEFT JOIN class c ON c.id = s.class_id
                LEFT JOIN instructors i ON i.id = s.instructor_id
                LEFT JOIN rooms r ON r.id = s.room_id
                WHERE s.schoolyear_id = :schoolyear_id
                  AND {$filterColumn} = :id
                ORDER BY FIELD(LOWER(s.day_of_week), 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'),
                         s.start_time";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $rows]);
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

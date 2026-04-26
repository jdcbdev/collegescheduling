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

function getActiveSchoolYearId(PDO $conn): int {
    $activeStmt = $conn->query("SELECT id FROM schoolyear WHERE is_active = TRUE LIMIT 1");
    $activeRow = $activeStmt->fetch(PDO::FETCH_ASSOC);
    return $activeRow ? (int)$activeRow['id'] : 0;
}

function ensureScheduleClassModeColumn(PDO $conn): void {
    $columnStmt = $conn->query("SHOW COLUMNS FROM schedules LIKE 'class_mode'");
    $columnExists = $columnStmt->fetch(PDO::FETCH_ASSOC);
    if (!$columnExists) {
        $conn->exec("ALTER TABLE schedules ADD COLUMN class_mode VARCHAR(10) NULL AFTER subject_id");
    }
}

function isOpenVenueRoom(PDO $conn, ?int $room_id): bool {
    if ($room_id === null || $room_id <= 0) return false;
    $stmt = $conn->prepare("SELECT room_name FROM rooms WHERE id = :id LIMIT 1");
    $stmt->bindValue(':id', $room_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return false;
    $name = strtolower(trim($row['room_name']));
    return $name === 'field' || $name === 'gym';
}

function findScheduleConflicts(PDO $conn, int $schoolyear_id, int $class_id, ?int $instructor_id, ?int $room_id, string $day_of_week, string $start_time, string $end_time, ?int $exclude_id = null): array {
    $openVenue = isOpenVenueRoom($conn, $room_id);

    $conflictClauses = ["s.class_id = :class_id_conflict"];
    if ($instructor_id !== null && $instructor_id > 0) {
        $conflictClauses[] = "s.instructor_id = :instructor_id_conflict";
    }
    if (!$openVenue && $room_id !== null && $room_id > 0) {
        $conflictClauses[] = "s.room_id = :room_id_conflict";
    }

    $conflictSql = "SELECT s.id, s.day_of_week, s.start_time, s.end_time,
                           s.class_id, s.instructor_id, s.room_id,
                           c.section_name AS class_section,
                           CONCAT(i.lastname, ', ', i.firstname) AS instructor_name,
                           r.room_name
                    FROM schedules s
                    LEFT JOIN class c ON c.id = s.class_id
                    LEFT JOIN instructors i ON i.id = s.instructor_id
                    LEFT JOIN rooms r ON r.id = s.room_id
                    WHERE s.schoolyear_id = :schoolyear_id
                      AND LOWER(s.day_of_week) = LOWER(:day_of_week)
                      AND s.start_time < :new_end_time
                      AND s.end_time > :new_start_time
                      AND (" . implode(' OR ', $conflictClauses) . ")";

    if ($exclude_id !== null && $exclude_id > 0) {
        $conflictSql .= " AND s.id <> :exclude_id";
    }

    $conflictStmt = $conn->prepare($conflictSql);
    $conflictStmt->bindValue(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
    $conflictStmt->bindValue(':day_of_week', $day_of_week, PDO::PARAM_STR);
    $conflictStmt->bindValue(':new_start_time', $start_time, PDO::PARAM_STR);
    $conflictStmt->bindValue(':new_end_time', $end_time, PDO::PARAM_STR);
    $conflictStmt->bindValue(':class_id_conflict', $class_id, PDO::PARAM_INT);
    if ($instructor_id !== null && $instructor_id > 0) {
        $conflictStmt->bindValue(':instructor_id_conflict', $instructor_id, PDO::PARAM_INT);
    }
    if (!$openVenue && $room_id !== null && $room_id > 0) {
        $conflictStmt->bindValue(':room_id_conflict', $room_id, PDO::PARAM_INT);
    }
    if ($exclude_id !== null && $exclude_id > 0) {
        $conflictStmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
    }
    $conflictStmt->execute();

    return $conflictStmt->fetchAll(PDO::FETCH_ASSOC);
}

try {
    $db = new Database();
    if ($action === 'getActiveSchoolYear') {
        $conn = $db->connect();
        $stmt = $conn->query("SELECT id, start_year, end_year, semester FROM schoolyear WHERE is_active = TRUE LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $row ?: null]);
        exit;
    }
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
        ensureScheduleClassModeColumn($conn);

        if ($schoolyear_id <= 0) {
            $schoolyear_id = getActiveSchoolYearId($conn);
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
                    s.class_mode,
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
    if ($action === 'addSchedule') {
        $conn = $db->connect();
        ensureScheduleClassModeColumn($conn);

        $schoolyear_id = (int)($_POST['schoolyear_id'] ?? 0);
        $class_id = (int)($_POST['class_id'] ?? 0);
        $class_mode = strtoupper(trim((string)($_POST['class_mode'] ?? 'LEC')));
        $subject_id_raw = $_POST['subject_id'] ?? null;
        $instructor_id_raw = $_POST['instructor_id'] ?? null;
        $room_id_raw = $_POST['room_id'] ?? null;
        $day_of_week = trim((string)($_POST['day_of_week'] ?? ''));
        $start_time = trim((string)($_POST['start_time'] ?? ''));
        $end_time = trim((string)($_POST['end_time'] ?? ''));

        $subject_id = ($subject_id_raw === null || $subject_id_raw === '') ? null : (int)$subject_id_raw;
        $instructor_id = ($instructor_id_raw === null || $instructor_id_raw === '') ? null : (int)$instructor_id_raw;
        $room_id = ($room_id_raw === null || $room_id_raw === '') ? null : (int)$room_id_raw;

        if ($schoolyear_id <= 0) {
            $schoolyear_id = getActiveSchoolYearId($conn);
        }

        if ($schoolyear_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'No active school year found']);
            exit;
        }

        if ($class_id <= 0 || $subject_id === null || $day_of_week === '' || $start_time === '' || $end_time === '') {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        if (!in_array($class_mode, ['LEC', 'LAB'], true)) {
            $class_mode = 'LEC';
        }

        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        if (!in_array(strtolower($day_of_week), $validDays, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid day of week']);
            exit;
        }

        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start_time) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end_time)) {
            echo json_encode(['success' => false, 'message' => 'Invalid time format']);
            exit;
        }

        if (strlen($start_time) === 5) $start_time .= ':00';
        if (strlen($end_time) === 5) $end_time .= ':00';

        if ($start_time >= $end_time) {
            echo json_encode(['success' => false, 'message' => 'End time must be later than start time']);
            exit;
        }

        $conflicts = findScheduleConflicts($conn, $schoolyear_id, $class_id, $instructor_id, $room_id, $day_of_week, $start_time, $end_time);

        if (!empty($conflicts)) {
            echo json_encode(['success' => false, 'message' => 'Schedule conflict detected', 'conflicts' => $conflicts]);
            exit;
        }

        $insertSql = "INSERT INTO schedules
                                                (schoolyear_id, class_id, subject_id, class_mode, instructor_id, room_id, day_of_week, start_time, end_time)
                      VALUES
                                                (:schoolyear_id, :class_id, :subject_id, :class_mode, :instructor_id, :room_id, :day_of_week, :start_time, :end_time)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bindValue(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
        $insertStmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
        $insertStmt->bindValue(':subject_id', $subject_id, $subject_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
                $insertStmt->bindValue(':class_mode', $class_mode, PDO::PARAM_STR);
        $insertStmt->bindValue(':instructor_id', $instructor_id, $instructor_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $insertStmt->bindValue(':room_id', $room_id, $room_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $insertStmt->bindValue(':day_of_week', $day_of_week, PDO::PARAM_STR);
        $insertStmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        $insertStmt->bindValue(':end_time', $end_time, PDO::PARAM_STR);
        $insertStmt->execute();

        echo json_encode(['success' => true, 'id' => (int)$conn->lastInsertId()]);
        exit;
    }
    if ($action === 'updateSchedule') {
        $conn = $db->connect();
        ensureScheduleClassModeColumn($conn);

        $id = (int)($_POST['id'] ?? 0);
        $schoolyear_id = (int)($_POST['schoolyear_id'] ?? 0);
        $class_id = (int)($_POST['class_id'] ?? 0);
        $class_mode = strtoupper(trim((string)($_POST['class_mode'] ?? 'LEC')));
        $subject_id_raw = $_POST['subject_id'] ?? null;
        $instructor_id_raw = $_POST['instructor_id'] ?? null;
        $room_id_raw = $_POST['room_id'] ?? null;
        $day_of_week = trim((string)($_POST['day_of_week'] ?? ''));
        $start_time = trim((string)($_POST['start_time'] ?? ''));
        $end_time = trim((string)($_POST['end_time'] ?? ''));

        $subject_id = ($subject_id_raw === null || $subject_id_raw === '') ? null : (int)$subject_id_raw;
        $instructor_id = ($instructor_id_raw === null || $instructor_id_raw === '') ? null : (int)$instructor_id_raw;
        $room_id = ($room_id_raw === null || $room_id_raw === '') ? null : (int)$room_id_raw;

        if ($id <= 0 || $class_id <= 0 || $subject_id === null || $day_of_week === '' || $start_time === '' || $end_time === '') {
            echo json_encode(['success' => false, 'message' => 'Missing required fields']);
            exit;
        }

        if ($schoolyear_id <= 0) {
            $lookupStmt = $conn->prepare("SELECT schoolyear_id FROM schedules WHERE id = ? LIMIT 1");
            $lookupStmt->execute([$id]);
            $lookup = $lookupStmt->fetch(PDO::FETCH_ASSOC);
            $schoolyear_id = $lookup ? (int)$lookup['schoolyear_id'] : 0;
        }

        if ($schoolyear_id <= 0) {
            $schoolyear_id = getActiveSchoolYearId($conn);
        }

        if ($schoolyear_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'No active school year found']);
            exit;
        }

        if (!in_array($class_mode, ['LEC', 'LAB'], true)) {
            $class_mode = 'LEC';
        }

        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        if (!in_array(strtolower($day_of_week), $validDays, true)) {
            echo json_encode(['success' => false, 'message' => 'Invalid day of week']);
            exit;
        }

        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start_time) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end_time)) {
            echo json_encode(['success' => false, 'message' => 'Invalid time format']);
            exit;
        }

        if (strlen($start_time) === 5) $start_time .= ':00';
        if (strlen($end_time) === 5) $end_time .= ':00';

        if ($start_time >= $end_time) {
            echo json_encode(['success' => false, 'message' => 'End time must be later than start time']);
            exit;
        }

        $conflicts = findScheduleConflicts($conn, $schoolyear_id, $class_id, $instructor_id, $room_id, $day_of_week, $start_time, $end_time, $id);
        if (!empty($conflicts)) {
            echo json_encode(['success' => false, 'message' => 'Schedule conflict detected', 'conflicts' => $conflicts]);
            exit;
        }

        $updateSql = "UPDATE schedules
                      SET class_id = :class_id,
                          subject_id = :subject_id,
                          class_mode = :class_mode,
                          instructor_id = :instructor_id,
                          room_id = :room_id,
                          day_of_week = :day_of_week,
                          start_time = :start_time,
                          end_time = :end_time
                      WHERE id = :id AND schoolyear_id = :schoolyear_id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindValue(':id', $id, PDO::PARAM_INT);
        $updateStmt->bindValue(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
        $updateStmt->bindValue(':class_id', $class_id, PDO::PARAM_INT);
        $updateStmt->bindValue(':subject_id', $subject_id, $subject_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $updateStmt->bindValue(':class_mode', $class_mode, PDO::PARAM_STR);
        $updateStmt->bindValue(':instructor_id', $instructor_id, $instructor_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $updateStmt->bindValue(':room_id', $room_id, $room_id === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $updateStmt->bindValue(':day_of_week', $day_of_week, PDO::PARAM_STR);
        $updateStmt->bindValue(':start_time', $start_time, PDO::PARAM_STR);
        $updateStmt->bindValue(':end_time', $end_time, PDO::PARAM_STR);
        $updateStmt->execute();

        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'deleteSchedule') {
        $conn = $db->connect();
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid schedule id']);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
        exit;
    }
    if ($action === 'getPrograms') {
        $stmt = $db->connect()->query("SELECT id, program_code, program_name FROM programs ORDER BY program_code");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }
    if ($action === 'getClassSections') {
        $conn = $db->connect();
        $activeSchoolYearId = getActiveSchoolYearId($conn);
        $program_id = (int)($_GET['program_id'] ?? 0);
        if ($activeSchoolYearId <= 0) {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }

                $stmt = $conn->prepare("SELECT c.id, c.section_name, p.id AS program_id, p.program_code, p.program_name
                                FROM class c
                                JOIN curriculum cu ON c.curriculum_id = cu.id
                                JOIN programs p ON p.id = cu.program_id
                                WHERE cu.program_id = ?
                                  AND c.schoolyear_id = ?
                                ORDER BY c.section_name");
        $stmt->execute([$program_id, $activeSchoolYearId]);
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
    if ($action === 'getAllClassSections') {
        $conn = $db->connect();
        $activeSchoolYearId = getActiveSchoolYearId($conn);
        if ($activeSchoolYearId <= 0) {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }

        $stmt = $conn->prepare("SELECT c.id, c.section_name, p.id AS program_id, p.program_code, p.program_name
                                FROM class c
                                LEFT JOIN curriculum cu ON cu.id = c.curriculum_id
                                LEFT JOIN programs p ON p.id = cu.program_id
                                WHERE c.schoolyear_id = ?
                                ORDER BY p.program_code, c.section_name");
        $stmt->execute([$activeSchoolYearId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }
    if ($action === 'getSubjectsByClass') {
        $conn = $db->connect();
        $activeSchoolYearId = getActiveSchoolYearId($conn);
        $class_id = (int)($_GET['class_id'] ?? 0);
        if ($class_id <= 0 || $activeSchoolYearId <= 0) {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }

        $stmt = $conn->prepare("SELECT s.id, s.subject_code, s.subject_name, s.lec_credits, s.lab_credits
                                FROM class c
                                JOIN schoolyear sy ON sy.id = c.schoolyear_id
                                JOIN subjects s ON s.curriculum_id = c.curriculum_id
                                               AND s.year_level = c.year_level
                                               AND s.semester = sy.semester
                                WHERE c.id = ?
                                  AND c.schoolyear_id = ?
                                ORDER BY s.subject_code");
        $stmt->execute([$class_id, $activeSchoolYearId]);
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

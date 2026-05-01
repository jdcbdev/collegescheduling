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

function ensureScheduleClassSizeColumn(PDO $conn): void {
    $columnStmt = $conn->query("SHOW COLUMNS FROM schedules LIKE 'class_size'");
    $columnExists = $columnStmt->fetch(PDO::FETCH_ASSOC);
    if (!$columnExists) {
        $conn->exec("ALTER TABLE schedules ADD COLUMN class_size INT(11) NOT NULL DEFAULT 40 AFTER end_time");
    }
}

function getFallbackCurriculumId(PDO $conn): int {
    $stmt = $conn->query("SELECT id FROM curriculum ORDER BY id ASC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (int)$row['id'] : 0;
}

function ensureServiceNullableColumns(PDO $conn): void {
    try {
        $conn->exec("ALTER TABLE class MODIFY curriculum_id INT(11) DEFAULT NULL");
        $conn->exec("ALTER TABLE class MODIFY year_level INT(11) DEFAULT NULL");
    } catch (\Exception $e) {
        // Already nullable; ignore
    }
    try {
        $conn->exec("ALTER TABLE subjects MODIFY year_level INT(11) DEFAULT NULL");
        $conn->exec("ALTER TABLE subjects MODIFY semester INT(11) DEFAULT NULL");
    } catch (\Exception $e) {
        // Already nullable; ignore
    }
}

function findOrCreateServiceClassId(PDO $conn, int $schoolyear_id, string $sectionName): int {
    $sectionName = trim($sectionName);
    if ($sectionName === '') {
        return 0;
    }

    $findStmt = $conn->prepare("SELECT id FROM class WHERE schoolyear_id = :schoolyear_id AND LOWER(section_name) = LOWER(:section_name) LIMIT 1");
    $findStmt->bindValue(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
    $findStmt->bindValue(':section_name', $sectionName, PDO::PARAM_STR);
    $findStmt->execute();
    $found = $findStmt->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        return (int)$found['id'];
    }

    ensureServiceNullableColumns($conn);

    $insertStmt = $conn->prepare("INSERT INTO class (schoolyear_id, curriculum_id, section_name, year_level) VALUES (:schoolyear_id, NULL, :section_name, NULL)");
    $insertStmt->bindValue(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
    $insertStmt->bindValue(':section_name', substr($sectionName, 0, 50), PDO::PARAM_STR);
    $insertStmt->execute();

    return (int)$conn->lastInsertId();
}

function parseServiceSubjectInput(string $rawText): array {
    $text = trim($rawText);
    if ($text === '') {
        return ['', ''];
    }

    $parts = preg_split('/\s*-\s*/', $text, 2);
    if ($parts && count($parts) === 2) {
        $subjectCode = trim($parts[0]);
        $subjectName = trim($parts[1]);
        if ($subjectCode !== '' && $subjectName !== '') {
            return [$subjectCode, $subjectName];
        }
    }

    $sanitized = preg_replace('/[^A-Za-z0-9]+/', '', strtoupper($text));
    $subjectCode = $sanitized !== '' ? substr($sanitized, 0, 50) : 'SRV' . date('His');
    return [$subjectCode, $text];
}

function findOrCreateSubjectId(PDO $conn, int $semester, string $rawSubjectText): ?int {
    $trimmed = trim($rawSubjectText);
    if ($trimmed === '') {
        return null;
    }

    [$subjectCode, $subjectName] = parseServiceSubjectInput($trimmed);

    $findStmt = $conn->prepare("SELECT id FROM subjects WHERE LOWER(subject_code) = LOWER(:subject_code) OR LOWER(subject_name) = LOWER(:subject_name) LIMIT 1");
    $findStmt->bindValue(':subject_code', $subjectCode, PDO::PARAM_STR);
    $findStmt->bindValue(':subject_name', $subjectName, PDO::PARAM_STR);
    $findStmt->execute();
    $found = $findStmt->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        return (int)$found['id'];
    }

    ensureServiceNullableColumns($conn);

    $insertStmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name, lec_credits, lab_credits, curriculum_id, year_level, semester)
                                  VALUES (:subject_code, :subject_name, 0, 0, NULL, NULL, NULL)");
    $insertStmt->bindValue(':subject_code', substr($subjectCode, 0, 50), PDO::PARAM_STR);
    $insertStmt->bindValue(':subject_name', substr($subjectName, 0, 200), PDO::PARAM_STR);
    $insertStmt->execute();

    return (int)$conn->lastInsertId();
}

function findOrCreateRoomId(PDO $conn, string $roomName): ?int {
    $roomName = trim($roomName);
    if ($roomName === '') {
        return null;
    }

    $findStmt = $conn->prepare("SELECT id FROM rooms WHERE LOWER(room_name) = LOWER(:room_name) LIMIT 1");
    $findStmt->bindValue(':room_name', $roomName, PDO::PARAM_STR);
    $findStmt->execute();
    $found = $findStmt->fetch(PDO::FETCH_ASSOC);
    if ($found) {
        return (int)$found['id'];
    }

    $insertStmt = $conn->prepare("INSERT INTO rooms (room_name, capacity) VALUES (:room_name, 50)");
    $insertStmt->bindValue(':room_name', substr($roomName, 0, 50), PDO::PARAM_STR);
    $insertStmt->execute();

    return (int)$conn->lastInsertId();
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
        ensureScheduleClassSizeColumn($conn);

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
                    s.class_size,
                    sub.subject_code,
                    sub.subject_name,
                    c.section_name AS class_section,
                    CONCAT(i.firstname, ' ', i.lastname) AS instructor_name,
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
        ensureScheduleClassSizeColumn($conn);

        $schoolyear_id = (int)($_POST['schoolyear_id'] ?? 0);
        $class_id = (int)($_POST['class_id'] ?? 0);
        $program_id = (int)($_POST['program_id'] ?? 0);
        $class_section_name = trim((string)($_POST['class_section_name'] ?? ''));
        $class_mode = strtoupper(trim((string)($_POST['class_mode'] ?? 'LEC')));
        $subject_id_raw = $_POST['subject_id'] ?? null;
        $subject_name = trim((string)($_POST['subject_name'] ?? ''));
        $instructor_id_raw = $_POST['instructor_id'] ?? null;
        $room_id_raw = $_POST['room_id'] ?? null;
        $room_name = trim((string)($_POST['room_name'] ?? ''));
        $day_of_week = trim((string)($_POST['day_of_week'] ?? ''));
        $start_time = trim((string)($_POST['start_time'] ?? ''));
        $end_time = trim((string)($_POST['end_time'] ?? ''));
        $class_size_raw = $_POST['class_size'] ?? 40;

        $subject_id = ($subject_id_raw === null || $subject_id_raw === '') ? null : (int)$subject_id_raw;
        $instructor_id = ($instructor_id_raw === null || $instructor_id_raw === '') ? null : (int)$instructor_id_raw;
        $room_id = ($room_id_raw === null || $room_id_raw === '') ? null : (int)$room_id_raw;
        $class_size = (int)$class_size_raw;

        if ($class_size <= 0) {
            $class_size = 40;
        }

        if ($schoolyear_id <= 0) {
            $schoolyear_id = getActiveSchoolYearId($conn);
        }

        if ($schoolyear_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'No active school year found']);
            exit;
        }

        if ($class_id <= 0 && $class_section_name !== '') {
            $class_id = findOrCreateServiceClassId($conn, $schoolyear_id, $class_section_name);
        }

        if ($subject_id === null && $subject_name !== '') {
            $semesterStmt = $conn->prepare("SELECT semester FROM schoolyear WHERE id = :id LIMIT 1");
            $semesterStmt->bindValue(':id', $schoolyear_id, PDO::PARAM_INT);
            $semesterStmt->execute();
            $semesterRow = $semesterStmt->fetch(PDO::FETCH_ASSOC);
            $semester = $semesterRow ? (int)$semesterRow['semester'] : 1;
            $subject_id = findOrCreateSubjectId($conn, $semester, $subject_name);
        }

        if ($room_id === null && $room_name !== '') {
            $room_id = findOrCreateRoomId($conn, $room_name);
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
                                                (schoolyear_id, class_id, subject_id, class_mode, instructor_id, room_id, day_of_week, start_time, end_time, class_size)
                      VALUES
                                                (:schoolyear_id, :class_id, :subject_id, :class_mode, :instructor_id, :room_id, :day_of_week, :start_time, :end_time, :class_size)";
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
        $insertStmt->bindValue(':class_size', $class_size, PDO::PARAM_INT);
        $insertStmt->execute();

        echo json_encode(['success' => true, 'id' => (int)$conn->lastInsertId()]);
        exit;
    }
    if ($action === 'updateSchedule') {
        $conn = $db->connect();
        ensureScheduleClassModeColumn($conn);
        ensureScheduleClassSizeColumn($conn);

        $id = (int)($_POST['id'] ?? 0);
        $schoolyear_id = (int)($_POST['schoolyear_id'] ?? 0);
        $class_id = (int)($_POST['class_id'] ?? 0);
        $program_id = (int)($_POST['program_id'] ?? 0);
        $class_section_name = trim((string)($_POST['class_section_name'] ?? ''));
        $class_mode = strtoupper(trim((string)($_POST['class_mode'] ?? 'LEC')));
        $subject_id_raw = $_POST['subject_id'] ?? null;
        $subject_name = trim((string)($_POST['subject_name'] ?? ''));
        $instructor_id_raw = $_POST['instructor_id'] ?? null;
        $room_id_raw = $_POST['room_id'] ?? null;
        $room_name = trim((string)($_POST['room_name'] ?? ''));
        $day_of_week = trim((string)($_POST['day_of_week'] ?? ''));
        $start_time = trim((string)($_POST['start_time'] ?? ''));
        $end_time = trim((string)($_POST['end_time'] ?? ''));
        $class_size_raw = $_POST['class_size'] ?? 40;

        $subject_id = ($subject_id_raw === null || $subject_id_raw === '') ? null : (int)$subject_id_raw;
        $instructor_id = ($instructor_id_raw === null || $instructor_id_raw === '') ? null : (int)$instructor_id_raw;
        $room_id = ($room_id_raw === null || $room_id_raw === '') ? null : (int)$room_id_raw;
        $class_size = (int)$class_size_raw;

        if ($class_size <= 0) {
            $class_size = 40;
        }

        if ($id <= 0 || $day_of_week === '' || $start_time === '' || $end_time === '') {
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

        if ($class_id <= 0 && $class_section_name !== '') {
            $class_id = findOrCreateServiceClassId($conn, $schoolyear_id, $class_section_name);
        }

        if ($subject_id === null && $subject_name !== '') {
            $semesterStmt = $conn->prepare("SELECT semester FROM schoolyear WHERE id = :id LIMIT 1");
            $semesterStmt->bindValue(':id', $schoolyear_id, PDO::PARAM_INT);
            $semesterStmt->execute();
            $semesterRow = $semesterStmt->fetch(PDO::FETCH_ASSOC);
            $semester = $semesterRow ? (int)$semesterRow['semester'] : 1;
            $subject_id = findOrCreateSubjectId($conn, $semester, $subject_name);
        }

        if ($room_id === null && $room_name !== '') {
            $room_id = findOrCreateRoomId($conn, $room_name);
        }

        if ($class_id <= 0 || $subject_id === null) {
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
                          end_time = :end_time,
                          class_size = :class_size
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
        $updateStmt->bindValue(':class_size', $class_size, PDO::PARAM_INT);
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

        $stmt = $conn->prepare("SELECT c.id, c.section_name, c.curriculum_id, p.id AS program_id, p.program_code, p.program_name
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
    if ($action === 'getAllSubjects') {
        $conn = $db->connect();
        $stmt = $conn->query("SELECT id, subject_code, subject_name, lec_credits, lab_credits, curriculum_id FROM subjects ORDER BY subject_code, subject_name");
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

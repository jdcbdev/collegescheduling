<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../classes/SchoolClass.php';
    require_once __DIR__ . '/../classes/SchoolYear.php';
    require_once __DIR__ . '/../classes/Curriculum.php';

    $action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'list';
    $schoolClass = new SchoolClass();
    $schoolYear = new SchoolYear();
    $curriculum = new Curriculum();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading classes: ' . $e->getMessage()
    ]);
    exit;
}

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

// Load additional classes
require_once __DIR__ . '/../classes/Program.php';

try {
    switch ($action) {
        case 'list':
            $schoolyear_id = isset($_REQUEST['schoolyear_id']) ? (int) $_REQUEST['schoolyear_id'] : 0;
            if ($schoolyear_id <= 0) {
                jsonResponse(false, null, 'Invalid school year.');
            }
            $data = $schoolClass->getSectionsBySchoolYear($schoolyear_id);
            jsonResponse(true, $data);
            break;

        case 'listByProgram':
            $program_id = isset($_REQUEST['program_id']) ? (int) $_REQUEST['program_id'] : 0;
            $schoolyear_id = isset($_REQUEST['schoolyear_id']) ? (int) $_REQUEST['schoolyear_id'] : 0;
            
            if ($program_id <= 0 || $schoolyear_id <= 0) {
                jsonResponse(false, null, 'Invalid program or school year.');
            }
            
            $conn = $schoolClass->connect();
            $sql = "SELECT 
                        c.id,
                        c.schoolyear_id,
                        c.curriculum_id,
                        c.section_name,
                        c.year_level,
                        c.created_at,
                        sy.start_year,
                        sy.end_year,
                        sy.semester,
                        cur.program_id,
                        p.program_code,
                        p.program_name
                    FROM class c
                    LEFT JOIN schoolyear sy ON c.schoolyear_id = sy.id
                    LEFT JOIN curriculum cur ON c.curriculum_id = cur.id
                    LEFT JOIN programs p ON cur.program_id = p.id
                    WHERE c.schoolyear_id = :schoolyear_id 
                    AND p.id = :program_id
                    ORDER BY c.year_level, c.section_name";
            
            $query = $conn->prepare($sql);
            $query->bindParam(':program_id', $program_id, PDO::PARAM_INT);
            $query->bindParam(':schoolyear_id', $schoolyear_id, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchAll(PDO::FETCH_ASSOC);
            jsonResponse(true, $data);
            break;

        case 'getPrograms':
            $program = new Program();
            $data = $program->getAllPrograms();
            jsonResponse(true, $data);
            break;

        case 'getCurriculums':
            $data = $curriculum->getAllCurriculum();
            jsonResponse(true, $data);
            break;

        case 'getCurriculumDetail':
            $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
            if ($id <= 0) {
                jsonResponse(false, null, 'Invalid curriculum ID.');
            }
            $data = $curriculum->getCurriculumById($id);
            if (!$data) {
                jsonResponse(false, null, 'Curriculum not found.');
            }
            jsonResponse(true, $data);
            break;

        case 'getYearLevelsByCurriculum':
            $curriculum_id = isset($_REQUEST['curriculum_id']) ? (int) $_REQUEST['curriculum_id'] : 0;
            if ($curriculum_id <= 0) {
                jsonResponse(false, null, 'Invalid curriculum ID.');
            }
            
            $conn = $schoolClass->connect();
            $sql = "SELECT DISTINCT year_level 
                    FROM subjects 
                    WHERE curriculum_id = :curriculum_id 
                    ORDER BY year_level ASC";
            
            $query = $conn->prepare($sql);
            $query->bindParam(':curriculum_id', $curriculum_id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_COLUMN);
            jsonResponse(true, $result);
            break;

        case 'get':
            $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
            if ($id <= 0) {
                jsonResponse(false, null, 'Invalid section ID.');
            }
            $data = $schoolClass->getSectionById($id);
            if (!$data) {
                jsonResponse(false, null, 'Section not found.');
            }
            jsonResponse(true, $data);
            break;

        case 'getActiveSchoolYear':
            $data = $schoolYear->getActiveSchoolYear();
            if (!$data) {
                jsonResponse(false, null, 'No active school year found.');
            }
            jsonResponse(true, $data);
            break;

        case 'add':
            $schoolyear_id = isset($_POST['schoolyear_id']) ? (int) $_POST['schoolyear_id'] : 0;
            $curriculum_id = isset($_POST['curriculum_id']) ? (int) $_POST['curriculum_id'] : 0;
            $year_level = isset($_POST['year_level']) ? (int) $_POST['year_level'] : 0;
            $section_letter = isset($_POST['section_letter']) ? trim(strtoupper($_POST['section_letter'])) : '';

            if ($schoolyear_id <= 0 || $curriculum_id <= 0 || $year_level <= 0 || empty($section_letter)) {
                jsonResponse(false, null, 'Please provide all required information.');
            }

            // Validate section letter is a single character
            if (strlen($section_letter) !== 1 || !ctype_alpha($section_letter)) {
                jsonResponse(false, null, 'Section letter must be a single alphabetic character.');
            }

            // Get curriculum details
            $curriculumData = $curriculum->getCurriculumById($curriculum_id);
            if (!$curriculumData) {
                jsonResponse(false, null, 'Curriculum not found.');
            }

            $program_code = $curriculumData['program_code'];

            // Generate section name
            $section_name = $schoolClass->generateSectionName($program_code, $year_level, $section_letter);

            // Check if section name already exists
            $existing = $schoolClass->getSectionsByCurriculum($curriculum_id, $schoolyear_id);
            foreach ($existing as $section) {
                if ($section['section_name'] === $section_name) {
                    jsonResponse(false, null, 'Section ' . $section_name . ' already exists for this curriculum and school year.');
                }
            }

            $schoolClass->schoolyear_id = $schoolyear_id;
            $schoolClass->curriculum_id = $curriculum_id;
            $schoolClass->section_name = $section_name;
            $schoolClass->year_level = $year_level;

            $result = $schoolClass->addSection();
            if ($result) {
                jsonResponse(true, null, 'Section ' . $section_name . ' added successfully.');
            }
            jsonResponse(false, null, 'Failed to add section.');
            break;

        case 'delete':
            $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
            if ($id <= 0) {
                jsonResponse(false, null, 'Invalid section ID.');
            }

            if ($schoolClass->deleteSection($id)) {
                jsonResponse(true, null, 'Section deleted successfully.');
            }
            jsonResponse(false, null, 'Failed to delete section.');
            break;

        default:
            jsonResponse(false, null, 'Invalid action.');
    }
} catch (PDOException $e) {
    jsonResponse(false, null, 'Database error: ' . $e->getMessage());
} catch (Exception $e) {
    jsonResponse(false, null, 'Error: ' . $e->getMessage());
}

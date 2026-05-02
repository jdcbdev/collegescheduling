<?php
require_once __DIR__ . '/../classes/Database.php';

$criteriaId = isset($_GET['criteria_id']) ? (int) $_GET['criteria_id'] : 0;
$programId = isset($_GET['program_id']) && $_GET['program_id'] !== '' ? (int) $_GET['program_id'] : null;
$schoolyearId = isset($_GET['schoolyear_id']) && $_GET['schoolyear_id'] !== '' ? (int) $_GET['schoolyear_id'] : null;

if ($criteriaId <= 0) {
    die('Invalid criteria.');
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare(
    "SELECT ac.id, ac.title, ac.schoolyear_id, ac.gwa_cutoff,
            CONCAT(sy.start_year, '-', sy.end_year, ' ',
                CASE sy.semester WHEN 1 THEN '1st Sem' WHEN 2 THEN '2nd Sem' WHEN 3 THEN 'Summer' ELSE CONCAT('Sem ', sy.semester) END
            ) AS school_year_label
     FROM awards_criteria ac
     LEFT JOIN schoolyear sy ON sy.id = ac.schoolyear_id
     WHERE ac.id = :criteria_id
     LIMIT 1"
);
$stmt->bindValue(':criteria_id', $criteriaId, PDO::PARAM_INT);
$stmt->execute();
$criteria = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$criteria) {
    die('Criteria not found.');
}

if ($schoolyearId === null) {
    $schoolyearId = (int) ($criteria['schoolyear_id'] ?? 0);
}

if ($schoolyearId <= 0) {
    $activeStmt = $conn->query("SELECT id FROM schoolyear WHERE is_active = 1 LIMIT 1");
    $activeRow = $activeStmt ? $activeStmt->fetch(PDO::FETCH_ASSOC) : null;
    $schoolyearId = $activeRow ? (int) $activeRow['id'] : 0;
}

$cutoff = isset($criteria['gwa_cutoff']) && $criteria['gwa_cutoff'] !== null ? (float) $criteria['gwa_cutoff'] : null;

$leftLogoPath = '../assets/images/logos/image 2.png';
$rightLogoPath = '../assets/images/logos/image 1.png';

$preparedByName = '';
$preparedByRole = 'COLLEGE SECRETARY';
$approvedByName = '';
$approvedByRole = 'DEAN';

try {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'college_officials'");
    $hasOfficialTable = $tableCheck && $tableCheck->fetch(PDO::FETCH_ASSOC);

    if ($hasOfficialTable) {
        $secStmt = $conn->query("SELECT name, title FROM college_officials WHERE is_secretary = 1 ORDER BY id ASC LIMIT 1");
        $secRow = $secStmt ? $secStmt->fetch(PDO::FETCH_ASSOC) : null;
        if (!empty($secRow['name'])) {
            $preparedByName = (string) $secRow['name'];
        }
        if (!empty($secRow['title'])) {
            $preparedByRole = strtoupper((string) $secRow['title']);
        }

        $deanStmt = $conn->query("SELECT name, title FROM college_officials WHERE is_dean = 1 ORDER BY id ASC LIMIT 1");
        $deanRow = $deanStmt ? $deanStmt->fetch(PDO::FETCH_ASSOC) : null;
        if (!empty($deanRow['name'])) {
            $approvedByName = (string) $deanRow['name'];
        }
        if (!empty($deanRow['title'])) {
            $approvedByRole = strtoupper((string) $deanRow['title']);
        }
    }
} catch (Exception $e) {
    // Keep default signature labels when officials table is unavailable.
}

$sql = "SELECT a.id, a.student_no, a.fn, a.mn, a.ln, a.gwa,
                             p.program_code,
               CONCAT(sy.start_year, '-', sy.end_year, ' ',
                    CASE sy.semester WHEN 1 THEN '1st Sem' WHEN 2 THEN '2nd Sem' WHEN 3 THEN 'Summer' ELSE CONCAT('Sem ', sy.semester) END
               ) AS school_year_label
        FROM applicants a
        LEFT JOIN programs p ON p.id = a.program_id
        LEFT JOIN schoolyear sy ON sy.id = a.schoolyear_id
        LEFT JOIN awards_criteria ac ON ac.id = a.criteria_id
        WHERE a.criteria_id = :criteria_id
          AND a.gwa IS NOT NULL
          AND ac.gwa_cutoff IS NOT NULL
          AND a.gwa <= ac.gwa_cutoff";

$params = [':criteria_id' => $criteriaId];
if ($programId !== null && $programId > 0) {
    $sql .= " AND a.program_id = :program_id";
    $params[':program_id'] = $programId;
}
if ($schoolyearId !== null && $schoolyearId > 0) {
    $sql .= " AND a.schoolyear_id = :schoolyear_id";
    $params[':schoolyear_id'] = $schoolyearId;
}

$sql .= " ORDER BY a.gwa ASC, a.ln ASC, a.fn ASC";

$listStmt = $conn->prepare($sql);
foreach ($params as $key => $value) {
    $listStmt->bindValue($key, $value, PDO::PARAM_INT);
}
$listStmt->execute();
$applicants = $listStmt->fetchAll(PDO::FETCH_ASSOC);

$schoolYearLabel = '';
if (!empty($applicants[0]['school_year_label'])) {
    $schoolYearLabel = (string) $applicants[0]['school_year_label'];
} elseif (!empty($criteria['school_year_label'])) {
    $schoolYearLabel = (string) $criteria['school_year_label'];
}

$programLabel = 'All Programs';
if ($programId !== null && $programId > 0) {
    $pStmt = $conn->prepare("SELECT program_code FROM programs WHERE id = :id LIMIT 1");
    $pStmt->bindValue(':id', $programId, PDO::PARAM_INT);
    $pStmt->execute();
    $pRow = $pStmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($pRow['program_code'])) {
        $programLabel = (string) $pRow['program_code'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Awards Qualified Applicants Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .header-main {
            display: flex;
            flex-direction: column;
        }

        .header-top{
            display: flex;
            justify-content: center;
        }

        .header-top-text{
            display: flex;
            flex-direction: column;
            align-items: center;
            width: max-content;
        }

        .header-top-text h3, .header-top-text p {
            display: flex;
        }

        @media print {
            @page {
                size: 8.5in 13in;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                background: #fff !important;
            }
        }

        html {
            background: #fff;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #fff;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: 1px solid #ccc;
            background: white;
            cursor: pointer;
            border-radius: 4px;
            font-size: 14px;
        }

        .btn:hover {
            background: #f0f0f0;
        }

        .print-container {
            width: 8.5in;
            height: 13in;
            max-height: 13in;
            background: white;
            margin: 0 auto;
            padding: 0.6in 1in 1in 1in;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-top {
            display: flex;
            grid-template-columns: 76px 1fr 76px;
            align-items: center;
            column-gap: 8px;
            margin-bottom: 4px;
        }

        .header-logo-wrap {
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .header-top-text {
            text-align: center;
        }

        .header-top-text h3 {
            margin: 1px 0;
            font-size: 14px;
        }

        .header-top-text p {
            margin: 1px 0;
            font-size: 12px;
        }

        .header .title {
            font-weight: bold;
            margin: 25px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
            table-layout: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            height: 20px;
            vertical-align: middle;
        }

        th {
            background: #f0f0f0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            font-size: 11px;
        }

        .center { text-align: center; }

        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            font-size: 12px;
        }

        .signature-line {
            text-align: left;
        }

        .signature-name {
            margin-top: 20px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .signature-role {
            margin-top: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-top: 1px solid #000;
        }

        .text-align-center { text-align: center !important; }

        .empty-row { text-align: center; color: #666; font-style: italic; }

        .column-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 10px;
            font-size: 14px;
        }

        .hide-name-column .name-column {
            display: none;
        }

        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-container {
                border: none;
                width: 100%;
                height: auto;
                max-height: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn" onclick="window.print()">🖨️ Print</button>
        <button class="btn" onclick="downloadPDF()">⬇️ Download PDF</button>
        <button class="btn" onclick="window.close()">Close</button>
        <label class="column-toggle"><input type="checkbox" id="chkNameColumn" checked onchange="toggleNameColumn(this)">Show Student Name Column</label>
    </div>

    <div class="print-container" id="printContainer">
        <div class="header">
            <div class="header-main">
                <div class="header-top">
                    <div class="header-logo-wrap" style="margin-right: 20px;">
                        <img class="header-logo" src="<?php echo htmlspecialchars($leftLogoPath); ?>" alt="Left Logo">
                    </div>
                    <div class="header-top-text">
                        <h3>WESTERN MINDANAO STATE UNIVERSITY</h3>
                        <h3>COLLEGE OF COMPUTING STUDIES</h3>
                        <p>S.Y. <?php echo htmlspecialchars($schoolYearLabel !== '' ? $schoolYearLabel : 'N/A'); ?></p>
                    </div>
                    <div class="header-logo-wrap" style="margin-left: 20px;">
                        <img class="header-logo" src="<?php echo htmlspecialchars($rightLogoPath); ?>" alt="Right Logo">
                    </div>
                </div>
                <div class="title"><?php echo htmlspecialchars(strtoupper((string)($criteria['title'] ?? ''))); ?> QUALIFIERS</div>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th class="name-column">Student Name</th>
                    <th>Student No.</th>
                    <th>Program</th>
                    <th>GWA</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$applicants): ?>
                    <tr>
                        <td colspan="5" class="empty-row">No qualified applicants found based on the selected cut-off.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($applicants as $idx => $row): ?>
                        <tr class="applicant-row">
                            <td class="row-num center"><?php echo (int) $idx + 1; ?></td>
                            <td class="name-column">
                                <?php
                                    $name = trim((string)($row['ln'] ?? '') . ', ' . (string)($row['fn'] ?? '') . ' ' . (string)($row['mn'] ?? ''));
                                    $displayName = strtoupper(preg_replace('/\s+/', ' ', $name));
                                ?>
                                <?php echo htmlspecialchars($displayName); ?>
                            </td>
                            <td class="center"><?php echo htmlspecialchars((string)($row['student_no'] ?? '')); ?></td>
                            <td class="center"><?php echo htmlspecialchars((string)($row['program_code'] ?? '')); ?></td>
                            <td class="center"><?php echo htmlspecialchars(number_format((float) $row['gwa'], 5, '.', '')); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer">
            <div class="signature-line">
                <p>PREPARED BY:</p>
                <div class="signature-name text-align-center"><?php echo htmlspecialchars($preparedByName !== '' ? $preparedByName : '____________________________________'); ?></div>
                <div class="signature-role text-align-center"><?php echo htmlspecialchars($preparedByRole); ?></div>
            </div>
            <div class="signature-line">
                <p>APPROVED BY:</p>
                <div class="signature-name text-align-center"><?php echo htmlspecialchars($approvedByName !== '' ? $approvedByName : '____________________________________'); ?></div>
                <div class="signature-role text-align-center"><?php echo htmlspecialchars($approvedByRole); ?></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function toggleNameColumn(chk) {
            document.getElementById('printContainer').classList.toggle('hide-name-column', !chk.checked);
        }

        function downloadPDF() {
            const element = document.querySelector('.print-container');
            const docName = 'awards-qualified-<?php echo htmlspecialchars((string)($criteria['title'] ?? 'criteria')); ?>.pdf'.replace(/[^\w\s-]/g, '_');

            if (typeof html2pdf !== 'undefined') {
                const opt = {
                    margin: 0,
                    filename: docName,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { orientation: 'portrait', unit: 'in', format: [8.5, 13] }
                };
                html2pdf().set(opt).from(element).save();
            } else {
                alert('PDF library not loaded. Using print dialog instead.');
                window.print();
            }
        }
    </script>
</body>
</html>

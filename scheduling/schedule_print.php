<?php
require_once __DIR__ . '/../classes/Database.php';

$type = strtolower(trim($_GET['type'] ?? 'class'));
$id = (int)($_GET['id'] ?? 0);
$schoolyear_id = (int)($_GET['schoolyear_id'] ?? 0);

if (!in_array($type, ['class', 'instructor', 'room'], true) || $id <= 0) {
    die('Invalid parameters');
}

$db = new Database();
$conn = $db->connect();

// Get active school year if not provided
if ($schoolyear_id <= 0) {
    $stmt = $conn->query("SELECT id FROM schoolyear WHERE is_active = TRUE LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $schoolyear_id = $row ? (int)$row['id'] : 0;
    if ($schoolyear_id <= 0) die('No active school year found');
}

// Get school year details
$stmt = $conn->prepare("SELECT start_year, end_year, semester FROM schoolyear WHERE id = ?");
$stmt->execute([$schoolyear_id]);
$schoolYear = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$schoolYear) die('School year not found');

$semesterValue = (string)($schoolYear['semester'] ?? '');
$semesterMap = [
    '1' => '1st Sem',
    '2' => '2nd Sem',
    '3' => 'Summer'
];
$semesterLabel = $semesterMap[$semesterValue] ?? ($semesterValue !== '' ? ('Sem ' . $semesterValue) : '');
$isInstructorView = ($type === 'instructor');

function timeToMinutes(string $timeValue): int {
    $parts = explode(':', substr($timeValue, 0, 5));
    if (count($parts) < 2) {
        return -1;
    }
    return ((int)$parts[0] * 60) + (int)$parts[1];
}

function minutesTo12H(int $minutes): string {
    $hour24 = (int)floor($minutes / 60);
    $min = $minutes % 60;
    $ampm = $hour24 < 12 ? 'AM' : 'PM';
    $hour12 = $hour24 % 12;
    if ($hour12 === 0) {
        $hour12 = 12;
    }
    return sprintf('%d:%02d %s', $hour12, $min, $ampm);
}

function hslToHex(int $h, int $s, int $l): string {
    $h /= 360;
    $s /= 100;
    $l /= 100;

    if ($s == 0) {
        $r = $g = $b = (int)round($l * 255);
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
    $p = 2 * $l - $q;

    $hueToRgb = function ($pVal, $qVal, $tVal) {
        if ($tVal < 0) $tVal += 1;
        if ($tVal > 1) $tVal -= 1;
        if ($tVal < 1 / 6) return $pVal + ($qVal - $pVal) * 6 * $tVal;
        if ($tVal < 1 / 2) return $qVal;
        if ($tVal < 2 / 3) return $pVal + ($qVal - $pVal) * (2 / 3 - $tVal) * 6;
        return $pVal;
    };

    $r = (int)round($hueToRgb($p, $q, $h + 1 / 3) * 255);
    $g = (int)round($hueToRgb($p, $q, $h) * 255);
    $b = (int)round($hueToRgb($p, $q, $h - 1 / 3) * 255);

    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

function getColorKeyByView(array $item, string $viewType): string {
    $subject = (string)($item['subject_code'] ?? $item['subject_name'] ?? '');
    $section = (string)($item['class_section'] ?? '');
    $instructor = (string)($item['instructor_name'] ?? '');

    if ($viewType === 'instructor') {
        return $section . '|' . $subject;
    }

    return $subject . '|' . $instructor . '|' . $section;
}

function makeGeneratedColor(string $seed): array {
    $hue = abs((int)crc32($seed)) % 360;
    return [
        'bg' => hslToHex($hue, 88, 94),
        'border' => hslToHex($hue, 72, 38)
    ];
}

function buildColorMapForSchedules(array $schedules, string $viewType): array {
    $palette = [
        ['bg' => '#eaf2ff', 'border' => '#1d4ed8'],
        ['bg' => '#e9f9ef', 'border' => '#15803d'],
        ['bg' => '#fff8e8', 'border' => '#b45309'],
        ['bg' => '#ffecec', 'border' => '#b91c1c'],
        ['bg' => '#f4ecff', 'border' => '#7e22ce'],
        ['bg' => '#e8fbff', 'border' => '#0f766e'],
        ['bg' => '#fff1e8', 'border' => '#c2410c'],
        ['bg' => '#eceffb', 'border' => '#4338ca'],
        ['bg' => '#f4ffe8', 'border' => '#4d7c0f'],
        ['bg' => '#ffe8f3', 'border' => '#be185d']
    ];

    $colorMap = [];
    $index = 0;

    foreach ($schedules as $item) {
        $key = getColorKeyByView($item, $viewType);
        if (!isset($colorMap[$key])) {
            if ($index < count($palette)) {
                $colorMap[$key] = $palette[$index];
            } else {
                $colorMap[$key] = makeGeneratedColor($key . '|' . $index);
            }
            $index++;
        }
    }

    return $colorMap;
}

// Get schedules
$filterColumn = 's.class_id';
$titleInfo = 'Class Schedule';
$departmentInfo = '';

if ($type === 'instructor') {
    $filterColumn = 's.instructor_id';
    $stmt = $conn->prepare("SELECT CONCAT(firstname, ' ', lastname) as name, d.department_name FROM instructors i LEFT JOIN departments d ON i.department_id = d.id WHERE i.id = ?");
    $stmt->execute([$id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $titleInfo = $info['name'] ?? 'Unknown Instructor';
    $departmentInfo = $info['department_name'] ?? '';
} elseif ($type === 'room') {
    $filterColumn = 's.room_id';
    $stmt = $conn->prepare("SELECT room_name FROM rooms WHERE id = ?");
    $stmt->execute([$id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $titleInfo = $info['room_name'] ?? 'Unknown Room';
} else {
    $stmt = $conn->prepare("SELECT c.section_name, p.program_name FROM class c LEFT JOIN curriculum cu ON c.curriculum_id = cu.id LEFT JOIN programs p ON cu.program_id = p.id WHERE c.id = ?");
    $stmt->execute([$id]);
    $info = $stmt->fetch(PDO::FETCH_ASSOC);
    $titleInfo = $info['section_name'] ?? 'Unknown Class';
    $departmentInfo = $info['program_name'] ?? '';
}

// Fetch schedules for the selected entity
$sql = "SELECT s.*, 
        sub.subject_code, sub.subject_name,
        c.section_name as class_section,
        CONCAT(i.firstname, ' ', i.lastname) as instructor_name,
        r.room_name,
        p.program_name
FROM schedules s
LEFT JOIN subjects sub ON sub.id = s.subject_id
LEFT JOIN class c ON c.id = s.class_id
LEFT JOIN curriculum cu ON c.curriculum_id = cu.id
LEFT JOIN instructors i ON i.id = s.instructor_id
LEFT JOIN rooms r ON r.id = s.room_id
LEFT JOIN programs p ON cu.program_id = p.id
WHERE s.schoolyear_id = ? AND {$filterColumn} = ?
ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), s.start_time";

$stmt = $conn->prepare($sql);
$stmt->execute([$schoolyear_id, $id]);
$schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create schedule grid with 30-minute slots (7:00 AM - 7:00 PM)
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
$dayToIndex = array_flip($days);
$dayStartMinutes = 7 * 60;
$dayEndMinutes = 19 * 60;
$intervalMinutes = 30;
$timeSlots = [];

for ($minutes = $dayStartMinutes; $minutes < $dayEndMinutes; $minutes += $intervalMinutes) {
    $timeSlots[] = [
        'start' => $minutes,
        'end' => $minutes + $intervalMinutes,
        'label' => minutesTo12H($minutes) . ' - ' . minutesTo12H($minutes + $intervalMinutes)
    ];
}

$colorMap = buildColorMapForSchedules($schedules, $type);
$eventStarts = [];
$coveredSlots = [];

foreach ($schedules as $sched) {
    $dayRaw = trim((string)($sched['day_of_week'] ?? ''));
    $day = ucfirst(strtolower($dayRaw));
    if (!isset($dayToIndex[$day])) {
        continue;
    }

    $startMinutes = timeToMinutes((string)($sched['start_time'] ?? ''));
    $endMinutes = timeToMinutes((string)($sched['end_time'] ?? ''));
    if ($startMinutes < 0 || $endMinutes < 0 || $endMinutes <= $startMinutes) {
        continue;
    }

    $clampedStart = max($startMinutes, $dayStartMinutes);
    $clampedEnd = min($endMinutes, $dayEndMinutes);
    if ($clampedEnd <= $clampedStart) {
        continue;
    }

    $startSlot = (int)floor(($clampedStart - $dayStartMinutes) / $intervalMinutes);
    $endSlot = (int)ceil(($clampedEnd - $dayStartMinutes) / $intervalMinutes);
    $rowSpan = max(1, $endSlot - $startSlot);

    $dayIndex = $dayToIndex[$day];
    $startCellKey = $dayIndex . '|' . $startSlot;
    if (isset($eventStarts[$startCellKey]) || isset($coveredSlots[$startCellKey])) {
        continue;
    }

    $colorKey = getColorKeyByView($sched, $type);
    $eventStarts[$startCellKey] = [
        'schedule' => $sched,
        'rowspan' => $rowSpan,
        'color' => $colorMap[$colorKey] ?? ['bg' => '#eaf2ff', 'border' => '#1d4ed8']
    ];

    for ($slot = $startSlot + 1; $slot < $endSlot; $slot++) {
        $coveredSlots[$dayIndex . '|' . $slot] = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Form - <?php echo htmlspecialchars($titleInfo); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @media print {
            @page {
                size: 13in 8.5in;
                margin: 0.25in;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .print-container {
            width: 13in;
            min-height: 8.5in;
            background: white;
            margin: 0 auto;
            padding: 0.25in;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        
        .header h3 {
            font-size: 14px;
            margin: 2px 0;
        }
        
        .header .title {
            font-weight: bold;
            margin: 5px 0;
        }
        
        .info-row {
            display: flex;
            gap: 40px;
            margin: 10px 0;
            font-size: 12px;
        }
        
        .info-field {
            flex: 1;
        }
        
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        
        .info-value {
            display: inline-block;
            border-bottom: 1px dotted #000;
            min-width: 150px;
            padding: 0 5px;
        }

        .load-field.align-right {
            text-align: right;
        }

        .load-field.align-right .info-label,
        .load-field.align-right .info-value {
            text-align: right;
        }

        .control-panel {
            display: flex;
            justify-content: center;
            align-items: end;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .control-field {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
            min-width: 180px;
        }

        .control-label {
            font-size: 12px;
            font-weight: 700;
            color: #111;
        }

        .control-input {
            width: 100%;
            border: 1px solid #bbb;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 13px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
            table-layout: fixed;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            height: 25px;
        }
        
        th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        
        td {
            vertical-align: top;
            font-size: 10px;
            line-height: 1.2;
        }
        
        .schedule-cell {
            font-size: 8px;
            padding: 3px;
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.1;
        }

        .schedule-event {
            height: 100%;
            padding: 4px;
            border-left-width: 3px;
            border-left-style: solid;
            border-radius: 2px;
        }

        .event-title {
            font-weight: 700;
            margin-bottom: 2px;
        }

        .event-line {
            font-size: 8px;
            line-height: 1.2;
        }
        
        .time-col {
            width: 10%;
            font-weight: bold;
            text-align: center;
        }
        
        .day-col {
            width: 15%;
            text-align: center;
        }
        
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            font-size: 11px;
        }

        .text-align-center{
            text-align: center !important;
        }

        .text-align-left{
            text-align: left !important;
        }
        
        .signature-line {
            text-align: left;
        }
        
        .signature-name {
            margin-top: 20px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .signature-role {
            margin-top: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        @media print {
            .print-container {
                width: 100%;
                height: auto;
                box-shadow: none;
                padding: 0;
            }

            table {
                font-size: 9px;
            }

            th, td {
                padding: 3px;
            }
            
            .no-print {
                display: none !important;
            }
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
    </style>
</head>
<body>
    <div class="no-print">
        <div class="control-panel">
            <div class="control-field">
                <label class="control-label" for="regularLoadInput">Regular Load</label>
                <input id="regularLoadInput" class="control-input" type="text" placeholder="Enter regular load">
            </div>
            <div class="control-field">
                <label class="control-label" for="overloadInput">Overload</label>
                <input id="overloadInput" class="control-input text-align-cener" type="text" placeholder="Enter overload">
            </div>
        </div>
        <button class="btn" onclick="window.print()">🖨️ Print</button>
        <button class="btn" onclick="downloadPDF()">⬇️ Download PDF</button>
        <button class="btn" onclick="window.close()">❌ Close</button>
    </div>
    
    <div class="print-container">
        <div class="header">
            <h3>WESTERN MINDANAO STATE UNIVERSITY</h3>
            <h3>COLLEGE OF COMPUTING STUDIES</h3>
            <p>S.Y. <?php echo htmlspecialchars($schoolYear['start_year'] . '-' . $schoolYear['end_year'] . ', ' . $semesterLabel); ?></p>
            <div class="title">LOADING FORM</div>
        </div>
        
        <div class="info-row">
            <div class="info-field">
                <span class="info-label">Department:</span>
                <span class="info-value"><?php echo htmlspecialchars($departmentInfo); ?></span>
            </div>
            <div class="info-field load-field<?php echo $isInstructorView ? ' align-right' : ''; ?>">
                <span class="info-label">Regular Load:</span>
                <span id="regularLoadValue" class="info-value"></span>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-field">
                <span class="info-label"><?php echo ucfirst($type); ?>:</span>
                <span class="info-value text-align-center"><?php echo htmlspecialchars($titleInfo); ?></span>
            </div>
            <div class="info-field load-field<?php echo $isInstructorView ? ' align-right' : ''; ?>">
                <span class="info-label">Overload:</span>
                <span id="overloadValue" class="info-value text-align-center"></span>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th class="time-col">TIME</th>
                    <?php foreach ($days as $day): ?>
                        <th class="day-col"><?php echo strtoupper(substr($day, 0, 3)); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($timeSlots as $slotIndex => $slot): ?>
                    <tr>
                        <td class="time-col"><?php echo htmlspecialchars($slot['label']); ?></td>
                        <?php foreach ($days as $dayIndex => $day): ?>
                            <?php
                                $cellKey = $dayIndex . '|' . $slotIndex;
                                if (isset($coveredSlots[$cellKey])) {
                                    continue;
                                }

                                if (isset($eventStarts[$cellKey])) {
                                    $event = $eventStarts[$cellKey];
                                    $s = $event['schedule'];
                                    $color = $event['color'];
                                    $subjectTitle = trim((string)($s['subject_name'] ?? ''));
                                    if ($subjectTitle === '') {
                                        $subjectTitle = trim((string)($s['subject_code'] ?? 'Scheduled'));
                                    }
                                    $subjectCode = trim((string)($s['subject_code'] ?? ''));
                                    $classMode = trim((string)($s['class_mode'] ?? ''));
                                    $classSection = trim((string)($s['class_section'] ?? ''));
                                    $instructorName = trim((string)($s['instructor_name'] ?? ''));
                                    $roomName = trim((string)($s['room_name'] ?? ''));
                                    $timeLabel = minutesTo12H(timeToMinutes((string)$s['start_time'])) . ' - ' . minutesTo12H(timeToMinutes((string)$s['end_time']));

                                    $lines = [];
                                    if ($subjectCode !== '') $lines[] = $subjectCode;
                                    if ($classMode !== '') $lines[] = $classMode;
                                    if ($classSection !== '') $lines[] = $classSection;
                                    if ($timeLabel !== '') $lines[] = $timeLabel;
                                    if ($type !== 'instructor' && $instructorName !== '') $lines[] = $instructorName;
                                    if ($type !== 'room' && $roomName !== '') $lines[] = $roomName;
                                    $tooltip = implode(' | ', $lines);
                            ?>
                                <td class="schedule-cell" rowspan="<?php echo (int)$event['rowspan']; ?>">
                                    <div class="schedule-event" title="<?php echo htmlspecialchars($tooltip); ?>" style="background: <?php echo htmlspecialchars($color['bg']); ?>; border-left-color: <?php echo htmlspecialchars($color['border']); ?>;">
                                        <div class="event-title"><?php echo htmlspecialchars($subjectTitle); ?></div>
                                        <?php if ($subjectCode !== ''): ?><div class="event-line"><?php echo htmlspecialchars($subjectCode); ?></div><?php endif; ?>
                                        <?php if ($classMode !== ''): ?><div class="event-line"><?php echo htmlspecialchars($classMode); ?></div><?php endif; ?>
                                        <?php if ($classSection !== ''): ?><div class="event-line"><?php echo htmlspecialchars($classSection); ?></div><?php endif; ?>
                                        <div class="event-line"><?php echo htmlspecialchars($timeLabel); ?></div>
                                        <?php if ($type !== 'instructor' && $instructorName !== ''): ?><div class="event-line"><?php echo htmlspecialchars($instructorName); ?></div><?php endif; ?>
                                        <?php if ($type !== 'room' && $roomName !== ''): ?><div class="event-line"><?php echo htmlspecialchars($roomName); ?></div><?php endif; ?>
                                    </div>
                                </td>
                            <?php } else { ?>
                                <td class="schedule-cell"></td>
                            <?php } ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="footer">
            <div class="signature-line">
                <p>PREPARED BY:</p>
                <div class="signature-name text-align-left">____________________________________</div>
                <div class="signature-role text-align-center">COLLEGE SECRETARY</div>
            </div>
            <div class="signature-line">
                <p>APPROVED BY:</p>
                <div class="signature-name text-align-left">____________________________________</div>
                <div class="signature-role text-align-center">DEAN, COLLEGE OF COMPUTING STUDIES</div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function applyLoadValues() {
            const regularInput = document.getElementById('regularLoadInput');
            const overloadInput = document.getElementById('overloadInput');
            const regularOutput = document.getElementById('regularLoadValue');
            const overloadOutput = document.getElementById('overloadValue');

            if (regularOutput) {
                regularOutput.textContent = regularInput ? regularInput.value.trim() : '';
            }
            if (overloadOutput) {
                overloadOutput.textContent = overloadInput ? overloadInput.value.trim() : '';
            }
        }

        window.addEventListener('beforeprint', applyLoadValues);

        document.addEventListener('DOMContentLoaded', function () {
            const regularInput = document.getElementById('regularLoadInput');
            const overloadInput = document.getElementById('overloadInput');
            if (regularInput) regularInput.addEventListener('input', applyLoadValues);
            if (overloadInput) overloadInput.addEventListener('input', applyLoadValues);
            applyLoadValues();
        });

        function downloadPDF() {
            applyLoadValues();
            const element = document.querySelector('.print-container');
            const docName = 'schedule-<?php echo htmlspecialchars($titleInfo); ?>.pdf'.replace(/[^\w\s-]/g, '_');
            
            if (typeof html2pdf !== 'undefined') {
                const opt = {
                    margin: 0,
                    filename: docName,
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { orientation: 'landscape', unit: 'in', format: [13, 8.5] }
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

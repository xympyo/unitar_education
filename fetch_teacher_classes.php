<?php
require_once 'db.php';
session_start();
$teacher_id = $_SESSION['teacher_id'] ?? null;
if (!$teacher_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
// Get all approved student assignments for this teacher
$query = "SELECT ps.student_id, ps.name, ps.email, ps.classes
FROM teacher_student_requests tsr
JOIN parents_students ps ON tsr.student_id = ps.student_id
WHERE tsr.teacher_id = ? AND tsr.approved = 1";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
// Group students by class
$classes = [];
foreach ($students as $stu) {
    $classNum = isset($stu['classes']) ? $stu['classes'] : '-';
    if (!isset($classes[$classNum])) $classes[$classNum] = [];
    $classes[$classNum][] = $stu;
}
// For each student, get their average point from report_history
foreach ($classes as $classNum => &$stuList) {
    foreach ($stuList as &$stu) {
        $avgQuery = "SELECT AVG((participation + understanding + behavior + emotional)/4) as avg_point FROM report_history WHERE student_id = ?";
        $avgStmt = $conn->prepare($avgQuery);
        $avgStmt->bind_param('i', $stu['student_id']);
        $avgStmt->execute();
        $avgRes = $avgStmt->get_result()->fetch_assoc();
        $stu['avg'] = $avgRes['avg_point'] !== null ? round($avgRes['avg_point'], 2) : null;
    }
}
// Format for frontend
$out = ['classes' => []];
foreach ($classes as $classNum => $stuList) {
    $out['classes'][] = [
        'class' => $classNum,
        'students' => array_values($stuList)
    ];
}
header('Content-Type: application/json');
echo json_encode($out);

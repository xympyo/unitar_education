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
$query = "SELECT ps.student_id, ps.name, ps.classes,
    AVG((rh.participation + rh.understanding + rh.behavior + rh.emotional)/4) as avg_point
FROM teacher_student_requests tsr
JOIN parents_students ps ON tsr.student_id = ps.student_id
LEFT JOIN report_history rh ON ps.student_id = rh.student_id
WHERE tsr.teacher_id = ? AND tsr.approved = 1
GROUP BY ps.student_id, ps.name, ps.classes";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = [
        'id' => $row['student_id'],
        'name' => $row['name'],
        'classes' => $row['classes'],
        'avg' => $row['avg_point'] !== null ? round($row['avg_point'], 2) : null
    ];
}
// For each student, get all their reports
foreach ($students as &$stu) {
    $stu['reports'] = [];
    $rep_stmt = $conn->prepare(
        "SELECT rh.participation, rh.understanding, rh.behavior, rh.emotional, rh.notes, t.name as teacher_name
        FROM report_history rh
        LEFT JOIN teachers t ON rh.teacher_id = t.user_id
        WHERE rh.student_id = ?");
    $rep_stmt->bind_param('i', $stu['id']);
    $rep_stmt->execute();
    $rep_res = $rep_stmt->get_result();
    while ($rep = $rep_res->fetch_assoc()) {
        $stu['reports'][] = $rep;
    }
}
header('Content-Type: application/json');
echo json_encode(['students' => $students]);

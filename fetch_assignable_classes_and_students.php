<?php
require_once 'db.php';
session_start();
$teacher_id = $_SESSION['teacher_id'] ?? null;
if (!$teacher_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}
// Get students who are NOT assigned to this teacher (either assigned to another teacher, or not assigned at all)
$query = "SELECT ps.student_id, ps.name, ps.classes
FROM parents_students ps
LEFT JOIN teacher_student_requests tsr ON ps.student_id = tsr.student_id AND tsr.approved = 1
WHERE (tsr.teacher_id IS NULL OR tsr.teacher_id != ? OR tsr.approved != 1)
AND ps.student_id NOT IN (
    SELECT student_id FROM teacher_student_requests WHERE teacher_id = ? AND approved = 1
)";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $teacher_id, $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$classes = [];
$students = [];
while ($row = $result->fetch_assoc()) {
    if ($row['classes'] !== null && $row['classes'] !== '') {
        $classes[] = $row['classes'];
    }
    $students[] = [
        'id' => $row['student_id'],
        'name' => $row['name'],
        'classes' => $row['classes']
    ];
}
// Remove duplicate classes
$classes = array_values(array_unique($classes));
header('Content-Type: application/json');
echo json_encode(['classes' => $classes, 'students' => $students]);

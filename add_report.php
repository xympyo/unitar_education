<?php
header('Content-Type: application/json');
require_once 'db.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$teacher_email = $_SESSION['teacher_email'] ?? '';
$teacher = $conn->query("SELECT * FROM teachers WHERE email='$teacher_email'")->fetch_assoc();
$teacher_id = $teacher['user_id'] ?? 1;

$cls = $conn->real_escape_string($data['cls'] ?? '');
$student_id = intval($data['student_id'] ?? 0);
$participation = intval($data['participation'] ?? 0);
$understanding = intval($data['understanding'] ?? 0);
$behavior = intval($data['behavior'] ?? 0);
$emotional = intval($data['emotional'] ?? 0);
$notes = $conn->real_escape_string($data['notes'] ?? '');

if (!$cls || !$student_id || !$participation || !$understanding || !$behavior || !$emotional) {
    echo json_encode(['status'=>'error','message'=>'All fields are required.']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO report_history (teacher_id, student_id, class, participation, understanding, behavior, emotional, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iisiisss', $teacher_id, $student_id, $cls, $participation, $understanding, $behavior, $emotional, $notes);
if ($stmt->execute()) {
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to add report.']);
}
?>

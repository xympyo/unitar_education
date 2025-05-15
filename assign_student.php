<?php
require_once 'db.php';
session_start();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status'=>'error','message'=>'Method Not Allowed']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);
$teacher_id = isset($input['teacher_id']) ? intval($input['teacher_id']) : ($_SESSION['teacher_id'] ?? null);
$student_id = isset($input['student_id']) ? intval($input['student_id']) : null;

if (!$teacher_id || !$student_id) {
    http_response_code(400);
    echo json_encode(['status'=>'error','message'=>'Missing teacher_id or student_id']);
    exit;
}

// Only block if already assigned (approved=1)
$stmt = $conn->prepare('SELECT * FROM teacher_student_requests WHERE teacher_id = ? AND student_id = ? AND approved = 1');
$stmt->bind_param('ii', $teacher_id, $student_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    echo json_encode(['status'=>'error','message'=>'Student already assigned to this teacher']);
    exit;
}
// Optionally: Clean up any old pending requests for this pair
$stmt = $conn->prepare('DELETE FROM teacher_student_requests WHERE teacher_id = ? AND student_id = ? AND approved = 0');
$stmt->bind_param('ii', $teacher_id, $student_id);
$stmt->execute();
// Insert assignment request as pending (approved = 0)
$stmt = $conn->prepare('INSERT INTO teacher_student_requests (teacher_id, student_id, approved) VALUES (?, ?, 0)');
$stmt->bind_param('ii', $teacher_id, $student_id);
if ($stmt->execute()) {
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'error','message'=>'Failed to assign student']);
}
?>

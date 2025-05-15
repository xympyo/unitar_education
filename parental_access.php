<?php
require_once 'db.php';
session_start();
header('Content-Type: application/json');

$parent_email = $_SESSION['parent_email'] ?? null;
if (!$parent_email) {
    echo json_encode(['error' => 'Not logged in.']);
    exit;
}
$parent = $conn->query("SELECT student_id FROM parents_students WHERE email='" . $conn->real_escape_string($parent_email) . "'")->fetch_assoc();
$student_id = $parent['student_id'] ?? null;
if (!$student_id) {
    echo json_encode(['error' => 'Student not found.']);
    exit;
}

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($action === 'get_report_history') {
    $result = $conn->query("SELECT * FROM report_history WHERE student_id=$student_id ORDER BY report_id DESC");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode(['data' => $rows]);
    exit;
}

if ($action === 'get_teacher_requests') {
    $sql = "SELECT tsr.id, t.name, t.email, tsr.approved FROM teacher_student_requests tsr JOIN teachers t ON tsr.teacher_id = t.user_id WHERE tsr.student_id=$student_id";
    $result = $conn->query($sql);
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode(['data' => $rows]);
    exit;
}

if ($action === 'approve_teacher') {
    $req_id = intval($_POST['id'] ?? 0);
    if ($req_id) {
        $conn->query("UPDATE teacher_student_requests SET approved=1 WHERE id=$req_id AND student_id=$student_id");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid request id.']);
    }
    exit;
}

if ($action === 'update_class') {
    $new_class = intval($_POST['new_class'] ?? 0);
    if ($new_class > 0) {
        $conn->query("UPDATE parents_students SET classes=$new_class WHERE student_id=$student_id");
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Invalid class value.']);
    }
    exit;
}

echo json_encode(['error' => 'Unknown action.']);

<?php
session_start();

header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

function respond($arr) {
    echo json_encode($arr);
    exit;
}

if ($action === 'register_teacher') {
    $name = $conn->real_escape_string($data['name'] ?? '');
    $email = $conn->real_escape_string($data['email'] ?? '');
    $password = $data['password'] ?? '';
    if (!$name || !$email || !$password) respond(['status'=>'error','message'=>'All fields required.']);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $check = $conn->query("SELECT * FROM teachers WHERE email='$email'");
    if ($check && $check->num_rows > 0) respond(['status'=>'error','message'=>'Email already registered as teacher.']);
    $stmt = $conn->prepare("INSERT INTO teachers (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $name, $email, $hash);
    if ($stmt->execute()) {
    
    $_SESSION['parent_email'] = $email;
    respond(['status'=>'success']);
}
    else respond(['status'=>'error','message'=>'Registration failed.']);
}

if ($action === 'register_student') {
    $name = $conn->real_escape_string($data['name'] ?? '');
    $email = $conn->real_escape_string($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $parent_pin = $conn->real_escape_string($data['parent_pin'] ?? '');
    if (!$name || !$email || !$password || !$parent_pin) respond(['status'=>'error','message'=>'All fields required.']);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $check = $conn->query("SELECT * FROM parents_students WHERE email='$email'");
    if ($check && $check->num_rows > 0) respond(['status'=>'error','message'=>'Email already registered as student.']);
    $stmt = $conn->prepare("INSERT INTO parents_students (name, email, password, parent_pin) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sssi', $name, $email, $hash, $parent_pin);
    if ($stmt->execute()) {
        $_SESSION['parent_email'] = $email;
        respond(['status'=>'success']);
    } else {
        respond(['status'=>'error','message'=>'Registration failed.']);
    }
}

if ($action === 'login') {
    $email = $conn->real_escape_string($data['email'] ?? '');
    $password = $data['password'] ?? '';
    if (!$email || !$password) respond(['status'=>'error','message'=>'Email and password required.']);

    $student = $conn->query("SELECT * FROM parents_students WHERE email='$email'");
    $teacher = $conn->query("SELECT * FROM teachers WHERE email='$email'");
    $found_student = $student && $student->num_rows > 0 ? $student->fetch_assoc() : null;
    $found_teacher = $teacher && $teacher->num_rows > 0 ? $teacher->fetch_assoc() : null;

    $valid_student = $found_student && password_verify($password, $found_student['password']);
    $valid_teacher = $found_teacher && password_verify($password, $found_teacher['password']);

    if ($valid_student && $valid_teacher) {
        respond(['status'=>'success','type'=>'choose']);
    } elseif ($valid_student) {
        $_SESSION['parent_email'] = $email;
        respond(['status'=>'success','type'=>'parent']);
    } elseif ($valid_teacher) {
        $_SESSION['teacher_email'] = $email;
        respond(['status'=>'success','type'=>'teacher']);
    } else {
        respond(['status'=>'error','message'=>'Email or password is incorrect.']);
    }
}

respond(['status'=>'error','message'=>'Invalid request.']);

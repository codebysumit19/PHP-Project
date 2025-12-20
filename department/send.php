<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
}

// Auto logout after 5 minutes (300 seconds) of inactivity
$timeout = 5 * 60; // 5 minutes

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    $_SESSION = [];
    session_destroy();
    header('Location: ../login.php');
    exit;
}
$_SESSION['last_activity'] = time();

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.php');
    exit;
}

$department_id = trim($_POST['department_id'] ?? '');
$dname         = trim($_POST['dname'] ?? '');
$email         = trim($_POST['email'] ?? '');
$number        = trim($_POST['number'] ?? '');
$nemployees    = (int)($_POST['nemployees'] ?? 0);
$resp          = trim($_POST['resp'] ?? '');
$budget        = trim($_POST['budget'] ?? '');
$status        = trim($_POST['status'] ?? '');
$description   = trim($_POST['description'] ?? '');

// simple required checks
if ($department_id === '' || $dname === '' || $email === '' || $number === '' || $nemployees <= 0 ||
    $resp === '' || $budget === '' || $status === '') {
    die('All required fields must be filled correctly.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email format.');
}

// Insert department_id as the business ID; id remains internal PK
$stmt = $conn->prepare(
    "INSERT INTO departments (department_id, dname, email, number, nemployees, resp, budget, status, description)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param(
    "ssssissss",
    $department_id, $dname, $email, $number, $nemployees, $resp, $budget, $status, $description
);

if ($stmt->execute()) {
    header("Location: ../submit.php");
    exit;
} else {
    echo "Error saving department: " . $stmt->error;
}

$stmt->close();
$conn->close();

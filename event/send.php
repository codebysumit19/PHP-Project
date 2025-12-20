<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
}

// Auto logout after 50 minutes (300 seconds) of inactivity
$timeout = 50 * 60;

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
$name          = trim($_POST['name'] ?? '');
$address       = trim($_POST['address'] ?? '');
$date          = trim($_POST['date'] ?? '');
$stime         = trim($_POST['stime'] ?? '');
$etime         = trim($_POST['etime'] ?? '');
$type          = trim($_POST['type'] ?? '');
$happend       = trim($_POST['happend'] ?? '');

// required
if ($department_id === '' || $name === '' || $address === '' || $date === '' || $stime === '' ||
    $etime === '' || $type === '' || $happend === '') {
    die('All required fields must be filled correctly.');
}

// simple date format check
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    die('Invalid date format.');
}

// check department_id exists in departments table
$check = $conn->prepare("SELECT department_id FROM departments WHERE department_id = ?");
$check->bind_param("s", $department_id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $check->close();
    echo "<script>
        alert('Department ID does not exist.');
        window.history.back();
    </script>";
    exit;
}

$check->close();


$stmt = $conn->prepare(
    "INSERT INTO events (department_id, name, address, date, stime, etime, type, happend)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("ssssssss",
    $department_id, $name, $address, $date, $stime, $etime, $type, $happend
);

if ($stmt->execute()) {
    header("Location: ../submit.php");
    exit;
} else {
    echo "Error saving event: " . $stmt->error;
}
$stmt->close();
$conn->close();

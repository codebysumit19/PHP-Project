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
$pname         = trim($_POST['pname'] ?? '');
$cname         = trim($_POST['cname'] ?? '');
$pmanager      = trim($_POST['pmanager'] ?? '');
$sdate         = trim($_POST['sdate'] ?? '');
$edate         = trim($_POST['edate'] ?? '');
$status        = trim($_POST['status'] ?? '');
$pdescription  = trim($_POST['pdescription'] ?? '');

// required
if ($department_id === '' || $pname === '' || $cname === '' || $pmanager === '' ||
    $sdate === '' || $edate === '' || $status === '') {
    die('All required fields must be filled correctly.');
}

// simple date format check
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $sdate) ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $edate)) {
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
    "INSERT INTO projects (department_id, pname, cname, pmanager, sdate, edate, status, pdescription)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param(
    "ssssssss",
    $department_id, $pname, $cname, $pmanager, $sdate, $edate, $status, $pdescription
);

if ($stmt->execute()) {
    header("Location: ../submit.php");
    exit;
} else {
    echo "Error saving project: " . $stmt->error;
}
$stmt->close();
$conn->close();

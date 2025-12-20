<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login.php');
    exit;
}

// Auto logout after 50 minutes of inactivity
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
$ename         = trim($_POST['ename'] ?? '');
$dob           = trim($_POST['dob'] ?? '');
$gender        = trim($_POST['gender'] ?? '');
$email         = trim($_POST['email'] ?? '');
$pnumber       = trim($_POST['pnumber'] ?? '');
$address       = trim($_POST['address'] ?? '');
$designation   = trim($_POST['designation'] ?? '');
$salary        = trim($_POST['salary'] ?? '');
$joining_date  = trim($_POST['joining_date'] ?? '');
$aadhar        = trim($_POST['aadhar'] ?? '');

// basic required checks
if ($department_id === '' || $ename === '' || $dob === '' || $gender === '' || $email === '' ||
    $pnumber === '' || $address === '' || $designation === '' ||
    $salary === '' || $joining_date === '') {
    die('All required fields must be filled correctly.');
}

// email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email format.');
}

// phone length
if (strlen($pnumber) < 10 || strlen($pnumber) > 13) {
    die('Phone number length must be between 10 and 13 characters.');
}

// salary numeric
if (!is_numeric($salary)) {
    die('Salary must be numeric.');
}

// insert with required foreign key department_id
$stmt = $conn->prepare(
    "INSERT INTO employees
     (department_id, ename, dob, gender, email, pnumber, address, designation, salary, joining_date, aadhar)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    die('Prepare failed: ' . $conn->error);
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

$stmt->bind_param(
    "sssssssssss",
    $department_id, $ename, $dob, $gender, $email, $pnumber,
    $address, $designation, $salary, $joining_date, $aadhar
);

if ($stmt->execute()) {
    header("Location: ../submit.php");
    exit;
} else {
    echo "Error saving employee: " . $stmt->error;
}
$stmt->close();
$conn->close();

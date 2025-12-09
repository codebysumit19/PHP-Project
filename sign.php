<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user    = trim($_POST['userName'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $pass    = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // basic validation
    if ($user === '' || $email === '' || $pass === '') {
        die('All fields are required.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email format.');
    }
    if ($pass !== $confirm) {
        die('Passwords do not match.');
    }

    // hash password
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // insert into DB
    $stmt = $conn->prepare("INSERT INTO signup (userName, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $email, $hash);

    if ($stmt->execute()) {
        header("Location: successful.php");
        exit;
    } else {
        echo "Error creating account.";
    }
    $stmt->close();
}

$conn->close();

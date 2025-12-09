<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invalid Credentials</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="fi-snsuxx-php-logo.jpg">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{
            font-family:Arial,sans-serif;
            background:linear-gradient(135deg,#fee2e2,#ffffff);
            display:flex;justify-content:center;align-items:center;
            min-height:100vh;
        }
        .card{
            background:#fff;padding:30px;border-radius:10px;
            box-shadow:0 8px 24px rgba(0,0,0,0.12);
            text-align:center;max-width:400px;width:90%;
        }
        h1{
            color:#b91c1c;margin-bottom:10px;font-size:24px;
        }
        p{
            color:#4b5563;margin-bottom:20px;font-size:14px;
        }
        a{
            display:inline-block;padding:10px 18px;border-radius:8px;
            background:#3b82f6;color:#fff;text-decoration:none;
            font-size:14px;
        }
        a:hover{background:#2563eb}
    </style>
</head>
<body>
<div class="card">
    <h1>Invalid Login</h1>
    <p>Email and password are incorrect. Please try again.</p>
    <a href="login.php">Back to Login</a>
</div>
</body>
</html>

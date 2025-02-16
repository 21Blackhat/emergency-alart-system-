<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Emergency Alert System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: center;
            background: url('images/img.jpg') no-repeat center center/cover; /* Uses your provided image */
            height: 100vh;
            color: white;
        }
        .overlay {
            background: rgba(0, 0, 0, 0.6); /* Dark overlay for better text visibility */
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        h1 { font-size: 2.5em; }
        p { font-size: 1.2em; max-width: 600px; }
        .button {
            display: inline-block;
            padding: 12px 20px;
            margin: 10px;
            font-size: 18px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .button:hover { background-color: darkred; }
    </style>
</head>
<body>

    <div class="overlay">
        <h1>🚨 Welcome to the Emergency Alert System</h1>
        <p>This platform ensures campus safety by allowing students to report emergencies, receive alerts, and communicate with emergency response teams.</p>

        <h2>Login as:</h2>
        <a href="login.html" class="button">🎓 Student Login</a>
        <a href="admin_login.php" class="button">🛡 Admin Login</a>
    </div>

</body>
</html>

<?php
include '../include/db.php';
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } else {
        try {
            $stmt = $pdo->prepare(".");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];


                header("Location: .");
                exit;
            } else {
                $message = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login failed: " . $e->getMessage());
            $message = "Something went wrong. Try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<style>

.container {
    width: 400px;
    margin: 100px auto;
    padding: 20px;
    border-radius: 10px;
}
input[type=email], input[type=password] {
    width: 80%; 
    padding: 10px; 
    margin: 8px 0; 
    border: 1px solid #ccc; 
    border-radius: 5px;
}
.message {
    color: red; 
    margin-bottom: 10px;
}
</style>
<script>
function validateLogin() {
    const email = document.getElementById("email").value;
    if (!email.includes("@")) {
        alert("Please enter a valid email address");
        return false;
    }
    return true;
}
</script>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form method="POST" onsubmit="return validateLogin()">
        <div class="message"><?= htmlspecialchars($message) ?></div>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" minlength="6" required>
        <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="signup.php">Sign Up</a></p>
</div>
</body>
</html>

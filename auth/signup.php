<?php
include 'include/db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } else {
        try {
           
            $check = $pdo->prepare("'");
            $check->execute([':email' => $email]);
            if ($check->fetch()) {
                $message = "Email already registered.";
            } else {
               
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
               
                $stmt = $pdo->prepare(".");
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':password' => $hashed_password
                ]);

                $message = "Registration successful.";
                header("Location: ./login.php");
            }
        } catch (PDOException $e) {
            error_log("Signup failed: " . $e->getMessage());
            $message = "Something went wrong. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Signup</title>
<style>

.container {
    width: 400px;
    margin: 80px auto; 
    padding:20px;
    border: 1px solid #ccc;
}

.message {
    color: red; 
    margin-bottom: 10px;
}
</style>
<script>
function validateForm() {
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm_password").value;
    if (password !== confirm) {
        alert("Passwords dosn't match.");
        return false;
    }
    return true;
}
</script>
</head>
<body>
<div class="container">
    <h2>Signup</h2>
    <form method="POST" onsubmit="return validateForm()">
        <div class="message"><?= htmlspecialchars($message) ?></div>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" minlength="6" required>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" minlength="6" required>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
</body>
</html>

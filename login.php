<?php
session_start();
include 'db.php'; // this defines $pdo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    try {
        // Prepare and execute SQL securely using PDO
        $stmt = $pdo->prepare("SELECT UserID, RoleID, Password FROM Users WHERE Username = :username AND IsActive = 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(); // fetch associative array

        if ($user) {
            $hashedPassword = $user['Password'];

            // Verify hashed or plain password
            if (password_verify($password, $hashedPassword) || $password === $hashedPassword) {
                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['RoleID'] = $user['RoleID'];
                $_SESSION['Username'] = $username;

               $bytes = random_bytes(16);

                $secureid = $user['UserID'] . bin2hex($bytes);
                
                $_SESSION['SessionID'] = $secureid;

                $stmt = $pdo->prepare("INSERT INTO UserLogins(SessionID, UserID, RoleID) VALUES (:SessionID, :UserID, :RoleID)");
                $stmt->execute([
                    ':SessionID' => $_SESSION['SessionID'],
                    ':UserID' => $user['UserID'],
                    ':RoleID' => $user['RoleID']
                ]);


                // Redirect user based on RoleID
                switch ($user['RoleID']) {
                    case 1:
                        header("Location: Utility-Management-System-Administrator/dashboard.php");
                        break;
                    case 2:
                        header("Location: Utility-Management-System-Field-Officer/dashboard.php");
                        break;
                    case 3:
                        header("Location: Utility-Management-System-Cashier/index.php");
                        break;
                    case 4:
                        header("Location: Utility-Management-System-Manager/index.php");
                        break;
                    default:
                        echo "<p style='color:red;'>Unknown role.</p>";
                }
                exit();
            } else {
                echo "<p style='color:red;'>❌ Invalid password!</p>";
            }
        } else {
            echo "<p style='color:red;'>⚠️ User not found or inactive!</p>";
        }
    } catch (PDOException $e) {
        // Optional: log the error
        error_log("Login query failed: " . $e->getMessage());
        echo "<p style='color:red;'>Database error. Please try again later.</p>";
    }
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>UnityGrid - Login</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
<style>

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  height: 100%;
  width: 100%;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  background: #0b121c;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
}

.main-wrapper {
  width: 100%;
  max-width: 1200px;
  display: flex;
  background: #162029;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 12px 45px rgba(0, 0, 0, 0.45);
  animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.left-section {
  flex: 1;
  padding: 40px;
  background: linear-gradient(135deg, #213655 0%, #162029 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.brand-name {
  font-size: 48px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: 2px;
  margin-bottom: 12px;
}

.brand-tagline {
  font-size: 16px;
  color: #e5d283;
   font-weight: 500;
  margin-bottom: 50px;
}


.features {
  width: 100%;
  max-width: 400px;
}

.feature {
  display: flex;
  align-items: center;
  margin-bottom: 28px;
}

.feature-icon {
  width: 45px;
  height: 45px;
  min-width: 45px;
  background: rgba(229, 210, 131, 0.2);
  border-radius: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #e5d283;
  font-size: 20px;
  margin-right: 15px;
}

.feature-text {
  font-family: "Open Sans", sans-serif;;
  color: white;
  font-size: 15px;
}

.right-section {
  flex: 1;
  padding: 60px;
  display: flex;
  justify-content: center;
  flex-direction: column;
}

.form-wrapper {
  max-width: 430px;
  width: 100%;
  margin: auto;
}

.form-title {
  font-size: 32px;
  color: #ffffff;
  font-weight: 600;
  margin-bottom: 10px;
}

.form-subtitle {
  font-size: 14px;
  font-weight: 500;
  color: #e5d283;
  margin-bottom: 35px;
}

.input-group {
  margin-bottom: 25px;
}

.input-label {
  color: #ffffff;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 8px;
  display: block;
}

.input-field {
  width: 100%;
  padding: 12px;
  background: rgba(11, 18, 28, 0.6);
  border: 2px solid rgba(229, 210, 131, 0.25);
  border-radius: 8px;
  color: #ffffff;
  font-size: 16px;
  transition: 0.3s ease;
  outline: none;
}

.input-field:focus {
  border-color: #e5d283;
  background: rgba(11, 18, 28, 0.75);
}

.input-field::placeholder {
  color: rgba(240, 240, 240, 0.45);
}

.form-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 30px;
}

.checkbox-group {
  display: flex;
  align-items: center;
}

.checkbox-group input {
  width: 18px;
  height: 18px;
  margin-right: 8px;
  cursor: pointer;
  accent-color: #e5d283;
}

.checkbox-label {
  color: #ffffff;
  font-size: 14px;
}

.forgot-link {
  color: #e5d283;
  font-size: 14px;
  text-decoration: none;
}

.forgot-link:hover {
  text-decoration: underline;
}

.submit-button {
  width: 100%;
  padding: 16px;
  border: none;
  background: linear-gradient(135deg, #e5d283 0%, #d4c06f 100%);
  color: #0b121c;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: 0.25s ease;
}

.submit-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(229, 210, 131, 0.35);
}


@media (max-width: 968px) {
  .main-wrapper {
    flex-direction: column;
  }

  .left-section, .right-section {
    padding: 45px 30px;
  }

  .brand-name { font-size: 36px; }
}

@media (max-width: 480px) {
  .left-section, .right-section {
    padding: 25px 20px;
  }

  .brand-name { font-size: 28px; }
  .form-title { font-size: 24px; }
}

</style>
</head>

<body>

<div class="main-wrapper">


  <div class="left-section">
    <h1 class="brand-name">UNITYGRID</h1>
    <p class="brand-tagline">Future-proofing Your Infrastructure</p>

    <div class="features">

      <div class="feature">
        <div class="feature-icon">
          <i class="fa-solid fa-bolt-lightning"></i>
        </div>
        <div class="feature-text">Unified electricity, gas & water  management</div>
      </div>

      <div class="feature">
        <div class="feature-icon">
          <i class="fa-solid fa-chart-line"></i>
        </div>
        <div class="feature-text">Real-time monitoring & analytics</div>
      </div>

      <div class="feature">
        <div class="feature-icon">
          <i class="fa-solid fa-lock"></i>
        </div>
        <div class="feature-text">Enterprise-grade security & reliability</div>
      </div>

    </div>
  </div>

 
  <div class="right-section">
    <div class="form-wrapper">

      <h2 class="form-title">Welcome Back</h2>
      <p class="form-subtitle">Sign in to access your dashboard</p>

      <form action="login.php" method="POST">

        <div class="input-group">
          <label class="input-label">Username</label>
          <input type="text" class="input-field"  name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
          <label class="input-label">Password</label>
          <input type="password" class="input-field" name="password" placeholder="Enter your password" required>
        </div>

        <div class="form-row">
          <div class="checkbox-group">
            <input type="checkbox" id="remember">
            <label for="remember" class="checkbox-label">Remember me</label>
          </div>

          <a class="forgot-link" href="#">Forgot Password?</a>
        </div>

        <button class="submit-button" type="submit">Sign In</button>

      </form>

    </div>
  </div>

</div>

</body>
</html>

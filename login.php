<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["username"];
    $password = $_POST["pass"];

    // Fetch user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        // Save session info
        $_SESSION["user"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        // Redirect based on role
        if ($user["role"] === "admin") {
            header("Location: admin.php");
        } else {
            header("Location: users.php");
        }
        exit();
    } else {
        echo "<script>alert('Invalid credentials'); window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - HappnNow</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { 
      font-family: 'Segoe UI', sans-serif; 
      min-height: 100vh; 
      display: flex; 
      justify-content: center; 
      align-items: center;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: url('logo.jpg') center/cover no-repeat fixed;
      filter: blur(8px);
      z-index: -2;
    }
    body::after {
      content: '';
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(255, 236, 210, 0.4);
      z-index: -1;
    }
    .login-container { 
      background: linear-gradient(135deg, #fff5f0, #ffe4d6); 
      padding: 40px; 
      border-radius: 20px; 
      box-shadow: 0 20px 60px rgba(252, 182, 159, 0.4); 
      width: 100%; max-width: 380px;
      animation: slideUp 0.5s ease-out;
    }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    h1 { text-align: center; color: #d4845c; font-size: 28px; margin-bottom: 25px; font-weight: 700; }
    .form-group { margin-bottom: 15px; }
    input { 
      width: 100%; padding: 12px 15px; 
      border: 2px solid #ffc9b9; border-radius: 10px; 
      background: #fffbf9; color: #7a5a47; font-size: 14px;
      transition: 0.3s;
    }
    input:focus { outline: none; border-color: #ffb3a7; background: #fff; box-shadow: 0 6px 15px rgba(255, 179, 167, 0.25); transform: translateY(-2px); }
    button { 
      width: 100%; padding: 12px; 
      background: linear-gradient(135deg, #ffb3a7, #ff9a92); 
      color: #fff; border: none; border-radius: 10px; 
      cursor: pointer; font-weight: 600; font-size: 16px; 
      transition: 0.3s; margin-top: 8px;
    }
    button:hover { background: linear-gradient(135deg, #ff9a92, #ff8577); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(255, 179, 167, 0.4); }
    button:active { transform: translateY(-1px); }
    .message { margin-top: 15px; font-size: 13px; text-align: center; display: none; animation: fadeIn 0.3s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .error { color: #c97965; }
    .success { color: #7ca67c; }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Login</h1>
    <form method="POST" action="login.php">
      <div class="form-group">
        <input type="text" id="username" name="username" placeholder="Username" required>
      </div>
      <div class="form-group">
        <input type="password" id="pass" name="pass" placeholder="Password" required>
      </div>
      <button type="submit">Login</button>
      <div class="message" id="msg"></div>
    </form>
    <div class="register-link">Don't have an account? <a href="register.php">Register here</a></div>
  </div>
</body>
</html>

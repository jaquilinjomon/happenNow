<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>User Profile - Registration</title>
<style>
  body {
    font-family: "Georgia", "Times New Roman", serif;
    background: linear-gradient(135deg, #F5DEB3, #FFE4C4, #FFDAB9);
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    margin: 0;
    padding: 40px 20px;
  }

  .container {
    background: rgba(255, 255, 255, 0.95);
    padding: 50px;
    border-radius: 20px;
    border: 3px solid #DEB887;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 850px;
    position: relative;
    backdrop-filter: blur(10px);
  }

  .container::before {
    content: "";
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 150px;
    height: 6px;
    background-color: #CD853F;
    border-radius: 0 0 10px 10px;
  }

  h2 {
    text-align: center;
    margin-top: 0;
    margin-bottom: 40px;
    color: #8B4513;
    font-size: 2.5em;
    text-shadow: 1px 1px 2px rgba(139, 69, 19, 0.2);
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
  }

  .full-width {
    grid-column: span 2;
  }

  .field-group {
    display: flex;
    flex-direction: column;
  }

  label {
    margin-bottom: 8px;
    font-weight: bold;
    color: #8B4513;
    font-size: 15px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  input, textarea {
    padding: 14px;
    font-size: 16px;
    border: 2px solid #EEDCBB;
    border-radius: 10px;
    font-family: "Georgia", serif;
    background-color: #FFFDFB;
    transition: all 0.3s ease;
  }

  input:focus, textarea:focus {
    border-color: #CD853F;
    box-shadow: 0 4px 12px rgba(205, 133, 63, 0.15);
    outline: none;
    background-color: #fff;
    transform: translateY(-2px);
  }

  button.submit-btn {
    margin-top: 10px;
    padding: 18px;
    background: linear-gradient(135deg, #D2691E, #A0522D);
    color: #fff;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 18px;
    font-family: "Georgia", serif;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: all 0.3s ease;
    box-shadow: 0 8px 15px rgba(210, 105, 30, 0.2);
  }

  button.submit-btn:hover {
    background: linear-gradient(135deg, #A0522D, #8B4513);
    transform: translateY(-3px);
    box-shadow: 0 12px 20px rgba(210, 105, 30, 0.3);
  }

  @media (max-width: 650px) {
    .form-grid {
      grid-template-columns: 1fr;
    }
    .full-width {
      grid-column: span 1;
    }
    .container {
      padding: 30px 20px;
    }
  }
</style>
</head>
<body>

<div class="container">
    <h2>Registration</h2>

    <!-- FIX: Added method and name attributes -->
    <form method="POST" action="">
        <div class="form-grid">
            <div class="field-group">
                <label for="signupName">Full Name</label>
                <input type="text" name="name" id="signupName" placeholder="e.g. John Doe" required />
            </div>

            <div class="field-group">
                <label for="signupEmail">Email Address</label>
                <input type="email" name="email" id="signupEmail" placeholder="john@example.com" required />
            </div>

            <div class="field-group">
                <label for="signupPassword">Password</label>
                <input type="password" name="password" id="signupPassword" placeholder="Minimum 8 characters" required />
            </div>

            <div class="field-group">
                <label for="signupPhone">Phone Number</label>
                <input type="tel" name="phone" id="signupPhone" placeholder="+1 (555) 000-0000" required />
            </div>

            <div class="field-group full-width">
                <label for="signupAddress">Residential Address</label>
                <textarea name="address" id="signupAddress" rows="3" placeholder="Street, City, State, Zip Code" required></textarea>
            </div>

            <button class="submit-btn full-width" type="submit">Sign Up</button>
        </div>
    </form>
</div>

<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // secure hashing
    $phone = $_POST["phone"];
    $address = $_POST["address"];

    // FIX: Correct column names
    $stmt = $conn->prepare("INSERT INTO users (name, email, pass, phone, addr) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $password, $phone, $address);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Error: Email already exists'); window.location.href='registration.php';</script>";
    }
}
?>

</body>
</html>

<?php
session_start();
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $instagram = $_POST['instagram'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($username) || empty($instagram) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR instagram_handle = ?");
        $stmt->execute([$email, $instagram]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Email or Instagram handle already exists";
        } else {
            // Create user
            $unique_link = generateUniqueLink($username);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, instagram_handle, email, password_hash, unique_link) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$username, $instagram, $email, $password_hash, $unique_link])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Tehillz Anonymous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Create Your Account</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                
                <div class="form-group">
                    <label>Instagram Handle:</label>
                    <input type="text" name="instagram" placeholder="@username" required>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>
            
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
<?php
session_start();
require_once '../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store token in database
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            if ($stmt->execute([$reset_token, $reset_token_expiry, $user['id']])) {
                
                // In production, you would send an email here
                // For demo purposes, we'll show the reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/tehillz_anonymous/pages/reset_password.php?token=" . $reset_token;
                
                $message = "Password reset instructions have been sent to your email.";
                
                // Demo mode - show the reset link
                $demo_mode = true; // Set to false in production
                if ($demo_mode) {
                    $message .= "<br><br><strong>Demo Reset Link:</strong><br>
                                <input type='text' value='{$reset_link}' readonly style='width: 100%; padding: 8px; margin-top: 10px;'>
                                <small>In production, this would be sent via email</small>";
                }
            } else {
                $error = "Error generating reset token. Please try again.";
            }
        } else {
            // Don't reveal whether email exists or not
            $message = "If an account with that email exists, we've sent password reset instructions.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Tehillz Anonymous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .demo-reset-link {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border: 1px solid #dee2e6;
        }
        .demo-reset-link input {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: white;
        }
        .instructions {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Reset Your Password</h2>
            <p>Enter your email address and we'll send you instructions to reset your password.</p>
            
            <div class="instructions">
                <strong>How it works:</strong>
                <ol>
                    <li>Enter your email address below</li>
                    <li>Check your email for reset instructions</li>
                    <li>Click the reset link (valid for 1 hour)</li>
                    <li>Create your new password</li>
                </ol>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="forgotPasswordForm">
                <div class="form-group">
                    <label for="email">Email Address:</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           placeholder="Enter your email address" 
                           required
                           autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    Send Reset Instructions
                </button>
                
                <div class="login-options">
                    <p>Remember your password? <a href="login.php">Back to Login</a></p>
                    <p><a href="../index.php">← Back to Home</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form submission handler with loading state
        document.getElementById('forgotPasswordForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = 'Sending...';
            submitBtn.disabled = true;
        });

        // Clear messages when user starts typing
        document.getElementById('email').addEventListener('input', function() {
            const errorAlert = document.querySelector('.alert-error');
            const successAlert = document.querySelector('.alert-success');
            
            if (errorAlert) errorAlert.style.display = 'none';
            if (successAlert) successAlert.style.display = 'none';
        });
    </script>
</body>
</html>
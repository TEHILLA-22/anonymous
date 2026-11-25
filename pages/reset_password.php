<?php
session_start();
require_once '../includes/functions.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';
$valid_token = false;
$token = $_GET['token'] ?? '';

// Validate token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id, email, reset_token_expiry FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $valid_token = true;
        $user_id = $user['id'];
    } else {
        $error = "Invalid or expired reset token. Please request a new password reset.";
    }
} else {
    $error = "No reset token provided.";
}

// Process password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Update password and clear reset token
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        
        if ($stmt->execute([$password_hash, $user_id])) {
            $success = "Password reset successfully! You can now login with your new password.";
            $valid_token = false; // Token is now used and invalid
        } else {
            $error = "Error resetting password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Tehillz Anonymous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .password-strength {
            margin-top: 5px;
            font-size: 0.8em;
        }
        .strength-weak { color: #e74c3c; }
        .strength-medium { color: #f39c12; }
        .strength-strong { color: #27ae60; }
        .password-toggle {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Create New Password</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                    <br><br>
                    <a href="login.php" class="btn btn-primary">Login Now</a>
                </div>
            <?php endif; ?>
            
            <?php if ($valid_token && empty($success)): ?>
                <p>Please enter your new password below.</p>
                
                <form method="POST" id="resetPasswordForm">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group password-toggle">
                        <label for="password">New Password:</label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Enter new password (min. 8 characters)" 
                               required
                               minlength="8">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password')">
                            👁️
                        </button>
                        <div id="passwordStrength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group password-toggle">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               placeholder="Confirm your new password" 
                               required
                               minlength="8">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">
                            👁️
                        </button>
                        <div id="passwordMatch" class="password-strength"></div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="resetBtn">
                        Reset Password
                    </button>
                </form>
            <?php elseif (empty($success)): ?>
                <div class="login-options">
                    <p><a href="forgot_password.php" class="btn btn-primary">Request New Reset Link</a></p>
                    <p><a href="login.php">Back to Login</a></p>
                    <p><a href="../index.php">← Back to Home</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleButton = passwordInput.parentNode.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🔒';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            
            if (password.length === 0) {
                strengthDiv.textContent = '';
                return;
            }
            
            let strength = 'Weak';
            let strengthClass = 'strength-weak';
            
            if (password.length >= 12) {
                strength = 'Strong';
                strengthClass = 'strength-strong';
            } else if (password.length >= 8) {
                strength = 'Medium';
                strengthClass = 'strength-medium';
            }
            
            strengthDiv.textContent = `Password strength: ${strength}`;
            strengthDiv.className = `password-strength ${strengthClass}`;
        });

        // Password match indicator
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (confirmPassword.length === 0) {
                matchDiv.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.textContent = '✓ Passwords match';
                matchDiv.className = 'password-strength strength-strong';
            } else {
                matchDiv.textContent = '✗ Passwords do not match';
                matchDiv.className = 'password-strength strength-weak';
            }
        });

        // Form validation
        document.getElementById('resetPasswordForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
            
            // Show loading state
            const resetBtn = document.getElementById('resetBtn');
            resetBtn.innerHTML = 'Resetting...';
            resetBtn.disabled = true;
        });
    </script>
</body>
</html>
<?php
session_start();
require_once '../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? OR instagram_handle = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_premium'] = $user['is_premium'];
            
            // Update last login time (you might want to add this field to your users table)
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$user['id']]);
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email/Instagram handle or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tehillz Anonymous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
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
        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
        }
        .forgot-password a:hover {
            text-decoration: underline;
        }
        .login-options {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .demo-account {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-form">
            <h2>Login to Your Account</h2>
            <p>Access your anonymous messages and dashboard</p>
            
            <?php if (isset($error) && !empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <!-- Demo Account Info (remove in production) -->
            <div class="demo-account">
                <strong>Demo Account:</strong><br>
                Email: demo@tehillz.com<br>
                Password: demo123
            </div>
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="email">Email or Instagram Handle:</label>
                    <input type="text" 
                           id="email" 
                           name="email" 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           placeholder="Enter your email or @instagram" 
                           required
                           autofocus>
                </div>
                
                <div class="form-group password-toggle">
                    <label for="password">Password:</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password" 
                           required>
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility()">
                        👁️
                    </button>
                </div>
                
                <div class="forgot-password">
                    <a href="forgot_password.php">Forgot your password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary" id="loginBtn">
                    Login
                </button>
                
                <div class="login-options">
                    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
                    <p><a href="../index.php">← Back to Home</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleButton.textContent = '🔒';
            } else {
                passwordInput.type = 'password';
                toggleButton.textContent = '👁️';
            }
        }

        // Form submission handler with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.innerHTML = 'Logging in...';
            loginBtn.disabled = true;
        });

        // Clear error when user starts typing
        document.getElementById('email').addEventListener('input', function() {
            const errorAlert = document.querySelector('.alert-error');
            if (errorAlert) {
                errorAlert.style.display = 'none';
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const errorAlert = document.querySelector('.alert-error');
            if (errorAlert) {
                errorAlert.style.display = 'none';
            }
        });

        // Enter key support
        document.getElementById('email').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('password').focus();
            }
        });

        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('loginForm').dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>
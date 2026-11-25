<?php
session_start();
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tehillz Anonymous - Send Anonymous Messages</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Tehillz Anonymous</h1>
            <p>Send and receive anonymous messages</p>
        </header>
        
        <div class="hero">
            <h2>Get Anonymous Feedback</h2>
            <p>Share your unique link and receive honest anonymous messages from your followers</p>
            
            <div class="cta-buttons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="pages/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                    <a href="pages/inbox.php" class="btn btn-secondary">View Messages</a>
                <?php else: ?>
                    <a href="pages/signup.php" class="btn btn-primary">Get Started</a>
                    <a href="pages/login.php" class="btn btn-secondary">Login</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="features">
            <div class="feature">
                <h3>100% Anonymous</h3>
                <p>Messages are completely anonymous by default</p>
            </div>
            <div class="feature">
                <h3>Easy Sharing</h3>
                <p>Share your unique link on Instagram, WhatsApp, etc.</p>
            </div>
            <div class="feature">
                <h3>Premium Insights</h3>
                <p>Upgrade to get hints about your anonymous senders</p>
            </div>
        </div>
    </div>
</body>
</html>
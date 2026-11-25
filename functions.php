<?php
require_once './config/database.php';

function generateUniqueLink($username) {
    $hash = md5($username . time());
    return 'tehillz_' . substr($hash, 0, 10);
}

function getUserByLink($link) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE unique_link = ?");
    $stmt->execute([$link]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function logMessage($user_id, $message, $ip, $user_agent) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message_text, sender_ip, sender_user_agent) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$user_id, $message, $ip, $user_agent]);
}

function getUserMessages($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getDeviceInfo($user_agent) {
    $device_info = [];
    
    // Detect device type
    if (strpos($user_agent, 'Mobile') !== false) {
        $device_info['type'] = 'Mobile';
    } else {
        $device_info['type'] = 'Desktop';
    }
    
    // Detect browser
    if (strpos($user_agent, 'Chrome') !== false) {
        $device_info['browser'] = 'Chrome';
    } elseif (strpos($user_agent, 'Firefox') !== false) {
        $device_info['browser'] = 'Firefox';
    } elseif (strpos($user_agent, 'Safari') !== false) {
        $device_info['browser'] = 'Safari';
    } else {
        $device_info['browser'] = 'Unknown';
    }
    
    return $device_info;
}


// ... existing functions ...

function generateResetToken() {
    return bin2hex(random_bytes(32));
}

function validateResetToken($token) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function sendResetEmail($email, $reset_token) {
    // In production, implement actual email sending
    $reset_link = "https://yourdomain.com/tehillz_anonymous/pages/reset_password.php?token=" . $reset_token;
    
    $subject = "Password Reset Request - Tehillz Anonymous";
    $message = "
        <html>
        <head>
            <title>Password Reset Request</title>
        </head>
        <body>
            <h2>Password Reset Request</h2>
            <p>You requested a password reset for your Tehillz Anonymous account.</p>
            <p>Click the link below to reset your password (valid for 1 hour):</p>
            <a href='{$reset_link}' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Reset Password</a>
            <p>If you didn't request this, please ignore this email.</p>
            <br>
            <p>Best regards,<br>Tehillz Anonymous Team</p>
        </body>
        </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@tehillz.com" . "\r\n";
    
    // Uncomment to send actual email in production
    // return mail($email, $subject, $message, $headers);
    
    // For demo, just return true
    return true;
}

function cleanupExpiredTokens() {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE users SET reset_token = NULL, reset_token_expiry = NULL WHERE reset_token_expiry < NOW()");
    return $stmt->execute();
}
?>
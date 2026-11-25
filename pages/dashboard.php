<?php
session_start();
require_once '../functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get message count
$stmt = $pdo->prepare("SELECT COUNT(*) as message_count FROM messages WHERE user_id = ?");
$stmt->execute([$user_id]);
$message_count = $stmt->fetch(PDO::FETCH_ASSOC)['message_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tehillz Anonymous</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <nav>
                <a href="inbox.php">Inbox (<?php echo $message_count; ?>)</a>
                <a href="../index.php">Home</a>
                <a href="logout.php">Logout</a>
            </nav>
        </header>
        
        <div class="dashboard">
            <div class="user-link">
                <h3>Your Unique Anonymous Link</h3>
                <div class="link-box">
                    <input type="text" value="<?php echo "https://tehillz.com/" . $user['unique_link']; ?>" id="userLink" readonly>
                    <button onclick="copyLink()" class="btn btn-secondary">Copy Link</button>
                </div>
                <p>Share this link on your Instagram story, WhatsApp status, or other social media to receive anonymous messages!</p>
            </div>
            
            <div class="stats">
                <div class="stat-card">
                    <h3>Total Messages</h3>
                    <p class="stat-number"><?php echo $message_count; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Your Status</h3>
                    <p class="stat-number"><?php echo $user['is_premium'] ? 'Premium' : 'Free'; ?></p>
                </div>
            </div>
            
            <?php if (!$user['is_premium']): ?>
            <div class="premium-offer">
                <h3>Upgrade to Premium</h3>
                <p>Get insights about your anonymous message senders:</p>
                <ul>
                    <li>📍 Location data</li>
                    <li>📱 Device information</li>
                    <li>👤 Username hints</li>
                </ul>
                <a href="premium.php" class="btn btn-premium">Upgrade Now - $4.99/month</a>
            </div>
            <?php endif; ?>
            
            <div class="sharing-tips">
                <h3>Sharing Tips</h3>
                <ul>
                    <li>Add the link to your Instagram Story with a creative sticker</li>
                    <li>Share on WhatsApp Status for close friends</li>
                    <li>Post on Twitter with engaging text</li>
                    <li>Add to your TikTok bio</li>
                </ul>
            </div>
        </div>
    </div>
    
    <script>
    function copyLink() {
        const linkInput = document.getElementById('userLink');
        linkInput.select();
        linkInput.setSelectionRange(0, 99999);
        document.execCommand('copy');
        alert('Link copied to clipboard!');
    }
    </script>
</body>
</html>
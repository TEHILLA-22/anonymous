<?php
require_once '../includes/functions.php';

$link = $_GET['link'] ?? '';
$user = getUserByLink($link);

if (!$user) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Anonymous Message to <?php echo htmlspecialchars($user['username']); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="message-form">
            <h2>Send Anonymous Message to <?php echo htmlspecialchars($user['username']); ?></h2>
            <p>Your message will be completely anonymous</p>
            
            <form id="messageForm">
                <div class="form-group">
                    <textarea name="message" placeholder="Type your anonymous message here..." required rows="5"></textarea>
                </div>
                <input type="hidden" name="user_link" value="<?php echo $link; ?>">
                <button type="submit" class="btn btn-primary">Send Anonymously</button>
            </form>
            
            <div id="responseMessage" style="display: none;"></div>
        </div>
    </div>
    
    <script>
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const data = {
            user_link: formData.get('user_link'),
            message: formData.get('message')
        };
        
        fetch('../api/send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            const responseDiv = document.getElementById('responseMessage');
            responseDiv.style.display = 'block';
            
            if (result.success) {
                responseDiv.className = 'alert alert-success';
                responseDiv.textContent = result.message;
                document.getElementById('messageForm').reset();
            } else {
                responseDiv.className = 'alert alert-error';
                responseDiv.textContent = result.error;
            }
        });
    });
    </script>
</body>
</html>
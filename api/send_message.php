<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $link = $data['user_link'] ?? '';
    $message = $data['message'] ?? '';
    
    if (empty($link) || empty($message)) {
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        exit;
    }
    
    // Get user by link
    $user = getUserByLink($link);
    
    if (!$user) {
        echo json_encode(['success' => false, 'error' => 'Invalid link']);
        exit;
    }
    
    // Log message with sender info
    $ip = getClientIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    if (logMessage($user['id'], $message, $ip, $user_agent)) {
        echo json_encode(['success' => true, 'message' => 'Message sent anonymously!']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to send message']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>
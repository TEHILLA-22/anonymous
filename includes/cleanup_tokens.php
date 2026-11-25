<?php
require_once '../config/database.php';
require_once 'functions.php';

// Run this script periodically (e.g., via cron job) to clean up expired tokens
cleanupExpiredTokens();
echo "Expired tokens cleaned up successfully.\n";
?>
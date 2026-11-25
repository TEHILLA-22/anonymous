<?php
session_start();

// Simple path resolution
function get_base_path($path) {
    $root = dirname(__DIR__);
    return $root . '/' . ltrim($path, '/');
}

// Include database
require_once get_base_path('config/database.php');

// Include functions
require_once get_base_path('includes/functions.php');
?>
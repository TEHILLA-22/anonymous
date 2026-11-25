<?php
// config/paths.php

// Define the absolute root path
define('ROOT_PATH', realpath(dirname(__FILE__) . '/..'));

// Define important directories
define('CONFIG_DIR', ROOT_PATH . '/config');
define('INCLUDES_DIR', ROOT_PATH . '/includes');
define('PAGES_DIR', ROOT_PATH . '/pages');
define('API_DIR', ROOT_PATH . '/api');
define('ASSETS_DIR', ROOT_PATH . '/assets');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'tehillz_anonymous');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Auto-load the database configuration
require_once CONFIG_DIR . '/database.php';
?>
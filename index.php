<?php
/**
 * Main Entry Point for Online Course Application
 * Handles routing and session management
 */

// Start session
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', dirname(__FILE__));
define('VIEWS_PATH', BASE_PATH . '/views');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('MODELS_PATH', BASE_PATH . '/models');
define('CONFIG_PATH', BASE_PATH . '/config');
define('ASSETS_PATH', BASE_PATH . '/assets');

// Autoload classes
require_once CONFIG_PATH . '/Database.php';

/**
 * Simple Autoloader for Models and Controllers
 */
spl_autoload_register(function($class) {
    $file = MODELS_PATH . '/' . $class . '.php';
    if (!file_exists($file)) {
        $file = CONTROLLERS_PATH . '/' . $class . '.php';
    }
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Get requested URL
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url_parts = explode('/', $url);

// Determine controller and action
$controller_name = !empty($url_parts[0]) ? ucfirst($url_parts[0]) . 'Controller' : 'HomeController';
$action = isset($url_parts[1]) ? $url_parts[1] : 'index';

// Check if controller exists and instantiate
$controller_file = CONTROLLERS_PATH . '/' . $controller_name . '.php';

if (file_exists($controller_file)) {
    require_once $controller_file;
    
    if (class_exists($controller_name)) {
        $controller = new $controller_name();
        
        // Check if action exists
        if (method_exists($controller, $action)) {
            // Pass remaining URL parts as parameters
            $params = array_slice($url_parts, 2);
            call_user_func_array([$controller, $action], $params);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Action not found: " . $action;
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Controller class not found: " . $controller_name;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Controller not found: " . $controller_name;
}
?>

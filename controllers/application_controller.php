<?php 
define('ROOT_DIR', __DIR__ . '/../');
define('RESOURCES', ROOT_DIR . 'public/resources/');
// ======= CONSTANTS CONFIGURABLES =======
define('HOST_DB', 'localhost');
define('NAME_DB', 'foroweb');
define('USER_DB', 'root');
define('PASS_DB', '');

define('URL', 'https://localhost/foroweb/');
// =======================================

/**
 * This function is used to get the current URL of the project
 * 
 * @return string
 */
function get_current_url() {
  $host = $_SERVER['HTTP_HOST'];
  $uri = $_SERVER['REQUEST_URI'];

  return 'http://' . $host . $uri;
}

/**
 * This function is used to get the last URL of the project
 * 
 * @return string
 */
function get_last_url() {
  if (isset($_SERVER['HTTP_REFERER'])) {
    return $_SERVER['HTTP_REFERER'];
  }
  
  return URL;
}

/**
 * This function is used to get a controller
 * 
 * @param string $controller_name, in plural
 * @return void
 */
function get_controller($controller_name) {
  $controller_path = ROOT_DIR . 'controllers/' . $controller_name . '_controller.php';

  file_exists($controller_path) ? require_once $controller_path : throw new Exception('Controller not found');
}

/**
 * This function is used to get a model
 * 
 * @param string $model_name, in singular
 * @return void
 */
function get_model($model_name) {
  $model_path = ROOT_DIR . 'models/' . $model_name . '.php';

  file_exists($model_path) ? require_once $model_path : throw new Exception('Model not found');
}

/**
 * This function is used to render a layout
 * 
 * @param string $template_name
 * @return html, the layout content from the views/layouts directory
 */
function render_layout($template_name) {
  ob_start();

  $layout_path = ROOT_DIR . 'views/layouts/_' . $template_name . '.php';

  file_exists($layout_path) ? require_once $layout_path : die('Layout not found');

  $template_content = ob_get_clean();

  return $template_content;
}
?>

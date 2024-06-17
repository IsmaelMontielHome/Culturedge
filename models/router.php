<?php
require_once "base.php";

class Router extends Base {
  private $uri;

  public $controller;
  public $action;
  public $params;

  public function __construct() {
    $this->filter_request();
    
    $this->controller = $this->get_controller();
    $this->action = $this->get_action();
    $this->params = $this->get_params();

    if ($this->post_request()) {
      $this->dispatch();
      exit;
    }
    }

  public function dispatch() {
    try {
      $controller = $this->controller;
      $action = $this->action;
      $data = [
        'method' => $this->post_request() ? $this->post_request() : $this->params,
        'files' => $_FILES
      ];

      ob_start();
      get_controller("$controller");
      $controller_name = ucfirst($controller) . 'Controller';
      $controller = new $controller_name($data);
      
      return $controller->$action();
    } catch (Exception | Error $e) {
      return $this->error('404');
    }
  }

  private function filter_request() {
    $request = filter_input_array(INPUT_GET);
    
    if (isset($request['uri'])) {
      $this->uri = $request['uri'];
      $this->uri = rtrim($this->uri, '/');
      $this->uri = ltrim($this->uri, '/');
      $this->uri = filter_var($this->uri, FILTER_SANITIZE_URL);
      $this->uri = explode('/', strtolower($this->uri));
    }

    return;
  }

  private function get_controller() {
    if (isset($this->uri[0]) && !empty($this->uri[0])) {
      $controller = $this->uri[0];
      unset($this->uri[0]);
    } else {
      $controller = 'posts';
    }

    return $controller;
  }

  private function get_action() {
    if (isset($this->uri[1])) {
      $action = $this->uri[1];
      unset($this->uri[1]);
    } else {
      $action = 'index';
    }

    return $action;
  }

  private function get_params() {
    $params = [];
    
    if (!empty($this->uri)) {
      foreach ($this->uri as $param) {
        $param = explode(':', $param);
        $params[$param[0]] = $param[1];
      }
    }

    return $params;
  }

  protected function post_request() {
    $params = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($params)) {
      return false;
    }

    return $params;
  }
}
?>

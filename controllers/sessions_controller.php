<?php
ob_start();
get_model('user');

class SessionsController extends User  {
  private $params;
  
  public function __construct($params) {
    try {
      parent::__construct();
      $this->params = $params['method'];
    } catch (Exception $e) {
      return $this->error('500');
    }
  }

  public function show() {
    return $this->render('show', $this->params);
  }

  public function new() {
    return $this->render('new', $this->params);
  }

  public function create() {
    try {
      $response = $this->verify_credentials($this->params);
      if ($response["status"]) {
        
        $_SESSION["user"] = $response["data"];
        // Actualizar last_activity después de autenticar al usuario
        $_SESSION["user"]["last_activity"] = date("Y-m-d H:i:s");

        $this->update_last_activity($_SESSION["user"]["id"], $_SESSION["user"]["last_activity"]);
  
        
        if ($response["data"]["rol"] === 0)
          header('Location: /admins/console');
        else {
          header('Location: /');
        }
      } else {
        throw new Exception("Failed to login user: " . $response["message"]);
      }
    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      error_log($e->getMessage());
      header("Location: /sessions/new");
    }
  }

  public function destroy() {
    session_destroy();
    header('Location: /');
  }

  protected function render($view, $data = []) {
    $params = $data;

    include ROOT_DIR . 'views/users/sessions/' . $view . '.php';

    return ob_get_clean();
  }
}
?>

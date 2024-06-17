<?php
ob_start();
get_model('user');

class PasswordsController extends User {
  private $params;
  
  public function __construct($params) {
    try {
      parent::__construct();
      $this->params = $params['method'];
    } catch (Exception $e) {
      return $this->error('500');
    }
  }

  public function new() {
    return $this->render('new', $this->params);
  }

  public function create() {
    try {
      $response = $this->send_reset_password_instructions($this->params);
      if ($response["status"]) {
        header('Location: /passwords/new');
      } else {
        throw new Exception("Failed to resend code: " . $response["message"]);
      }
    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      header('Location: /passwords/new');
    }
  }

  public function edit() {
    return $this->render('edit', $this->params);
  }

  public function patch() {
    try {
      $response = $this->authenticate();

      if ($response["status"]) {
        header('Location: /sessions/new');
      } else {
        throw new Exception("Failed to update password: " . $response["message"]);
      }
    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      error_log($e->getMessage());
      header('Location: /passwords/edit/reset_password_token:' . $this->params["token"]);
    }
  }

  private function authenticate() {
    try {
      if ($this->params['newpassword'] !== $this->params['cpassword']) {
        throw new Exception("Passwords do not match");
      }

      $user = $this->get_user_by_reset_password_token($this->params['token']);
      $user_data = $user["data"];

      if (password_verify($this->params['newpassword'], $user_data['password'])) {
        throw new Exception("Password cannot be the same as the current password");
      }

      $this->params = array_merge($this->params, $user_data);
      $this->params['newpassword'] = password_hash($this->params['newpassword'], PASSWORD_DEFAULT);

      return $this->update_password($this->params);
    } catch (Exception $e) {
      throw new Exception("Failed to authenticate user: " . $e->getMessage());
    }
  }
    
  protected function render($view, $data = []) {
    $params = $data;

    include ROOT_DIR . 'views/users/passwords/' . $view . '.php';

    return ob_get_clean();
  }
}
?>

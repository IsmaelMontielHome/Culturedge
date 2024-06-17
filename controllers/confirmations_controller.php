<?php
ob_start();
get_model('user');

class ConfirmationsController extends User {
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
      $response = $this->confirm_confirmation_token($this->params);

      if ($response["status"]) {
        header('Location: /sessions/new');
      } else {
        throw new Exception("Failed to verify email: " . $response["message"]);
      }
    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      error_log($e->getMessage());
      header('Location: /confirmations/new/confirm_token:' . $this->params["token"]);
    }
  }

  public function patch() {
    try {
      $response = $this->resend_code($this->params);

      if ($response["status"]) {
        header('Location: /confirmations/new/confirm_token:' . $response["data"]["token"]);
      } else {
        throw new Exception("Failed to resend code: " . $response["message"]);
      }
    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      error_log($e->getMessage());
      header('Location: /confirmations/new/confirm_token:' . $this->params["token"]);
    }
  }

  private function render($view, $data = []) {
    $params = $data;

    include ROOT_DIR . 'views/users/confirmations/' . $view . '.php';

    return ob_get_clean();
  }
}
?>

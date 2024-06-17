<?php
ob_start();
get_model('user');

/*
 * This class inherits from the base class and contains the calls to the posts procedures
 */
class RegistrationsController extends User {
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
      $response = $this->authenticate();

      if ($response["status"]) {
        header("Location: /confirmations/new/confirm_token:" . $response["data"]["token"]);
      } else {
        throw new Exception($response["message"]);
      }
    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
      header("Location: /registrations/new");
    }
  }

  private function authenticate() {
    try {
      if ($this->params['password'] !== $this->params['cpassword']) {
        throw new Exception("Passwords do not match");
      }

      $this->params['password'] = password_hash($this->params['password'], PASSWORD_DEFAULT);

      return $this->save($this->params);
    } catch (Exception $e) {
      throw new Exception("Failed to create user: " . $e->getMessage());
    }
  }

  protected function render($view, $data = []) {
    $params = $data;

    include ROOT_DIR . 'views/users/registrations/' . $view . '.php';

    return ob_get_clean();
  }
}
?>

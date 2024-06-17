<?php 
ob_start();
get_model('unesco');

class UnescoController extends Unesco {
  private $params;

  public function __construct($params) {
    try {
      parent::__construct();
      $this->params = $params['method'];
    } catch (Exception $e) {
      return $this->error('500');
    }
  }

  public function get_themes() {
    try {
      $response = $this->get_all($this->params['limit']);
      $response = json_decode($response);

      if ($response->status) {
        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function get_theme() {
    try {
      $response = $this->get_by_id($this->params['id']);
      $response = json_decode($response);

      if ($response->status) {
        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function create() {
    try {
      $response = $this->insert_theme($this->params);
      $response = json_decode($response);

      if ($response->status) {
        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function patch() {
    try {
      $response = $this->update_theme($this->params);
      $response = json_decode($response);

      if ($response->status) {
        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function destroy() {
    try {
      $response = $this->delete_theme($this->params['id']);
      $response = json_decode($response);

      if ($response->status) {
        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }
}
?>

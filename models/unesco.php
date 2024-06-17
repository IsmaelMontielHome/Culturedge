<?php
require_once "base.php";

class Unesco extends Base {
  public function __construct() {
    try {
      $this->conn = $this->db_connection();
      $this->check_connection();
    } catch (PDOException | Exception $e) {
      // throw new Exception("Failed to connect to the database: " . $e->getMessage());
      echo $this->error('500');
      exit;
    }
  }

  public function get_all($limit = 5) {
    try {
      $this->t = 'unesco';
      $response = $this->limit($limit)->get();

      if (empty($response)) {
        throw new Exception("No UNESCO themes found.");
      }

      $r = $this->response(status: true, data: $response, message: "UNESCO themes retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to retrieve UNESCO themes: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function get_by_id($id) {
    try {
      $this->t = 'unesco';
      $response = $this->select()->where([['id', '=', $id]])->first();

      if (empty($response)) {
        throw new Exception("UNESCO theme not found.");
      }

      $r = $this->response(status: true, data: $response, message: "UNESCO theme retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to retrieve the UNESCO theme: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function insert_theme($data) {
    try {
      $this->t = 'unesco';
      $this->pp = ['theme', 'icon'];

      $response = $this->insert([
        'theme' => $data['theme'],
        'icon' => $data['icon']
      ]);

      if (empty($response)) {
        throw new Exception("Failed to insert the UNESCO theme.");
      }

      $r = $this->response(status: true, message: "UNESCO theme inserted successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to insert the UNESCO theme: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function update_theme($data) {
    try {
      $this->t = 'unesco';
      $this->pp = ['theme', 'icon', 'updated_at'];

      $response = $this->where([
        ['id', '=', $data['id']]
      ])->update([
        'theme' => $data['theme'],
        'icon' => $data['icon'],
        'updated_at' => date('Y-m-d H:i:s')
      ]);

      if (empty($response)) {
        throw new Exception("UNESCO theme not found.");
      }

      $r = $this->response(status: true, message: "UNESCO theme updated successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to update the UNESCO theme: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function delete_theme($id) {
    try {
      $this->t = 'unesco';
      $response = $this->where([['id', '=', $id]])->delete();

      if (empty($response)) {
        throw new Exception("UNESCO theme not found.");
      }

      $r = $this->response(status: true, message: "UNESCO theme deleted successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to delete the UNESCO theme: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }
}
?>

<?php 
require_once "base.php";

class Notification extends Base {
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

  public function save($data) {
    try {
      $this->t = 'notifications';
      $this->pp = ['user_id', 'type', 'type_id', 'causer_id'];

      $result = $this->insert($data);

      if (empty($result)) {
        throw new Exception("Failed to save the notification.");
      }

      $r = $this->response(status: true, data: $result, message: "Notification saved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to save the notification: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function get_unseen_notifications_count($user_id) {
    try {
      $this->t = 'notifications';

      $response = $this->select([
        'COUNT(*) as notifications',
      ])->where([
        ['user_id', '=', $user_id],
        ['seen', '=', 0],
      ])->first();

      if (empty($response)) {
        throw new Exception("No unseen notifications.");
      }

      $r = $this->response(status: true, data: $response, message: "Unseen notifications retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to retrieve unseen notifications: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function get_unseen_notifications($user_id) {
    try {
      $this->t = 'notifications';

      $response = $this->select([
        'a.id as notification_id',
        'a.type',
        'a.type_id as id',
        'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
        'a.causer_id',
        'a.seen',
        'b.username as username'
      ])->left_join('users as b', 'a.causer_id = b.id')
      ->where([
        ['user_id', '=', $user_id],
        ['seen', '=', 0],
      ])->get();

      if (empty($response)) {
        throw new Exception("No unseen notifications.");
      }

      $r = $this->response(status: true, data: $response, message: "Unseen notifications retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to retrieve unseen notifications: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function get_notifications($user_id) {
    try {
      $this->t = 'notifications';

      $response = $this->select([
        'a.id as notification_id',
        'a.type',
        'a.type_id as id',
        'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
        'a.causer_id',
        'a.seen',
        'b.username as username'
      ])->left_join('users as b', 'a.causer_id = b.id')
      ->where([
        ['user_id', '=', $user_id],
      ])->get();

      if (empty($response)) {
        throw new Exception("No notifications.");
      }

      $r = $this->response(status: true, data: $response, message: "Notifications retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to retrieve notifications: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function mark_as_read($ids) {
    try {
      $this->t = 'notifications';
      $this->pp = ['seen', 'updated_at'];

      $where = [];

      foreach ($ids as $id) {
        $where[] = ['id', '=', $id];
      }
      
      $response = $this->where_complex(
        orConditions: $where
      )->update([
        'seen' => 1,
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      if (empty($response)) {
        throw new Exception("Failed to mark the notifications as read.");
      }

      $r = $this->response(status: true, message: "Notifications marked as read successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: "Failed to mark the notification as read: " . $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }
}
?>

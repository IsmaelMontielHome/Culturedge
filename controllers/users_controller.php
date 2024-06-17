<?php
ob_start();
get_model('user');
get_model('post');
get_model('notification');

class UsersController extends User {
  private $params;
  private $files;
  
  public function __construct($params) {
    try {
      parent::__construct();
      $this->params = $params['method'];
      $this->files = $params["files"] ?? [];
    } catch (Exception $e) {
      return $this->error('500');
    }
  }

  public function show() {
    try {
      $response = $this->find_by_id($this->params['id']);

      $follower = ['follower' => false];
      if (isset($_SESSION['user'])) {
        $follower['follower'] = $this->is_following($this->params['id'], $_SESSION['user']['id']);
      }

      $response['data'] = array_merge($response['data'], $follower);

      if ($response['status']) {
        return $this->render('show', $response['data']);
      } else {
        throw new Exception('Failed to get the user with id ' . $this->params['id'] . ': ' . $response['message']);
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
      return $this->error('404');
    }
  }


  public function follow() {
    try {
      $response = $this->create_follow($this->params);
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

  public function unfollow() {
    try {
      $response = $this->destroy_follow($this->params);
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

  public function followers() {
    try {
      $response = $this->get_followers($this->params['id']);
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

  public function following() {
    try {
      $response = $this->get_following($this->params['id']);
      $response = json_decode($response, true);

      if ($response->status) {
        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function is_following($user, $follower) {
    try {
      $db = new self(['method' => $this->params]);

      $db->t = 'followers';
      $db->pp = ['user_id', 'follower_id'];

      $response = $db->where([
        ['user_id', '=', $user],
        ['follower_id', '=', $follower]
      ])->first();

      if (!empty($response)) {
        return true;
      } else {
        throw new Exception('Is not following');
      }
    } catch (Exception $e) {
      return false;
    }
  }

  public function notifications_count() {
    try {
      $notifications = new Notification();

      $response = $notifications->get_unseen_notifications_count($this->params['id']);
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

  public function notifications() {
    try {
      $notifications = new Notification();

      $response = $notifications->get_unseen_notifications($this->params['id']);
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

  public function all_notifications() {
    try {
      $notifications = new Notification();

      $response = $notifications->get_notifications($this->params['id']);
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

  public function mark_notifications_as_read() {
    try {
      $notifications = new Notification();

      $response = $notifications->mark_as_read($this->params['ids']);
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
  
  public function posts() {
    try {
      $posts = new Post();
      $response = $posts->find_by_author_id($this->params['id']);
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

  public function media() {
    try {
      $posts = new Post();
      $response = $posts->posts_media_by_author_id($this->params['id']);
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

  public function comments() {
    try {
      $posts = new Post();
      $response = $posts->posts_comments_by_author_id($this->params['id']);
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

  public function get_data() {
    try {
      $response = $this->find_by_id($this->params['id']);

      if ($response['status']) {
        $dateObj = DateTime::createFromFormat('d M Y', $response['data']['birthdate']);
        if ($dateObj) {
          $response['data']['birthdate'] = $dateObj->format('Y-m-d');
        }

        echo json_encode($response);
      } else {
        throw new Exception(json_encode($response));
      }
    } catch (Exception $e) {
      $r = $this->response(status: false, message: $e->getMessage());

      echo json_encode($r);
    }
  }

  public function has_data() {
    try {
      $this->t = 'user_data';

      $result = $this->where([['user_id', '=', $this->params['user_id']]])->first();

      if (!empty($result)) {
        echo json_encode($this->response(status: true, message: 'User has data', data: $result));
      } else {
        throw new Exception('User has no data');
      }
    } catch (Exception $e) {
      echo json_encode($this->response(status: false, message: $e->getMessage(), data: $result));
    }
  }

  public function save_user_data() {
    try {
      $resultData = $this->filtrate_user_data($this->params);
      $resultImage = $this->user_data_images($this->files);
      
      $response = $this->create_data($resultData, $resultImage['data']);
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

  public function update_user_data() {
    try {
      $resultData = $this->filtrate_user_data($this->params);
      $resultImage = $this->user_data_images($this->files);

      $response = $this->update_data($resultData, $resultImage['data']);
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

  private function filtrate_user_data($data) {
    foreach ($data as $key => $value) {
      if (empty($value)) {
        $data[$key] = NULL;
      }
    }

    return $data;
  }

  private function user_data_images($images) {
    try {
      $imagesChecked = [];
      $hasImage = false;

      if (isset($images['banner'])) {
        $hasImage = true;
      }

      if (isset($images['profile'])) {
        $hasImage = true;
      }

      if ($hasImage) {
        foreach ($images as $key => $image) {
          $imagesChecked[$key] = $this->check_image($image)['data'];
        }
      }
      
      return $this->response(status: true, data: $imagesChecked, message: 'Images checked');
    } catch (Exception $e) {
      throw new Exception('There was an error checking for images. ' . $e->getMessage());
    }
  }

  protected function render($view, $data = []) {
    $data = $this->to_obj($data);

    include ROOT_DIR . 'views/users/' . $view . '.php';

    return ob_get_clean();
  }
}
?>

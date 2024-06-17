<?php 
require_once "base.php";
require_once "email.php";
require_once "notification.php";

class User extends Base {
  public function __construct() {
    try {
      $this->conn = $this->db_connection();
      $this->check_connection();
    } catch (Exception $e) {
      echo $this->error('500');
      exit;
    }
  }

  public function find_by_id($id) {
    try {
      $this->t = 'users';

      $result1 = $this->select([
        'id', 'username', 'email',
        'DATE_FORMAT(created_at, "%e %M %Y") as created_at',
      ])->where([['id', '=', $id]])
      ->first();

      if (empty($result1)) {
        throw new Exception("User not found");
      }

      $result2 = $this->select([
        'COUNT(*) as posts',
        'COUNT(DISTINCT c.follower_id) as followers',
        'COUNT(DISTINCT d.user_id) as following',
        'f.image as avatar',
        'g.image as banner',
        'e.gender',
        'DATE_FORMAT(e.birthdate, "%e %M %Y") as birthdate',
      ])->left_join('posts as b', 'b.user_id = a.id')
      ->left_join('followers as c', 'a.id = c.user_id')
      ->left_join('followers as d', 'a.id = d.follower_id')
      ->left_join('user_data as e', 'a.id = e.user_id')
      ->left_join('images as f', 'e.pfp = f.id')
      ->left_join('images as g', 'e.banner = g.id')
      ->where([['a.id', '=', $id]])
      ->first();

      $result = array_merge($result1, $result2);

      return $this->response(status: true, data: $result, message: "User retrieved successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function get_followers($id) {
    try {
      $this->t = 'users';
      $this->pp = ['id'];

      $result = $this->select([
        'a.id', 'a.username'
      ])->join('followers as b', 'b.follower_id = a.id')
        ->where([['b.user_id', '=', $id]])
        ->get();

      if (!$result > 0) {
        throw new Exception("Followers not found");
      }

      $r = $this->response(status: true, data: $result, message: "Followers retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function get_following($id) {
    try {
      $this->t = 'users';
      $this->pp = ['id'];

      $result = $this->select([
        'a.id', 'a.username'
      ])->join('followers as b', 'b.user_id = a.id')
        ->where([['b.follower_id', '=', $id]])
        ->get();

      if (!$result > 0) {
        throw new Exception("Following not found");
      }

      $r = $this->response(status: true, data: $result, message: "Following retrieved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function save($data) {
    try {
      $code = $this->generate_code();
      $token = $this->generate_token();

      $stmt = $this->conn->prepare("CALL save_user(:username, :email, :password, :code, :token)");
      $stmt->bindParam(":username", $data["username"], PDO::PARAM_STR);
      $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
      $stmt->bindParam(":password", $data["password"], PDO::PARAM_STR);
      $stmt->bindParam(":code", $code, PDO::PARAM_STR);
      $stmt->bindParam(":token", $token, PDO::PARAM_STR);
      $stmt->execute();

      $success = Email::confirm_email($data["email"], $code);

      if (!$success) {
        throw new Exception("Error sending the confirmation email.");
      }

      return $this->response(status: true, data: ["token" => $token], message: "User saved successfully.");
    } catch (PDOException | Exception $e) {
      if (str_contains($e->getMessage(), '1062 Duplicate entry')) {
        $cause = str_contains($e->getMessage(), 'email') ? 'Email' : 'Username';

        throw new Exception("$cause already exists.");
      }

      throw new Exception("Unknown error");
    }
  }

  public function update_password($data) {
    try {
      $this->expiration_time($data['reset_password_sent_at'], minutes: 20);

      $stmt = $this->conn->prepare("CALL update_user_password(:token, :password)");
      $stmt->bindParam(":token", $data["reset_password_token"], PDO::PARAM_STR);
      $stmt->bindParam(":password", $data["newpassword"], PDO::PARAM_STR);
      $stmt->execute();

      return $this->response(status: true, message: "Password updated successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception("Failed to update password: " . $e->getMessage());
    }
  }

  public function get_user_by_reset_password_token($token) {
    try {
      $stmt = $this->conn->prepare("CALL get_user_by_reset_password_token(:token)");
      $stmt->bindParam(":token", $token, PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (empty($user)) {
        throw new Exception("User not found");
      }

      return $this->response(status: true, data: $user, message: "User retrieved successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception("Failed to get user by reset password token: " . $e->getMessage());
    }
  }

  public function confirm_confirmation_token($data) {
    try {
      $this->t = 'users';

      $result = $this->where([
        ['confirmation_code', '=', $data['code']],
        ['confirmation_token', '=', $data['token']]
      ])->first();
        
      if (empty($result)) {
        throw new Exception("Invalid code or token");
      }

      $this->confirmation_token_expiration($data['token']);
        
      $stmt = $this->conn->prepare("CALL confirm_user(:code, :token)");
      $stmt->bindParam(':code', $data['code'], PDO::PARAM_STR);
      $stmt->bindParam(':token', $data['token'], PDO::PARAM_STR);
      $stmt->execute();

      return $this->response(status: true, message: "User confirmed successfully.");
    } catch (Exception | PDOException $e) {
      throw new Exception("Failed to confirm user: " . $e->getMessage());
    }
  }

  public function resend_code($data) {
    try {
      $stmt = $this->conn->prepare("CALL get_user_by_confirmation_token(:token)");
      $stmt->bindParam(':token', $data['token'], PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (empty($user)) {
        throw new Exception("User not found");
      }

      $code = $this->generate_code();
      $token = $this->generate_token();

      $stmt = $this->conn->prepare("CALL update_user_confirmation_code(:old_token, :code, :token)");
      $stmt->bindParam(':old_token', $data['token'], PDO::PARAM_STR);
      $stmt->bindParam(':code', $code, PDO::PARAM_STR);
      $stmt->bindParam(':token', $token, PDO::PARAM_STR);
      $stmt->execute();

      $success = Email::confirm_email($user['email'], $code);

      if (!$success) {
        throw new Exception("Failed to send the confirmation email.");
      }

      return $this->response(status: true, data: ["token" => $token], message: "Code resent successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception("Failed to resend code: " . $e->getMessage());
    }
  }

  public function send_reset_password_instructions($data) {
    try {
      $stmt = $this->conn->prepare("CALL get_user_by_email(:email)");
      $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (empty($user)) {
        throw new Exception("User not found");
      }

      $token = $this->generate_token(32);
      $link = URL . "/passwords/edit/reset_password_token:" . $token;

      $stmt = $this->conn->prepare("CALL update_user_reset_password_token(:email, :token)");
      $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
      $stmt->bindParam(":token", $token, PDO::PARAM_STR);
      $stmt->execute();

      $success = Email::reset_password_email($data["email"], $link);

      if (!$success) {
        throw new Exception("Failed to send the reset password email.");
      }

      return $this->response(status: true, message: "Reset password instructions sent successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception("Failed to send reset password instructions: " . $e->getMessage());
    }
  }

  public function verify_credentials($data) {
    try {
        $stmt = $this->conn->prepare("CALL get_user_by_email(:email)");
        $stmt->bindParam(":email", $data["email"], PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (empty($user)) {
            throw new Exception("Incorrect credentials");
        }
        
        if (!password_verify($data["password"], $user["password"])) {
            throw new Exception("Incorrect credentials");
        }

        if (empty($user['confirmed_at'])) {
            throw new Exception("Verify your email to proceed.");
        }

        if ($user['ban'] == 1) {
            throw new Exception("Your account is banned.");
        }

        return $this->response(status: true, data: $user, message: "User logged in successfully.");
    } catch (Exception $e) {
        throw new Exception("Failed to login: " . $e->getMessage());
    }
}


  public function create_follow($data) {
    try {
      $this->t = 'followers';
      $this->pp = ['user_id', 'follower_id'];

      $result = $this->insert(values: $data, returnId: false);

      if (empty($result)) {
        throw new Exception("Got an empty response");
      }

      $r = $this->response(status: true, data: $result, message: "User followed successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function destroy_follow($data) {
    try {
      $this->t = 'followers';
      $this->pp = ['user_id', 'follower_id'];

      $result = $this->where([
        ['user_id', '=', $data['user_id']], 
        ['follower_id', '=', $data['follower_id']]
      ])->delete();

      if (empty($result)) {
        throw new Exception("Got an empty response");
      }

      $r = $this->response(status: true, data: $result, message: "User unfollowed successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $r = $this->response(status: false, message: $e->getMessage());

      throw new Exception(json_encode($r));
    }
  }

  public function create_data($data, $files = []) {
    try {
      // Upload images to the server
      $uploadedImages = [];
      foreach ($files as $key => $file) {
        if (!empty($file)) {
          $response = $this->upload_image($file);
          $uploadedImages[$key] = $response['data'];
        }
      }
      
      $this->begin_transaction();
      
      // Insert images to the database and get the id of the images
      foreach ($uploadedImages as $key => $image) {
        $response = $this->insert_image(
          ['user_id' => $data['user_id'], 'image' => $image]
        );

        $data[$key] = $response['data'];
      }

      $this->t = 'user_data';
      $this->pp = ['user_id', 'pfp', 'banner', 'gender', 'birthdate'];

      // Insert user pfp(id), banner(id), gender and birthdate
      $result = $this->insert(values: $data, returnId: false);

      if (empty($result)) {
        throw new Exception("Got an empty response");
      }

      $this->commit();

      $r = $this->response(status: true, data: $result, message: "User data saved successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $this->roll_back();
      
      // Delete uploaded images from the server
      $response = '';
      foreach ($uploadedImages as $key => $image) {
        $response .= $this->rid_image($image)['message'] . "Image: $key. ";
      }

      $error = $e->getMessage() . ". $response";

      $r = $this->response(status: false, message: $error);

      throw new Exception(json_encode($r));
    }
  }

  public function update_data($data, $files = []) {
    try {
      // Get previous images, id and image
      $previusImages = $this->get_data_images($data['user_id']);
      
      // Upload new images to the server
      $uploadedImages = [];
      foreach ($files as $key => $file) {
        if (!empty($file)) {
          $response = $this->upload_image($file);
          $uploadedImages[$key] = $response['data'];
        }
      }

      $this->begin_transaction();

      $this->t = 'user_data';
      if (!empty($data['birthdate'])) {
        $this->pp = ['birthdate'];
  
        // Update user data, gender and birthdate
        $result = $this->where([
          ['user_id', '=', $data['user_id']]
        ])->update([
          'birthdate' => $data['birthdate'],
        ]);

        if (empty($result)) {
          throw new Exception("Got an empty response on birthdate");
        }
      }


      if (!empty($data['gender'])) {
        $this->pp = ['gender'];

        // Update user data, gender and birthdate
        $result = $this->where([
          ['user_id', '=', $data['user_id']]
        ])->update([
          'gender' => $data['gender'],
        ]);
        
        if (empty($result)) {
          throw new Exception("Got an empty response");
        }
      }

      
      // Update images to the database
      foreach ($uploadedImages as $key => $image) {
        $response = $this->update_image([
          'id' => $previusImages[$key]['id'],
          'image' => $image,
        ]);
      }

      // Delete previous images from the server
      foreach ($previusImages as $image) {
        $response = $this->rid_image($image['image']);
      }

      $this->commit();

      $r = $this->response(status: true, data: $result, message: "User data updated successfully.");

      return json_encode($r);
    } catch (PDOException | Exception $e) {
      $this->roll_back();

      // Delete uploaded images from the server
      $response = '';
      foreach ($uploadedImages as $key => $image) {
        $response .= $this->rid_image($image)['message'] . "Image: $key. ";
      }

      $error = $e->getMessage() . ". $response";

      $r = $this->response(status: false, message: $error);

      throw new Exception(json_encode($r));
    }
  }

  public function insert_image($data) {
    try {
      $this->t = 'images';
      $this->pp = ['user_id', 'image'];
      
      $result = $this->insert($data);

      if (empty($result)) {
        throw new Exception("Got an empty response");
      }

      return $this->response(status: true, data: $result, message: "Image saved successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function update_image($data) {
    try {
      $this->t = 'images';
      $this->pp = ['image'];

      $result = $this->where([
        ['id', '=', $data['id']]
      ])->update([
        'image' => $data['image'],
      ]);

      if (empty($result)) {
        throw new Exception("Got an empty response");
      }

      return $this->response(status: true, data: $result, message: "Image updated successfully.");
    } catch (PDOException | Exception $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function get_data_images($user_id) {
    $this->t = 'user_data';

    $result = $this->select([
      'a.pfp as pfp_id',
      'a.banner as banner_id',
      'b.image as pfp',
      'c.image as banner',
    ])->left_join('images b', 'a.pfp = b.id')
    ->left_join('images c', 'a.banner = c.id')
    ->where([
      ['a.user_id', '=', $user_id]
    ])->first();
    
    $result = [
      'pfp' => [
        'id' => $result['pfp_id'],
        'image' => $result['pfp'],
      ],
      'banner' => [
        'id' => $result['banner_id'],
        'image' => $result['banner'],
      ],
    ];

    return $result;
  }

  /**
   * Generate a random code
   * It used to generate a shorter token, if want to use a more secure token use the generate_token method from the base class
   * 
   * @param int $longitud
   * @return string
   * 
   * By Ismael March 12th, 2024 9:19 PM
   * Modified by Alejandro March 16th, 2024 23:02 AM UTC-6
   */
  public function generate_code($longitud = 6) {
    $bytes = random_bytes($longitud);
    return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $longitud);
  }

  private function confirmation_token_expiration($token) {
    try {
      $stmt = $this->conn->prepare("CALL get_user_by_confirmation_token(:token)");
      $stmt->bindParam(':token', $token, PDO::PARAM_STR);
      $stmt->execute();
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
      if (empty($user)) {
        throw new Exception("Invalid token");
      }

      return $this->expiration_time($user['confirmation_sent_at']);
    } catch (Exception | PDOException $e) {
      throw new Exception($e->getMessage());
    }
  }

  private function expiration_time($date, $minutes = 10) {
    $utc = new DateTimeZone('UTC');
    $now = new DateTime('now', $utc);
    $expiration = new DateTime($date, $utc);
    $expiration_time = 60 * $minutes;

    if ($now->getTimestamp() > $expiration->getTimestamp() + $expiration_time) {
      throw new Exception("Expired token");
    }

    return true;
  }

  public function update_last_activity($user_id, $last_activity) {
    try {
        $this->t = 'users'; // Tabla de usuarios
        $this->pp = ['last_activity']; // Campos que se van a actualizar

        $result = $this->where([
            ['id', '=', $user_id]
        ])->update([
            'last_activity' => $last_activity
        ]);

        if (empty($result)) {
            throw new Exception("Got an empty response");
        }

        return $this->response(status: true, data: $result, message: "Last activity updated successfully.");
    } catch (PDOException | Exception $e) {
        throw new Exception($e->getMessage());
    }
}

}
?>

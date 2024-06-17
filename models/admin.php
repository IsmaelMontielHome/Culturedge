<?php 
require_once "base.php";

class Admin extends Base {

    public function __construct() {
        try {
            $this->conn = $this->db_connection();
            $this->check_connection();

            if (isset($_SESSION['user']) && $_SESSION['user']['rol'] !== 0) {
                header("Location: /");
                exit;
            }
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to connect to the database: " . $e->getMessage());
        }
    }

    public function all_users() {
        try {
            $this->t = 'users';
    
            $result = $this->select([
                'id',
                'username',
                'ban',
                'rol'
            ])->group_by('id, username', 'ban', 'rol')
            ->order_by([
                ['created_at', 'DESC']
            ])->get();
            return $this->response(status: true, data: $result, message: "Users retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all users: " . $e->getMessage());
        }
    }

    
    public function ban($userId) {
        try {
            $stmt = $this->conn->prepare("CALL BanUser(:id)");
            $stmt->bindparam(":id", $userId, PDO::PARAM_INT);  
            $stmt->execute();

            return ["status" => true, "message" => "User banned successfully"];
        } catch (PDOException | Exception $e) {

            throw new Exception("Failed to ban the user: " . $e->getMessage());
        }
    }
    
    public function unban($userId) {
        try {
            $stmt = $this->conn->prepare("CALL UnbanUser(:id)");
            $stmt->bindparam(":id", $userId, PDO::PARAM_INT);  
            $stmt->execute();

            return ["status" => true, "message" => "User banned successfully"];
        } catch (PDOException | Exception $e) {

            throw new Exception("Failed to ban the user: " . $e->getMessage());
        }
    }

   public function user_info_delete($userId) {
        try {
            $stmt = $this->conn->prepare("CALL DeleteUser(:id)");
            $stmt->bindparam(":id", $userId, PDO::PARAM_INT);  
            $stmt->execute();

            return ["status" => true, "message" => "User banned successfully"];
        } catch (PDOException | Exception $e) {

            throw new Exception("Failed to ban the user: " . $e->getMessage());
        }
    }

    public function all_topics() {
        try {
            $this->t = 'unesco';
    
            $result = $this->select([
                'id',
                'theme',
                'icon',
                'DATE_FORMAT(created_at, "%d-%m-%Y") as created_at',
                'DATE_FORMAT(updated_at, "%d-%m-%Y") as updated_at'
            ])->order_by([
                ['id', 'DESC'],
            ])->get();
            return $this->response(status: true, data: $result, message: "Themes retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all themes: " . $e->getMessage());
        }
    }
    
    public function rejected_post($id, $reason) {
        try {
            $this->t = 'posts';
            $this->pp = ['reason', 'permission'];
            $whereConditions = [
                ['id', '=', $id]
            ];
            $values = [
                'reason' => $reason,
                'permission' => "2"
            ];
            $result = $this->where($whereConditions)->update($values);
            return $result;
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to update reason: " . $e->getMessage());
        }
    }
    
    public function accepted_post($id) {
        try {
            $this->t = 'posts';
            $this->pp = ['permission'];
            $whereConditions = [
                ['id', '=', $id]
            ];
            $values = [
                'permission' => "3"
            ];
            $result = $this->where($whereConditions)->update($values);
            return $result;
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to update reason: " . $e->getMessage());
        }
    }

    public function top_themes_with_posts() {
        try {
            $this->reset();
            $this->t = 'unesco';
            $result = $this->select(['a.theme', 'COUNT(p.id) as post_count'])
                ->left_join('posts p', 'a.id = p.theme AND (p.eliminated = 0 OR p.eliminated IS NULL)', 'p') 
                ->group_by('a.theme')
                ->order_by([['post_count', 'DESC']])
                ->limit(5)
                ->get();
            return $result;
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get top themes with posts: " . $e->getMessage());
        }
    }
    public function getTotalUsers() {
        try {   
          $this->reset();
          $this->t = 'users';
          $result = $this->select(["COUNT(*) as total_users"])
                         ->get();
    
          return $result[0]['total_users'];
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get top themes with posts: " . $e->getMessage());
        }
      }
      public function getTotalComments() {
        try {
            $this->reset();
            $this->t = 'comments';
            $result = $this->select(["COUNT(*) as total_comments"])
                           ->get();
            
            return $result[0]['total_comments'];
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get total comments: " . $e->getMessage());
        }
    }

    public function getTotalReactions() {
        try {
            $this->reset();
            $this->t = 'post_reactions';
            $result = $this->select(["COUNT(*) as total_reactions"])
                           ->get();
            
            return $result[0]['total_reactions'];
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get total reactions: " . $e->getMessage());
        }
    }
    

    public function getActiveUsers() {
        try {
            $this->reset();
            $this->t = 'users';
            $result = $this->select(["COUNT(*) as active_users"])
                           ->where([['last_activity', '>=', date('Y-m-d H:i:s', strtotime('-30 minutes'))]])
                           ->get();
            
            return $result[0]['active_users'];
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get active users: " . $e->getMessage());
        }
    }

public function countThemes() {
    try {
        $this->reset();
        $this->t = 'unesco';
        $result = $this->select(["COUNT(*) as total"])
                       ->get();
        
        return $result[0]['total'];
    } catch (PDOException | Exception $e) {
        throw new Exception("Failed to count themes: " . $e->getMessage());
    }
}

public function getPendingPosts() {
    try {
        $this->reset();
        $this->t = 'posts';
        $result = $this->select([
                            "a.id", 
                            "a.created_at", 
                            "u.username", 
                            "a.title"
                        ])
                        ->join('users u', 'a.user_id = u.id')
                        ->where([
                            ['a.permission', '=', 1],
                            ['a.eliminated', '=', 0]
                        ])
                        ->order_by([['a.created_at', 'DESC']])
                        ->get();

        return $result;
    } catch (PDOException | Exception $e) {
        throw new Exception("Failed to count themes: " . $e->getMessage());
    }
}

}
?>

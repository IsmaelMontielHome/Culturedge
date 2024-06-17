<?php 
require_once "base.php";

/*
 * This class inherits from the base class and contains the calls to the posts procedures
 * 
 * The model do not have to be called in other file than the posts_controller
 * Info: The model file name must be in singular and be in snake case, the class name must be
 *       in camel case with the first letter in uppercase and inherits the base class
 */
class Post extends Base {

    /**
     * The constructor is used to connect to the database
     * 
     * @param void
     * @throws Exception if it fails to connect to the database
     * @return void
     */
    public function __construct() {
        try {
            $this->conn = $this->db_connection();
            $this->check_connection();
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to connect to the database: " . $e->getMessage());
        }
    }

    /**
     * Get all the posts in the database and each image by post
     * 
     * @param void
     * @throws PDOException if it fails to execute the query
     * @throws Exception if it fails to get all posts
     * @return array
     */
    public function all() {
        try {
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.permission',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'img.image as avatar',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->left_join('user_data ud', 'b.id = ud.user_id')
            ->left_join('images img', 'ud.pfp = img.id')
            ->group_by('a.id, b.username, b.email, c.theme', 'img.image')
            ->where([
                ['a.permission', '=', "3"]
            ])
            ->order_by([
                ['a.created_at', 'DESC']
            ])->get();

            foreach($result as &$post) {
                $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                $stmt->bindParam(":id", $post["id"], PDO::PARAM_INT);
                $stmt->execute();
                $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->conn->prepare("CALL GetUserReactions(:userId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userReactions as $userReaction) {
                foreach ($result as &$post) {
                    if ($post['id'] == $userReaction['post_id']) {
                        $post['user_reactions'] = $userReaction['reactType'];
                        break;
                    }
                }
            }

            return $this->response(status: true, data: $result, message: "Posts retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all posts: " . $e->getMessage());
        }
    }

    

    /**
     * Get a post by id and each image
     * 
     * @param int $id
     * @throws PDOException if it fails to execute the query
     * @throws Exception if it fails to get the post
     * @return array
     */
    public function find_by_id($id) {
        try {
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'img.image as avatar',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->left_join('user_data ud', 'b.id = ud.user_id')
            ->left_join('images img', 'ud.pfp = img.id')
            
            ->where([
                ['a.id', '=', $id]
            ])->group_by('a.id, b.username, b.email, c.theme, img.image')
            ->first();
    
            $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Retrieve user reactions for this post
            $stmt = $this->conn->prepare("CALL getReactionForUser(:userId, :postId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->bindParam(":postId", $id, PDO::PARAM_INT);
            $stmt->execute();
            $userReaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($userReaction) {
                $result["user_reactions"] = $userReaction["reactType"];
            }
            
            return $this->response(status: true, data: $result);
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get the post: " . $e->getMessage());
        }
    }

    public function find_by_author_id($id) {
        try {
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.permission',
                'a.reason',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->where([
                ['a.user_id', '=', $id]
            ])->group_by('a.id, b.username, b.email, c.theme')
            ->order_by([
                ['a.created_at', 'DESC']
            ])->get();

            foreach($result as &$post) {
                $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                $stmt->bindParam(":id", $post["id"], PDO::PARAM_INT);
                $stmt->execute();
                $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->conn->prepare("CALL GetUserReactions(:userId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userReactions as $userReaction) {
                foreach ($result as &$post) {
                    if ($post['id'] == $userReaction['post_id']) {
                        $post['user_reactions'] = $userReaction['reactType'];
                        break;
                    }
                }
            }
            
            $r = $this->response(status: true, data: $result, message: "Posts retrieved successfully.");

            return json_encode($r);
        } catch (PDOException | Exception $e) {
            $r = $this->response(status: false, message: $e->getMessage());

            throw new Exception(json_encode($r));
        }
    }

    /**
     * Save a post and each image
     *  1. upload the images to the server and get the names
     *  2. initialize a transaction to save the post and each image to the database
     *  3. save the post and get the id
     *  4. save each image with the post id
     *  5. if not fails commit the transaction
     *  6. if fails rollback the transaction and get rid of each image from the server
     * 
     * @param array $data
     * @throws PDOException if it fails to execute the query
     * @throws Exception if it fails to save the post, it fails to save the image or it fails to upload the image
     * @return array
     */
    public function save($data) {
        try {

            $uploadedImages = [];
            foreach($data['images'] as $image) {
                $response = $this->upload_image($image);
                $uploadedImages[] = $response['data'];
            }

            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("CALL save_post(:user_id, :title, :description, :theme_id, @inserted_id)");
            $stmt->bindparam(":user_id", $data["user_id"], PDO::PARAM_INT);   
            $stmt->bindParam(":title", $data["title"], PDO::PARAM_STR);
            $stmt->bindParam(":description", $data["description"], PDO::PARAM_STR);
            $stmt->bindparam(":theme_id", $data["unesco_theme_id"], PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->conn->prepare("SELECT @inserted_id");
            $stmt->execute();
            $post_id = $stmt->fetch(PDO::FETCH_ASSOC)['@inserted_id'];

            foreach($uploadedImages as $image) {
                $this->save_image('post', $post_id, $image);
            }

            $this->conn->commit();

            return $this->response(status: true, message: "Post saved successfully.");
        } catch (PDOException | Exception $e) {
            $this->conn->rollBack();

            foreach($uploadedImages as $image) {
                $this->rid_image($image);
            }

            throw new Exception("Failed to save the post: " . $e->getMessage());
        }
    }

    /**
     * Update a post or add new images to the post
     *  1. upload the images to the server and get the names
     *  2. initialize a transaction
     *  3. update the post
     *  4. save each image with the post id
     *  5. if not fails commit the transaction
     *  6. if fails rollback the transaction and get rid of each image from the server
     * 
     * @param array $data
     * @throws PDOException if it fails to execute the query
     * @throws Exception if it fails to update the post, it fails to upload the image or it fails to save the image
     * @return array
     */
    public function update_post($data) {
        try {
            $uploadedImages = [];
            foreach($data['images'] as $image) {
                $response = $this->upload_image($image);
                $uploadedImages[] = $response['data'];
            }

            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("CALL update_post(:id, :title, :description, :theme_id)");
            $stmt->bindParam(":id", $data["id"], PDO::PARAM_INT);
            $stmt->bindParam(":title", $data["title"], PDO::PARAM_STR);
            $stmt->bindParam(":description", $data["description"], PDO::PARAM_STR);
            $stmt->bindparam(":theme_id", $data["unesco_theme_id"], PDO::PARAM_INT);
            $stmt->execute();

            foreach($uploadedImages as $image) {
                $this->save_image('post', $data["id"], $image);
            }

            $this->conn->commit();

            return $this->response(status: true, message: "Post updated successfully.");
        } catch (PDOException | Exception $e) {
            $this->conn->rollBack();

            foreach($uploadedImages as $image) {
                $this->rid_image($image);
            }

            throw new Exception("Failed to update the post: " . $e->getMessage());
        }
    }

    /**
     * Delete a post and each image
     *  1. initialize a transaction
     *  2. get the images by post id
     *  3. delete the post
     *  4. get rid of each image from the server
     *  5. if not fails commit the transaction
     *  6. if fails rollback the transaction
     * 
     * @param int $id
     * @throws PDOException if it fails to execute the query
     * @throws Exception if it fails to delete the post or it fails to get rid of each image
     * @return array
     */
    public function destroy($id) {
        try {

            $this->conn->beginTransaction();
            
            $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->conn->prepare("CALL delete_post(:id)");
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            foreach($images as $image) {
                $this->rid_image($image["image"]);
            }

            $this->conn->commit();

            return $this->response(status: true, message: "Post deleted successfully.");
        } catch (PDOException | Exception $e) {
            $this->conn->rollBack();

            throw new Exception("Failed to delete the post: " . $e->getMessage());
        }
    }

    public function comment_father($data){
        try{
            $stmt = $this->conn->prepare("CALL create_comment(:user_id, :post_id, :comment)");
            $stmt->bindparam(":user_id", $data["user_id"], PDO::PARAM_INT);      
            $stmt->bindparam(":post_id", $data["post_id"], PDO::PARAM_INT);  
            $stmt->bindparam(":comment", $data["comment"], PDO::PARAM_STR);  
            $stmt->execute();
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to save the comment " . $e->getMessage());
        }
    }

    public function comment_son($data) {
        try {
            $stmt = $this->conn->prepare("CALL create_son_comment(:parentCommentId, :comment, :postId, :userId)");
            $stmt->bindParam(":parentCommentId", $data["parentCommentId"], PDO::PARAM_INT);
            $stmt->bindParam(":comment", $data["comment"], PDO::PARAM_STR);
            $stmt->bindParam(":postId", $data["postId"], PDO::PARAM_INT);
            $stmt->bindParam(":userId", $data["userId"], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (PDOException | Exception $e) {
            throw new Exception("Error to comment " . $e->getMessage());
        }
    }

    public function get_all_comments($data) {
        try {
            $stmt = $this->conn->prepare("CALL get_comments_by_post_id(:postId)");
            $stmt->bindParam(":postId", $data["postId"], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException | Exception $e) {
            throw new Exception("Error to get comments: " . $e->getMessage());
        }
    }

    public function edit_comment($data){
        try{
            $stmt= $this->conn->prepare("CALL update_comment_by_id(:id, :comment)");
            $stmt->bindParam(":id",$data["id"], PDO::PARAM_INT);
            $stmt->bindParam(":comment",$data["comment"], PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException | Exception $e){
            throw new Exception("Error to edit comment: " . $e->getMessage());
        }
    }


    public function delete_comment($data){
        try{
            $stmt= $this->conn->prepare("CALL delete_comment_by_id(:id)");
            $stmt->bindParam(":id",$data["id"], PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException | Exception $e){
            throw new Exception("Error to delete comments " . $e->getMessage());
        }
    }

    public function reactions_insert($data) {
        try {
            $stmt = $this->conn->prepare("CALL InsertUserReaction(:userId, :postId, :reactType, @total_reactions)");
            $stmt->bindParam(":userId", $data["userId"], PDO::PARAM_INT);
            $stmt->bindParam(":postId", $data["postId"], PDO::PARAM_INT);
            $stmt->bindParam(":reactType", $data["reactType"], PDO::PARAM_STR);
            $stmt->execute();
            $stmt = $this->conn->query("SELECT @total_reactions");
            $total_reactions = $stmt->fetch(PDO::FETCH_ASSOC)['@total_reactions'];
            return ['total_reactions' => $total_reactions];
        } catch (PDOException | Exception $e) {
            throw new Exception("Error to get reactions: " . $e->getMessage());
        }
    }

    public function reactions_delete($data) {
        try {
            $stmt = $this->conn->prepare("CALL DeleteReaction(:userId, :postId, @total_reactions)");
            $stmt->bindParam(":userId", $data["userId"], PDO::PARAM_INT);
            $stmt->bindParam(":postId", $data["postId"], PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $this->conn->query("SELECT @total_reactions");
            $total_reactions = $stmt->fetch(PDO::FETCH_ASSOC)['@total_reactions'];
            if ($total_reactions == -1) {
                throw new Exception("Error, the count of reactions no updated correctly.");
            }
            return ['total_reactions' => $total_reactions];
        } catch (PDOException | Exception $e) {
            throw new Exception("Error to delete reaction: " . $e->getMessage());
        }
    }


    public function searchPosts($query) {
        try {
            $this->t = 'posts';
            $this->pp = ['id', 'user_id', 'title', 'description', 'created_at', 'username', 'theme', 'theme_icon'];
    
            $result = $this
                ->select(['a.id', 'a.user_id', 'a.title', 'a.description', 'a.created_at', 'users.username', 'unesco.theme AS theme', 'unesco.icon AS theme_icon'])
                ->join('users', 'a.user_id = users.id')
                ->join('unesco', 'a.theme = unesco.id')
                ->where_complex(orConditions: [
                    ['unesco.theme', 'LIKE', '%' . $query . '%'],
                    ['a.title', 'LIKE', '%' . $query . '%'],
                    ['a.description', 'LIKE', '%' . $query . '%'],
                    ['users.username', 'LIKE', '%' . $query . '%']
                ])->get();
            return $result;
        } catch (PDOException | Exception $e) {
            throw new Exception("Error searching posts: " . $e->getMessage());
        }
    }

    public function searchUsers($query) {
        try {
            $this->t = 'users';
            $this->pp = ['id', 'username'];
    
            $result = $this
                ->select(['id', 'username'])
                ->where([['username', 'LIKE', '%' . $query . '%']])
                ->get();
            return $result;
        } catch (PDOException | Exception $e) {
            throw new Exception("Error searching users: " . $e->getMessage());
        }
    }
    
    public function all_posts_users() {
        try {

            $userId = $_SESSION['user']['id'];
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.reason',
                'a.permission',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->group_by('a.id, b.username, b.email, c.theme')
            ->where([
                ['a.user_id', '=', $userId]
            ])
            ->order_by([
                ['a.created_at', 'DESC']
            ])->get();

            foreach($result as &$post) {
                $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                $stmt->bindParam(":id", $post["id"], PDO::PARAM_INT);
                $stmt->execute();
                $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->conn->prepare("CALL GetUserReactions(:userId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userReactions as $userReaction) {
                foreach ($result as &$post) {
                    if ($post['id'] == $userReaction['post_id']) {
                        $post['user_reactions'] = $userReaction['reactType'];
                        break;
                    }
                }
            }

            return $this->response(status: true, data: $result, message: "Posts retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all posts: " . $e->getMessage());
        }
    }
    
    public function all_posts() {
        try {

            $userId = $_SESSION['user']['id'];
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.reason',
                'a.permission',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->group_by('a.id, b.username, b.email, c.theme')
            ->order_by([
                ['a.created_at', 'DESC']
            ])->get();

            foreach($result as &$post) {
                $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                $stmt->bindParam(":id", $post["id"], PDO::PARAM_INT);
                $stmt->execute();
                $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->conn->prepare("CALL GetUserReactions(:userId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userReactions as $userReaction) {
                foreach ($result as &$post) {
                    if ($post['id'] == $userReaction['post_id']) {
                        $post['user_reactions'] = $userReaction['reactType'];
                        break;
                    }
                }
            }

            return $this->response(status: true, data: $result, message: "Posts retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all posts: " . $e->getMessage());
        }
    }
    
    
    
    public function posts_media_by_author_id($id) {
        try {
            $this->t = 'posts';
            
            $result = $this->select([
                'id as post_id',
                'title as post_title',
            ])->where([
                ['user_id', '=', $id]
            ])->order_by([
                ['created_at', 'DESC']
            ])->get();

            if (count($result) > 0) {
                foreach ($result as &$post) {
                    $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                    $stmt->bindParam(":id", $post["post_id"], PDO::PARAM_INT);
                    $stmt->execute();
                    $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            
            $r = $this->response(status: true, data: $result, message: "Media retrieved successfully.");

            return json_encode($r);
        } catch (PDOException | Exception $e) {
            $r = $this->response(status: false, message: $e->getMessage());

            throw new Exception(json_encode($r));
        }
    }

    public function posts_comments_by_author_id($id) {
        try {
            $this->t = 'comments';

            $result = $this->select([
                'id',
                'post_id',
                'comment',
                'DATE_FORMAT(created_at, "%e %M %Y") as created_at',
            ])->where([
                ['user_id', '=', $id]
            ])->order_by([
                ['created_at', 'DESC']
            ])->get();
            
            $r = $this->response(status: true, data: $result, message: "Comments retrieved successfully.");

            return json_encode($r);
        } catch (PDOException | Exception $e) {
            $r = $this->response(status: false, message: $e->getMessage());

            throw new Exception(json_encode($r));
        }
    }

    public function get_populars_limit($limit = 3) {
        try {
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.permission',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->group_by('a.id, b.username, b.email, c.theme')
            ->where([
                ['a.permission', '=', 2]
            ])
            ->order_by([
                ['total_reactions', 'DESC']
            ])->limit($limit)
            ->get();

            $r = $this->response(status: true, data: $result, message: "Popular posts retrieved successfully.");

            return json_encode($r);
        } catch (PDOException | Exception $e) {
            $r = $this->response(status: false, message: $e->getMessage());

            throw new Exception(json_encode($r));
        }
    }

    public function get_populars() {
        try {
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.permission',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->group_by('a.id, b.username, b.email, c.theme')
            ->where([
                ['a.permission', '=', 2]
            ])
            ->order_by([
                ['total_reactions', 'DESC']
            ])->get();

            foreach($result as &$post) {
                $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                $stmt->bindParam(":id", $post["id"], PDO::PARAM_INT);
                $stmt->execute();
                $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->conn->prepare("CALL GetUserReactions(:userId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userReactions as $userReaction) {
                foreach ($result as &$post) {
                    if ($post['id'] == $userReaction['post_id']) {
                        $post['user_reactions'] = $userReaction['reactType'];
                        break;
                    }
                }
            }

            return $this->response(status: true, data: $result, message: "Posts retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all posts: " . $e->getMessage());
        }
    }

    public function get_by_topic($topic) {
        try {
            $this->t = 'posts';

            $result = $this->select([
                'a.id',
                'a.user_id',
                'a.title',
                'a.description',
                'a.permission',
                'DATE_FORMAT(a.created_at, "%e %M %Y") as created_at',
                'b.username',
                'b.email',
                'c.icon as theme_icon',
                'c.theme',
                'COUNT(DISTINCT d.id) as total_reactions',
                'COUNT(DISTINCT e.id) as total_comments'
            ])->join('users b', 'a.user_id = b.id')
            ->join('unesco c', 'a.theme = c.id')
            ->left_join('post_reactions d', 'a.id = d.post_id')
            ->left_join('comments e', 'a.id = e.post_id')
            ->group_by('a.id, b.username, b.email, c.theme')
            ->where_complex(
                [
                    ['a.permission', '=', "3"]
                ],
                'AND',
                [
                    ['c.theme', 'LIKE', $topic],
                    ['c.id', 'LIKE', $topic]
                ],
            )->order_by([
                ['a.created_at', 'DESC']
            ])->get();            

            foreach($result as &$post) {
                $stmt = $this->conn->prepare("CALL get_images_by_post_id(:id)");
                $stmt->bindParam(":id", $post["id"], PDO::PARAM_INT);
                $stmt->execute();
                $post["images"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $stmt = $this->conn->prepare("CALL GetUserReactions(:userId)");
            $userId = $_SESSION['user']['id'];
            $stmt->bindParam(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();
            $userReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($userReactions as $userReaction) {
                foreach ($result as &$post) {
                    if ($post['id'] == $userReaction['post_id']) {
                        $post['user_reactions'] = $userReaction['reactType'];
                        break;
                    }
                }
            }

            return $this->response(status: true, data: $result, message: "Posts retrieved successfully.");
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to get all posts: " . $e->getMessage());
        }
    }
}
?>

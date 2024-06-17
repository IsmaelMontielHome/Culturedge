<?php
ob_start();
get_model('post');

class PostsController extends Post {
    private $params;
    private $files;

    public function __construct($params) {
        try {
            parent::__construct();
            $this->params = $params["method"];
            $this->files = $params["files"];
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    public function index() {
        
        try {
            $response = $this->all();

            if ($response["status"]) {
                return $this->render('index', $response["data"]);
            } else {
                throw new Exception("Failed to get all posts: " . $response["message"]);
            }
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    public function show() {
        try {
            $response = $this->find_by_id($this->params['id']);

            if ($response["status"]) {
                return $this->render('show', $response["data"]);
            } else {
                throw new Exception("Failed to get the post with id " . $this->params['id'] . ": " . $response["message"]);
            }
        } catch (Exception $e) {
            return $this->error('404');
        }
    }

    public function new() {
        if (!isset($_SESSION['user'])) {
            header('Location: /sessions/new');
        }

        return $this->render('new', $this->params);
    }

    public function create() {
        try {
            $images = $this->check_images($this->files['images']);
            
            $this->params = array_merge($this->params, [ "images" => $images["data"] ]);
            $response = $this->save($this->params);

            if ($response["status"]) {
                header('Location: /posts/my_posts');
            } else {
                throw new Exception("Failed to create the post: " . $response["message"]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            header('Location: /posts/new');
        }
    }

    public function edit() {
        try {
            $response = $this->find_by_id($this->params['id']);

            if ($response["status"]) {
                return $this->render('edit', $response["data"]);
            } else {
                throw new Exception("Failed to get the post with id " . $this->params['id'] . ": " . $response["message"]);
            }
        } catch (Exception $e) {
            return $this->error('404');
        }
    }

    public function patch() {
        try {
            $images = $this->check_images($this->files['images']);

            $this->params = array_merge($this->params, [ "images" => $images["data"] ]);
            $response = $this->update_post($this->params);
            

            if ($response["status"]) {
                header('Location: /posts/my_posts');
            } else {
                throw new Exception("Failed to update the post with id " . $this->params['id'] . ": " . $response["message"]);
            }
        } catch (Exception $e) {
            header('Location: /posts/edit/id:' . $this->params['id']);
        }
    }

    public function purge_image() {
        try {
            $response = $this->delete_image($this->params['id']);
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

    public function create_comment_father() {
        try{
            $this->comment_father($this->params);
            header('Location: /posts/show/id:' . $this->params['post_id']);
        } catch(Exception $e) {
            return $this->error('404');
        }
    }

    public function show_comments() {
        try {
            $response = $this->get_all_comments($this->params);
            if (!empty($response)) {
                $jsonstring = json_encode($response);
                echo $jsonstring;
                exit;
            } else {
                http_response_code(404);
                $message = 'No comments found';
            }
        } catch (Exception $e) {
            http_response_code(500);
            $message = 'Internal server error';
        }
    
        if (isset($message) && $message === 'No comments found') {
            $message = 'No comments available';
        }
    
        echo json_encode(['error' => $message]);
        exit;
    }
    

    /**
     * Delete a post and redirect to the index view
     * 
     * @param void
     * @throws Exception if it fails to delete the post redirect to error 404
     * @return void
     */
    public function drop() {
        try {
            
            $response = $this->destroy($this->params['id']);
            
            if ($response["status"]) {
                header('Location: /posts/my_posts');
                
            } else {
                throw new Exception("Failed to delete the post with id " . $this->params['id'] . ": " . $response["message"]);
            }
        } catch (Exception $e) {
            return $this->error('404');
        }
    }

    public function delete_comments() {
        try {
            $this->delete_comment($this->params);
            exit;          
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
            exit;
        }
    }

    public function edit_comments() {
        try {
            $response = $this->edit_comment($this->params);
            if (!empty($response)) {
                $jsonstring = json_encode($response);
                echo $jsonstring;
                exit;
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'No comments found']);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
            exit;
        }
    }

    public function comments_son() {
        try {
            $response = $this->comment_son($this->params);
            if (!empty($response)) {
                $jsonstring = json_encode($response);
                echo $jsonstring;
                exit;
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'No comments found']);
                exit;
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
            exit;
        }
    }

    public function insert_reactions(){
        try {
            $response = $this->reactions_insert($this->params);
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            return $this->error('500');
        }
    }
    

    public function delete_reactions(){
        try{
            $response = $this->reactions_delete($this->params);
            header('Content-Type: application/json');
            echo json_encode($response);
        }catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal server error']);
            exit;
        }
    }

    public function search() {
        try {
            $query = $this->params['query'] ?? '';
            if (strpos($query, '@') === 0) {
                $users = $this->searchUsers(substr($query, 1)); 
                echo json_encode($users);
            } else {
                $posts = $this->searchPosts($query);
                echo json_encode($posts);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function show_json() {
        try {
            $response = $this->find_by_id($this->params['id']);

            if ($response["status"]) {
                echo json_encode($response["data"]);
            } else {
                throw new Exception("Failed to get the post with id " . $this->params['id'] . ": " . $response["message"]);
            }
        } catch (Exception $e) {
            echo json_encode(["message" => "Post not found"]);
        }
    }
    
    public function my_posts() {
        try {
          $post = new Post;
          $response = $post->all_posts_users();

          if ($response["status"]) {
              return $this->render('my_posts', ['data' => $response["data"]]);
          } else {
              throw new Exception("Failed to get all posts: " . $response["message"]);
          }
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    public function populars_limit() {
        try {
            $response = $this->get_populars_limit($this->params['limit']);
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

    public function populars() {
        try {
            $response = $this->get_populars();

            if ($response["status"]) {
                return $this->render('popular', $response["data"]);
            } else {
                throw new Exception("Failed to get all posts: " . $response["message"]);
            }
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    public function unesco() {
        try {
            $result = $this->check_topic($this->params['topic']);
            $response = $this->get_by_topic($result);

            if ($response["status"]) {
                return $this->render('index', $response["data"]);
            } else {
                throw new Exception("Failed to get all posts: " . $response["message"]);
            }
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    private function check_topic($topic) {
        $response = null;

        if (is_numeric($topic)) {
            $topicInt = (int) $topic;
            $response = $topicInt;
        }

        if (is_string($topic)) {
            $topicTheme = str_replace(' ', '_', $topic);
            $response = $topicTheme;
        }

        return $response;
    }
      
    protected function render($view, $data = []) {
        $params = $data;
        include ROOT_DIR . 'views/posts/' . $view . '.php';
        return ob_get_clean();
    }
}
?>

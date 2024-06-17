<?php
ob_start();
get_model('extra');

class ExtrasController extends extra {
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

    public function aboutUs() {
        try {
            return $this->render('aboutus');
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    public function condiction() {
        try {
            return $this->render('condiction');
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    public function questions() {
        try {
            return $this->render('questions');
        } catch (Exception $e) {
            return $this->error('500');
        }
    }

    protected function render($view, $data = []) {
        $params = $data;
        include ROOT_DIR . 'views/extra/' . $view . '.php';
        return ob_get_clean();
    }
}
?>

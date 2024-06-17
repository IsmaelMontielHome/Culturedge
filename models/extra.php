<?php
require_once "base.php";

/*
 * This class inherits from the base class and contains the calls to the extras procedures
 * 
 * The model do not have to be called in other file than the extras_controller
 * Info: The model file name must be in singular and be in snake case, the class name must be
 *       in camel case with the first letter in uppercase and inherits the base class
 */
class Extra extends Base {
    public function __construct() {
        try {
        } catch (PDOException | Exception $e) {
            throw new Exception("Failed to connect to the database: " . $e->getMessage());
        }
    }

    public function aboutUsData() {
        return [
            'title' => 'About Us',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit...'
        ];
    }

    public function condictionData() {
        return [
            'title' => 'condiction',
            'content' => 'Need help? We are here to assist you...'
        ];
    }

    public function questionsData() {
        return [
            'title' => 'Frequently Asked Questions',
            'questions' => [
                ['question' => 'How do I reset my password?', 'answer' => 'You can reset your password by visiting the "Forgot Password" page...'],
                ['question' => 'How do I contact customer support?', 'answer' => 'You can contact customer support by emailing support@example.com...']
            ]
        ];
    }
}
?>

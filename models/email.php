<?php

/**
 * This class is used to send emails, for confirmation and reset password
 * 
 * By Ismael March 14th, 2024 2:09 AM CST
 * Modified by Alejandro March 16th, 2024 01:38 AM UTC-6
 */
class Email {
  public static function confirm_email($email, $code) {
    $self = new self();

    $to = $email;
    $subject = "Confirmation instructions";
    $logo_data = $self->read_logo();

    $htmlMessage = $self->message_confirmation($code, $logo_data["name"]);
    $message = $self->construct_message($htmlMessage, $logo_data);
    $headers = $self->construct_headers();

    try {
      mail($to, $subject, $message, $headers);
      
      return true;
    } catch (Exception $e) {
      throw new Exception("Failed to send the email: " . $e->getMessage());
    }
  }

  public static function reset_password_email($email, $url) {
    $self = new self();

    $to = $email;
    $subject = "Change Password";
    $logo_data = $self->read_logo();

    $htmlMessage = $self->message_reset_password($url, $logo_data["name"]);
    $message = $self->construct_message($htmlMessage, $logo_data);
    $headers = $self->construct_headers();

    try {
      mail($to, $subject, $message, $headers);
      return true;
    } catch (Exception $e) {
      throw new Exception("Failed to send the email: " . $e->getMessage());
    }
  }

  private function read_logo() {
    $logoPath = RESOURCES . "img/fav.png";
    $fileContent = file_get_contents($logoPath);
    $fileContentEncoded = chunk_split(base64_encode($fileContent));
    $fileMimeType = mime_content_type($logoPath);
    $fileName = basename($logoPath);

    return [ "encoded" => $fileContentEncoded, "mime" => $fileMimeType, "name" => $fileName ];
  }

  private function message_confirmation($code, $logo_data) {
    $html = "
      <html>
      <head>
          <style>
              body {font-family: Arial, sans-serif;}
              .logo {width: 200px;}
              .code {color: red; font-weight: bold;}
          </style>
      </head>
      <body>
          <p>¡Thank you for registering! your code is:</p>
          <center><h1 class='code'>$code<h1></center>
          <center><img src='cid:$logo_data' class='logo' alt='Logo'></center>
      </body>
      </html>
    ";

    return $html;
  }

  private function message_reset_password($url, $logo_data) {
    $html = "
      <html>
      <head>
          <style>
              body {font-family: Arial, sans-serif;}
              .logo {width: 200px;}
              .url {color: blue; font-weight: bold;}
          </style>
      </head>
      <body>
          <p>¡Enter the following link to reset your password!</p>
          <p>Link: <a href='$url' class='url'>$url</a></p>
          <center><img src='cid:$logo_data' class='logo' alt='Logo'></center>
      </body>
      </html>
    ";

    return $html;
  }

  private function construct_message($htmlMessage, $logo_data) {
    $boundary = md5(time());

    $message = "--$boundary\r\n";
    $message .= "Content-Type: text/html; charset=UTF-8\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $message .= chunk_split(base64_encode($htmlMessage));
    $message .= "--$boundary\r\n";
    $message .= "Content-Type: " . $logo_data["mime"] . "; name=\"" . $logo_data["name"] . "\"\r\n";
    $message .= "Content-Transfer-Encoding: base64\r\n";
    $message .= "Content-ID: <" . $logo_data["name"] . ">\r\n";
    $message .= "Content-Disposition: inline; filename=\"" . $logo_data["name"] . "\"\r\n\r\n";
    $message .= $logo_data["encoded"] . "\r\n\r\n";
    $message .= "--$boundary--";

    return $message;
  }

  private function construct_headers() {
    $boundary = md5(time());
    
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From:<tu@email.com>\r\n"; // Ajusta el remitente según tus necesidades
    $headers .= "Content-Type: multipart/related; boundary=\"$boundary\"\r\n";

    return $headers;
  }
}

?>

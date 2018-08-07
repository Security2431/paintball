<?php

  if (is_file('./lib/Exception.php')) {
    require_once("./lib/Exception.php");
  }
  
  if (is_file('./lib/PHPMailer.php')) {
    require_once("./lib/PHPMailer.php");
  }
  
  
  if (is_file('./lib/SMTP.php')) {
    require_once("./lib/SMTP.php");
  }
  
  if (is_file('./lib/newsletter.php')) {
    require_once("./lib/newsletter.php");
  }

  $http_host = $_SERVER["HTTP_HOST"];
  $body = "";
  $data = array();

  if ( substr($http_host, 0, 4)=="www.") {
    $host_name = substr($http_host, 4);
  } else {
    $host_name = $http_host;
  }
  if (isset($_SERVER["HTTP_REFERER"])) {
    $http_referer = $_SERVER["HTTP_REFERER"];
  } else {
    $http_referer = "";
  }
  define ("HTTP_SERVER", "http://" . $http_host . "/");
  define ("HOST_NAME", $host_name);
  define ("HTTP_REFERER", $http_referer);
  $post = array( 
    "host_name"     => HOST_NAME,
    "host_dir"      => HTTP_SERVER,
    "host_referer"  => HTTP_REFERER
  );

  $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  if (!$_POST) {
    echo "Форма пустая!";
    exit;
  }
  
  //header("Content-Type: text/html; charset=utf-8");
  //echo "<pre>";
  //var_dump($_POST);
  //echo "</pre>";
  //exit;

  if ( (!empty($_POST["form"])) && (isset($_POST["form"])) ) {
    $post["user_form"] = $_POST["form"];

    $stack = array(
      "key"   => "Форма: ",
      "value" => $post["user_form"]
    );
    array_push($data, $stack);
  }

  if ( (!empty($_POST["email"])) && (isset($_POST["email"])) ) {
    $post["user_email"] = $_POST["email"];
    $stack = array(
      "key"   => "Email: ",
      "value" => $post["user_email"]
    );
    array_push($data, $stack);
  }

  if ( (!empty($_POST["phone"])) && (isset($_POST["phone"])) ) {
    $post["user_phone"] = $_POST["phone"];
    $stack = array(
      "key"   => "Телефон: ",
      "value" => $post["user_phone"]
    );
    array_push($data, $stack);
  }

  if ( (!empty($_POST["name"])) && (isset($_POST["name"])) ) {
    $post["user_name"] = $_POST["name"];
    $stack = array(
      "key"   => "Имя: ",
      "value" => $post["user_name"]
    );
    array_push($data, $stack);
  }

  if ( (!empty($_POST["message"])) && (isset($_POST["message"])) ) {
    $post["user_message"] = $_POST["message"];
    $stack = array(
      "key"   => "Сообщение: ",
      "value" => $post["user_message"]
    );
    array_push($data, $stack);
  }

  $stack = array(
    "key"   => "Форма отправлена с сайта: ",
    "value" => $post["host_referer"]
  );
  array_push($data, $stack);

  foreach ($data as $key => $value) {
    $body .= $value['key'] . $value['value'] . chr(10) . chr(13);
  }


  $mail = new PHPMailer\PHPMailer\PHPMailer();              // Passing `true` enables exceptions
  try {
        //Server settings
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'paintballphoenix.odessa@gmail.com';                 // SMTP username
    $mail->Password = 'ghjtrn9ghjtrn9';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                    // TCP port to connect to

    $mail->SetFrom('paintballphoenix.odessa@gmail.com', 'Paintball Phoenix');
    $mail->AddAddress($post["user_email"]);

    //Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject      = 'Admin@' . HOST_NAME;
    $NewsLetterClass    = new NewsLetterClass();
    $mail->Body         = $NewsLetterClass->generateHTMLLetter($data);
    $mail->AltBody      = $body;

    if(!$mail->send()) {
      echo "Что-то пошло не так. " . $mail->ErrorInfo;
      return false;
    } else {
      echo json_encode(array("success" => true));
      return true;
    }
  } catch (Exception $e) {
      echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
  }
?>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once 'vendor/autoload.php';

$errors = [];
$errorMessage = '';
$username = '';
$password = '';

function sendWithGmail($username, $password, $to, $from, $name, $msgBody) {
	$mail = new PHPMailer(true);
	$mail->isSMTP();
	$mail->SMTPAuth = true;
	//to view proper logging details for success and error messages
	// $mail->SMTPDebug = 1;
	$mail->Host = 'smtp.gmail.com';  //gmail SMTP server
	$mail->Username = $username;   //email
	$mail->Password = $password;   //16 character obtained from app password created
	$mail->Port = 465;                    //SMTP port
	$mail->SMTPSecure = "ssl";

	//sender information
	$mail->setFrom($from, $name);

	//receiver address and name
	$mail->addAddress($to, 'RECEPIENT_NAME');


	// Add cc or bcc   
	// $mail->addCC('email@mail.com');  
	// $mail->addBCC('user@mail.com');  

	//$mail->addAttachment(__DIR__ . '/download.png');
	// $mail->addAttachment(__DIR__ . '/exampleattachment2.jpg');

	$mail->isHTML(true);

	$mail->Subject = 'ContactRequest: ' . $name;
	$mail->Body    = "<h4> Alexanderelling.com </h4>
	<b>------------------------------------------</b>" . $msgBody;

	// Send mail   
	if (!$mail->send()) {
		//echo 'Email not sent an error was encountered: ' . $mail->ErrorInfo;
		return false;
	} else {
		//echo 'Message has been sent.';
		return true;
	}
	$mail->smtpClose();
}

if (!empty($_POST)) {
   $name = $_POST['name'];
   $email = $_POST['email'];
   $message = $_POST['message'];

   if (empty($name)) {
       $errors[] = 'Name is empty';
   }

   if (empty($email)) {
       $errors[] = 'Email is empty';
   } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
       $errors[] = 'Email is invalid';

   }

   if (empty($message)) {
       $errors[] = 'Message is empty';
   }
   
	
   if (empty($errors)) {
       $toEmail = 'example@example.com';
       $emailSubject = 'New email from your contact form';
       $headers = ['From' => $email, 'Reply-To' => $email, 'Content-type' => 'text/html; charset=utf-8'];
       $bodyParagraphs = ["<p>Name: {$name}</p>", "<p>Email: {$email}</p>", "<p>Message:</p>", $message];
       $body = join(PHP_EOL, $bodyParagraphs);
		if (sendWithGmail($username, $password, 'rickard.joh@gmail.com', 'rickard.joh@gmail.com', $name, $body)) {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array(
				'status' => 200,
				'message' => "Message has been sent."
				));
		} else {
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array(
				'status' => 400,
				'message' => "Unable to send message."
			));
		}
        
   } else {
	$errorMessage = 'Oops, something went wrong. Please try again later';
	}
} else {
	$allErrors = join('<br/>', $errors);
	$errorMessage = "<p style='color: red;'>{$allErrors}</p>";
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode(array(
				'status' => 400,
				'message' => $errorMessage
				));
}

?>

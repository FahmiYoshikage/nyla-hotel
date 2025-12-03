<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader (created by composer, not included with PHPMailer)
require 'vendor/autoload.php';

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'nylapens@gmail.com';                     //SMTP username
    $mail->Password   = 'zetgzaowceqvkups';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('from@example.com', 'Mailer');
    $mail->addAddress('nilaanidia@gmail.com', 'Nyla');     //Add a recipient            //Name is optional
    $mail->addReplyTo('no-reply@example.com', 'Information');

    //Content
    $mail->isHTML(true);
    $email_template = "
    <h2>kamu telah melakukan pendaftaran akun</h2>
    <h4> verifikasi email mu agar dapat login, klik tautan berikut!</h4>
    <a href='#'> [klik disini]</a>
    " ;                               //Set email format to HTML
    $mail->Subject = 'Verifikasi email';
    $mail->Body    = $email_template;

    $mail->send();
    echo 'email terkirim';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
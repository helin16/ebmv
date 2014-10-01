<?php 
require_once 'bootstrap.php';
 echo '<pre>';
 
$mail = new PHPMailer();


$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.websiteforyou.com.au';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'test@websiteforyou.com.au';                 // SMTP username
$mail->Password = 'TEST@websiteforyou.com.au';                           // SMTP password
$mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to

$mail->From = 'noreplay@ebmv.com.au';
$mail->FromName = 'New Order';
$mail->addAddress('helin16@gmail.com', 'Lin');     // Add a recipient
$mail->addCC('helin16@gmail.com');

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}


?>
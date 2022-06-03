<?php

namespace App\Service;
// use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

// require 'PHPMailer-master/src/Exception.php';
// require 'PHPMailer-master/src/PHPMailer.php';
// require 'PHPMailer-master/src/SMTP.php';

class SendMailService
{
    public function send(): void
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";

        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "randriamampionona9@gmail.com";
        $mail->Password   = "#Lantoniaina9";

        $mail->IsHTML(true);
        $mail->AddAddress("lrandriamampionona@meilleurtaux.com", "recipient-name");
        $mail->SetFrom("randriamampionona9@gmail.com", "from-name");
        $mail->AddReplyTo("lrandriamampionona@meilleurtaux.com", "reply-to-name");
        $mail->AddCC("lrandriamampionona@meilleurtaux.com", "cc-recipient-name");
        $mail->Subject = "Test is Test Email sent via Gmail SMTP Server using PHP Mailer";
        $content = "<b>This is a Test Email sent via Gmail SMTP Server using PHP mailer class.</b>";

        $mail->MsgHTML($content);
        if (!$mail->Send()) {
            echo "Error while sending Email.";
            var_dump($mail);
        } else {
            echo "Email sent successfully";
        }
    }
}

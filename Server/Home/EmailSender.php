<?php

  function SendEmail($message,$email,$type){
    include 'PHPMailerAutoload.php'; //
    $name = 'Maam/Sir';
    $mailer = new PHPMailer(); // instantiate PHPMailer
    $mailer->IsSMTP(); // set the type of protocol to smtp
    $mailer->Host = 'smtp.gmail.com:465'; // i think its default well i didn't change
    $mailer->SMTPAuth = TRUE; // gmg
    $mailer->Port = 465; //port given by google for test runs
    $mailer->mailer="smtp";  // gmg
    $mailer->SMTPSecure = 'ssl'; //gmg
    $mailer->IsHTML(true); //gmg (GOOGLE MO GAGO)
    $mailer->SMTPOptions = array('ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true)
                );
    $mailer->Username = 'homeoversightapp@gmail.com'; //email as far as im concern i dont want to give my password or email
    $mailer->Password = 'oversightpass'; // so I stick with this 1
    $mailer->From = 'homeoversightapp@gmail.com'; // displayed name of sender in the description later
    $mailer->FromName = 'OVERSIGHT ' . $type; // name on inbox
    $mailer->Body =  $message ;
    $mailer->Subject = $type; //subject
    $mailer->AddAddress($email); // add email as receiver of the mail
    return($mailer->send());
  }


?>

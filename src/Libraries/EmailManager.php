<?php

namespace App\Libraries;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class EmailManager
{
    public $container, $em;

    public function __construct(Container $container, EntityManager $em)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function sendEmail($path, $filename, $content, $mailto)
    {
        $file = $path . $filename;
        $from_name = 'Alfalaj Water';
        $replyto = 'noreply@mail.com';
        $from_mail = 'admin@alfalajwater.com';
        $message = 'Find Data in the attachment';
        $subject = 'Monthly Summary';
//        $mailto = 'm.faizanaltaf@gmail.com';
        $content = chunk_split(base64_encode($content));
        $uid = md5(uniqid(time()));
        $name = basename($file);

// header
        $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
        $header .= "Reply-To: " . $replyto . "\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";

// message & attachment
        $nmessage = "--" . $uid . "\r\n";
        $nmessage .= "Content-type:text/plain; charset=iso-8859-1\r\n";
        $nmessage .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $nmessage .= $message . "\r\n\r\n";
        $nmessage .= "--" . $uid . "\r\n";
        $nmessage .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
        $nmessage .= "Content-Transfer-Encoding: base64\r\n";
        $nmessage .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
        $nmessage .= $content . "\r\n\r\n";
        $nmessage .= "--" . $uid . "--";

        if (mail($mailto, $subject, $nmessage, $header)) {
            return true; // Or do something here
        } else {
            return false;
        }
    }


}
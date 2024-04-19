<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require "../config/basedados.php";
    require '../config/newsletterConfig.php';

    require '../plugins/PHPMailer/src/Exception.php';
    require '../plugins/PHPMailer/src/PHPMailer.php';
    require '../plugins/PHPMailer/src/SMTP.php';

    function logOutput($output) {
        file_put_contents('logs.txt', $output, FILE_APPEND);
    }

    if(isset($_GET['id'])) {
        $newsletter_id = $_GET['id'];

        // Retrieve newsletter content from the database based on ID
        $sql = "SELECT titulo, conteudo FROM newsletter WHERE id = $newsletter_id";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);

            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {

                //Server settings
                $mail->SMTPDebug = 2; //Enable verbose debug output
                $mail->Debugoutput = function($str, $level) {
                    file_put_contents('sendEmails.log', gmdate('Y-m-d H:i:s'). "\t$level\t$str", FILE_APPEND | LOCK_EX);
                };                      
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = HOST_SMTP;                                   //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = PORT_SMTP;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                //User settings
                $mail->Username   = USERNAME_SMTP;                               //SMTP username
                $mail->Password   = PASSWORD_SMTP;                               //SMTP password
                

                //Recipients
                $mail->setFrom(USERNAME_SMTP, 'Techn & Art');
                $mail->addAddress('dinis685@gmail.com', "TU");       //Add a recipient
                $mail->addReplyTo(USERNAME_SMTP, 'Information');
                //$mail->addAddress('ellen@example.com');               //Name is optional
                //$mail->addCC('cc@example.com');
                //$mail->addBCC('bcc@example.com');

                //Attachments
                //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = $row['titulo'];
                $mail->Body    = $row['conteudo'];
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo 'Newsletter not found';
        }
    } else {
        echo 'Invalid request';
    }
?>

<?php
mysqli_close($conn);
?>
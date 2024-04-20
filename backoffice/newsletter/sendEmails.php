<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require "../config/basedados.php";
    require '../config/newsletterConfig.php';

    require '../plugins/PHPMailer/src/Exception.php';
    require '../plugins/PHPMailer/src/PHPMailer.php';
    require '../plugins/PHPMailer/src/SMTP.php';

    $subcritores = [];

    if(isset($_GET['id'])) {
        $newsletter_id = $_GET['id'];

        // Retrieve newsletter content from the database based on ID
        $sql = "SELECT titulo, conteudo FROM newsletter WHERE id = $newsletter_id";
        $result = mysqli_query($conn, $sql);
        //SELECT nome, email FROM investigadores WHERE email IS NOT NULL AND email != ''
        //UNION
        $sql2 = "SELECT nome, email FROM subscritores WHERE email IS NOT NULL AND email != ''";
        $result2 = mysqli_query($conn, $sql2);

        $sql3 = "UPDATE newsletter SET enviarStatus = 1 WHERE id = $newsletter_id";
        $result3 = mysqli_query($conn, $sql3);

        if(mysqli_num_rows($result2) > 0){
            while($row2 = mysqli_fetch_assoc($result2)){
                $subcritores[] = $row2;
            };
        }

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);

            //Create an instance; passing `true` enables exceptions
            $mail = new PHPMailer(true);

            //Server settings
            $mail->SMTPDebug = 2; //Enable verbose debug output
            $mail->Debugoutput = function($str, $level) {
                file_put_contents('sendEmails.log', gmdate('Y-m-d H:i:s'). "\t$level\t$str", FILE_APPEND | LOCK_EX);
            };                      
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = HOST_SMTP;                              //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = PORT_SMTP;                              //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            //User settings
            $mail->Username   = USERNAME_SMTP;                          //SMTP username
            $mail->Password   = PASSWORD_SMTP;                          //SMTP password
            

            //Sender
            $mail->setFrom(EMAIL_SMTP, 'Techn & Art');
            $mail->addReplyTo(EMAIL_SMTP, 'Information');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');             //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');        //Optional name

            //Content
            $mail->isHTML(true);                                        //Set email format to HTML
            $mail->Subject = $row['titulo'];
            $mail->Body    = $row['conteudo'];
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            foreach($subcritores as $sub) {
                try{
                    //Recipient
                    $mail->addAddress($sub['email'], $sub['nome']);     //Add a recipient
                    //$mail->addAddress('ellen@example.com');           //Name is optional
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    if(!$mail->send()){
                        file_put_contents('sendEmails.log', gmdate('Y-m-d H:i:s'). "\tEmail not sent to  " . $sub['email'] . ".\n" . PHP_EOL, FILE_APPEND);
                    } else {
                        file_put_contents('sendEmails.log', gmdate('Y-m-d H:i:s'). "\tMessage sent to " . $sub['email'] . ".\n" . PHP_EOL, FILE_APPEND);
                    }
                    
                    //Clear address for the next iteration
                    $mail->clearAllRecipients();

                    $delay = rand(1, 5);
                    sleep($delay);
                } catch (Exception $e) {
                    echo "Message could not be sent to ". $sub['email'] . ". Mailer Error: {$mail->ErrorInfo}\n";
                }
            }

            $updateSql = "UPDATE newsletter SET enviarStatus = 0 WHERE id = $newsletter_id";
            mysqli_query($conn, $updateSql);
            $updateSql2 = "UPDATE newsletter SET enviado = 1 WHERE id = $newsletter_id";
            mysqli_query($conn, $updateSql2);

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
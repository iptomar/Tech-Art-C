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
    $investigadores = [];

    if(isset($_GET['id'])) {
        $newsletter_id = $_GET['id'];

        // Buscar o titulo e conteudo da newsletter
        $sql = "SELECT titulo, conteudo FROM newsletter WHERE id = $newsletter_id";
        $result = mysqli_query($conn, $sql);
        // Buscar as noticias pertencentes á newsletter
        $sqlNoticias = "SELECT n.id, n.titulo, n.imagem, n.data FROM noticias n
        JOIN newsletter_noticias nl ON n.id = nl.noticia_id WHERE nl.newsletter_id = $newsletter_id 
        ORDER BY n.data, n.titulo;";
        $resultNoticias = mysqli_query($conn, $sqlNoticias);
        // Buscar os invesigadores
        $sql1 = "SELECT nome, email FROM investigadores WHERE email IS NOT NULL AND email != ''";
        $result1 = mysqli_query($conn, $sql1);
        // Buscar os subscritores da newsletter
        $sql2 = "SELECT nome, email, token FROM subscritores WHERE email IS NOT NULL AND email != ''";
        $result2 = mysqli_query($conn, $sql2);
        // Atualizar o status de envio da newsletter
        $sql3 = "UPDATE newsletter SET enviarStatus = 1 WHERE id = $newsletter_id";
        $result3 = mysqli_query($conn, $sql3);

        if(mysqli_num_rows($result1) > 0){
            while($row1 = mysqli_fetch_assoc($result1)){
                $investigadores[] = $row1;
            }
        }

        if(mysqli_num_rows($result2) > 0){
            while($row2 = mysqli_fetch_assoc($result2)){
                $subcritores[] = $row2;
            };
        }

        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);

            $noticiaHtml = '<tr>';
            $counter = 0;
            // Preenscer o email com as noticias
            if(mysqli_num_rows($resultNoticias) > 0) {
                while($rowNoticias = mysqli_fetch_assoc($resultNoticias)) {
                    $noticiaHtml .= '<td valign="top" width="50%" style="padding-top: 20px; padding-right: 0px;">';
                    $noticiaHtml .= '<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">';
                    $noticiaHtml .= '<tbody>';
                    $noticiaHtml .= '<tr>';
                    $noticiaHtml .= '<td>';
                    $noticiaHtml .= '<div class="container" style="height: 170px; position: relative; overflow: hidden;">';
                    $noticiaHtml .= '<img id="noticiaImagem" src="http://www.techneart.ipt.pt/backoffice/assets/noticias/'. $rowNoticias['imagem'] .'" alt="" style="position: absolute; top: 50%; transform: translateY(-50%);">';
                    $noticiaHtml .= '</div>';
                    $noticiaHtml .= '</td>';
                    $noticiaHtml .= '</tr>';
                    $noticiaHtml .= '<tr>';
                    $noticiaHtml .= '<td class="text-services" style="text-align: left;">';
                    $noticiaHtml .= '<p class="meta" style="color: #999"><span>DATA: '. $rowNoticias["data"] .'</span></p>';
                    $noticiaHtml .= '<h3>'. $rowNoticias["titulo"] .'</h3>';
                    $noticiaHtml .= '<p><a href="http://www.techneart.ipt.pt/tecnart/noticia.php?noticia='. $rowNoticias["id"] .'" class="btn btn-primary">Saber mais</a></p>';
                    $noticiaHtml .= '</td>';
                    $noticiaHtml .= '</tr>';
                    $noticiaHtml .= '</tbody>';
                    $noticiaHtml .= '</table>';
                    $noticiaHtml .= '</td>';
                    $counter++;
                    if($counter % 1 == 0){
                        $noticiaHtml .= '</tr><tr>';
                    }
                }
            }
            $noticiaHtml .= '</tr>';
            
            // Conteudo HTML do email
            $htmlContent = file_get_contents('newsletterTemplate.html');
            $titulo = $row["titulo"];
            $conteudo = $row["conteudo"];

            $htmlContent = str_replace('{{TITULO}}', $titulo, $htmlContent);
            $htmlContent = str_replace('{{CONTEUDO}}', $conteudo, $htmlContent);
            $htmlContent = str_replace('{{NOTICIAS_HTML}}', $noticiaHtml, $htmlContent);

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
            $mail->addReplyTo(EMAIL_SMTP, 'Techn & Art');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');             //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');        //Optional name

            //Content
            $mail->isHTML(true);                                        //Set email format to HTML
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $row['titulo'];
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            foreach($investigadores as $inv) {
                try{
                    //Recipient
                    $mail->addAddress($inv['email'], $inv['nome']);     //Add a recipient
                    //$mail->addAddress('ellen@example.com');           //Name is optional
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    $htmlContentCopy = $htmlContent;
                    
                    $htmlContentCopy = str_replace('{{TOKEN}}', '<p><a href="http://www.techneart.ipt.pt/tecnart/index.php" style="color: white; display: none;">Cancelar Subscrição</a></p>', $htmlContentCopy);

                    $mail->Body = $htmlContentCopy;

                    if(!$mail->send()){
                        file_put_contents('sendEmails.log', gmdate('Y-m-d H:i:s'). "\tEmail not sent to  " . $inv['email'] . ".\n" . PHP_EOL, FILE_APPEND);
                    } else {
                        file_put_contents('sendEmails.log', gmdate('Y-m-d H:i:s'). "\tMessage sent to " . $inv['email'] . ".\n" . PHP_EOL, FILE_APPEND);
                    }
                    
                    //Clear address for the next iteration
                    $mail->clearAllRecipients();

                    $delay = rand(1, 5);
                    sleep($delay);
                } catch (Exception $e) {
                    echo "Message could not be sent to ". $inv['email'] . ". Mailer Error: {$mail->ErrorInfo}\n";
                }
            }

            foreach($subcritores as $sub) {
                try{
                    //Recipient
                    $mail->addAddress($sub['email'], $sub['nome']);     //Add a recipient
                    //$mail->addAddress('ellen@example.com');           //Name is optional
                    //$mail->addCC('cc@example.com');
                    //$mail->addBCC('bcc@example.com');

                    $htmlContentCopy = $htmlContent;
                    
                    // MUDAR O LINK DEPOIS
                    $htmlContentCopy = str_replace('{{TOKEN}}', '<p><a href="http://localhost/Tech-Art-C/tecnart/cancelarSubscricao.php?token=' . $sub['token'] . '" style="color: white;">Cancelar Subscrição</a></p>', $htmlContentCopy);

                    $mail->Body = $htmlContentCopy;

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

            // Atualizar o estado de envio da newsletter
            $updateSql = "UPDATE newsletter SET enviarStatus = 0 WHERE id = $newsletter_id";
            mysqli_query($conn, $updateSql);
            // Atualizar a newsletter para "enviada"
            $updateSql2 = "UPDATE newsletter SET enviado = 1 WHERE id = $newsletter_id";
            mysqli_query($conn, $updateSql2);
            // Atualizar as noticias da newsletter para "enviadas"
            $updateSql3 = "UPDATE noticias n JOIN newsletter_noticias nl ON n.id = nl.noticia_id SET n.enviado = 1 WHERE nl.newsletter_id = $newsletter_id";
            mysqli_query($conn, $updateSql3);

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
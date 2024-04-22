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

        // Buscar o titulo e conteudo da newsletter
        $sql = "SELECT titulo, conteudo FROM newsletter WHERE id = $newsletter_id";
        $result = mysqli_query($conn, $sql);
        // Buscar as noticias pertencentes á newsletter
        $sqlNoticias = "SELECT n.id, n.titulo, n.imagem, n.data FROM noticias n
        JOIN newsletter_noticias nl ON n.id = nl.noticia_id WHERE nl.newsletter_id = $newsletter_id 
        ORDER BY n.data, n.titulo;";
        $resultNoticias = mysqli_query($conn, $sqlNoticias);
        // Buscar os invesigadores e subscritores da newsletter
        //SELECT nome, email FROM investigadores WHERE email IS NOT NULL AND email != ''
        //UNION
        $sql2 = "SELECT nome, email FROM subscritores WHERE email IS NOT NULL AND email != ''";
        $result2 = mysqli_query($conn, $sql2);
        // Atualizar o status de envio da newsletter
        $sql3 = "UPDATE newsletter SET enviarStatus = 1 WHERE id = $newsletter_id";
        $result3 = mysqli_query($conn, $sql3);

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
                    $noticiaHtml .= '<td valign="top" width="50%" style="padding-top: 20px; padding-right: 20px;">';
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
                    if($counter % 2 == 0){
                        $noticiaHtml .= '</tr><tr>';
                    }
                }
            }
            $noticiaHtml .= '</tr>';
            
            // Conteudo HTML do email
            $htmlContent = '<!DOCTYPE html>
            <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <meta name="x-apple-disable-message-reformatting">
                <title>TechnArt Newsletter</title>
            
                <link href="https://fonts.googleapis.com/css?family=Work+Sans:200,300,400,500,600,700" rel="stylesheet">
            
                <!-- CSS Reset : BEGIN -->
                <style>
                    /* What it does: Remove spaces around the email design added by some email clients. */
                    /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
                    html, body {
                        margin: 0 auto !important;
                        padding: 0 !important;
                        height: 100% !important;
                        width: 100% !important;
                        background: #f1f1f1;
                    }
            
                    /* What it does: Stops email clients resizing small text. */
                    * {
                        -ms-text-size-adjust: 100%;
                        -webkit-text-size-adjust: 100%;
                    }
            
                    /* What it does: Centers email on Android 4.4 */
                    div[style*="margin: 16px 0"] {
                        margin: 0 !important;
                    }
            
                    /* What it does: Stops Outlook from adding extra spacing to tables. */
                    table,
                    td {
                        mso-table-lspace: 0pt !important;
                        mso-table-rspace: 0pt !important;
                    }
            
                    /* What it does: Fixes webkit padding issue. */
                    table {
                        border-spacing: 0 !important;
                        border-collapse: collapse !important;
                        table-layout: fixed !important;
                        margin: 0 auto !important;
                    }
            
                    /* What it does: Uses a better rendering method when resizing images in IE. */
                    img {
                        -ms-interpolation-mode:bicubic;
                    }
            
                    /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
                    a {
                        text-decoration: none;
                    }
            
                    /* What it does: A work-around for email clients meddling in triggered links. */
                    *[x-apple-data-detectors],  /* iOS */
                    .unstyle-auto-detected-links *,
                    .aBn {
                        border-bottom: 0 !important;
                        cursor: default !important;
                        color: inherit !important;
                        text-decoration: none !important;
                        font-size: inherit !important;
                        font-family: inherit !important;
                        font-weight: inherit !important;
                        line-height: inherit !important;
                    }
            
                    /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
                    .a6S {
                        display: none !important;
                        opacity: 0.01 !important;
                    }
            
                    /* What it does: Prevents Gmail from changing the text color in conversation threads. */
                    .im {
                        color: inherit !important;
                    }
            
                    /* If the above doesnt work, add a .g-img class to any image in question. */
                    img.g-img + div {
                        display: none !important;
                    }
            
                    /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
                    /* Create one of these media queries for each additional viewport size youd like to fix */
            
                    /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
                    @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
                        u ~ div .email-container {
                            min-width: 320px !important;
                        }
                    }
                    /* iPhone 6, 6S, 7, 8, and X */
                    @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
                        u ~ div .email-container {
                            min-width: 375px !important;
                        }
                    }
                    /* iPhone 6+, 7+, and 8+ */
                    @media only screen and (min-device-width: 414px) {
                        u ~ div .email-container {
                            min-width: 414px !important;
                        }
                    }
                </style>
                <!-- CSS Reset : END -->
            
                <!-- Progressive Enhancements : BEGIN -->
                <style>
                    /*Colors*/
                    .bg_primary{
                        background: #191A2E;
                    }
                    .bg_white{
                        background: #ffffff;
                    }
                    .bg_light{
                        background: #fafafa;
                    }
                    .bg_black{
                        background: #000000;
                    }
                    .bg_dark{
                        background: #DBDEE1;
                    }
            
                    .email-section{
                        padding:2.5em;
                    }
            
                    /*BUTTON*/
                    .btn{
                        padding: 5px 15px;
                        display: inline-block;
                    }
                    .btn.btn-primary{
                        border-radius: 5px;
                        background: #191A2E;
                        color: #ffffff;
                    }
                    .btn.btn-white{
                        border-radius: 5px;
                        background: #ffffff;
                        color: #000000;
                    }
                    .btn.btn-white-outline{
                        border-radius: 5px;
                        background: transparent;
                        border: 1px solid #fff;
                        color: #fff;
                    }
            
                    body{
                        font-family: "Nunito Sans", sans-serif;
                        font-weight: 400;
                        font-size: 15px;
                        line-height: 1.8;
                        color: rgba(0,0,0,.4);
                    }
            
                    h1,h2,h3,h4,h5,h6{
                        font-family: "Nunito Sans", sans-serif;
                        color: #000000;
                        margin-top: 0;
                    }
            
                    a{
                        color: #333F50;
                    }
            
                    /*LOGO*/
                    .logo h1{
                        margin: 0;
                    }
                    .logo h1 a{
                        color: #000;
                        font-size: 20px;
                        font-weight: 700;
                        text-transform: uppercase;
                        
                    }
            
                    .navigation{
                        padding: 0;
                    }
                    .navigation li{
                        list-style: none;
                        display: inline-block;;
                        margin-left: 5px;
                        font-size: 12px;
                        font-weight: 700;
                        font-family: "Nunito Sans", sans-serif;
                    }
                    .navigation li a{
                        color: rgba(255, 255, 255, 0.6);
                    }
                    .navigation li a:hover{
                        color: rgba(255, 255, 255, 0.911);
                    }
            
                    /*HERO*/
                    .hero{
                        position: relative;
                        z-index: 0;
                    }
                    .hero .overlay{
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        content: "";
                        width: 100%;
                        background: #000000;
                        z-index: -1;
                        opacity: .3;
                    }
            
                    .hero .icon a{
                        display: block;
                        width: 60px;
                        margin: 0 auto;
                    }
                    .hero .text{
                        color: rgba(255,255,255,.8);
                        padding: 0 4em;
                    }
                    .hero .text h2{
                        color: #ffffff;
                        font-size: 25px;
                        margin-bottom: 0;
                        line-height: 1.2;
                        font-weight: 900;
                    }
            
                    /*HEADING SECTION*/
                    .heading-section h2{
                        color: #000000;
                        font-size: 24px;
                        margin-top: 0;
                        line-height: 1.4;
                        font-weight: 700;
                    }
                    .heading-section .subheading{
                        margin-bottom: 20px !important;
                        display: inline-block;
                        font-size: 13px;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        color: rgba(0,0,0,.4);
                        position: relative;
                    }
                    .heading-section .subheading::after{
                        position: absolute;
                        left: 0;
                        right: 0;
                        bottom: -10px;
                        content: "";
                        width: 100%;
                        height: 2px;
                        background: #333F50;
                        margin: 0 auto;
                    }
            
                    .heading-section-black{
                        color: #0c0c0c;
                    }
                    .heading-section-black h2{
                        font-family: "Nunito Sans", sans-serif;
                        line-height: 1;
                        padding-bottom: 0;
                    }
                    .heading-section-black h2{
                        color: #333F50;
                    }
                    .heading-section-black .subheading{
                        margin-bottom: 0;
                        display: inline-block;
                        font-size: 13px;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        color: rgba(255,255,255,.4);
                    }
                    
                    #newsletterConteudo{
                        text-align: left;
                    }

                    /*IMAGES*/
                    #noticiaImagemContainer{
                        height: 170px; 
                        width: 100%;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        overflow: hidden;
                    }
                    #noticiaImagem {
                        max-width: 100%;
                        height: auto;
                        width: auto;
                    }
            
                    /*SERVICES*/
                    .services{
                        background: rgba(0,0,0,.03);
                    }
                    .text-services{
                        text-align: center;
                    }
                    .text-services h3{
                        /*font-size: 16px;*/
                        font-weight: 600;
                    }
            
                    .services-list{
                        padding: 0;
                        margin: 0 0 10px 0;
                        width: 100%;
                        float: left;
                    }
            
                    .services-list .text{
                        width: 100%;
                        float: right;
                    }
                    .services-list h3{
                        margin-top: 0;
                        margin-bottom: 0;
                        font-size: 18px;
                    }
                    .services-list p{
                        margin: 0;
                    }
            
                    /*BLOG*/
                    .text-services .meta{
                        text-transform: uppercase;
                        font-size: 14px;
                        margin-bottom: 0;
                    }
                </style>
                <!-- Progressive Enhancements : END -->
            </head>
            <body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #222222;">
                <center style="width: 100%; background-color: #f1f1f1;">
                    <div class="email-container" style="max-width: 600px; margin: 0 auto;">
                        <!-- BEGIN BODY -->
                        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                            <tbody>
                                <!-- BEGIN NAV -->
                                <tr>
                                    <td class="bg_primary" valign="top" style="padding: 1em 2.5em;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td class="logo" width="40%" style="text-align: center;">
                                                        <img width="90%" src="http://www.techneart.ipt.pt/tecnart/assets/images/TechnArt5FundoTrans.png" alt="#">
                                                    </td>
                                                    <!-- <td class="logo" width="60%" style="text-align: right;">
                                                        <ul class="navigation">
                                                            <li><a>Projetos</a></li>
                                                            <li><a>Inestigadores/as</a></li>
                                                            <li><a>Notícias</a></li>
                                                            <li><a>Publicações</a></li>
                                                        </ul>
                                                    </td> -->
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <!-- END NAV -->
                                <!-- <tr>
                                    <td class="hero bg_white" valign="middle" style="background-image: url(https://st5.depositphotos.com/35914836/63547/i/450/depositphotos_635479512-stock-photo-brown-wooden-wall-texture-background.jpg); background-size: cover; height: 400px;">
                                        <div class="overlay"></div>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="text" style="text-align: center;">
                                                            <h2>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h2>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr> -->
                                <tr>
                                    <td class="bg_dark email-section" style="text-align: center;">
                                        <div class="heading-section heading-section-black">
                                            <h2>O que é a Techn&Art?</h2>
                                            <p>Centro de investigação e desenvolvimento nos domínios da Salvaguarda do Património e da sua Valorização, experimental e aplicada.</p>
                                            <p><a href="#" class="btn btn-primary">Saber mais</a></p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg_white email-section">
                                        <div class="heading-section" style="text-align: center; padding: 0 30px;">
                                            <h2>' . $row["titulo"] . '</h2>
                                            <div id="newsletterConteudo">' . $row["conteudo"] . '</div>
                                        </div>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tbody>
                                                <!-- NOTICIA ROW BEGIN -->
                                                ' . $noticiaHtml . '
                                                <!-- NOTICIA ROW END -->
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- END BODY -->
                        <!-- BEGIN FOOTER -->
                        <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: auto;">
                            <tbody>
                                <tr>
                                    <td class="bg_primary footer email-section" valign="middle">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td valign="top" width="33.333%" style="padding-top: 20px;">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: left; padding-right: 10px;">
                                                                        <img id="noticiaImagem" src="http://www.techneart.ipt.pt/tecnart/assets/images/IPT_i_1-vertical-branco-img-para-fundo-escuro.png">
                                                                        <p><img id="noticiaImagem"  src="http://www.techneart.ipt.pt/tecnart/assets/images/TechnArt6FundoTrans.png"></p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td valign="top" width="33.333%" style="padding-top: 20px;">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: left; padding-right: 10px;">
                                                                        <p style="color: white;">Quinta do Contador, Estrada da Serra 2300-313 Tomar - Portugal</p>
                                                                        <a style="color: white;">sec.techenart@ipt.pt</a>
                                                                        <p style="color: white;"><strong>Siga-nos</strong></p>
                                                                        <span><a target="_blank" href="https://www.facebook.com/Techn.Art.IPT/">&nbsp;<img height="20" src="https://cdn-icons-png.flaticon.com/256/124/124010.png"></a></span>
                                                                        <span><a target="_blank" href="https://www.youtube.com/channel/UC3w94LwkXczhZ12WYINYKzA">&nbsp;<img height="20" src="https://cdn-icons-png.flaticon.com/256/1384/1384060.png"></a></span>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td valign="top" width="33.333%" style="padding-top: 20px;">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: left; padding-right: 10px;">
                                                                        <img id="noticiaImagem"  src="http://www.techneart.ipt.pt/tecnart/assets/images/cienciavitaeFundoTrans.png">
                                                                        <p><img id="noticiaImagem"  src="http://www.techneart.ipt.pt/tecnart/assets/images/2022_FCT_Logo_A_horizontal_branco.png"></p>
                                                                        <p style="color: white;">Projeto UDP/05488/2020</p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bg_primary footer email-section" valign="middle">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td valign="top" width="33.333%">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <p style="color: white;">© Instituto Politécnico de Tomar <br> Todos os direitos reservados | <a href="http://www.techneart.ipt.pt/tecnart/copyright.php" style="color: white;">Copyright</a></p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td valign="center" width="33.333%">
                                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                                            <tbody>
                                                                <tr>
                                                                    <td style="text-align: center;">
                                                                        <p><a href="" style="color: white;">Cancelar Subscrição</a></p>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!-- BEGIN FOOTER -->
                    </div>
                </center>
            </body>
            </html>
            ';

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
            $mail->Body    = $htmlContent;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

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
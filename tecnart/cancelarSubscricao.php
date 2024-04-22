<?php
    include 'config/dbconnection.php';
    include 'models/functions.php';

    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['newsletterButton'])) {
        if(isset($_GET['token'])) {
            $token = $_GET['token'];
            $token = strval($token);

            $pdo = pdo_connect_mysql();

            $sql = "SELECT email FROM subscritores WHERE token = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$token]);
            $subscritor = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if($subscritor) {
                $query = "DELETE FROM subscritores WHERE email = ?";
                $stmt2 = $pdo->prepare($query);
                $stmt2->execute([$subscritor[0]["email"]]);
                echo '<header style="background-color: #333f50;"><img src="./assets/images/TechnArt5FundoTrans.png" alt="#" style="width: 30%"></header>';
                echo '<div id="messageDiv" style="display: block; margin-top: 20px; padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc; text-align: center;">'. change_lang("newsletter-unsubscribe-redirect") .'</div>';
            } else {
                echo '<header style="background-color: #333f50;"><img src="./assets/images/TechnArt5FundoTrans.png" alt="#" style="width: 30%"></header>';
                echo '<div id="messageDiv" style="display: block; margin-top: 20px; padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc; text-align: center;">'. change_lang("newsletter-unsubscribe-invalid") .'</div>';
            }
        } else {
            echo '<header style="background-color: #333f50;"><img src="./assets/images/TechnArt5FundoTrans.png" alt="#" style="width: 30%"></header>';
            echo '<div id="messageDiv" style="display: block; margin-top: 20px; padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc; text-align: center;">'. change_lang("newsletter-unsubscribe-missing") .'</div>';
        }

        header( "refresh: 3; url=index.php" );
        exit();
    }
?>

<!DOCTYPE html>
<html>
<style>

    #newsletterButton {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
        text-transform: none;
        display: inline-block;
        box-sizing: border-box;
    }

    #newsletterButton {
        background-color: #333f50;
        color: white;
        border: none;
    }

    #newsletterButton:hover {
        opacity: 0.8;
    }
</style>
    <body>
        <div style="padding-top: 0px; background-color: #333f50;" class="header_section">
            <img src="./assets/images/TechnArt5FundoTrans.png" alt="#" style="width:30%">
        </div>
        <section class="newsletter_section">
            <div style="padding: 50px;">
                <div class="section-intro pb-60px">
                    <form method="get">
                        <div class="container" style="padding-left: 0px; padding-right: 0px;">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                        <button id="newsletterButton" name="newsletterButton" type="submit" style="display: inline-block; background-color:#333F50; border: 2px solid #000000; color: #ffffff; border-radius: 0; 
                                -webkit-transition: all 0.3s; transition: all 0.3s;  font-family: 'Quicksand', sans-serif;  font-size: 20px;"><?= change_lang("newsletter-unsubscribe-button") ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </body>
</html>
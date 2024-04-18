<?php
include 'config/dbconnection.php';
include 'models/functions.php';

$pdo = pdo_connect_mysql();

$language = ($_SESSION["lang"] == "en") ? "_en" : "";

$query = "SELECT id,
        COALESCE(NULLIF(titulo{$language}, ''), titulo) AS titulo,
        COALESCE(NULLIF(conteudo{$language}, ''), conteudo) AS conteudo,
        imagem,data
        FROM noticias WHERE data<=NOW() ORDER BY DATA DESC;";
$stmt = $pdo->prepare($query);
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<style>
   form h2 {
      font-family: 'Quicksand', sans-serif;
   }

   input[type=text], #newsletterButton {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
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

<?= template_header('Notícias'); ?>


<section class="product_section layout_padding">
   <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
      <div class="container">
         <div class="heading_container3">
            <h3 style="margin-bottom: 5px;">
               <?= change_lang("news-page-heading") ?>
            </h3>
            <h5 class="heading2_h5">
               <?= change_lang("news-page-heading-desc") ?>
            </h5>

         </div>
      </div>
   </div>
</section>


<section class="product_section layout_padding">
   <div style="padding-top: 20px;">
      <div class="container">
         <div class="row justify-content-center mt-3">

            <?php foreach ($noticias as $noticia) : ?>
               <div class="ml-5 imgList">
                  <a href="noticia.php?noticia=<?= $noticia['id'] ?>">
                     <div class="image_default">
                        <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/noticias/<?= $noticia['imagem'] ?>" alt="">
                        <div class="imgText justify-content-center m-auto" style="top:75%">
                           <?php
                           $titulo = trim($noticia['titulo']);
                           if (strlen($noticia['titulo']) > 35) {
                              $titulo = preg_split("/\s+(?=\S*+$)/", substr($noticia['titulo'], 0, 35))[0];
                           }
                           echo ($titulo !=  trim($noticia['titulo'])) ? $titulo . "..." : $titulo;
                           ?>
                        </div>
                        <h6 class="imgText m-auto" style="font-size: 11px; font-weight: 100; top:95%"><?= date("d.m.Y", strtotime($noticia['data'])) ?></h6>
                     </div>
                  </a>
               </div>

            <?php endforeach; ?>

         </div>

      </div>

   </div>
</section>

<!-- newsletter section -->
<section class="newsletter_section">
   <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
      <div class="section-intro pb-60px">
         <form>
            <div class="container">
               <h2>
                  <?= change_lang("newsletter-title") ?>
               </h2>
               <p><?= change_lang("newsletter-p") ?></p>
            </div>

            <div class="container" style="background-color:white">
               <input type="text" placeholder="<?= change_lang("newsletter-placeholder-name") ?>" name="name" required>
               <input type="text" placeholder="<?= change_lang("newsletter-placeholder-email") ?>" name="mail" required>
            </div>

            <div class="container" style="padding-left: 0px; padding-right: 0px;">
               <button id="newsletterButton" type="submit" style="display: inline-block; padding: 5px 25px; background-color:#333F50; border: 2px solid #000000; color: #ffffff; border-radius: 0; 
                     -webkit-transition: all 0.3s; transition: all 0.3s;  font-family: 'Quicksand', sans-serif;  font-size: 20px;"><?= change_lang("newsletter-subscribe-button") ?></button>
            </div>
         </form>
      </div>
   </div>
</section>
<!-- end newsletter section-->

<?= template_footer(); ?>

</body>

</html>
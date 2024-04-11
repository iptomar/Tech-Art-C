<?php
include 'config/dbconnection.php';
include 'models/functions.php';

$pdo = pdo_connect_mysql();
$language = ($_SESSION["lang"] == "en") ? "_en" : "";

// Check if search query is provided
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query based on the search query
$query = "SELECT id, COALESCE(NULLIF(nome{$language}, ''), nome) AS nome, fotografia FROM projetos WHERE concluido=false";
if (!empty($search_query)) {
    $query .= " AND (nome LIKE :search_query)";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search_query', '%' . $search_query . '%', PDO::PARAM_STR);
} else {
    $stmt = $pdo->prepare($query);
}
$stmt->execute();
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<?= template_header(change_lang("projects-ongoing-page-heading")); ?>

<!-- product section -->
<section class="product_section layout_padding">
   <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
      <div class="container">
         <div class="heading_container3">
            <h3 style="margin-bottom: 5px;">
               <?= change_lang("projects-ongoing-page-heading") ?>
            </h3>
            <h5 class="heading2_h5">
               <?= change_lang("projects-ongoing-page-description") ?>
            </h5>
         </div>
      </div>
   </div>
</section>
<!-- end product section -->

<section class="product_section layout_padding">
   <!-- Search Bar -->
   <form method="GET" action="projetos_em_curso">
      <div class="row justify-content-center">
         <div class="col-md-6 mb-3">
            <div class="input-group mb-3">
               <input type="text" name="search" class="form-control" placeholder="Search projects..." id="searchInput">
               <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="submit" id="searchButton"><i class="fa fa-search"></i></button>
               </div>
            </div>
         </div>
      </div>
   </form>
   <!-- End of Search Bar -->

   <div style="padding-top: 20px;">
      <div class="container">
         <div class="row justify-content-center mt-3">
            <?php foreach ($projetos as $projeto) : ?>
               <div class="ml-5 imgList">
                  <a href="projeto.php?projeto=<?= $projeto['id'] ?>">
                     <div class="image_default">
                        <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/projetos/<?= $projeto['fotografia'] ?>" alt="">
                        <div class="imgText justify-content-center m-auto"><?= $projeto['nome'] ?></div>
                     </div>
                  </a>
               </div>
            <?php endforeach; ?>
         </div>
      </div>
   </div>
</section>

<!-- end product section -->

<?= template_footer(); ?>

</body>
</html>

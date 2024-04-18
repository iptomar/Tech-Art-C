<?php
include 'config/dbconnection.php';
include 'models/functions.php';

$pdo = pdo_connect_mysql();
$language = ($_SESSION["lang"] == "en") ? "_en" : "";

// Check if search query is provided
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query based on the search query
$query = "SELECT id, COALESCE(NULLIF(nome{$language}, ''), nome) AS nome, fotografia FROM projetos WHERE concluido=true";
$params = [];
if (!empty($search_query)) {
    $query .= " AND (nome LIKE :search_query)";
    $params[':search_query'] = '%' . $search_query . '%';
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

<?= template_header(change_lang("projects-finished-page-heading")); ?>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style type="text/css">
    <?php
    $css = file_get_contents('../styleBackoffices.css');
    echo $css;
    ?>
</style>

<!-- product section -->
<section class="product_section layout_padding">
   <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
      <div class="container">
         <div class="heading_container3">
            <h3 style="margin-bottom: 5px;">
               <?= change_lang("projects-finished-page-heading") ?>
            </h3>
            <h5 class="heading2_h5">
               <?= change_lang("projects-finished-page-description") ?>
            </h5>
         </div>
      </div>
   </div>
</section>
<!-- end product section -->

<section class="product_section layout_padding">
   <!-- Search Bar -->
   <form method="GET" action="projetos_concluidos.php">
      <div class="row justify-content-center">
         <div class="col-md-6 mb-3">
            <div class="input-group mb-3">
               <input type="text" name="search" class="form-control" placeholder="Pesquisar por projetos..." id="searchInput">
               <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="submit" id="searchButton"><i class="fa fa-search"></i></button>
               </div>
            </div>
         </div>
      </div>
   </form>
  <!-- End of Search Bar -->
  
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
</section>


<!-- end product section -->

<?= template_footer(); ?>

</body>

</html>
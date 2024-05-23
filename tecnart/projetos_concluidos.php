<?php
include 'config/dbconnection.php';
include 'models/functions.php';

$pdo = pdo_connect_mysql();
$language = ($_SESSION["lang"] == "en") ? "_en" : "";

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 9; 

$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query to calculate total number of projects
$total_query = "SELECT COUNT(*) FROM projetos WHERE concluido=true";
$params = [];
if (!empty($search_query)) {
   $total_query .= " AND nome LIKE :search_query";
   $params[':search_query'] = '%' . $search_query . '%';
}

// Execute total count query
$total_stmt = $pdo->prepare($total_query);
$total_stmt->execute($params);
$total_projects = $total_stmt->fetchColumn();
$total_pages = ceil($total_projects / $records_per_page);

// Calculate the offset for the current page
$limit = ($page - 1) * $records_per_page;

// Add limit and offset to the query
$query = "SELECT id, COALESCE(NULLIF(nome{$language}, ''), nome) AS nome, fotografia FROM projetos WHERE concluido=true";
if (!empty($search_query)) {
   $query .= " AND nome LIKE :search_query";
}
$query .= " LIMIT :limit OFFSET :offset";

// Bind parameters for the LIMIT and OFFSET
$params[':limit'] = $records_per_page;
$params[':offset'] = $limit;

try {
   $stmt = $pdo->prepare($query);
   $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
   $stmt->bindValue(':offset', $limit, PDO::PARAM_INT);

   if (!empty($search_query)) {
      $stmt->bindParam(':search_query', $params[':search_query'], PDO::PARAM_STR);
   }
   $stmt->execute();
   $projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   echo 'Error: ' . $e->getMessage();
}

?>

<style>
   
.pagination-container {
  display: flex;
  justify-content: center;
  margin-top: 20px;
  margin-bottom: 20px;
}

.pagination {
  display: inline-block;
}

.pagination-link {
  display: inline-block;
  padding: 8px 12px;
  margin: 0 4px;
  border: 1px solid #ccc;
  border-radius: 4px;
  color: #333;
  text-decoration: none;
  transition: background-color 0.3s;
}

.pagination-link.active {
  background-color: #333F50;
  color: #fff;
  border-color: #333F50;
}

.pagination-link:hover {
  background-color: #5f728c;
  color: #fff;
  border-color: #5f728c;
}

</style>



<!DOCTYPE html>
<html>

<?= template_header(change_lang("projects-finished-page-heading")); ?>

<!-- product section -->
<section class="product_section layout_padding">
   <div style="background-color:<?= colors("cinzento") ?>; padding-top: 50px; padding-bottom: 50px;">
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
               <input type="text" name="search" class="form-control" placeholder="Search projects..." id="searchInput">
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

<!-- Seção de paginação -->
<div class="pagination-container">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= htmlspecialchars($search_query) ?>" class="pagination-link">&laquo; Anterior</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search_query) ?>" class="pagination-link <?= ($page == $i) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= htmlspecialchars($search_query) ?>" class="pagination-link">Seguinte &raquo;</a>
    <?php endif; ?>
</div>
<!-- Fim da seção de paginação -->

<?= template_footer(); ?>

</body>

</html>
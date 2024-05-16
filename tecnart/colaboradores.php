<?php
include 'config/dbconnection.php';
include 'models/functions.php';

$pdo = pdo_connect_mysql();
$language = ($_SESSION["lang"] == "en") ? "_en" : "";

// variaveis da página
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
// numero de registos por pagina
$records_per_page = 9; 

// Calcular o limite da query 
$limit = ($page - 1) * $records_per_page;

// Query para buscar os colaboradores
$query = "SELECT id, email, nome, COALESCE(NULLIF(sobre{$language}, ''), sobre) AS sobre, COALESCE(NULLIF(areasdeinteresse{$language}, ''), areasdeinteresse) AS areasdeinteresse, ciencia_id, tipo, fotografia, orcid, scholar, research_gate, scopus_id FROM investigadores WHERE tipo = \"Colaborador\" ORDER BY nome LIMIT $limit, $records_per_page";
$stmt = $pdo->query($query);
$investigadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar o numero total de colaboradores
$total_investigadores_query = "SELECT COUNT(*) AS total FROM investigadores WHERE tipo = \"Colaborador\"";
$total_stmt = $pdo->query($total_investigadores_query);
$total_investigadores = $total_stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calcular o toral de páginas
$total_pages = ceil($total_investigadores / $records_per_page);



$params = []; // Inicializa a variável $params

// Check if search query is provided
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the SQL query based on the search query
$query = "SELECT id, email, nome, COALESCE(NULLIF(sobre{$language}, ''), sobre) AS sobre, COALESCE(NULLIF(areasdeinteresse{$language}, ''), areasdeinteresse) AS areasdeinteresse, ciencia_id, tipo, fotografia, orcid, scholar, research_gate, scopus_id FROM investigadores WHERE tipo = \"Colaborador\"";

// Add search condition if search query is provided
if (!empty($search_query)) {
    $query .= " AND (nome LIKE :search_query)";
    $params[':search_query'] = '%' . $search_query . '%';
}

// Order by clause
$query .= " ORDER BY nome LIMIT $limit, $records_per_page";

// Prepare and execute the SQL query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$investigadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<?= template_header('Colaboradores'); ?>

<!-- product section -->
<section class="product_section layout_padding">
   <div style="background-color: #dbdee1; padding-top: 50px; padding-bottom: 50px;">
      <div class="container">
         <div class="heading_container3">

            <h3 style="margin-bottom: 5px;">
               <?= change_lang("colaborative-researchers-page-heading") ?>
            </h3>

            <h5 class="heading2_h5">
               <?= change_lang("colaborative-researchers-page-heading-desc") ?>
            </h5>

         </div>
      </div>
   </div>
</section>
<!-- end product section -->

<section class="product_section layout_padding">
   <!-- Barra de Pesquisa -->
   <form method="GET" action="colaboradores.php">
      <div class="row justify-content-center">
         <div class="col-md-6 mb-3">
            <div class="input-group mb-3">
               <input type="text" name="search" class="form-control" placeholder="Pesquisar colaboradores..." id="searchInput">
               <div class="input-group-append">
                  <button class="btn btn-outline-secondary" type="submit" id="searchButton"><i class="fa fa-search"></i></button>
               </div>
            </div>
         </div>
      </div>
   </form>
   <!-- Fim da Barra de Pesquisa -->





<section class="product_section layout_padding">
   <div style="padding-top: 20px;">
      <div class="container">
         <div class="row justify-content-center mt-3">

            <?php foreach ($investigadores as $investigador) : ?>

               <div class="ml-5 imgList">
                  <a href="colaborador.php?colaborador=<?= $investigador['id'] ?>">
                     <div class="image_default">
                        <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="../backoffice/assets/investigadores/<?= $investigador['fotografia'] ?>" alt="">
                        <div class="imgText justify-content-center m-auto"><?= $investigador['nome'] ?></div>
                     </div>
                  </a>
               </div>

            <?php endforeach; ?>

         </div>


         <!--             <div class="row justify-content-center mt-3">
               
               <div  class="ml-4 imgList">
               
                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/joana-bento-rodrigues.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">teresa silva</div>
                  </div>  
               
               </div>

               <div class="ml-4 imgList">

                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/maisum.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">josé constâncio</div>
                  </div>

               </div>

               <div class="ml-4 imgList">
               
                  <div class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/pexels-photo-2272853.jpeg" alt="">
                     <div class="imgText justify-content-center m-auto">josefa vasconcelos</div>
                  </div>


               </div>
   
            </div>


            <div class="row justify-content-center mt-3">
               
               <div  class="ml-4 imgList">
               
                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/whatsapp-image-2021.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">ana maria simões</div>
                  </div>  
               
               </div>

               <div class="ml-4 imgList">

                  <div  class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/55918.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">maria bettencourt</div>
                  </div>

               </div>

               <div class="ml-4 imgList">
               
                  <div class="image_default">
                  <img class="centrare" style="object-fit: cover; width:225px; height:280px;" src="./assets/images/5591801.jpg" alt="">
                     <div class="imgText justify-content-center m-auto">cristina marques</div>
                  </div>


               </div>
            
            </div> -->


      </div>

   </div>
</section>

<!-- end product section -->

<!-- pagination section-->
<section class="pagination-container">
   <div class="pagination">
      <?php if ($page > 1) : ?>
         <a href="?page=<?= $page - 1 ?>" class="pagination-link">&laquo; Anterior</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
         <a href="?page=<?= $i ?>" class="pagination-link <?= ($page == $i) ? 'active' : '' ?>"><?= $i ?></a>
      <?php endfor; ?>

      <?php if ($page < $total_pages) : ?>
         <a href="?page=<?= $page + 1 ?>" class="pagination-link">Seguinte &raquo;</a>
      <?php endif; ?>
   </div>
</section>
<!-- end pagination section-->

<?= template_footer(); ?>

</body>

</html>
<?php
include 'config/dbconnection.php';
include 'models/functions.php';

// Função para retornar todos os anos distintos de publicações, ordenados da mais recente para a mais antiga
function getDistinctYears($pdo)
{
    $query = "SELECT DISTINCT YEAR(data) AS publication_year FROM publicacoes ORDER BY publication_year DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para verificar se um ano está selecionado para filtragem
function isYearSelected($year)
{
    if (isset($_GET['years']) && in_array($year, $_GET['years'])) {
        return true;
    }
    return false;
}

?>

<?= template_header('Publicações'); ?>
<section class='product_section layout_padding'>
    <div style='padding-top: 50px; padding-bottom: 30px;'>
        <div class='container'>
            <div class='heading_container3'>
                <h3 class="heading_h3" style="text-transform: uppercase;">
                    <?= change_lang("publications-page-heading") ?>
                </h3><br><br>

                <!-- Formulário para filtrar por ano de publicações -->
                <div class="text-left">
                    <form id="filterForm" method="get" action="<?= $_SERVER['PHP_SELF']; ?>">
                        <div class="form-group row">
                            <label for="years" class="col-auto col-form-label">Filtrar Ano:</label>
                            <div class="col-sm-10">
                                <select name="years[]" id="years" multiple onchange="submitForm()" class="form-control">
                                    <option value="">Sem Filtros</option>
                                    <?php
                                    $pdo = pdo_connect_mysql();
                                    $distinctYears = getDistinctYears($pdo);
                                    foreach ($distinctYears as $year) {
                                        // Formatando o ano (exibindo quatro dígitos)
                                        $formattedYear = $year['publication_year'];
                                        // Verificando se o ano está selecionado
                                        $selected = isYearSelected($year['publication_year']) ? 'selected' : '';
                                        echo "<option value='{$year['publication_year']}' $selected>{$formattedYear}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <?php
                // Filtrar publicações com base nos anos selecionados
                $whereClause = null; // Definir a cláusula de filtro como nula por padrão
                if (isset($_GET['years'])) {
                    if ($_GET['years'][0] !== '') {
                        $years = implode(",", $_GET['years']);
                        $whereClause = "AND YEAR(data) IN ($years)";
                    }
                }

                $lang = isset($_SESSION["lang"]) ? $_SESSION["lang"] : "pt";
                $valorSiteName = "valor_site_$lang";
                $query = "SELECT dados, YEAR(data) AS publication_year, p.tipo, pt.$valorSiteName FROM publicacoes p
                                LEFT JOIN publicacoes_tipos pt ON p.tipo = pt.valor_API
                                WHERE visivel = true $whereClause
                                ORDER BY publication_year DESC, pt.$valorSiteName, data DESC";
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $publicacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Agrupar publicações por ano e site
                $groupedPublicacoes = array();
                foreach ($publicacoes as $publicacao) {
                    $year = $publicacao['publication_year'];
                    if ($year == null) {
                        $year = change_lang("year-unknown");
                    }

                    $site = $publicacao[$valorSiteName];

                    if (!isset($groupedPublicacoes[$year])) {
                        $groupedPublicacoes[$year] = array();
                    }

                    if (!isset($groupedPublicacoes[$year][$site])) {
                        $groupedPublicacoes[$year][$site] = array();
                    }

                    $groupedPublicacoes[$year][$site][] = $publicacao['dados'];
                }
                ?>

                <script src="../backoffice/assets/js/citation-js-0.6.8.js"></script>
                <script>
                    const Cite = require('citation-js');

                    // Função para enviar o formulário automaticamente quando uma opção de ano é selecionada
                    function submitForm() {
                        document.getElementById("filterForm").submit();
                    }
                </script>

                <div id="publications">
                    <?php foreach ($groupedPublicacoes as $year => $yearPublica) : ?>
                        <div class="mb-5">
                            <b><?= $year ?></b><br>
                            <?php foreach ($yearPublica as $site => $publicacoes) : ?>
                                <div style="margin-left: 10px;" class="mt-3"><b><?= $site ?></b><br></div>
                                <div style="margin-left: 20px;" id="publications<?= $year ?><?= $site ?>">
                                    <?php foreach ($publicacoes as $publicacao) : ?>
                                        <script>
                                        var formattedCitation = new Cite(<?= json_encode($publicacao) ?>).format('bibliography', {
                                                format: 'html',
                                                template: 'apa',
                                                lang: 'en-US'
                                            });;
                                            var citationContainer = document.createElement('div');
                                            citationContainer.innerHTML = formattedCitation;
                                            citationContainer.classList.add('mb-3');
                                            document.getElementById('publications<?= $year ?><?= $site ?>').appendChild(citationContainer);
                                        </script>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div><br>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= template_footer(); ?>



<?php
require "../config/basedados.php";

$page = $_GET['page'];
$limit = 3; // Ajuste conforme necessário
$offset = ($page - 1) * $limit;

$sql = "SELECT id, nome, tipo FROM investigadores ORDER BY CASE WHEN tipo = 'Externo' THEN 1 ELSE 0 END, tipo, nome LIMIT $offset, $limit;";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Verificar se há investigadores selecionados armazenados na variável de sessão
    $selected_investigators = isset($_SESSION['selected_investigators']) ? $_SESSION['selected_investigators'] : [];

    while ($row = mysqli_fetch_assoc($result)) {?>
        <input type="checkbox" name="investigadores[]" value="<?= $row["id"] ?>" <?= in_array($row["id"], $selected_investigators) ? 'checked' : '';?>>
        <label><?= $row["tipo"] . " - " .  $row["nome"] ?></label><br>
    <?php }
} else {
    echo 'No investigators found.';
}

// Gera os links de paginação
$sql_total = "SELECT COUNT(*) AS total FROM investigadores";
$result_total = mysqli_query($conn, $sql_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_records = $row_total['total'];
$total_pages = ceil($total_records / $limit);

echo '<div class="pagination">';
for ($i = 1; $i <= $total_pages; $i++) {
    echo '<a href="#" class="pagination-link" data-page="' . $i . '">' . $i . '</a>';
}
echo '</div>';

mysqli_close($conn);

?>

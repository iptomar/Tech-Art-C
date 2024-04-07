<?php
require "../verifica.php";
require "../config/basedados.php";

// Consulta SQL para buscar informações dos projetos e seus investigadores associados
$sql = "SELECT p.id, p.nome, p.referencia, p.areapreferencial, p.financiamento, p.fotografia, p.concluido,
               GROUP_CONCAT(DISTINCT i.nome SEPARATOR ', ') AS investigadores
        FROM projetos p
        LEFT JOIN investigadores_projetos ip ON p.id = ip.projetos_id
        LEFT JOIN investigadores i ON ip.investigadores_id = i.id
        GROUP BY p.id
        ORDER BY p.nome";

$result = mysqli_query($conn, $sql);

// Consulta SQL para buscar informações dos projetos e seus gestores associados
$sql2 = "SELECT p.id, p.nome, p.referencia, p.areapreferencial, p.financiamento, p.fotografia, p.concluido,
               GROUP_CONCAT(DISTINCT g.nome SEPARATOR ', ') AS gestores
        FROM projetos p
        LEFT JOIN gestores_projetos ip ON p.id = ip.projetos_id
        LEFT JOIN investigadores g ON ip.gestor_id = g.id
        GROUP BY p.id
        ORDER BY p.nome";

$result_gestores = mysqli_query($conn, $sql2);

?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style type="text/css">
    <?php
    $css = file_get_contents('../styleBackoffices.css');
    echo $css;
    ?>
</style>

<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>Projetos</h2>
                    </div>
                    <div class="col-sm-6">
                        <a href="create.php" class="btn btn-success"><i class="material-icons">&#xE147;</i> <span>Adicionar
                                Novo Projeto</span></a>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Estado</th>
                        <th>Referência</th>
                        <th>TECHN&ART Área Preferencial</th>
                        <th>Financiamento</th>
                        <th>Fotografia</th>
                        <th>Gestores</th>
						<th>Investigadores</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row["nome"] . "</td>";
                            echo "<td>" . ($row["concluido"] ? "Concluído" : "Em Curso") . "</td>";
                            echo "<td>" . $row["referencia"] . "</td>";
                            echo "<td>" . $row["areapreferencial"] . "</td>";
                            echo "<td>" . $row["financiamento"] . "</td>";
                            echo "<td><img src='../assets/projetos/{$row['fotografia']}' width='100px' height='100px'></td>";
							
							// Buscar os gestores correspondentes a este projeto
                            $gestores = "";
                            mysqli_data_seek($result_gestores, 0); // Reiniciar o ponteiro do resultado
                            while ($row_gestores = mysqli_fetch_assoc($result_gestores)) {
                                if ($row_gestores["id"] == $row["id"]) {
                                    $gestores = $row_gestores["gestores"];
                                    break;
                                }
                            }
                            echo "<td>" . $gestores . "</td>";
							echo "<td>" . $row["investigadores"] . "</td>";

                            echo "<td>
                                    <a href='edit.php?id=" . $row["id"] . "' class='btn btn-primary'><span>Alterar</span></a>
                                    <a href='remove.php?id=" . $row["id"] . "' class='btn btn-danger'><span>Apagar</span></a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Nenhum projeto encontrado.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
mysqli_close($conn);
?>

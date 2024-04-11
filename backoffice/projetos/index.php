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


    <div class="px-5">
        <div class="table-responsive">
            <!-- Add search bar here -->
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search projects..." id="searchInput">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" id="searchButton"><i class="fa fa-search"></i></button>
                </div>
            </div>
            <!-- End of search bar -->
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Projetos</h2>
                        </div>
                        <div class="col-sm-6">
                            <a href="create.php" class="btn btn-success"><i class="material-icons">&#xE147;</i> <span>Adicionar Novo Projeto</span></a>
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
                    <tbody id="projectTableBody">
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
                                
                                $sql1 = "SELECT investigadores_id FROM investigadores_projetos WHERE projetos_id = " . $row["id"];
                                $result1 = mysqli_query($conn, $sql1);
                                $selected = array();
                                if (mysqli_num_rows($result1) > 0) {
                                    while (($row1 = mysqli_fetch_assoc($result1))) {
                                        $selected[] = $row1['investigadores_id'];
                                    }
                                }
                                if ($_SESSION["autenticado"] == "administrador" || in_array($_SESSION["autenticado"], $selected)) {
                                    echo "<td><a href='edit.php?id=" . $row["id"] . "' class='btn btn-primary'><span>Alterar</span></a></td>";
                                    echo "<td><a href='remove.php?id=" . $row["id"] . "' class='btn btn-danger'><span>Apagar</span></a></td>";
                                }
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


<script>
    $(document).ready(function(){
        // Function to perform search
        function performSearch() {
            var searchText = $('#searchInput').val().toLowerCase();
            $('#projectTableBody tr').filter(function(){
                $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
            });
        }

        // Trigger search on click
        $('#searchButton').click(function(){
            performSearch();
        });

        // Trigger search on input change
        $('#searchInput').on('input', function() {
            performSearch();
        });
    });
</script>

<?php
mysqli_close($conn);
?>

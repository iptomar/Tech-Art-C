<?php
require "../verifica.php";
require "../config/basedados.php";

// Selecionar os dados das noticias da base de dados
$sql = "SELECT id, titulo, conteudo, data, imagem FROM noticias ORDER BY data DESC, titulo";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Notícias</title>
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
        .div-textarea {
            display: block;
            padding: 5px 10px;
            border: 1px solid lightgray;
            resize: vertical;
            overflow: auto;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #495057;
        }

        .search-bar {
            margin-top: 20px; /* Espaçamento superior */
            margin-bottom: 20px;
        }

        .table-title {
            margin-top: 20px; /* Espaçamento entre a barra de pesquisa e o título */
        }
    </style>
</head>
<body>
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <!-- Adicionar barra de pesquisa aqui -->
                <div class="input-group mb-3 search-bar">
                    <input type="text" class="form-control" placeholder="Pesquisar por Título..." id="searchInput">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="searchButton"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <!-- Fim da barra de pesquisa -->
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Notícias</h2>
                        </div>
                        <div class="col-sm-6">
                            <?php
                            if ($_SESSION["autenticado"] == "administrador") {
                                echo '<a href="create.php" class="btn btn-success"><i class="material-icons">&#xE147;</i>';
                                echo '<span>Adicionar Nova Notícia</span></a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Conteúdo</th>
                            <th>Data</th>
                            <th>Imagem</th>
                        </tr>
                    </thead>
                    <tbody id="newsTableBody">
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td style='width:250px;'>" . $row["titulo"] . "</td>";
                                echo "<td style='width:500px; height:100px;'><div class='div-textarea' style='width:100%; height:100%;'>" . $row["conteudo"] . "</div></td>";
                                echo "<td style='width:250px;'>" . $row["data"] . "</td>";
                                echo "<td><img src='../assets/noticias/" . $row["imagem"] . "' width='100px' height='100px'></td>";
                                if ($_SESSION["autenticado"] == "administrador") {
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
        $(document).ready(function() {
            // Função para realizar a pesquisa
            function performSearch() {
                var searchText = $('#searchInput').val().toLowerCase();
                $('#newsTableBody tr').filter(function() {
                    $(this).toggle($(this).find('td:first').text().toLowerCase().indexOf(searchText) > -1);
                });
            }

            // Acionar a pesquisa ao clicar no botão
            $('#searchButton').click(function() {
                performSearch();
            });

            // Acionar a pesquisa ao alterar o texto de entrada
            $('#searchInput').on('input', function() {
                performSearch();
            });
        });
    </script>
</body>
</html>

<?php
mysqli_close($conn);
?>

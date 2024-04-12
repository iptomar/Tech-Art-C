<?php
    require "../verifica.php";
    require "../config/basedados.php";
    require "bloqueador.php";
    //Selecionar os dados das newsletters da base de dados
    $sql = "SELECT id, titulo, conteudo, data, enviado FROM newsletter ORDER BY DATA DESC, titulo";
    $result = mysqli_query($conn, $sql);
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
    .div-textarea {
		display: block;
		padding: 5px 10px;
		border: 1px solid lightgray;
		resize: vertical;
		overflow: auto;
		resize: vertical;
		font-size: 1rem;
		font-weight: 400;
		line-height: 1.5;
		color: #495057;
	}
</style>

<div class="container-xl">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
						<h2>Newsletters</h2>
					</div>
					<div class="col-sm-6">
                        <a href="create.php" class="btn btn-success">
                            <i class="material-icons">&#xE147;</i><span>Adicionar Nova Newsletter</span>
                        </a>
					</div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Titulo</th>
                        <th>Conteúdo</th>
                        <th>Data</th>
                        <th>Estado de Envio</th>
                        <th>Ações</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td style='width:250px;'>" . $row["titulo"] . "</td>";
                                echo "<td style='width:500px; height:100px;'>" . "<div class='div-textarea' style='width:100%; height:100%;'>" . $row["conteudo"] . "</div>" . "</td>";
                                echo "<td style='width:250px;'>" . $row["data"] . "</td>";
                                if($row["enviado"] == 0){
                                    echo "<td><a href='' class='btn btn-info'><span>Enviar</span></a></td>";
                                } else {
                                    echo "<td><button href='' class='btn btn-secondary' disabled><span>Enviado</span></button></td>";
                                }
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

<?php
mysqli_close($conn);
?>
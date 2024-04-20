<?php
    require "../verifica.php";
    require "../config/basedados.php";
    require "bloqueador.php";
    //Selecionar os dados das newsletters da base de dados
    $sql = "SELECT id, titulo, conteudo, data, enviarStatus, enviado FROM newsletter ORDER BY DATA DESC, titulo";
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
    .loading-icon {
        display: none;
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
                        <th class="col-auto">Data</th>
                        <th class="col-auto">Titulo</th>
                        <th class="col-auto">Conteúdo</th>
                        <th class="col-auto">Estado de Envio</th>
                        <th class="col-auto">Ações</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>";
                                echo "<td style='width:250px;'>" . $row["data"] . "</td>";
                                echo "<td style='width:250px;'>" . $row["titulo"] . "</td>";
                                echo "<td style='width:500px; height:100px;'>" . "<div class='div-textarea' style='width:100%; height:100%;'>" . $row["conteudo"] . "</div>" . "</td>";
                                if($row["enviado"] == 0 && $row["enviarStatus"] == 0){
                                    echo "<td>";
                                    echo "<div class='d-flex align-items-center'>";
                                    echo "<button href='sendEmails.php?id=" . $row["id"] . "' class='btn btn-info enviar-email'><span>Enviar</span></button>";
                                    echo "<span class='loading-icon ml-2' style='display: none;'><i class='fa fa-spinner fa-spin'></i></span>";
                                    echo "</div>";
                                    echo "</td>";
                                } elseif($row["enviarStatus"] == 1){
                                    echo "<td>";
                                    echo "<div class='d-flex align-items-center'>";
                                    echo "<button href='sendEmails.php?id=" . $row["id"] . "' class='btn btn-info enviar-email' disabled><span>A enviar...</span></button>";
                                    echo "<span class='loading-icon ml-2' style='display: inline;'><i class='fa fa-spinner fa-spin'></i></span>";
                                    echo "</div>";
                                    echo "</td>";
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
    <!-- <div id="message"></div> -->
</div>

<script>
    $(document).ready(function(){

        function checkEnviarStatus() {
            var enviarButtons = $(".enviar-email");
            var atLeastOneSending = false;
            enviarButtons.each(function() {
                var buttonText = $(this).find('span').text().trim();
                if (buttonText === 'A enviar...') {
                    atLeastOneSending = true;
                    return false;
                }
            });
            return atLeastOneSending;
        }

        function disableEnviarButtons() {
            if (checkEnviarStatus()) {
                $(".enviar-email").prop('disabled', true);
            }
        }

        disableEnviarButtons();

        $(".enviar-email").click(function(e){
            e.preventDefault();

            var $button = $(this);
            var url = $(this).attr("href");
            var $loadingIcon = $(this).siblings('.loading-icon');
            var $buttonText = $(this).find('span');

            disableEnviarButtons();

            $(".enviar-email").each(function() {
                if ($(this).find('span').text() !== 'Enviado') {
                    $(this).attr('disabled', true);
                }
            });

            $loadingIcon.show();
            $buttonText.text('A enviar...');
            $button.addClass('btn-secondary').attr('disabled', true);
            
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response){
                    //$("#message").html(response); // Display success or error message
                    $loadingIcon.hide();
                    $buttonText.text('Enviado');
                    $button.removeClass('btn-info');

                    $(".enviar-email").prop('disabled', false);
                },
                error: function(xhr, status, error){
                    //$("#message").html(response); // Display success or error message
                    console.error(xhr.responseText);
                    $loadingIcon.hide();
                    $buttonText.text('A enviar...');

                    $(".enviar-email").prop('disabled', false);
                }
            });
        });
    });
</script>


<?php
mysqli_close($conn);
?>

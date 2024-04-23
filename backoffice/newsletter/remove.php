<?php
    require "../verifica.php";
    require "../config/basedados.php";
    require "bloqueador.php";

    if($_SESSION["autenticado"] != "administrador"){
        // Usuário não tem permissão para eliminar newsletters, redireciona para o index das newsletters
        header("Location: index.php");
        exit;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = $_POST["id"];
        // Excluir registros relacionados da newsletter na outra tabela
        $sql = "DELETE FROM newsletter_noticias WHERE newsletter_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        // Excluir a newsletter
        $sql = "DELETE FROM newsletter WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php');
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        $sql = "SELECT titulo, titulo_en, conteudo, conteudo_en, data FROM newsletter WHERE id = ?";
        // id da newsletter
        $id = $_GET["id"];
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        // titulo da newsletter
        $titulo = $row["titulo"];
        $titulo_en = $row["titulo_en"];
        // conteudo da newsletter
        $conteudo = $row["conteudo"];
        $conteudo_en = $row["conteudo_en"];
        // data da newsletter
        $data = $row["data"];
    }

?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</link>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>
<style>
    .container {
        max-width: 550px;
        margin: 0 auto;
    }

    .has-error label,
    .has-error input,
    .has-error textarea {
        color: red;
        border-color: red;
    }

    .list-unstyled li {
        font-size: 13px;
        padding: 4px 0 0;
        color: red;
    }

    .ck-editor__editable {
        min-height: 200px;
    }

    .halfCol {
        max-width: 50%;
        display: inline-block;
        vertical-align: top;
        height: fit-content;
    }

    #scrolling {
        height: 0px;
        overflow-y: scroll;
        overflow-x: hidden;
    }

    #noticiaRow{
        height: auto;
        padding-right: 20px;
        padding-left: 20px;
    }
    
    #noticiaCard {
        box-shadow: 0 8px 10px 0 rgba(67, 93, 125, 1);
        border: 1px solid rgba(67, 93, 125, 1);
        transition: 0.3s;
        margin-bottom: 40px
    }
    
    .selected {
        transition: 0.3s;
        background-color: #354354;
        color: #fff
    }

    #noticiaCardContainer {
        padding-left: 0px;
        padding-right: 0px;
    }

    #noticiaCardContainer p {
        max-height: 50px;
        overflow: hidden;
        word-wrap: break-word;
    }

    #noticiaImagemContainer{
        height: 170px; 
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    
    #noticiaImagem {
        width:100%;
        height: auto;
    }

    #tit {
        height: 130px;
        overflow: auto;
    }
</style>

<div class="container-xl mt-5 mb-5">
    <div class="card">
        <h5 class="card-header text-center">Remover Newsletter</h5>
        <div class="card-body">
            <form role="form" data-toggle="validator" action="remove.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $id?>">
                <div class="form-group">
                    <label>Data da Newsletter</label>
                    <input type="date" readonly class="form-control" id="inputDate" required name="data" value="<?php echo $data ?>">
                    <!-- Error -->
                    <div class="help-block with-errors"></div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Titulo</label>
                            <input type="text" readonly name="titulo" class="form-control" data-error="Por favor adicione um titulo válido" id="inputTitle" placeholder="Titulo" value="<?php echo $titulo; ?>">
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Titulo (Inglês)</label>
                            <input type="text" readonly name="titulo_en" class="form-control" placeholder="Titulo (Inglês)" value="<?php echo $titulo_en; ?>">
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col halfCol">
                        <div class="form-group" style="height: fit-content;">
                            <label>Conteúdo da Newsletter</label>
                            <div readonly class="form-control ck_replace" id="inputContent" name="conteudo" style="width:100%; height:100%;"><?php echo $conteudo; ?></div>
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col halfCol">
                        <div class="form-group" style="height: fit-content;">
                            <label>Conteúdo (Inglês)</label>
                            <div readonly class="form-control ck_replace" id="inputContentEn" name="conteudo_en" style="width:100%; height:100%;"><?php echo $conteudo_en; ?></div>
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Noticias da Newsletter</label><br>
                    <div id="scrolling">
                        <div id="noticiaRow" class="row">
                            <?php
                                $sql = "SELECT noticia_id FROM newsletter_noticias WHERE newsletter_id = " . $id;
                                $result = mysqli_query($conn, $sql);
                                $select = array();
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $select[] = $row["noticia_id"];
                                        $noticias[] = $row["noticia_id"];
                                    }
                                }
                                $sql = "SELECT n.id, n.titulo, n.imagem, n.data, n.enviado FROM noticias n
                                    JOIN newsletter_noticias nl ON n.id = nl.noticia_id WHERE nl.newsletter_id = $id 
                                    ORDER BY n.data, n.titulo;";
                                $result = mysqli_query($conn, $sql);
                                $selected = array();
                                if (mysqli_num_rows($result) > 0) {
                                    $counter = 0;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $selected[] = $row["enviado"];
                                        ?>
                                        <div class="col-md-4">
                                            <div id="noticiaCard" class="card" data-id="<?= $row["id"] ?>">
                                                <div id="noticiaImagemContainer">
                                                    <img id="noticiaImagem" src="../assets/noticias/<?=$row["imagem"]?>">
                                                </div>
                                                <!--<img id="noticiaImagem" src="../assets/noticias/<?=$row["imagem"]?>">-->
                                                <div id="noticiaCardContainer" class="container">
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item selected" id="tit"><label>Titulo:</label><br><?= $row["titulo"] ?></li>
                                                        <li class="list-group-item selected">
                                                            <small class="text-muted">
                                                                Data: <?= $row["data"] ?><br>
                                                                <a style="display: inline;">Enviado?</a>
                                                                <?php
                                                                    if ($row["enviado"]) {
                                                                        echo '<a style="color:MediumSeaGreen; margin-bottom:0px;">SIM</a>';
                                                                    } else {
                                                                        echo '<a style="color:Tomato; margin-bottom:0px;">NÃO</a>';
                                                                    }
                                                                ?>
                                                                <br>
                                                                <label>Adicionado á Newsletter</label>
                                                                <input type="checkbox" <?= in_array($row["id"], $select) ? "checked" : "" ?> name="noticias[]" value="<?= $row["id"] ?>" disabled>
                                                            </small>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                        $counter++;
                                        if ($counter % 3 == 0) {
                                            echo '</div><div id="noticiaRow" class="row">';
                                        }
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Confirmar</button>
                </div>

                <div class="form-group">
                    <button type="button" onclick="window.location.href = 'index.php'" class="btn btn-danger btn-block">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        // Function to adjust the height of the scrolling container
        function adjustScrollingHeight() {
            var rowCount = $('#noticiaRow .col-md-4').length;
            var height = 0;
            if (rowCount <= 3) {
                height = 435; // Height for 3 items
            } else if (rowCount <= 6) {
                height = 870; // Height for 6 items
            } else if (rowCount <= 9 || rowCount >= 9 ) {
                height = 1305; // Height for 9 items
            }
            $('#scrolling').css('height', height + 'px');
        }

        // Call the function initially
        adjustScrollingHeight();
    })
</script>

<?php
mysqli_close($conn);
?>
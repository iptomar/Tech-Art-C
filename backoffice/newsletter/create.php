<?php
    require "../verifica.php";
    require "../config/basedados.php";
    require "bloqueador.php";

    if($_SERVER["REQUEST_METHOD"] == "POST") {

        $sql = "INSERT INTO newsletter (titulo, titulo_en, conteudo, conteudo_en, data, enviado, ultimo_editor) " . 
        "VALUES (?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt,"ssssssi", $titulo, $titulo_en, $conteudo, $conteudo_en, $data, $enviado, $ultimo_editor);
        
        // titulo da newsletter
        $titulo = $_POST["titulo"];
        $titulo_en = $_POST["titulo_en"];
        // conteudo da newsletter
        $conteudo = $_POST["conteudo"];
        $conteudo_en = $_POST["conteudo_en"];
        // data da newletter
        $data = $_POST["data"];
        // estado de envio da newsletter
        //$enviado = isset($_POST["enviado"]) ? 1 : 0;
        $enviado = 0;
        // ultimo utilizador a modificar a newsletter
        $ultimo_editor = $_SESSION["adminid"];

        $noticias = [];
        if (isset($_POST["noticias"])) {
            $noticias = $_POST["noticias"];
        }

        if (mysqli_stmt_execute($stmt)) {
            if (count($noticias) > 0){
                $sqlinsert = "";
                foreach ($noticias as $id) {
                    $sqlinsert .= "($id, last_insert_id()),";
                }
                $sqlinsert = rtrim($sqlinsert,",");
                $sql = "INSERT INTO newsletter_noticias(noticia_id, newsletter_id) VALUES" . $sqlinsert;
                if (!mysqli_query($conn, $sql)) {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                    exit;
                }
            }

            header('Location: index.php');
            exit;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
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
    
    #noticiaCard {
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        margin-bottom: 40px
    }

    #noticiaCard:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
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
        overflow: hidden;
    }
    
    #noticiaImagem {
        width:100%;
        height: auto;
    }
</style>

<div class="container-xl mt-5 mb-5">
    <div class="card">
        <h5 class="card-header text-center">Adicionar Newsletter</h5>
        <div class="card-body">
            <form role="form" data-toggle="validator" action="create.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Data da Newsletter</label>
                    <input type="date" class="form-control" id="inputDate" required name="data" value="<?php echo date('Y-m-d'); ?>">
                    <!-- Error -->
                    <div class="help-block with-errors"></div>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Titulo</label>
                            <input type="text" minlength="1" require maxlength="100" name="titulo" class="form-control" data-error="Por favor adicione um titulo válido" id="inputTitle" placeholder="Titulo">
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Titulo (Inglês)</label>
                            <input type="text" maxlength="100" name="titulo_en" class="form-control" placeholder="Titulo (Inglês)">
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col halfCol">
                        <div class="form-group">
                            <label>Conteúdo da Newsletter</label>
                            <textarea class="form-control ck_replace" cols="30" rows="5" data-error="Por favor adicione o conteudo da Newsletter" id="inputContent" name="conteudo"></textarea>
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="col halfCol">
                        <div class="form-group">
                            <label>Conteúdo (Inglês)</label>
                            <textarea class="form-control ck_replace" cols="30" rows="5" id="inputContentEn" name="conteudo_en"></textarea>
                            <!-- Error -->
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Adicionar Noticias</label><br>
                    <div id="noticiaRow" class="row">
                        <?php
                            $sql = "SELECT id, titulo, imagem, data, enviado FROM noticias  
                                ORDER BY data, titulo;";
                            $result = mysqli_query($conn, $sql);
                            $selected = array();
                            $selected2 = array();
                            if (mysqli_num_rows($result) > 0) {
                                $counter = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $selected[] = $row["enviado"];
                                    if ($counter % 3 == 0) {
                                        echo '</div><div class="row">';
                                    }
                                    if ($row["id"] == $_SESSION["autenticado"]) {
                                        echo "<input type='hidden' name='noticias[]' value='" . $row["id"] . "'/>";
                                    } 
                                    ?>
                                    <div class="col-md-4">
                                        <div id="noticiaCard" class="card" data-id="<?= $row["id"] ?>">
                                            <div id="noticiaImagemContainer">
                                                <img id="noticiaImagem" src="../assets/noticias/<?=$row["imagem"]?>">
                                            </div>
                                            <!--<img id="noticiaImagem" src="../assets/noticias/<?=$row["imagem"]?>">-->
                                            <div id="noticiaCardContainer" class="container">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item"><label>Titulo:</label><br><?= $row["titulo"] ?></li>
                                                    <li class="list-group-item">
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
                                                            <input type="checkbox" <?= in_array($row["id"], $selected2) || $row["id"] == $_SESSION["autenticado"] ? "checked" : "" ?> <?= $row["id"] == $_SESSION["autenticado"] ? "disabled" : "" ?> name="noticias[]" value="<?= $row["id"] ?>" disabled>
                                                        </small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                    $counter++;
                                }
                            }
                        ?>
                    </div>
                </div>
                    
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Criar</button>
                </div>

                <div class="form-group">
                    <button type="button" onclick="window.location.href = 'index.php'" class="btn btn-danger btn-block">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--Criar o CKEditor 5-->
<script src="../ckeditor5/build/ckeditor.js"></script>
<script>
    $(document).ready(function() {
        $('.ck_replace').each(function() {
            ClassicEditor.create(this, {
                licenseKey: '',
                simpleUpload: {
                    uploadUrl: '../ckeditor5/upload_image.php'
                }
            }).then(editor => {
                window.editor = editor;
            });
        });

        $('.card').on('click', function() {
            var id = $(this).attr('data-id');
            console.log('Card ID:', id);
            if (!id) return; // If no ID is found, exit the function
            var checkbox = $(this).find('input[type="checkbox"]');
            checkbox.prop('checked', !checkbox.prop('checked')); // Toggle the checkbox
            var selected = checkbox.prop('checked');
            console.log('Checkbox Selected:', selected);
            if (selected) {
                // If card is selected, add its ID to the array
                addIdToArray(id);
                $(this).css({
                    'box-shadow': '0 8px 10px 0 rgba(67, 93, 125, 1)',
                    'border': '1px solid rgba(67, 93, 125, 1)'
                }); // Add the 'selected-card' class to the clicked card
                $(this).find('.list-group-item').addClass('selected');
            } else {
                // If card is deselected, remove its ID from the array
                removeIdFromArray(id);
                $(this).css({
                    'box-shadow': '0 4px 8px 0 rgba(0, 0, 0, 0.2)',
                    'border': 'none',
                }); // Remove the 'selected-card' class from the clicked card
                $(this).find('.list-group-item').removeClass('selected');
            }
            console.log('Array:', $('#noticiaRow').find('input[type="hidden"]').map(function() {
                return $(this).val();
            }).get());
        });

        function addIdToArray(id) {
            // Add ID to the hidden input array
            $('<input>').attr({
                type: 'hidden',
                name: 'noticias[]',
                value: id
            }).appendTo('#noticiaRow');
        }

        function removeIdFromArray(id) {
            // Remove the hidden input with matching ID
            $('#noticiaRow').find('input[type="hidden"][value="' + id + '"]').remove();
        }

    });
    

    $('#inputDate').on("change", function(e) {
        var inputDate = $(this).val();
        console.log("TESTING")
        // Check if the input value is a valid date
        if (!isValidDate(inputDate)) {
            console.log("NOT VALID")

            e.currentTarget.setCustomValidity('Por favor adicione uma data válida');
        } else {

            e.currentTarget.setCustomValidity('');
        }
    });

    function isValidDate(dateString) {
        var dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (!dateRegex.test(dateString)) {
            return false;
        }
        var date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return false;
        }
        return true;
    }
</script>

<?php
mysqli_close($conn);
?>
<?php
// Verifica se o utilizador tem permissão para aceder às newsletters
if ($_SESSION["autenticado"] != "administrador") {
    header("Location: ../projetos");
    exit;
}
?>
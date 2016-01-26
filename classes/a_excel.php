<?php

    $op = $_GET['op'];
    switch ($op) {
        case 1:
            require 'excel.model.php';
            $f_inicio = $_GET['f_inicio'];
            $f_termino = $_GET['f_termino'];
            $excel = new AExcel();
            $excel->set_host(0);
            $excel->cierre_caja($f_inicio, $f_termino);
            break;

        default:
            ?>
            <script>
            window.location.href = 'http://www.mybu.cl/login';
            </script>
            <?php
            break;
    }

?>

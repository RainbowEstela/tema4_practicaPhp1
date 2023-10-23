<?php
    function establecerConexion() {
        try {
            $dsn = "mysql:host=172.17.0.2;dbname=biblioteca";//en host se usa la ip de contenedor sql y su puerto interno
            $conexion = new PDO($dsn, "root", "root");
        } catch (PDOException $e){
            echo $e->getMessage();
        }

        return $conexion;
    }
?>
<?php
    include_once("lib.php");

    //prueba de conexion
    $conexion = establecerConexion();

    $stmt = $conexion->prepare("SELECT * FROM usuarios");

    $stmt-> setFetchMode(PDO::FETCH_ASSOC);

    $stmt->execute();

    while($row = $stmt->fetch()) {
        echo "Nombre: ". $row["nombre"] . "<br>";
        echo "Email: ". $row["email"] . "<br>";
    }

?>
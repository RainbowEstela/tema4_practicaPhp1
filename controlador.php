<?php
    session_start();
    include_once("lib.php");
    /*
    //prueba de conexion
    $conexion = establecerConexion();

    $stmt = $conexion->prepare("SELECT * FROM usuarios");

    $stmt-> setFetchMode(PDO::FETCH_ASSOC);

    $stmt->execute();

    while($row = $stmt->fetch()) {
        echo "Nombre: ". $row["nombre"] . "<br>";
        echo "Email: ". $row["email"] . "<br>";
    }
    */

    /**
     * PETICIONES POR GET
     */

    //comprobar si llega un error
    if(isset($_GET["error"])) {

        //comprobar si el error es no login
        if(strcmp($_GET["error"],"noLogin") === 0) {

            //redirigimos a singin.php
            header("Location: singin.php");
            die();
        }
    }


    /**
     * PETICIONES POR POST
     */

    //formulario SIGN IN
    if(isset($_POST["formSignin"])) {
        //tratamos los datos
        $email = $_POST["email"];
        $password = $_POST["password"];

        //añadimos al usuario a la sesion
        $_SESSION["usuario"] = [
            "email" => $email,
            "password" => $password
        ];

        //redirigimos a index.php
        header("Location: index.php");
        die();
    }

?>
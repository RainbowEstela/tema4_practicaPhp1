<?php
    session_start();
    include_once("lib.php");

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

    //comprobar si llega una accion
    if(isset($_GET["accion"])) {
        if(strcmp($_GET["accion"],"logout") === 0) {
            session_destroy();

            //redirigimos a singin.php
            header("Location: singin.php");
            die();
        }

        if(strcmp($_GET["accion"],"borrarPrestamo") === 0) {
            //comprobar que se ha pasado una id
            if(isset($_GET["id"])) {
                //borrar de la base de datos el prestamo por esa id
                borrarPrestamo($_GET["id"]);
            }

            //redirigimos a index.php
            header("Location: index.php");
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
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

    //formulario de modificacion
    if(isset($_POST["modificarPrestamo"])) {
        //tratamos la informacion
        $id = $_POST["id"];
        $isbn = $_POST["isbn"];
        $dni = $_POST["dni"];
        $fechaIni = $_POST["fechaInicio"];
        $fechaFin = $_POST["fechaFin"];
        $estado = $_POST["estado"];

        $libro = buscarLibro($isbn);
        $usuario = buscarUsuario($dni);
        $prestamo = buscarPrestamo($id);

        //comprobar que el usuario y libro no existen en la base datos
        if( $libro == false || $usuario == false ) {
            //redirigimos a modificar.php
            header("Location: modificar.php?error=datosErroneos&id=".$id);
            die();
        }

        //comprobar el estado
                    //nuevo estado                            //estado antiguo
        if(strcmp($estado,"devuelto") === 0 && strcmp($prestamo["estado"],"devuelto") !== 0) {//cambiado de algo a devuelto sumo 1
           
            //sumamos un libro a la biblioteca
            $nuevaCantidad = $libro["numeroEjemplares"] +1;

                    //nuevo estado                            //estado antiguo
        } else if (strcmp($estado,"devuelto") !== 0 && strcmp($prestamo["estado"],"devuelto") === 0) { //cambiado de devuelto a otra cosa resto 1
            //comprobar que el stock no es 0
            if($libro["numeroEjemplares"] > 0) {//se puede sacar un libro
                
                //restamos un libro a la biblioteca
                $nuevaCantidad = $libro["numeroEjemplares"] -1;

            } else {                            //no se puede sacar un libro
                //redirigimos a modificar.php
                header("Location: modificar.php?error=noHayExistencias&id=".$id);
                die();
            }

        } else {//la cantidad no cambia
            echo "cambiado de algo a otro algo donde no cambia el numero de libros";
        }

    }

?>
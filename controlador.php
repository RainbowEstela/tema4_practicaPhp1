<?php
    session_start();
    include_once("lib.php");

    //FUNCIONES
    /**
     * funcion que compruba si quedan existencias de un libro para dar si no redirige a modificar.php con la id del prestamo pasado
     */
    function comprobarCantidad($libro,$id) {
        if($libro["numeroEjemplares"] <= 0) {

            //redirigimos a modificar.php
            header("Location: modificar.php?error=noHayExistencias&id=".$id);
            die();

        }
    }

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
            //comprobar usuario en sesion
            if(!isset($_SESSION["usuario"])) {
                header("Location: controlador.php?error=noLogin");
                die();
            }

            //comprobar que se ha pasado una id
            if(isset($_GET["id"])) {
                $prestamo = buscarPrestamo($_GET["id"]);
                
                //comprobar que el estado no es devuelto
                if(strcmp($prestamo["estado"],"devuelto") !== 0) {
                    $libro = buscarLibro($prestamo["isbn"]);

                    modificarCantidadLibro($libro["id"],$libro["numeroEjemplares"] + 1);
                }

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

        //comprobar usuario en sesion
        if(!isset($_SESSION["usuario"])) {
            header("Location: controlador.php?error=noLogin");
            die();
        }

        //tratamos la informacion
        $id = $_POST["id"];
        $isbn = $_POST["isbn"];
        $dni = $_POST["dni"];
        $fechaIni = date('d-m-Y',strtotime($_POST["fechaInicio"]));
        $fechaFin = date('d-m-Y',strtotime($_POST["fechaFin"]));
        $estado = $_POST["estado"];

        //si el estado es pasado1Anio pasarlo a pasado1Año
        if(strcmp($estado,"sobrepasado1Anio") === 0) {
            $estado = "sobrepasado1Año";
        }


        $libro = buscarLibro($isbn);
        $usuario = buscarUsuario($dni);
        $prestamo = buscarPrestamo($id);
        $libroViejo = buscarLibro($prestamo["isbn"]);

        //comprobar que el usuario y libro no existen en la base datos
        if( $libro == false || $usuario == false ) {
            //redirigimos a modificar.php
            header("Location: modificar.php?error=datosErroneos&id=".$id);
            die();
        }

        //comprobar si se ha cambiado de libro
        if(strcmp($prestamo["isbn"],$isbn) === 0) {//no se cambia de libro

                //comprobar el estado
                //nuevo estado  - devuelto                //estado antiguo -activo
                if(strcmp($estado,"devuelto") === 0 && strcmp($prestamo["estado"],"devuelto") !== 0) {//cambiado de activo a devuelto sumo 1

                    //SUMAMOS 1 AL LIBRO
                    //MODIFICAR CANTIDAD
                    modificarCantidadLibro($libro["id"],$libro["numeroEjemplares"] + 1);


                            //nuevo estado - activo                       //estado antiguo -devuelto
                } else if (strcmp($estado,"devuelto") !== 0 && strcmp($prestamo["estado"],"devuelto") === 0) { //cambiado de devuelto a activo resto 1

                        //si no quedan libros redirigimos
                        comprobarCantidad($libro,$id);//->MODIFICAR.PHP
                        
                        //RESTAMOS 1 AL LIBRO
                        //MODIFICAR CANTIDAD
                        modificarCantidadLibro($libro["id"],$libro["numeroEjemplares"] - 1);
                 

                }
            

        } else {//se cambia de libro comprobar si se cambio el estado
            // estado viejo-devuelto           estado nuevo - devuelto
            if(strcmp($prestamo["estado"],"devuelto") === 0 && strcmp($estado,"devuelto") === 0) {
                
                //MODIFICAR DATOS

            //          estado viejo-activo                        estado nuevo - activo
            } else if (strcmp($prestamo["estado"],"devuelto") !== 0 && strcmp($estado,"devuelto") !== 0) {
                        //si no quedan unidades del nuevo libro redirigimos
                        comprobarCantidad($libro,$id);//->MODIFICAR.PHP

                        //SUMAR 1 AL LIBRO VIEJO
                        //MODIFICAR CANTIDADES
                        modificarCantidadLibro($libroViejo["id"],$libroViejo["numeroEjemplares"] + 1);

                        //RESTAR 1 AL LIBRO NUEVO
                        //MODIFICAR CANTIDADES
                        modificarCantidadLibro($libro["id"],$libro["numeroEjemplares"] - 1);

                       


            //          estado viejo-devuelto                        estado nuevo - activo
            } else if (strcmp($prestamo["estado"],"devuelto") === 0 && strcmp($estado,"devuelto") !== 0) {
                //si no quedan unidades del nuevo libro redirigimos
                comprobarCantidad($libro,$id);//->MODIFICAR.PHP

                //RESTAR 1 AL LIBRO NUEVO
                //MODIFICAR CANTIDAD
                modificarCantidadLibro($libro["id"],$libro["numeroEjemplares"] - 1);

                

            //          estado viejo-activo                        estado nuevo - devuelto
            } else if (strcmp($prestamo["estado"],"devuelto") !== 0 && strcmp($estado,"devuelto") === 0) {

                //SUMAR 1 AL LIBRO VIEJO
                //MODIFICAR CANTIDAD
                modificarCantidadLibro($libroViejo["id"],$libroViejo["numeroEjemplares"] + 1);
                
            }

        }

        //MODIFICAR PRESTAMO
        modificarPrestamo($id,$libro["id"],$usuario["id"],$fechaIni,$fechaFin,$estado);

        header("Location: index.php");
        die();

            
    }

    //formulario de añadir prestamo
    if(isset($_POST["addPrestamo"])) {

        //comprobar usuario en sesion
        if(!isset($_SESSION["usuario"])) {
            header("Location: controlador.php?error=noLogin");
            die();
        }

        $isbn=$_POST["isbn"];
        $dni=$_POST["dni"];
        $fechaIni = date('d-m-Y',strtotime($_POST["fechaInicio"]));
        $fechaFin = date('d-m-Y',strtotime($_POST["fechaFin"]));
        
        //comprobar que existen el libro y el usuario
        $libro = buscarLibro($isbn);
        $usuario = buscarUsuario($dni);

        if( $libro == false || $usuario == false ) {
            //redirigimos a index.php
            header("Location: index.php?error=datosErroneos");
            die();
        }

        if($libro["numeroEjemplares"] <= 0) {
            //redirigimos a index.php
            header("Location: index.php?error=noHayExistencias");
            die();
        }

        //insertamos datos
        addPrestamo($libro["id"],$usuario["id"],$fechaIni,$fechaFin);


        //redirigir a index.php
        header("Location: index.php");
        die();


    }

?>
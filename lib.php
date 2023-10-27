<?php
    /**
     * crear una conexion con la base de datos y la devuelve
     */
    function establecerConexion() {
        try {
            $dsn = "mysql:host=172.17.0.2;dbname=biblioteca";//en host se usa la ip de contenedor sql y su puerto interno
            $conexion = new PDO($dsn, "root", "root");
        } catch (PDOException $e){
            echo $e->getMessage();
        }

        return $conexion;
    }

    /**
     * busca todos los prestamos y los devulve en un array asociativo
     */
    function selectPrestamos() {
        //crear conexion
        $con = establecerConexion();

        //crear y ejecutar consulta
        $consulta = $con-> prepare("SELECT prestamos.id, libros.isbn, usuarios.dni, prestamos.fechaInicio, prestamos.fechaFin, prestamos.estado
        FROM biblioteca.prestamos
        inner join usuarios
        on usuarios.id = prestamos.usuarioID
        inner join libros
        on libros.id = prestamos.libroId;");
        $consulta -> setFetchMode(PDO::FETCH_ASSOC);
        $consulta ->execute();

        //guardar los arrays asociativos en prestamos
        $prestamos = $consulta->fetchAll();

        //cerrar la conexion
        $con= null;

        //devolver todos los prestamos
        return $prestamos;
    }

    function searchPrestamos($tipo , $busqueda) {
        //crear conexion
        $con = establecerConexion();
        $consulta = null;

        if(strcmp($tipo,"dni") === 0) {
            //crear y ejecutar consulta
            $consulta = $con-> prepare("SELECT prestamos.id, libros.isbn, usuarios.dni, prestamos.fechaInicio, prestamos.fechaFin, prestamos.estado
            FROM biblioteca.prestamos
            inner join usuarios
            on usuarios.id = prestamos.usuarioID
            inner join libros
            on libros.id = prestamos.libroId
            WHERE dni = ?;");
        } elseif (strcmp($tipo,"estado") === 0) {
            //crear y ejecutar consulta
            $consulta = $con-> prepare("SELECT prestamos.id, libros.isbn, usuarios.dni, prestamos.fechaInicio, prestamos.fechaFin, prestamos.estado
            FROM biblioteca.prestamos
            inner join usuarios
            on usuarios.id = prestamos.usuarioID
            inner join libros
            on libros.id = prestamos.libroId
            WHERE estado = ?;");
        }

        

        $consulta->bindValue(1,$busqueda);

        $consulta -> setFetchMode(PDO::FETCH_ASSOC);
        $consulta ->execute();

        

        //guardar los arrays asociativos en prestamos
        $prestamos = $consulta->fetchAll();

        //cerrar la conexion
        $con= null;

        //devolver todos los prestamos
        return $prestamos;
    }
?>
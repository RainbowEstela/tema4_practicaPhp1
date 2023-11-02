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

    /**
     * busca los prestamos de un tipo concreto y los devuelve
     */
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

    /**
     * borra el prestamo de la id pasada
     */
    function borrarPrestamo($id) {
        //establecer conexion
        $con = establecerConexion();

        $consulta = $con->prepare("DELETE FROM prestamos where id=?;");
        $consulta->bindValue(1,$id);

        $consulta->execute();

        //cerramos conexion
        $con= null;
    }

    /**
     * busca un prestamo pasandole una id
     */
    function buscarPrestamo($id) {
        //establecer conexion
        $con = establecerConexion();

        //crear la consulta
        $consulta = $con->prepare("SELECT prestamos.id, libros.isbn, usuarios.dni, prestamos.fechaInicio, prestamos.fechaFin, prestamos.estado
        FROM biblioteca.prestamos
        right join usuarios
        on usuarios.id = prestamos.usuarioID
        right join libros
        on libros.id = prestamos.libroId
        WHERE prestamos.id = ?; ");

        $consulta->bindParam(1,$id);

        $consulta -> setFetchMode(PDO::FETCH_ASSOC);
        $consulta ->execute();

        $prestamo = $consulta->fetch();

        $con = null;

        return $prestamo;

    }

    /**
     * Busca un libro y lo devuelve
     */
    function buscarLibro($isbn) {
        //establecer conexion
        $con = establecerConexion();

        //crear la consulta 
        $consulta = $con->prepare("SELECT * FROM libros WHERE isbn = ? limit 1;");

        $consulta->bindParam(1,$isbn);

        $consulta -> setFetchMode(PDO::FETCH_ASSOC);
        $consulta ->execute();

        $libro = $consulta->fetch();

        $con = null;

        return $libro;

    }

    /**
     * busca y devuelve un usuario con el mismo dni
     */
    function buscarUsuario($dni) {
        //establecer conexion
        $con = establecerConexion();

        //crear la consulta 
        $consulta = $con->prepare("SELECT * 
        from usuarios
        where dni = ?
        limit 1;");

        $consulta->bindParam(1,$dni);

        $consulta -> setFetchMode(PDO::FETCH_ASSOC);
        $consulta ->execute();

        $usuario = $consulta->fetch();

        $con = null;

        return $usuario;
        
    }

    /**
     * modifica la cantidad de unideades de un libro
     */
    function modificarCantidadLibro($id,$cantidad) {
        //establecer conexion
        $con = establecerConexion();

        $consulta= $con->prepare("UPDATE libros SET numeroEjemplares = ? WHERE id = ?");

        $consulta->bindParam(1,$cantidad);
        $consulta->bindParam(2,$id);

        $consulta->execute();

        $con = null;

    }

    function modificarPrestamo($id,$libroId,$usuarioId,$fechaInicio,$fechaFin,$estado) {
        //establecer conexion
        $con = establecerConexion();

        $consulta = $con->prepare("UPDATE prestamos SET libroId = ?, usuarioID = ?, fechaInicio = ?, fechaFin = ? , estado = ? WHERE id = ?");
        $consulta->bindParam(1,$libroId);
        $consulta->bindParam(2,$usuarioId);
        $consulta->bindParam(3,$fechaInicio);
        $consulta->bindParam(4,$fechaFin);
        $consulta->bindParam(5,$estado);
        $consulta->bindParam(6,$id);

        $consulta->execute();

        $con = null;
    }

    function addPrestamo($libroId,$usuarioId,$fechaInicio,$fechaFin) {
        //establecer conexion
        $con = establecerConexion();

        try {

            $con->beginTransaction();

            $consulta = $con->prepare("INSERT INTO prestamos (libroId,usuarioID,fechaInicio,fechaFin,estado) VALUES (?,?,?,?,'activo')");
            $consulta->bindParam(1,$libroId);
            $consulta->bindParam(2,$usuarioId);
            $consulta->bindParam(3,$fechaInicio);
            $consulta->bindParam(4,$fechaFin);

            $consulta->execute();

            $con->commit();


        } catch (Exception $e) {
            $con->rollBack();
        }

        $con = null;
    }
?>
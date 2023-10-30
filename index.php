<?php
  include_once("cabecera.php");
?>

      

      <h2>Prestamos</h2>
      <div class="table-responsive">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">ISBN</th>
              <th scope="col">DNI</th>
              <th scope="col">fecha inicio</th>
              <th scope="col">fecha fin</th>
              <th scope="col">estado</th>
              <th scope="col">accion</th>
            </tr>
          </thead>
          <tbody>
<?php
    //declaramos la varaible prestamos
    $prestamos = [];
    
    //COMPROBAR SI SE HA BUSCADO ALGO
    if(isset($_POST["filtrarPrestamos"])) {
      //COMPROBAR SI LA BUSQUEDA ESTA VACIA
      if($_POST["busqueda"] != null) {
        //buscamos los prestamos que cumplan con los datos
        $prestamos = searchPrestamos($_POST["tipo"] , $_POST["busqueda"]);


      } else {
        //si esta vacia seleccionamos todos los prestamos
        $prestamos = selectPrestamos();
      }
    } else {
      $prestamos = selectPrestamos();
    }
  
    

  foreach($prestamos as $prestamo) {
    echo '
    <tr>
      <td>'.$prestamo["isbn"].'</td>
      <td>'.$prestamo["dni"].'</td>
      <td>'.$prestamo["fechaInicio"].'</td>
      <td>'.$prestamo["fechaFin"].'</td>
      <td>'.$prestamo["estado"].'</td>
      <td>
        <a href="modificar.php?id='.$prestamo["id"].'"><button class="btn btn-success"><img src="./img/configuracion.png" alt="modificar" width="20px">
        </button></a> 
        <a href="controlador.php?accion=borrarPrestamo&id='.$prestamo["id"].'"><button class="btn btn-danger">X</button></a> 
      </td>
    </tr>
    ';
  }
?>


          </tbody>
        </table>
      </div>

<?php
  include_once("pie.php");
?>
<?php
    include_once("cabecera.php");
    $prestamo = buscarPrestamo($_GET["id"]);
?>

<form action="controlador.php" method="POST">
    <h1 class="h3 mb-3 fw-normal">Datos del Prestamo</h1>
    <div class="col-5">
        <label for="isbn" class="form-label">ISBN</label>
        <input type="text" class="form-control" id="isbn" name="isbn" placeholder="000000000-0" value="<?= $prestamo["isbn"];?>" required>
    </div>
    <div class="col-5">
        <label for="dni" class="form-label">DNI</label>
        <input type="text" class="form-control" id="dni" name="dni" placeholder="0000-0000" value="<?= $prestamo["dni"];?>" required>
    </div>
    <div class="col-5">
        <label for="fechaInicio" class="form-label">Fecha Inicio</label>
        <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" value="<?=date('Y-m-d',strtotime($prestamo["fechaInicio"])) ;?>" >
    </div>
    <div class="col-5">
        <label for="fechaFin" class="form-label">Fecha Fin</label>
        <input type="date" class="form-control" id="fechaFin" name="fechaFin" value="<?=date('Y-m-d',strtotime($prestamo["fechaFin"]));?>" >
    </div>
    
    <div class="col-5">
        <label for="estado" class="form-label">Estado</label>
        <select class="form-select" id="estado" name="estado" required>
            <?php
                $opciones = [
                    [
                        "value" => "activo",
                        "valueOption" => "activo",
                        "text" => "Activo",
                    ],

                    [
                        "value" => "devuelto",
                        "valueOption" => "devuelto",
                        "text" => "Devuelto",
                    ],

                    [
                        "value" => "sobrepasado1Mes",
                        "valueOption" => "sobrepasado1Mes",
                        "text" => "Sobrepasado 1 mes",
                    ],

                    [
                        "value"=> "sobrepasado1Año",
                        "valueOption" => "sobrepasado1Anio",
                        "text" => "Sobrepasado 1 año",
                    ]
                ];

                foreach( $opciones as $opt) {
                    if(strcmp($prestamo["estado"],$opt["value"]) === 0) {
                        echo'<option value="'.$opt["valueOption"].'" selected>'.$opt["text"].'</option>';
                    } else {
                        echo'<option value="'.$opt["valueOption"].'">'.$opt["text"].'</option>';
                    }
                }


            ?>
        </select> 
    </div>
    <?php
    echo '
        <input type="hidden" name="id" value="'.$_GET["id"].'">
    ';
    ?>

    <button class="btn btn-primary w-30 py-2 my-3" type="submit" name="modificarPrestamo">Modificar</button>
  </form>


<?php
  include_once("pie.php");
?>
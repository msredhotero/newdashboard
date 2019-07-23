<?php

session_start();

if (!isset($_SESSION['usua_aif']))
{
	echo '<!DOCTYPE html>
   <html lang="es">
       <head>
           <meta charset="utf-8" />
           <title>Gestión: AIF</title>
       </head>
       <body>

         <h3>Informe: </h3>
       </body>
   </html>';
} else {
   include ('../../includes/funcionesArbitros.php');
   $serviciosArbitros 	= new ServiciosArbitros();

   $id = $_GET['informe'];

   $partido = $serviciosArbitros->traerPartidosPorArbitrosPartido($id);

   $resultado = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($id);

   $informe = str_replace(PHP_EOL, '******************', mysql_result($resultado,0,'observaciones'));
   $categoria = mysql_result($partido,0,'categoria');
   $division = mysql_result($partido,0,'division');
   $partido = mysql_result($partido,0,'partido');
   $fecha = mysql_result($partido,0,'fechajuego');
   $arbitro = $_GET['arbitro'];

   ?>
   <!DOCTYPE html>
   <html lang="es">
       <head>
           <meta charset="utf-8" />
           <title>Gestión: AIF</title>
       </head>
       <body>
         <div align="center">
            <h1>Partido: <?php echo utf8_encode($partido); ?></h1>
            <h2>Categoria: <?php echo utf8_encode($categoria); ?></h2>
            <h2>Division: <?php echo utf8_encode($division); ?></h2>
            <h2>Arbitro: <?php echo utf8_encode($arbitro); ?></h2>
            <h4>Fecha: <?php echo utf8_encode($fecha); ?></h4>
         </div>

         <h3>Informe: <?php echo utf8_encode(str_replace('******************', '<br>', $informe)); ?></h3>
       </body>
   </html>



<?php }  ?>

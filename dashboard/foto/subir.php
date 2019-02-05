<?php

session_start();

$servidorCarpeta = 'aifzndesarrollo';

if (!isset($_SESSION['usua_aif']))
{
	header('Location: ../../error.php');
} else {

    include ('../../includes/funciones.php');
    include ('../../includes/funcionesUsuarios.php');
    include ('../../includes/funcionesHTML.php');
    include ('../../includes/funcionesReferencias.php');
    include ('../../includes/base.php');

    $serviciosFunciones 	= new Servicios();
    $serviciosUsuario 		= new ServiciosUsuarios();
    $serviciosHTML 			= new ServiciosHTML();
    $serviciosReferencias 	= new ServiciosReferencias();

    $archivo = $_FILES['file'];

    $templocation = $archivo['tmp_name'];

    $name = $serviciosReferencias->sanear_string(str_replace(' ','',basename($archivo['name'])));


    if (!$templocation) {
        die('No ha seleccionado ningun archivo');
    }

	 $noentrar = '../../imagenes/index.php';

	 $resFoto = $serviciosReferencias->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($_POST['idjugador'], $_POST['iddocumentacion']);

	 if (mysql_num_rows($resFoto) > 0) {
		 // borro carpeta y archivo
		 $resEliminarArchivo = $serviciosReferencias->eliminarFotoJugadoresID($_POST['iddocumentacion'], $_POST['idjugador']);

		 // lo borro id de la tabla
		 $resEliminar = $serviciosReferencias->eliminarDocumentacionjugadorimagenes(mysql_result($resFoto,0,0));

	 }



	 $imagen = $serviciosReferencias->sanear_string(basename($archivo['name']));
	 $type = $archivo["type"];

	 $resDocumentacionImagen = $serviciosReferencias->insertarDocumentacionjugadorimagenes($_POST['iddocumentacion'],0,$imagen,$type,1,$_POST['idjugador']);

	 $iddocumentacionjugadorimagen = $resDocumentacionImagen;


	 // desarrollo
	 $dir_destino = './../../../'.$servidorCarpeta.'/data/'.$iddocumentacionjugadorimagen.'/';

	 // produccion
	 //$dir_destino = 'https://www.saupureinconsulting.com.ar/aifzn/data/'.mysql_result($resFoto,0,'iddocumentacionjugadorimagen').'/';

	 $imagen_subida = $dir_destino.$name;

	 // desarrollo
	 $nuevo_noentrar = './../../../'.$servidorCarpeta.'/'.'index.php';

	 // produccion
	 // $nuevo_noentrar = 'https://www.saupureinconsulting.com.ar/aifzn/data/'.$_SESSION['idclub_aif'].'/'.'index.php';


    if (!file_exists($dir_destino)) {
        mkdir($dir_destino, 0777);
    }



	if (move_uploaded_file($templocation, $imagen_subida)) {

		echo "Archivo guardado correctamente";
	} else {
		echo "Error al guardar el archivo ";
	}



}

?>

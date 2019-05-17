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
		include ('../../includes/funcionesArbitros.php');

		include '../../includes/ImageResize.php';
		include '../../includes/ImageResizeException.php';

		$serviciosFunciones 	= new Servicios();
		$serviciosUsuario 		= new ServiciosUsuarios();
		$serviciosHTML 			= new ServiciosHTML();
		$serviciosReferencias 	= new ServiciosReferencias();
		$serviciosArbitros 	= new ServiciosArbitros();

		$archivo = $_FILES['file'];

		$templocation = $archivo['tmp_name'];

		$name = $serviciosReferencias->sanear_string(str_replace(' ','',basename($archivo['name'])));


		if (!$templocation) {
		die('No ha seleccionado ningun archivo');
		}

		$noentrar = '../../imagenes/index.php';

		$idfixture = $_POST['idfixture'];
		$idarbitro = $_POST['idarbitro'];

		$resPlanilla = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($idfixture);

		$archivoAnterior = mysql_result($resPlanilla,0,'imagen2');

		$imagen = $serviciosReferencias->sanear_string(basename($archivo['name']));
		$type = $archivo["type"];


		// desarrollo
		$dir_destino = '../../arbitros/'.$idfixture.'/';

		// produccion
		//$dir_destino = 'https://www.saupureinconsulting.com.ar/aifzn/data/'.mysql_result($resFoto,0,'iddocumentacionjugadorimagen').'/';

		$imagen_subida = $dir_destino.'/2/'.$name;

		// desarrollo
		$nuevo_noentrar = '../../arbitros/index.php';

		// produccion
		// $nuevo_noentrar = 'https://www.saupureinconsulting.com.ar/aifzn/data/'.$_SESSION['idclub_aif'].'/'.'index.php';

		if (!file_exists($dir_destino)) {
			mkdir($dir_destino, 0777);
		}

		if (!file_exists($dir_destino.'/2/')) {
			mkdir($dir_destino.'/2/', 0777);
		}



		if (move_uploaded_file($templocation, $imagen_subida)) {
			$pos = strpos( strtolower($type), 'pdf');
			/*
			if ($pos === false) {
				$image = new \Gumlet\ImageResize($imagen_subida);
				$image->scale(50);
				$image->save($imagen_subida);
			}
			*/
			// update a la tabla dbplanillasarbitros
			$serviciosArbitros->actualizarArchivoPlanillaComplemento(mysql_result($resPlanilla,0,0),$name,$type);

			//borro el archivo anterior
			if ($archivoAnterior != '') {
				unlink($dir_destino.'/2/'.$archivoAnterior);
			}



			echo "Archivo guardado correctamente";
		} else {
			echo "Error al guardar el archivo";
		}



	}

	?>

<?php

session_start();

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

    $club = $serviciosReferencias->traerCountriesPorId($_SESSION['idclub_aif']);

    $archivo = $_FILES['file'];

    $templocation = $archivo['tmp_name'];

    $name = $serviciosReferencias->sanear_string(str_replace(' ','',basename($archivo['name'])));

    if (!$templocation) {
        die('No ha seleccionado ningun archivo');
    }


    $dir_destino = '../../archivos/countries/'.$_SESSION['idclub_aif'].'/';
    $imagen_subida = $dir_destino.$name;
    
    $noentrar = '../../imagenes/index.php';
    $nuevo_noentrar = '../../archivos/countries/'.$_SESSION['idclub_aif'].'/'.'index.php';

    $serviciosReferencias->borrarArchivos($dir_destino);
    
    if (!file_exists($dir_destino)) {
        mkdir($dir_destino, 0777);
    }

    

    if (move_uploaded_file($templocation, $imagen_subida)) {
        $serviciosReferencias->modificarImagenCuontries($_SESSION['idclub_aif'], $name);
        echo "Archivo guardado correctamente";
    } else {
        echo "Error al guardar el archivo";
    }



}

?>
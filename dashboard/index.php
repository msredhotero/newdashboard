<?php

session_start();

if (!isset($_SESSION['usua_aif']))
{
	header('Location: ../error.php');
} else {


include ('../includes/funcionesUsuarios.php');
include ('../includes/funcionesHTML.php');
include ('../includes/funciones.php');
include ('../includes/funcionesReferencias.php');
include ('../includes/base.php');

$serviciosUsuario = new ServiciosUsuarios();
$serviciosHTML = new ServiciosHTML();
$serviciosFunciones = new Servicios();
$serviciosReferencias 	= new ServiciosReferencias();
$baseHTML = new BaseHTML();

$fecha = date('Y-m-d');

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_aif'],"Dashboard",$_SESSION['refroll_aif'],'');

$configuracion = $serviciosReferencias->traerConfiguracion();

$tituloWeb = mysql_result($configuracion,0,'sistema');

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a>';
/////////////////////// Opciones pagina ///////////////////////////////////////////////
$singular = "Orden";

$plural = "Ordenes";

$eliminar = "eliminarOrdenes";

$insertar = "insertarOrdenes";

//$tituloWeb = "Gestión: Talleres";
//////////////////////// Fin opciones ////////////////////////////////////////////////


/////////////////////// Opciones para la creacion del formulario  /////////////////////

/////////////////////// Opciones para la creacion del view  patente,refmodelo,reftipovehiculo,anio/////////////////////
$cabeceras 		= "	<th>Ingreso</th>
					<th>Dueño</th>
					<th>Vehiculo</th>
					<th>Hora Entrada</th>
					<th>Hora Salida</th>
					<th>Usuario</th>
					<th>Estado</th>";

//////////////////////////////////////////////  FIN de los opciones //////////////////////////
/*
$lstCargados 	= $serviciosFunciones->camposTablaView($cabeceras,$serviciosReferencias->traerTurnosGridPorEstadoIn('3,4,5'),93);
$lstCargadosMora = $serviciosFunciones->camposTablaView($cabeceras,$serviciosReferencias->traerTurnosGridPorEstadoIn('1'),93);
$lstCargadosCancelados = $serviciosFunciones->camposTablaView($cabeceras,$serviciosReferencias->traerTurnosGridPorEstadoIn('2'),93);

$resEstado 	= $serviciosReferencias->traerEstados();
$cadRefEstado 	= $serviciosFunciones->devolverSelectBox($resEstado,array(1),'');
*/
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?php echo $tituloWeb; ?></title>
    <!-- Favicon-->
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <?php echo $baseHTML->cargarArchivosCSS('../'); ?>

</head>

<body class="theme-red">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Search Bar -->
    <div class="search-bar">
        <div class="search-icon">
            <i class="material-icons">search</i>
        </div>
        <input type="text" placeholder="Ingrese palabras...">
        <div class="close-search">
            <i class="material-icons">close</i>
        </div>
    </div>
    <!-- #END# Search Bar -->
    <!-- Top Bar -->
    <?php echo $baseHTML->cargarNAV($breadCumbs); ?>
    <!-- #Top Bar -->
    <?php echo $baseHTML->cargarSECTION($_SESSION['usua_aif'], $_SESSION['nombre_aif'], str_replace('..','../dashboard',$resMenu)); ?>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
        </div>
    </section>

    <?php echo $baseHTML->cargarArchivosJS('../'); ?>

    <script>

    </script>
</body>
<?php } ?>
</html>

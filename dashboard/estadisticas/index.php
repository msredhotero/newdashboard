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

include ('../../includes/funcionesArbitros.php');

$serviciosFunciones 	= new Servicios();
$serviciosUsuario 		= new ServiciosUsuarios();
$serviciosHTML 			= new ServiciosHTML();
$serviciosReferencias 	= new ServiciosReferencias();
$baseHTML = new BaseHTML();

$serviciosArbitros 	= new ServiciosArbitros();

//*** SEGURIDAD ****/
include ('../../includes/funcionesSeguridad.php');
$serviciosSeguridad = new ServiciosSeguridad();
$serviciosSeguridad->seguridadRuta($_SESSION['refroll_aif'], '../estadisticas/');
//*** FIN  ****/

$fecha = date('Y-m-d');

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_aif'],"Estadisticas",$_SESSION['refroll_aif'],$_SESSION['email_aif']);

$configuracion = $serviciosReferencias->traerConfiguracion();

$tituloWeb = mysql_result($configuracion,0,'sistema');

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a>';

$id = $_GET['id'];

$partido = $serviciosArbitros->traerPartidosPorArbitrosPartido($id);
$partidoAux = $serviciosArbitros->traerPartidosPorArbitrosPartido($id);

$resultado = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($id);


/////////////////////// Opciones pagina ///////////////////////////////////////////////
$singular = "Pre-Estadistica";

$plural = "Pre-Estadistica";

$eliminar = "eliminarPlanillasarbitros";

$insertar = "insertarPlanillasarbitros";

$tituloWeb = "Gestión: AIF";
//////////////////////// Fin opciones ////////////////////////////////////////////////

// si ya cargue el partido
if (mysql_num_rows($resultado) > 0) {
	$idPlanilla = mysql_result($resultado,0,0);

	$goleslocal = mysql_result($resultado,0,'goleslocal');
	$amarillaslocal = mysql_result($resultado,0,'amarillaslocal');
	$expulsadoslocal = mysql_result($resultado,0,'expulsadoslocal');
	$informadoslocal = mysql_result($resultado,0,'informadoslocal');
	$dobleamarillaslocal = mysql_result($resultado,0,'dobleamarillaslocal');
	$cantidadjugadoreslocal = mysql_result($resultado,0,'cantidadjugadoreslocal');

	$golesvisitante = mysql_result($resultado,0,'golesvisitante');
	$amarillasvisitante = mysql_result($resultado,0,'amarillasvisitante');
	$expulsadosvisitante = mysql_result($resultado,0,'expulsadosvisitante');
	$informadosvisitante = mysql_result($resultado,0,'informadosvisitante');
	$dobleamarillasvisitante = mysql_result($resultado,0,'dobleamarillasvisitante');
	$cantidadjugadoresvisitante = mysql_result($resultado,0,'cantidadjugadoresvisitante');

} else {
	//si todavia no cargue el partido
	$idPlanilla = $serviciosArbitros->insertarPlanillasarbitrosCorto($id,$_SESSION['idarbitro_aif']);

	//die(var_dump($idPlanilla));
	$goleslocal = 0;
	$amarillaslocal = 0;
	$expulsadoslocal = 0;
	$informadoslocal = 0;
	$dobleamarillaslocal = 0;
	$cantidadjugadoreslocal = 0;

	$golesvisitante = 0;
	$amarillasvisitante = 0;
	$expulsadosvisitante = 0;
	$informadosvisitante = 0;
	$dobleamarillasvisitante = 0;
	$cantidadjugadoresvisitante = 0;

	$resultado = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($id);
}

// Ruta del directorio donde están los archivos
$path  = '../../arbitros/'.$id;

if (!file_exists($path)) {
	mkdir($path, 0777);
}

$pathPlanilla  = '../../arbitros/'.$id.'/1';

if (!file_exists($pathPlanilla)) {
	mkdir($pathPlanilla, 0777);
}

$pathPlanillaComplemento  = '../../arbitros/'.$id.'/2';

if (!file_exists($pathPlanillaComplemento)) {
	mkdir($pathPlanillaComplemento, 0777);
}
// Arreglo con todos los nombres de los archivos
$filesPlanilla = array_diff(scandir($pathPlanilla), array('.', '..'));
$filesComplemento = array_diff(scandir($pathPlanillaComplemento), array('.', '..'));

/////////////////////// Opciones para la creacion del formulario  /////////////////////
$tabla 			= "dbplanillasarbitros";

$lblCambio	 	= array("reffixture","refarbitros","refestadospartidos","goleslocal","golesvisitante","dobleamarillas","refestados");
$lblreemplazo	= array("Partido","Arbitro","Estado","Goles Local","Goles Visitantes","Doble Amarillas","Estado Planilla");

$resVar1 = $serviciosArbitros->traerFixturePorId($id);
$cadRef 	= $serviciosFunciones->devolverSelectBoxActivo($resVar1,array(1),'Partido N°: ', $id);

$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', mysql_result($resultado,0,'refestadospartidos'));

$resAr = $serviciosArbitros->traerArbitrosPorId($_SESSION['idarbitro_aif']);
$cadAr = $serviciosFunciones->devolverSelectBoxActivo($resAr,array(1),'', $_SESSION['idarbitro_aif']);

$resEstadoActual = mysql_result($resultado,0,'refestados');

$refEstadoPlanilla = $serviciosArbitros->traerEstadosPorIn('2');
$cadEP = $serviciosFunciones->devolverSelectBoxActivo($refEstadoPlanilla,array(1),'', $resEstadoActual);

//die(var_dump($cadEstados));

$refdescripcion = array(0=>$cadRef,1=>$cadAr,2=>$cadEstados, 3=>$cadEP);
$refCampo 	=  array("reffixture","refarbitros","refestadospartidos","refestados");

$idTabla = 'idplanillaarbitro';
$tabla = 'dbplanillasarbitros';
$modificar = 'modificarPlanillasarbitros';

$formulario 	= $serviciosFunciones->camposTablaModificar($idPlanilla, $idTabla, $modificar,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);
//////////////////////////////////////////////  FIN de los opciones //////////////////////////



$resTemporadas = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);
} else {
    $ultimaTemporada = 0;
}

if ($resEstadoActual == 2) {
	$readonly = 'readonly';
} else {
	$readonly = '';
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?php echo $tituloWeb; ?></title>
    <!-- Favicon-->
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

	<?php echo $baseHTML->cargarArchivosCSS('../../'); ?>

	<link href="../../plugins/waitme/waitMe.css" rel="stylesheet" />
	<link href="../../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

	<!-- VUE JS -->
	<script src="../../js/vue.min.js"></script>

	<!-- axios -->
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

	<script src="https://unpkg.com/vue-swal"></script>

	<script src="../../js/image-compressor.js"></script>


	<!-- Bootstrap Material Datetime Picker Css -->
    <link href="../../plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

	<!-- Dropzone Css -->
    <link href="../../plugins/dropzone/dropzone.css" rel="stylesheet">

	 <style>
        .alert > i{ vertical-align: middle !important; }
		  .pdfobject-container { height: 30rem; border: 1rem solid rgba(0,0,0,.1); }

		  .thumbnail2 {
		    display: block;
		    padding: 4px;
		    margin-bottom: 20px;
		    line-height: 1.42857143;
		    background-color: #fff;
		    border: 1px solid #ddd;
		    border-radius: 4px;
		    -webkit-transition: border .2s ease-in-out;
		    -o-transition: border .2s ease-in-out;
		    transition: border .2s ease-in-out;
			 text-align: center;
		}


	</style>


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
		<p>Cargando...</p>
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
<?php echo $baseHTML->cargarSECTION($_SESSION['usua_aif'], $_SESSION['nombre_aif'], $resMenu,'../../'); ?>
<main id="app">
<section class="content" style="margin-top:-15px;">

	<div class="container-fluid">
		<div class="row clearfix">

        	<div class="row">


				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="card ">
						<div class="header bg-blue">
							<h2>
								Pre-Estadisticas
							</h2>
							<ul class="header-dropdown m-r--5">
								<li class="dropdown">
									<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
										<i class="material-icons">more_vert</i>
									</a>
									<ul class="dropdown-menu pull-right">
										<li><a href="javascript:void(0);" @click="showModal = true">Realizar Consulta</a></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="body table-responsive">
							<form class="formulario frmNuevo" role="form" id="sign_in">
								<div class="row">
									<div class="col-xs-12 col-md-12 col-lg-12">
										<div class="alert alert-danger">
											<p>Recuerda cargar todos los datos para continuar con la carga de la estadistica, por favor.</p>
										</div>
									</div>
									<div class="col-xs-12 col-md-12 col-lg-12">
										<div class="alert alert-success">
											<?php
											while ($row = mysql_fetch_array($partidoAux)) {
												echo 'N°: '.$row['idfixture'].' | Fecha Juego: '.$row['fechajuego'].' | Fecha: '.$row['fecha'].' | Partido: '.$row['partido'].' | Categoria: '.$row['categoria'].' | Division: '.$row['division'];
											}
											?>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="row">
									   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									   	<label for="reffixture" class="control-label" style="text-align:left">Partido</label>
									   	<select class="form-control show-tick" id="reffixture" name="reffixture"><?php echo $cadRef; ?></select>

									   </div>



									   <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									   	<label for="refarbitros" class="control-label" style="text-align:left">Arbitro</label>
									   	<select class="form-control show-tick" id="refarbitros" name="refarbitros"><?php echo $cadAr; ?></select>

									   </div>
									</div>

									   <div class="row">
									      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="display:block">

									         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg bg-blue" style="display:block">
									         	<label class="form-label">Datos Local</label>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Goles</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $goleslocal; ?>" type="text" class="form-control txtCarga" id="goleslocal" name="goleslocal" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Amarillas</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $amarillaslocal; ?>" type="text" class="form-control txtCarga" id="amarillaslocal" name="amarillaslocal" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Expulsados</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $expulsadoslocal; ?>" type="text" class="form-control txtCarga" id="expulsadoslocal" name="expulsadoslocal" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Informados</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $informadoslocal; ?>" type="text" class="form-control txtCarga" id="informadoslocal" name="informadoslocal" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Doble Amarillas</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $dobleamarillaslocal; ?>" type="text" class="form-control txtCarga" id="dobleamarillaslocal" name="dobleamarillaslocal" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Cantidad Jugadores</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $cantidadjugadoreslocal; ?>" type="text" class="form-control txtCarga" id="cantidadjugadoreslocal" name="cantidadjugadoreslocal" required/>

									         		</div>
									         	</div>
									         </div>

									      </div>
									      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6" style="display:block">
									         <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg bg-red" style="display:block">
									         	<label class="form-label">Datos Visitante</label>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Goles</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $golesvisitante; ?>" type="text" class="form-control txtCarga" id="golesvisitante" name="golesvisitante" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Amarillas</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $amarillasvisitante; ?>" type="text" class="form-control txtCarga" id="amarillasvisitante" name="amarillasvisitante" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Expulsados</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $expulsadosvisitante; ?>" type="text" class="form-control txtCarga" id="expulsadosvisitante" name="expulsadosvisitante" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Informados</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $informadosvisitante; ?>" type="text" class="form-control txtCarga" id="informadosvisitante" name="informadosvisitante" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Doble Amarillas</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $dobleamarillasvisitante; ?>" type="text" class="form-control txtCarga" id="dobleamarillasvisitante" name="dobleamarillasvisitante" required/>

									         		</div>
									         	</div>
									         </div>


									         <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									         	<label class="form-label">Cantidad Jugadores</label>
									         	<div class="form-group">
									         		<div class="form-line">
									         			<input <?php echo $readonly; ?> value="<?php echo $cantidadjugadoresvisitante; ?>" type="text" class="form-control txtCarga" id="cantidadjugadoresvisitante" name="cantidadjugadoresvisitante" required/>

									         		</div>
									         	</div>
									         </div>
									      </div>

									   </div>
									   <div class="row">
									      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									      	<label for="refestadospartidos" class="control-label" style="text-align:left">Estado</label>
									      	<select class="form-control show-tick" id="refestadospartidos" name="refestadospartidos"><?php echo $cadEstados; ?></select>

									      </div>



									      <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 margTop" style="display:block">
									      	<label for="refestados" class="control-label" style="text-align:left">Estado Planilla</label>
									      	<select class="form-control show-tick" id="refestados" name="refestados"><?php echo $cadEP; ?></select>

									      </div>
									   </div>

									   <div class="row">
											<div class="form-group col-md-12 col-lg-12 col-sm-12 col-xs-12" style="display:block">
												<label for="tieneincidencias" class="control-label" style="text-align:left">Lleva informe arbitral?.</label>
												<div class="row clearfix">
													<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
														<input type="checkbox" id="tieneincidencias" class="filled-in">
			                                 <label for="tieneincidencias">Si - No</label>
			                                 <br>
													</div>
												</div>
												<p style="color: #F00;">** Recuerde que primero debera guardar el partido sino perdera lo escrito</p>
												<button type="button" class="btn bg-green waves-effect btnImprmirInforme">
													<i class="material-icons">print</i>
													<span>IMPRIMIR</span>
												</button>

											</div>
									      <div class="form-group col-md-12 col-lg-12 col-sm-12 col-xs-12" style="display:block">
									      	<label for="observaciones" class="control-label" style="text-align:left">Escriba aqui el informe:</label>
									      	<div class="input-group col-md-12">
									      		<textarea type="text" rows="10" cols="6" class="form-control" id="observaciones" name="observaciones" placeholder="Ingrese el Observaciones..." required><?php echo mysql_result($resultado,0,'observaciones'); ?></textarea>
									      	</div>

									      </div>
									   </div>

									   <br><br><input type="hidden" id="accion" name="accion" value="modificarPlanillasarbitros"/><input type="hidden" id="id" name="id" value="<?php echo $id; ?>"/>
								</div>

								<div class="row">
									<div class="col-xs-12 col-md-12 col-lg-12">
										<div class="alert alert-info">
											<p>Para continuar debe GUARDAR la planilla. (caso contrario seguira en esta parte).</p>
										</div>
									</div>
									<div class="col-xs-12 col-md-12 col-lg-12">
										<?php
										if (mysql_result($resultado,0,'refestados') == 2) {
										?>
										<button type="submit" class="btn btn-info waves-effect btnGuardar">
											<i class="material-icons">save</i>
											<span>GUARDAR</span>
									   </button>

										<button type="button" class="btn btn-warning waves-effect btnEstadistica">
											<i class="material-icons">insert_chart</i>
											<span>CARGAR DETALLE DE ESTADISTICA</span>
										</button>
										<?php
										} else {
										?>


										<button type="submit" class="btn btn-info waves-effect btnGuardar">
											<i class="material-icons">save</i>
											<span>GUARDAR</span>
									   </button>

										<button type="button" class="btn btn-warning waves-effect btnEstadistica" style="display:none;">
											<i class="material-icons">insert_chart</i>
											<span>CARGAR DETALLE DE ESTADISTICA</span>
										</button>

										<?php } ?>


							   	</div>
								</div>

								<div class="row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										<?php
										if (count($filesPlanilla)<1) {
											$escondeAlertPlanilla = 'block';
										} else {
											$escondeAlertPlanilla = 'none';
										}?>
										<div class="alert alert-danger lblPlanilla" style="display: <?php echo $escondeAlertPlanilla; ?>;">
											<h4>Debe cargar la imagen de la PLANILLA aqui abajo.</h4>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
										</div>

									</div>
									<div class="col-xs-6 col-md-6 col-lg-6">
										<?php
										if (count($filesComplemento)<1) {
											$escondeAlertComplemento = '';
										} else {
											$escondeAlertComplemento = 'none';
										}?>
										<div class="alert alert-danger lblComplemento" style="display: <?php echo $escondeAlertComplemento; ?>;">
											<h4>Debe cargar la imagen del COMPLEMENTO INFORME aqui abajo.</h4>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
											<i class="material-icons">arrow_downward</i>
										</div>

							   	</div>
								</div>

							</form>

						</div>
					</div>
				</div>
			</div>


		</div>


		<div class="row clearfix subirImagen">
			<div class="row">
				<div class="col-xs-6 col-md-6 col-lg-6">
					<a href="javascript:void(0);" class="thumbnail timagen1">
						<img class="img-responsive">
					</a>
					<div id="example1"></div>

				</div>
				<div class="col-xs-6 col-md-6 col-lg-6">
					<a href="javascript:void(0);" class="thumbnail timagen2">
						<img class="img-responsive2">
					</a>
					<div id="example2"></div>

				</div>

			</div>
			<div class="row">

				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
					<div class="card">
						<div class="header">
							<h2>
								CARGA LA PLANILLA AQUI
							</h2>
							<ul class="header-dropdown m-r--5">
								<li class="dropdown">
									<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
										<i class="material-icons">more_vert</i>
									</a>

								</li>
							</ul>
						</div>
						<div class="body">

							<form action="subir.php" id="frmFileUpload" class="dropzone" method="post" enctype="multipart/form-data">
								<div class="dz-message">
									<div class="drag-icon-cph">
										<i class="material-icons">touch_app</i>
									</div>
									<h3>Arrastre y suelte una imagen O PDF aqui o haga click y busque una imagen en su ordenador.</h3>

								</div>
								<div class="fallback">

									<input name="file" type="file" id="archivos" />
									<input type="hidden" id="idfixture" name="idfixture" value="<?php echo $id; ?>" />



								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
					<div class="card">
						<div class="header">
							<h2>
								CARGA EL COMPLEMENTO INFORME AQUI
							</h2>
							<ul class="header-dropdown m-r--5">
								<li class="dropdown">
									<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
										<i class="material-icons">more_vert</i>
									</a>

								</li>
							</ul>
						</div>
						<div class="body">

							<form action="subircomplemento.php" id="frmFileUpload2" class="dropzone" method="post" enctype="multipart/form-data">
								<div class="dz-message">
									<div class="drag-icon-cph">
										<i class="material-icons">touch_app</i>
									</div>
									<h3>Arrastre y suelte una imagen O PDF aqui o haga click y busque una imagen en su ordenador.</h3>

								</div>
								<div class="fallback">

									<input name="file" type="file" id="archivos2" />
									<input type="hidden" id="idfixture" name="idfixture" value="<?php echo $id; ?>" />



								</div>
							</form>
						</div>
					</div>
				</div>
			</div>

		</div>




		</div>

	</div>




</section>

<?php
if (count($filesPlanilla)<1) {
?>
<!-- Modal -->
<div class="modal fade" id="myModalPlanilla" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Estado de la Planilla</h4>
			</div>
		<div class="modal-body">
			<h1>Aun no se cargo la imagen de la planilla</h1>
			<h3>Recuerde que debe cargar la planilla.</h3>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		</div>
		</div>

	</div>
</div>
<?php }  ?>

<?php
if (count($filesComplemento)<1) {
?>
<!-- Modal -->
<div class="modal fade" id="myModalComplemento" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Estado del Complemento</h4>
			</div>
		<div class="modal-body">
			<h1>Aun no se cargo la imagen del Complemento</h1>
			<h3>Recuerde que debe cargar el Complemento.</h3>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		</div>
		</div>

	</div>
</div>
<?php }  ?>


<?php echo $baseHTML->cargarArchivosJS('../../'); ?>
<!-- Wait Me Plugin Js -->
<script src="../../plugins/waitme/waitMe.js"></script>

<!-- Custom Js -->
<script src="../../js/pages/cards/colored.js"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="../../plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>

<!-- Dropzone Plugin Js -->
<script src="../../plugins/dropzone/dropzone.js"></script>

<script src="../../js/pdfobject.min.js"></script>


<form class="form" @submit.prevent="realizarConsulta">
<script type="text/x-template" id="modal-template">
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <slot name="header">
              default header
            </slot>
          </div>

          <div class="modal-body">
            <slot name="body">
			  <h4>Ingrese su consulta y en la brevedad se comunicarán con usted</h4>
			  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="form-label">Mensaje</label>
					<div class="form-group">
						<div class="form-line">
							<input type="text" class="form-control" id="mensaje" name="mensaje" />

						</div>
					</div>
				</div>
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
			<button class="btn bg-grey waves-effect" @click="$emit('close')">
                CANCELAR
			  </button>
			  <button type="button" class="btn bg-green waves-effect" @click="enviarConsulta()">
					<i class="material-icons">send</i>
					<span>ENVIAR</span>
				</button>

            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</script>
</form>


  <!-- use the modal component, pass in the prop -->
  <modal v-if="showModal" @close="showModal = false">
    <!--
      you can use custom content here to overwrite
      default content
    -->
    <h3 slot="header">Realizar Consulta</h3>
  </modal>


</main>

<script src="../../js/jquery.number.js"></script>


<script>

	function traerImagen(archivo, contenedorpdf, contenedor) {
		$.ajax({
			data:  {idfixture: <?php echo $id; ?>,
					idarbitro: <?php echo $_SESSION['idarbitro_aif']; ?>,
					archivo: archivo,
					accion: 'traerArchivoPlanillaPorArbitroFixture'},
			url:   '../../ajax/ajax.php',
			type:  'post',
			beforeSend: function () {

			},
			success:  function (response) {
				var cadena = response.datos.type.toLowerCase();

				if (response.datos.type != '') {
					if (cadena.indexOf("pdf") > -1) {
						PDFObject.embed(response.datos.imagen, "#"+contenedorpdf);
						$('#'+contenedorpdf).show();
						$("."+contenedor).hide();

					} else {
						$("." + contenedor + " img").attr("src",response.datos.imagen);
						$("."+contenedor).show();
						$('#'+contenedorpdf).hide();
					}

				}



			}
		});
	}

	traerImagen(1,'example1','timagen1');
	traerImagen(2,'example2','timagen2');



		Dropzone.prototype.defaultOptions.dictFileTooBig = "Este archivo es muy grande ({{filesize}}MiB). Peso Maximo: {{maxFilesize}}MiB.";

		Dropzone.options.frmFileUpload = {
			maxFilesize: 30,
			acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg,.pdf",
			accept: function(file, done) {
				done();
			},
			init: function() {
				this.on("sending", function(file, xhr, formData){
	               formData.append("idfixture", '<?php echo $id; ?>');
						formData.append("idarbitro", '<?php echo $_SESSION['idarbitro_aif']; ?>');
	         });
				this.on('success', function( file, resp ){
					traerImagen(1,'example1','timagen1');
					$('.lblPlanilla').hide();
					swal("Correcto!", resp.replace("1", ""), "success");
					$('.btnGuardar').show();
					$('.infoPlanilla').hide();
				});

				this.on('error', function( file, resp ){
					swal("Error!", resp.replace("1", ""), "warning");
				});
			}
		};


		Dropzone.options.frmFileUpload2 = {
			maxFilesize: 30,
			acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg,.pdf",
			accept: function(file, done) {
				done();
			},
			init: function() {
				this.on("sending", function(file, xhr, formData){
	               formData.append("idfixture", '<?php echo $id; ?>');
						formData.append("idarbitro", '<?php echo $_SESSION['idarbitro_aif']; ?>');
	         });
				this.on('success', function( file, resp ){
					traerImagen(2,'example2','timagen2');
					$('.lblComplemento').hide();
					swal("Correcto!", resp.replace("1", ""), "success");
					$('.btnGuardar').show();
					$('.infoPlanilla').hide();
				});

				this.on('error', function( file, resp ){
					swal("Error!", resp.replace("1", ""), "warning");
				});
			}
		};

		var myDropzone = new Dropzone("#archivos", {
			params: {
	          idfixture: <?php echo $id; ?>,
	          idarbitro: <?php echo $_SESSION['idarbitro_aif']; ?>
	      },
			url: 'subir.php'
		});


		var myDropzone2 = new Dropzone("#archivos2", {
			params: {
	          idfixture: <?php echo $id; ?>,
	          idarbitro: <?php echo $_SESSION['idarbitro_aif']; ?>
	      },
			url: 'subircomplemento.php'
		});

	$(document).ready(function(){

		$('.btnImprmirInforme').click(function() {

			window.open("informe.php?informe=" + <?php echo $id; ?> + "&arbitro=" + $('#refarbitros').text(),'_blank');
		});

		<?php
		if (count($filesComplemento)<1) {
		?>
		$('#myModalComplemento').modal();
		<?php } ?>

		<?php
		if (count($filesPlanilla)<1) {
		?>
		$('#myModalPlanilla').modal();
		<?php } ?>

		$('.btnEstadistica').click(function() {
			url = "estadistica.php?id=<?php echo $id; ?>";
			$(location).attr('href',url);
		});

		<?php if (mysql_result($resultado,0,'observaciones') == 'Sin novedad') { ?>
			$('#tieneincidencias').prop('checked',false);
			$('#observaciones').prop('readonly',true);
		<?php } else { ?>
			$('#tieneincidencias').prop('checked',true);
			$('#observaciones').prop('readonly',false);

		<?php } ?>

		$('.txtCarga').each(function(intIndex){
			$(this).number( true, 0 ,'','');
		});

		$('#tieneincidencias').change(function() {
			if ($(this).prop('checked')) {
				$('#observaciones').prop('readonly',false);
			} else {
				$('#observaciones').val('Sin novedad');
				$('#observaciones').prop('readonly',true);

			}
		});

		function validaCantidadJugadores(idestado) {
			var errorEstado = 0;
			var lblError = '';

			if (idestado < 15) {
				if ($('#cantidadjugadoreslocal').val() == 0) {
					errorEstado = 1;
					lblError += 'La cantidad de jugadores local debe ser mayor a cero. \n\
					\
					';
				}

				if ($('#cantidadjugadoresvisitante').val() == 0) {
					errorEstado = 1;
					lblError += 'La cantidad de jugadores vistante debe ser mayor a cero. \
					\
					';
				}
			}

			if (errorEstado == 1) {
				swal({
						title: "Respuesta",
						text: lblError,
						type: "error",
						timer: 2500,
						showConfirmButton: false
				});

				return false;
			} else {
				return true;
			}

		}

		$('.txtCarga').attr('readonly', false);

		$('.btnGuardar44').click(function() {
			/*
			if (validaCantidadJugadores($('#refestadospartidos').val())) {

				$("#sign_in").submit(function() {

				});
			}
			*/
			guardar();
		});


		$('#sign_in').submit(function(e){

			e.preventDefault();
			if (validaCantidadJugadores($('#refestadospartidos').val())) {


				if ($('#sign_in')[0].checkValidity()) {


					//información del formulario
					var formData = new FormData($(".formulario")[0]);
					var message = "";
					//hacemos la petición ajax
					$.ajax({
						url: '../../ajax/ajax.php',
						type: 'POST',
						// Form data
						//datos del formulario
						data: formData,
						//necesario para subir archivos via ajax
						cache: false,
						contentType: false,
						processData: false,
						//mientras enviamos el archivo
						beforeSend: function(){

						},
						//una vez finalizado correctamente
						success: function(data){

							if (data == '') {
								if ($('#refestados').val() == 2) {
									$('.btnEstadistica').show();


								} else {
									$('.btnEstadistica').hide();

								}
								swal({
										title: "Respuesta",
										text: "Registro Creado con exito!!",
										type: "success",
										timer: 1500,
										showConfirmButton: false
								});

							} else {
								swal({
										title: "Respuesta",
										text: data,
										type: "error",
										timer: 2500,
										showConfirmButton: false
								});


							}
						},
						//si ha ocurrido un error
						error: function(){
							$(".alert").html('<strong>Error!</strong> Actualice la pagina');
							$("#load").html('');
						}
					});

				}
			} else {
				e.preventDefault();
			}

		});



	});
</script>




<script>


	Vue.component('modal', {
		template: '#modal-template',
		methods: {
			enviarConsulta () {

				paramsNotificacion.set('mensaje',$('#mensaje').val());

				axios.post('../../ajax/ajax.php', paramsNotificacion)
				.then(res => {
					//this.setMensajes(res)


					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
						this.$emit('close')
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}

				});
			}
		}
	})

	const app = new Vue({
		el: "#app",
		data: {
			pag: 1,
			idclub: 5,
			activeClass: 'waves-effect',
			errorMensaje: '',
			successMensaje: '',
			activeDelegados: {},
			showModal: false

		},
		mounted () {
		},
		computed: {

		},
		methods: {

			setMensajes (res) {
				this.getDelegado()

				if (res.data.error) {
					this.errorMensaje = res.data.mensaje
				} else {
					this.successMensaje = res.data.mensaje
				}

				setTimeout(() => {
					this.errorMensaje = ''
					this.successMensaje = ''
				}, 3000);

			}
		}
	})
</script>
</body>
<?php } ?>
</html>

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
$baseHTML = new BaseHTML();

//*** SEGURIDAD ****/
include ('../../includes/funcionesSeguridad.php');
$serviciosSeguridad = new ServiciosSeguridad();
$serviciosSeguridad->seguridadRuta($_SESSION['refroll_aif'], '../jugadoresclub/');
//*** FIN  ****/

$fecha = date('Y-m-d');

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_aif'],"Jugadores Por Club",$_SESSION['refroll_aif'],$_SESSION['email_aif']);

$configuracion = $serviciosReferencias->traerConfiguracion();

$tituloWeb = mysql_result($configuracion,0,'sistema');

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a><a href="javascript:void(0)" class="navbar-brand"><i class="material-icons">navigate_next</i></a><a class="navbar-brand active" href="index.php">Jugadores Por Club</a>';


/////////////////////// Opciones pagina ///////////////////////////////////////////////
$singular = "Jugador Por Club";

$plural = "Jugadores Por Club";

$eliminar = "eliminarJugadoresclub";

$insertar = "insertarJugadoresclub";

$tituloWeb = "Gestión: AIF";
//////////////////////// Fin opciones ////////////////////////////////////////////////


/////////////////////// Opciones para la creacion del formulario  /////////////////////
$tabla 			= "dbjugadoresclub";

$lblCambio	 	= array("");
$lblreemplazo	= array("");


$cadRef 	= '';

$refdescripcion = array();
$refCampo 	=  array();
//////////////////////////////////////////////  FIN de los opciones //////////////////////////

if ($_SESSION['idroll_aif'] == 4) {
	$resJugadoresPorCountries = $serviciosReferencias->traerJugadoresClubPorCountrieActivos($_SESSION['club_aif']);
	$refClub = $_SESSION['club_aif'];
} else {
	$resJugadoresPorCountries = $serviciosReferencias->traerJugadoresClubPorCountrieActivos($_GET['id']);
	$refClub = $_GET['id'];
}


$resPermiteRegistrar = $serviciosReferencias->traerVigenciasoperacionesPorModuloVigenciasNuevo(2,date('Y-m-d'));

if (mysql_num_rows($resPermiteRegistrar)>0) {
	$permiteRegistrar = 1;
} else {
	$permiteRegistrar = 1;
}



$resTemporadas = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);
} else {
    $ultimaTemporada = 0;
}


$resHabilitado = $serviciosReferencias->traerCierrepadronesPorCountry($refClub);

$habilitado = 0;
if (mysql_num_rows($resHabilitado)>0) {
	$habilitado = 1;
} else {
	$habilitado = 1;
}


/////////////////////// Opciones para la creacion del view  apellido,nombre,nrodocumento,fechanacimiento,direccion,telefono,email/////////////////////


//////////////////////////////////////////////  FIN de los opciones //////////////////////////



/////////////////////// Opciones para la creacion del formulario  /////////////////////
$tabla2			= "dbjugadorespre";

$lblCambio2	 	= array("reftipodocumentos","nrodocumento","fechanacimiento","fechaalta","fechabaja","refcountries","refusuarios","numeroserielote");
$lblreemplazo2	= array("Tipo Documento","Nro Documento","Fecha Nacimiento","Fecha Alta","Fecha Baja","Countries","Usuario","Nro Serie Lote");


$resTipoDoc 	= $serviciosReferencias->traerTipodocumentos();
$cadRefj 	= $serviciosFunciones->devolverSelectBox($resTipoDoc,array(1),'');

$resCountries 	= $serviciosReferencias->traerCountriesPorId($refClub);
$cadRef2j 	= $serviciosFunciones->devolverSelectBox($resCountries,array(6),'');

$resUsua = $serviciosUsuario->traerUsuarioId($_SESSION['usuaid_aif']);
$cadRef3j 	= $serviciosFunciones->devolverSelectBox($resUsua,array(3),'');

$refdescripcion2 = array(0 => $cadRefj,1 => $cadRef2j,2 => $cadRef3j);
$refCampo2 	=  array("reftipodocumentos","refcountries","refusuarios");

$formularioJugador 	= $serviciosFunciones->camposTablaViejo("insertarJugadorespre" ,$tabla2,$lblCambio2,$lblreemplazo2,$refdescripcion2,$refCampo2);
//////////////////////////////////////////////  FIN de los opciones //////////////////////////


$cabeceras 		= "	<th>Tipo Documento</th>
					<th>Nro Doc</th>
					<th>Apellido</th>
					<th>Nombres</th>
					<th>Email</th>
					<th>Fecha Nac.</th>
					<th>Fecha Alta</th>
					<th>Nro Serie Lote</th>
					<th>Obs.</th>";

$lstNuevosJugadores = $serviciosReferencias->traerJugadoresprePorCountries($refClub);



$tabla 			= "dbdelegados";

$lblCambio	 	= array("refusuarios","email1","email2","email3","email4");
$lblreemplazo	= array("Usuario","Email de Contacto 1","Email de Contacto 2","Email de Contacto 3","Email de Contacto 4");


$resModelo 	= $serviciosReferencias->traerUsuariosPorId($_SESSION['usuaid_aif']);
$cadRef 	= $serviciosFunciones->devolverSelectBox($resModelo,array(5),'');

$refdescripcion = array(0 => $cadRef);
$refCampo 	=  array("refusuarios");

$frmPerfil 	= $serviciosFunciones->camposTabla("insertarDelegados" ,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);

$resJugadoresIncompletos = $serviciosReferencias->traerJugadoresClubIncompletos($refClub);
if ($_SESSION['refroll_aif'] != 1) {

} else {


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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js"></script>
	<!-- <script src="https://unpkg.com/axios/dist/axios.min.js"></script> -->

	<script src="https://unpkg.com/vue-swal"></script>

	<!-- Bootstrap Material Datetime Picker Css -->
    <link href="../../plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />


    <style>
        .alert > i{ vertical-align: middle !important; }


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
<section class="content" style="margin-top:-25px;">

	<div class="container-fluid">
		<div class="row clearfix">

        	<div class="row">


				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="card ">
						<div class="header bg-blue">
							<h2>
								JUGADORES CARGADOS
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
							<div class="row">
								<div class="alert bg-orange">
									<b>IMPORTANTE!!</b> Recuerde presionar el boton "GUARDAR" para aplicar los cambios. Al dar de baja a un jugador automáticamente se borrará el Nro de LOTE
								</div>
							</div>
							<?php if (mysql_num_rows($resJugadoresIncompletos)>0) { ?>
							<div class="row">
								<div class="col-xs-12">
									<p>Esta nomina de jugadores tiene inconsistencias en alguna documentacion, por favor corregirlas para imprimir el listado.</p>
									<ul class="list-group">
									<?php while ($rowI = mysql_fetch_array($resJugadoresIncompletos)) { ?>
										<li>Socio: <?php echo $rowI['apyn']; ?> - Nro Doc: <?php echo $rowI['nrodocumento']; ?></li>
									<?php } ?>
									</ul>
								</div>
							</div>
							<?php } ?>
							<div class="row clearfix">
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
									<label for="email_address_2">Buscar:</label>
								</div>
								<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
									<div class="form-group">
										<div class="form-line">
											<input type="text" id="buscar" class="form-control" placeholder="Ingrese los datos de la busqueda y presiona Enter" v-model="busqueda" v-on:keyup.enter="buscarJugadoresPorClub" />
										</div>
									</div>
								</div>
							</div>
							<form class="form" id="formJugadoresClub">
							<table class="table table-bordered table-striped table-hover highlight" id="example">
								<thead>
									<tr>
										<th>Apellido</th>
										<th>Nombre</th>
										<th>Nro Documento</th>
										<th>Numero de Socio/Lote</th>
										<th>Baja</th>
										<th>Art 2 Inciso D</th>
										<th>Accion</th>
									</tr>
								</thead>
								<tbody>


								<tr v-for="jugador in jugadoresPorClub" :key="jugador.idjugador">
									<td>{{ jugador.apellido }}</td>
									<td>{{ jugador.nombres }}</td>
									<td>{{ jugador.nrodocumento }}</td>
									<td>
										<div v-if="jugador.marcalote != ''">
											<input class='form-control' type='text' disabled readonly="readonly" name='numeroserielote' id='numeroserielote' :value="jugador.numeroserielote" v-model="jugador.numeroserielote"/>
										</div>
										<div v-else>
											<input class='form-control' type='text' name='numeroserielote' id='numeroserielote' :value="jugador.numeroserielote" v-model="jugador.numeroserielote"/>
										</div>

									</td>
									<td>
									<div class='switch'>
										<label><input type='checkbox' v-model="jugador.fechabajacheck"/><span class='lever switch-col-green'></span></label>
									</div>

									</div>
									</td>
									<td>
									<div class='switch'>
										<label><input type='checkbox' v-model="jugador.articulocheck"/><span class='lever switch-col-green'></span></label>
									</div>

									</td>

									<td>
									<?php
									if ($permiteRegistrar == 1) {
									if ($habilitado == 1) {
									?>
										<button type='button' class='btn btn-primary guardarJugadorClubSimple' @click="guardarJugadorClub(jugador)">Guardar</button>
									<?php
										}
									}
									?>
									</td>
								</tr>

								</tbody>
							</table>
							</form>
							<div align="center">
							<ul class="pagination">
								<li class="waves-effect"><a href="#" v-show="pag != 1" @click.prevent="activarPagina(pag -= 1)"><i class="material-icons">chevron_left</i></a></li>
								<li  v-for="n in paginasJC" :class="{active:pag == n}" @click="activarPagina(n)"><a href="#!">{{ n }}</a></li>
								<li class="waves-effect"><a href="#" v-show="pag < paginasJC" @click.prevent="activarPagina(pag += 1)"><i class="material-icons">chevron_right</i></a></li>
							</ul>
							</div>
						</div>
					</div>
				</div>


			</div>


            <div class="row">

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header bg-green">
                            <h2>
								Jugadores Nuevos
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
							<div class="row" style="margin-bottom:10px;">
							<?php if (($habilitado == 1) && ($permiteRegistrar == 1)) { ?>
							<button type="button" class="btn btn-success waves-effect" data-toggle="modal" data-target="#myModal3" id="agregarContacto">
								<i class="material-icons">add_circle</i>
								<span>Agregar Jugador</span>
							</button>
							<?php } ?>
							</div>
						<form class="form-inline formulario" role="form">
						<table class="table table-bordered table-striped table-hover js-basic-example dataTable highlight" id="example1">
							<thead>
								<tr>
									<th>Tipo Doc.</th>
									<th>Nro Doc</th>
									<th>Apellido</th>
									<th>Nombres</th>
									<th>Email</th>
									<th>Fecha Nac.</th>
									<th>Fecha Alta</th>
									<th>Nro Serie Lote</th>
									<th></th>
									<th></th>
								</tr>
							</thead>
							<tbody id="resultados">
							<?php while ($row = mysql_fetch_array($lstNuevosJugadores)) { ?>
								<tr>
									<td><?php echo $row['tipodocumento']; ?></td>
									<td><?php echo $row['nrodocumento']; ?></td>
									<td><?php echo $row['apellido']; ?></td>
									<td><?php echo $row['nombres']; ?></td>
									<td><?php echo $row['email']; ?></td>
									<td><?php echo $row['fechanacimiento']; ?></td>
									<td><?php echo $row['fechaalta']; ?></td>
									<td><?php echo $row['numeroserielote']; ?></td>
									<td align="center">
										<button type="button" class="btn bg-amber btn-circle waves-effect waves-circle waves-float varmodificar" id="<?php echo $row['idjugadorpre']; ?>">
											<i class="material-icons">create</i>
										</button>
									</td>
									<td align="center">
										<button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float eliminarJugPre" id="<?php echo $row['idjugadorpre']; ?>">
											<i class="material-icons">delete</i>
										</button>
									</td>
								</tr>

							<?php } ?>
							</tbody>
						</table>
						</form>
                        </div>
                    </div>
				</div>



            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>
							Operaciones

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

						<div class="col-md-12">
							<label class="control-label">Seleccione un año para generar el reporte</label>
							<select id="anio" name="anio" class="form-control">
								<?php
									if (date('m') >= 6) {
								?>
									<option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>
									<option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
								<?php
									} else {
								?>
									<option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
									<option value="<?php echo date('Y') + 1; ?>"><?php echo date('Y') + 1; ?></option>


								<?php
									}
								?>
							</select>
						</div>
						<div class="button-demo">
							<div v-if="permiteImprimir == 0">
							<button type="button" class="btn bg-brown" id="btnImprimir" style="margin-left:0px;">Imprimir</button>
							</div>
							<div v-else>
								<p>Falta completar datos de los Jugadores como ser Numero de Socio/Lote, Baja o Art 2 Inciso D</p>
							</div>
							<button type="button" class="btn bg-brown" id="btnCondicionJugador" style="margin-left:0px;">Reporte Condicion de Jugadores</button>
						</div>
					</div>
				</div>
			</div>

            <div class="row">
                <div class="col-md-12">
                <ul class="list-inline">


                </ul>
                </div>
            </div>
            <input type="hidden" id="refcountries" name="refcountries" value="<?php echo $refClub; ?>"/>


		</div>
	</div>

	<?php if (($habilitado == 1) && ($permiteRegistrar == 1)) { ?>



	<!-- Modal del guardar-->
	<div class="modal fade" id="myModal3" tabindex="1" style="z-index:50000;" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form class="form-inline formulario" role="form">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Crear Jugador</h4>
			</div>
			<div class="modal-body demo-masked-input">
				<div class="row">
				<?php echo $formularioJugador; ?>
				</div>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-primary" id="cargarJugador">Agregar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

			</div>
			</form>
		</div>
		</div>
	</div>

	<!-- del modal -->

	<!-- Modal del guardar-->
	<div class="modal fade" id="myModal4" tabindex="1" style="z-index:50000;" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form class="form-inline formulario" role="form">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Eliminar Jugador</h4>
			</div>
			<div class="modal-body demo-masked-input">
			<p>¿Esta seguro que desea eliminar al jugador?</p>
			</div>
			<div class="modal-footer">
			<input type="hidden" name="idEliminar" id="idEliminar" value=''/>
			<button type="button" class="btn btn-danger" data-dismiss="modal" id="btnEliminarJugador">Si</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

			</div>
			</form>
		</div>
		</div>
	</div>

	<!-- del modal -->


	<!-- Modal del guardar-->
	<div class="modal fade" id="myModal5" tabindex="1" style="z-index:50000;" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<form class="form-inline formulario2" role="form">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Modificar Jugador</h4>
			</div>
			<div class="modal-body">
				<div class="row modificarJugadorNuevo">

				</div>

			</div>
			<div class="modal-footer">
			<input type="hidden" name="idModificarJugadorNuevo" id="idModificarJugadorNuevo" value=''/>
			<button type="button" class="btn btn-warning" id="btnModificarJugadorNuevo">Modificar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

			</div>
			</form>
		</div>
		</div>
	</div>

	<!-- del modal -->
	<?php } ?>


</section>


<?php echo $baseHTML->cargarArchivosJS('../../'); ?>
<!-- Wait Me Plugin Js -->
<script src="../../plugins/waitme/waitMe.js"></script>

<!-- Custom Js -->
<script src="../../js/pages/cards/colored.js"></script>

<script src="../../plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="../../plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="../../js/pages/tables/jquery-datatable.js"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="../../plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>

<!-- Modal Large Size -->
<transition name="fade">
<form class="form" @submit.prevent="guardarDelegado">
<?php //echo $baseHTML->modalHTML('modalPerfil','Perfil','GUARDAR','Ingrese sus datos personales y los Email de los contactos','frmPerfil',$frmPerfil,'iddelegado','Delegados','VguardarDelegado'); ?>
</form>
</transition>


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




<script>
	$(document).ready(function(){

		$('#btnImprimir').click(function() {
			window.open("../../reportes/rptJugadoresPorCountries.php?refcountries1=" + <?php echo $refClub; ?> + "&bajas1=0" ,'_blank');
		});


		$('#btnCondicionJugador').click(function() {
			window.open("../../reportes/rptCondicionJugadorManual.php?id=0&reftemporada=" + <?php echo $ultimaTemporada; ?> + "&bajaequipos=1" + "&refcountries=" + <?php echo $refClub; ?> + "&anio=" + $('#anio').val() ,'_blank');
		});

		var $demoMaskedInput = $('.demo-masked-input');

		//Date
		$demoMaskedInput.find('.date').inputmask('dd-mm-yyyy', { placeholder: '__-__-____' });

		$('#fechaalta').val('<?php echo date('d-m-Y'); ?>');

		$('#menuPerfil').click(function() {
			$('#modalPerfil').modal();
		});

		$("#example1").on("click",'.eliminarJugPre', function(){
			$('#idEliminar').val($(this).attr("id"));
			$('#myModal4').modal('toggle');
		})

		$('#frmPerfil').validate({
			highlight: function (input) {
				console.log(input);
				$(input).parents('.form-line').addClass('error');
			},
			unhighlight: function (input) {
				$(input).parents('.form-line').removeClass('error');
			},
			errorPlacement: function (error, element) {
				$(element).parents('.input-group').append(error);
			}
		});

		$("#btnmodalPerfil9").submit(function(e){

			e.preventDefault();
		});

		<?php if (($habilitado == 1) && ($permiteRegistrar == 1)) { ?>
		$("#example1").on("click",'.varborrar', function(){
				usersid =  $(this).attr("id");
				if (!isNaN(usersid)) {
				$("#idEliminar").val(usersid);
				$("#dialog2").dialog("open");


				//url = "../clienteseleccionado/index.php?idcliente=" + usersid;
				//$(location).attr('href',url);
				} else {
				alert("Error, vuelva a realizar la acción.");
				}
		});//fin del boton eliminar

		$("#example1").on("click",'.varmodificar', function(){
			usersid =  $(this).attr("id");
			$('#myModal5').modal();

			$.ajax({
				data:  {id: $(this).attr("id"),
					    accion: 'modificarJugadorNuevo'},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {
					$('.modificarJugadorNuevo').html('');
				},
				success:  function (response) {
					$('.modificarJugadorNuevo').html(response);

					$('.modificarJugadorNuevo').find('.date').inputmask('dd-mm-yyyy', { placeholder: '__-__-____' });

				}
			});

		});//fin del boton modificar




		$('#btnEliminarJugador').click(function() {
			$.ajax({
				data:  {id: $('#idEliminar').val(),
					    accion: 'eliminarJugadorespre'},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {

				},
				success:  function (response) {
						url = "index.php";
						$(location).attr('href',url);

				}
			});
		});


				//al enviar el formulario
			$('#cargarJugador').click(function(){

				//información del formulario
				var formData = new FormData($(".formulario")[1]);
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
						$("#load").html('<img src="../../imagenes/load13.gif" width="50" height="50" />');
					},
					//una vez finalizado correctamente
					success: function(data){

						if (!isNaN(data)) {
							swal("Correcto!", "Se cargo exitosamente el Jugador. ", "success");

							$('#myModal4').modal('hide');

							url = "index.php";
							$(location).attr('href',url);
							$("#load").html('');



						} else {
							swal("Error!", data, "warning");

							$("#load").html('');
						}
					},
					//si ha ocurrido un error
					error: function(){
						$(".alert").html('<strong>Error!</strong> Actualice la pagina');
						$("#load").html('');
					}
				});




			});


				//al enviar el formulario

				$('#btnModificarJugadorNuevo').click(function(){
					//información del formulario
					var formData = new FormData($(".formulario2")[0]);
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
							$("#load").html('<img src="../imagenes/load13.gif" width="50" height="50" />');
						},
						//una vez finalizado correctamente
						success: function(data){

							if (!isNaN(data)) {
								swal("Correcto!", "Se modifico exitosamente el Jugador. ", "success");

								$('#myModal5').modal('hide');

								url = "index.php";
								$(location).attr('href',url);
								$("#load").html('');


							} else {
								swal("Error!", data, "warning");

								$("#load").html('');
							}
						},
						//si ha ocurrido un error
						error: function(){
							$(".alert").html('<strong>Error!</strong> Actualice la pagina');
							$("#load").html('');
						}
					});
				});


		<?php } ?>
	});
</script>




<script>

	const paramsGetPermiteImprimir = new URLSearchParams();
	 paramsGetPermiteImprimir.append('accion','VpermiteImprimir');
	paramsGetPermiteImprimir.append('idclub',<?php echo $refClub; ?>);

	const paramsGetDelegado = new URLSearchParams();
    paramsGetDelegado.append('accion','VtraerDelegadosPorId');
	paramsGetDelegado.append('iddelegado',<?php echo $_SESSION['usuaid_aif']; ?>);

	const paramsNotificacion = new URLSearchParams();
    paramsNotificacion.append('accion','VenviarMensaje');
	paramsNotificacion.append('mensaje','');
	paramsNotificacion.append('premensaje','Jugadores Por Club: ');
	paramsNotificacion.append('url','jugadoresclub/index.php?id=' + <?php echo $refClub; ?>);

	const paramsGetjugadores = new URLSearchParams();
	paramsGetjugadores.append('accion','VtraerJugadoresClubPorCountrieActivos');
	paramsGetjugadores.append('idclub',<?php echo $refClub; ?>);
	paramsGetjugadores.append('pagina',1);
	paramsGetjugadores.append('cantidad',10);
	paramsGetjugadores.append('busqueda','');

	const paramsGetPaginadorJC = new URLSearchParams();
	paramsGetPaginadorJC.append('accion','VtraerPaginasJugadoresPorClub');
	paramsGetPaginadorJC.append('idclub',<?php echo $refClub; ?>);
	paramsGetPaginadorJC.append('busqueda','');

	const paramsGetjugadoresClub = new URLSearchParams();
	paramsGetjugadoresClub.append('accion','guardarJugadorClubSimple');
	paramsGetjugadoresClub.append('idclub',<?php echo $refClub; ?>);
	paramsGetjugadoresClub.append('idjugador',0);
	paramsGetjugadoresClub.append('numeroserielote',0);
	paramsGetjugadoresClub.append('fechabaja',0);
	paramsGetjugadoresClub.append('articulo',1);

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
			idclub: <?php echo $refClub; ?>,
			cantidad: 10,
			activeClass: 'waves-effect',
			errorMensaje: '',
			successMensaje: '',
			activeDelegados: {},
			jugadoresPorClub: [],
			paginasJC: {},
			busqueda: '',
			nrosocio: '',
			baja: '',
			art: '',
			showModal: false,
			jugadorClubId: '',
			permiteImprimir: 0

		},
		mounted () {
			this.getJugadoresPorClub(this.busqueda),
			this.getPaginasJC(this.busqueda),
			this.getActivePagina(),
			this.getDelegado(),
			this.getPermiteImprimir()
		},
		computed: {

		},
		methods: {
			activarPagina(e) {
				this.pag = e
				this.getJugadoresPorClubPorPagina(this.busqueda)
			},
			getActivePagina() {
				if (this.pag == 1) {
					this.activeClass = 'active'
				}
			},
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

			},
			getPermiteImprimir() {

				axios.post('../../ajax/ajax.php',paramsGetPermiteImprimir)
				.then(res => {
					//console.log(res.data.datos[0].cantidad);
					this.permiteImprimir = res.data.datos[0].cantidad
				})
			},
			getJugadoresPorClub (filtro) {
				paramsGetjugadores.set('busqueda', filtro)

				axios.post('../../ajax/ajax.php',paramsGetjugadores)
				.then(res => {
					//console.log(res);
					this.jugadoresPorClub = res.data.datos
				})
			},
			getJugadoresPorClubPorPagina (filtro) {

				paramsGetjugadores.set('accion','VtraerJugadoresClubPorCountrieActivos')
				paramsGetjugadores.set('idclub',this.idclub)
				paramsGetjugadores.set('pagina',this.pag)
				paramsGetjugadores.set('cantidad',10)
				paramsGetjugadores.set('busqueda', filtro)

				axios.post('../../ajax/ajax.php',paramsGetjugadores)
				.then(res => {
					//console.log(res);
					this.jugadoresPorClub = res.data.datos
				})
			},
			getPaginasJC (filtro) {

				paramsGetPaginadorJC.set('busqueda', filtro)

				axios.post('../../ajax/ajax.php', paramsGetPaginadorJC)
				.then(res => {
					//console.log(res);
					//alert(res.data.datos[0]);
					this.paginasJC = res.data.datos[0]
				})
			},
			getDelegado () {
					axios.post('../../ajax/ajax.php',paramsGetDelegado)
					.then(res => {

                        //this.$refs['ref_nombres'].value = res.data.datos[0].nombres
						this.activeDelegados = res.data.datos[0]
					})
			},
			guardarDelegado (e) {
				axios.post('../../ajax/ajax.php', new FormData(e.target))
				.then(res => {
					//this.setMensajes(res)

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}

				});


			},
			guardarJugadorClub : function(jug){

				paramsGetjugadoresClub.set('idjugador',jug.idjugador);
				paramsGetjugadoresClub.set('numeroserielote',jug.numeroserielote);
				paramsGetjugadoresClub.set('fechabaja',jug.fechabajacheck == false ? 0 : 1);
				paramsGetjugadoresClub.set('articulo',jug.articulocheck == false ? 0 : 1);

				axios.post('../../ajax/ajax.php',paramsGetjugadoresClub)
				.then(res => {

					//this.$refs['ref_nombres'].value = res.data.datos[0].nombres
					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
						this.activarPagina(this.pag)
						this.getPermiteImprimir()
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}
				})
			},
			buscarJugadoresPorClub () {

				this.getPaginasJC(this.busqueda)
				this.getJugadoresPorClub(this.busqueda)
				this.activarPagina(1)


			}
		}
	})
</script>
</body>
<?php } ?>
</html>

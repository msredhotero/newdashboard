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
$serviciosSeguridad->seguridadRuta($_SESSION['refroll_aif'], '../equipos/');
//*** FIN  ****/

$fecha = date('Y-m-d');

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_aif'],"Equipos",$_SESSION['refroll_aif'],$_SESSION['email_aif']);

$configuracion = $serviciosReferencias->traerConfiguracion();

$tituloWeb = mysql_result($configuracion,0,'sistema');

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a><a href="javascript:void(0)" class="navbar-brand"><i class="material-icons">navigate_next</i></a><a class="navbar-brand active" href="index.php">Equipos</a>';

$club = $serviciosReferencias->traerNombreCountryPorId($_SESSION['idclub_aif']);

$permiteRegistrar = 1;

$habilitado = 1;


/////////////////////// Opciones pagina ///////////////////////////////////////////////
$singular = "Country";

$plural = "Countries";

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



$tabla 			= "dbdelegados";

$lblCambio	 	= array("refusuarios","email1","email2","email3","email4");
$lblreemplazo	= array("Usuario","Email de Contacto 1","Email de Contacto 2","Email de Contacto 3","Email de Contacto 4");


$resModelo 	= $serviciosReferencias->traerUsuariosPorId($_SESSION['usuaid_aif']);
$cadRef 	= $serviciosFunciones->devolverSelectBox($resModelo,array(5),'');

$refdescripcion = array(0 => $cadRef);
$refCampo 	=  array("refusuarios");

$frmPerfil 	= $serviciosFunciones->camposTabla($insertar ,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);


$resTemporadas = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);
} else {
    $ultimaTemporada = 0;
}

//die(var_dump($ultimaTemporada));

$resEquiposCountries = $serviciosReferencias->traerEquiposPorCountries($_SESSION['idclub_aif']);

$resCategorias 	= $serviciosReferencias->traerCategorias();
$cadRefCategorias 	= $serviciosFunciones->devolverSelectBox($resCategorias,array(1),'');

$resDivisiones 	= $serviciosReferencias->traerDivisiones();
$cadRefDivisiones 	= $serviciosFunciones->devolverSelectBox($resDivisiones,array(1),'');

$resCountries 	= $serviciosReferencias->traerCountriesMenosId($_SESSION['idclub_aif']);
$cadRefCountries 	= $serviciosFunciones->devolverSelectBox($resCountries,array(1),'');


$idusuario = $_SESSION['usuaid_aif'];

$confirmo = $serviciosReferencias->existeCabeceraConfirmacion($ultimaTemporada, $_SESSION['idclub_aif']);

$idEstado = 0;
//die(var_dump($confirmo));
if ($confirmo == 0) {
	$serviciosReferencias->insertarCabeceraconfirmacion( $ultimaTemporada, $_SESSION['idclub_aif'], 1, $_SESSION['nombre_aif'], $_SESSION['nombre_aif']);

}

$idEstado = $serviciosReferencias->devolverIdEstado("dbcabeceraconfirmacion",$confirmo,"idcabeceraconfirmacion");

if ($idEstado > 1) {
	header('Location: modificar.php');
}

////////////////////////////		 verifico si existe alguna fusion donde no se confirmaron los countries /////////////////////////
$verificarFusion = $serviciosReferencias->traerEstadosFusionesAceptadasPorCountrie($_SESSION['idclub_aif']);

////////////////////////////// 				FIN				  /////////////////////////

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

	<!-- Animation Css -->
    <link href="../../plugins/animate-css/animate.css" rel="stylesheet" />

	<!-- VUE JS -->
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

	<!-- axios -->
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

	<script src="https://unpkg.com/vue-swal"></script>

	<!-- Bootstrap Material Datetime Picker Css -->
    <link href="../../plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

	<!-- Dropzone Css -->
    <link href="../../plugins/dropzone/dropzone.css" rel="stylesheet">

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
		<p>Espere por favor...</p>
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
						<div class="header bg-teal">
							<h2>
								Country: <?php echo $club; ?>
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
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
										<h4>Equipos Activos Actuales</h4>
										<p>Recuerde que el plantel del equipo se deberá cargar </p>
										<div class="alert alert-warning animated shake">
											<strong>Importante!</strong> Toda la información será confirmada por la Asociación.
										</div>

									</div>

								</div>

								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<form class="form" id="formCountryEquiposEstable">
										<table class="table table-bordered table-striped table-hover highlight" id="example">
											<thead>
												<tr>
													<th>Equipo</th>
													<th>Categoria</th>
													<th>Division</th>
													<th>Es Fusion</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>

											<tr v-for="equipo in activeEquipos" :key="equipo.idequipo">
												<td>{{ equipo.nombre }}</td>
												<td>{{ equipo.categoria }}</td>
												<td>{{ equipo.division }}</td>
												<td>
													<button v-if="equipo.esfusion > 0" type='button' class='btn btn-info waves-effect' @click="verFusion(equipo.idequipo)">
														<i class="material-icons">search</i>
														<span>Ver</span>
													</button>
												</td>

												<td>
												<?php
												if ($permiteRegistrar == 1) {
													if ($habilitado == 1) {
												?>
													<button type='button' class='btn btn-danger waves-effect eliminarEquipo' @click="eliminarEquipoPasivo(equipo)">
														<i class="material-icons">delete</i>
														<span>Eliminar</span>
													</button>
													<button type='button' class='btn btn-success waves-effect' @click="mantenerEquipoPasivo(equipo)">
														<i class="material-icons">add</i>
														<span>Mantener</span>
													</button>
												<?php
													}
												}
												?>
												</td>
											</tr>

											</tbody>
										</table>
									</form>

									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
									<h4> <i class="material-icons">unarchive</i> Equipos que Quedarán</h4>
									</div>

									<form class="form" id="formCountryEquiposMantenidos">
										<table class="table table-bordered table-striped table-hover highlight" id="example">
											<thead>
												<tr>
													<th>Equipo</th>
													<th>Categoria</th>
													<th>Division</th>
													<th>Es Fusion</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>

											<tr v-for="equipo in activeEquiposMantenidos" :key="equipo.idequipodelegado">
												<td>{{ equipo.nombre }}</td>
												<td>{{ equipo.categoria }}</td>
												<td>{{ equipo.division }}</td>
												<td>
													<div class="icon-button-demo">
														<button v-if="equipo.esfusion > 0" type='button' class='btn btn-info btn-xs waves-effect' @click="verFusionDelegados(equipo.idequipodelegado)">
															<i class="material-icons">search</i>
															<span>Ver</span>
														</button>
														<button type='button' class='btn btn-success btn-xs waves-effect' @click="getFusion(equipo.idequipodelegado)">
															<i class="material-icons">add</i>
															<span>Fusion</span>
														</button>
													</div>
												</td>

												<td>
												<?php
												if ($permiteRegistrar == 1) {
													if ($habilitado == 1) {
												?>
													<button type='button' class='btn bg-red waves-effect eliminarEquipoDelegado' @click="eliminarEquiposDelegadoDefinitivo(equipo)">
														<i class="material-icons">clear</i>
														<span>Sacar</span>
													</button>
												<?php
													}
												}
												?>
												</td>
											</tr>

											</tbody>
										</table>

									</div>


									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
									<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
									<h4> <i class="material-icons">remove_circle</i> Equipos que seran Eliminados</h4>
									</div>

									<form class="form" id="formCountryEquiposEliminados">
										<table class="table table-bordered table-striped table-hover highlight" id="example">
											<thead>
												<tr>
													<th>Equipo</th>
													<th>Categoria</th>
													<th>Division</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>

											<tr v-for="equipo in activeEquiposEliminados" :key="equipo.idequipo">
												<td>{{ equipo.nombre }}</td>
												<td>{{ equipo.categoria }}</td>
												<td>{{ equipo.division }}</td>

												<td>
												<?php
												if ($permiteRegistrar == 1) {
													if ($habilitado == 1) {
												?>
													<button type='button' class='btn bg-green waves-effect eliminarEquipoDelegado' @click="eliminarEquiposDelegadoDefinitivo(equipo)">
														<i class="material-icons">autorenew</i>
														<span>Habilitar</span>
													</button>
												<?php
													}
												}
												?>
												</td>
											</tr>

											</tbody>
										</table>

									</div>
								</div>
								<hr>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

										<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
											<h4> <i class="material-icons">add_circle</i> Equipos que seran Agregados</h4>
										</div>
										<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
											<button type="button" class="btn btn-success waves-effect" @click="showModalEquipo = true">
                                    			<i class="material-icons">add_box</i>
												<span>Agregar</span>
											</button>
										</div>
									<form class="form" id="formCountryEquiposNuevos">
										<table class="table table-bordered table-striped table-hover highlight" id="example">
											<thead>
												<tr>
													<th>Equipo</th>
													<th>Categoria</th>
													<th>Division</th>
													<th>Es Fusion</th>
													<th>Acciones</th>
												</tr>
											</thead>
											<tbody>

											<tr v-for="equipo in activeEquiposNuevos" :key="equipo.idequipodelegado">
												<td>{{ equipo.nombre }}</td>
												<td>{{ equipo.categoria }}</td>
												<td>{{ equipo.division }}</td>
												<td>
													<button v-if="equipo.esfusion > 0" type='button' class='btn btn-info waves-effect' @click="verFusionDelegados(equipo.idequipodelegado)">
														<i class="material-icons">search</i>
														<span>Ver</span>
													</button>
													<button type='button' class='btn btn-success btn-xs waves-effect' @click="getFusion(equipo.idequipodelegado)">
														<i class="material-icons">add</i>
														<span>Fusion</span>
													</button>
												</td>

												<td>
												<?php
												if ($permiteRegistrar == 1) {
													if ($habilitado == 1) {
												?>
													<button type='button' class='btn btn-danger waves-effect eliminarEquiposDelegadoDefinitivo' @click="eliminarEquiposDelegadoDefinitivo(equipo)">
														<i class="material-icons">delete</i>
														<span>Eliminar</span>
													</button>


												<?php
													}
												}
												?>
												</td>
											</tr>

											</tbody>
										</table>

									</div>
								</div>
								<input type="hidden" value="VmodificarCountries" name="accion" id="accion" />
								<input type="hidden" id="id" name="id" :value="<?php echo $_SESSION['idclub_aif']; ?>"/>

							<div>
							<div class="alert bg-indigo">
								<p><strong>Importante!</strong> Finalizado el proceso, presione "GUARDAR" para enviar toda la información a la Asociación.</p>
								<p><strong>Importante!</strong> En curso el proceso, presione "PRESENTAR" para enviar toda la información a la Asociación.</p>
							</div>

							</form>

								<div class="button-demo">
									<button v-if="activeEquipos.length == 0" data-toggle="modal" data-target="#largeModal" class="btn bg-orange waves-effect">
										<i class="material-icons">assignment_turned_in</i>
										<span>PRESENTAR</span>
									</button>


								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


<?php echo $baseHTML->cargarArchivosJS('../../'); ?>
<!-- Wait Me Plugin Js -->
<script src="../../plugins/waitme/waitMe.js"></script>

<!-- Custom Js -->
<script src="../../js/pages/cards/colored.js"></script>

<script src="../../js/pages/ui/animations.js"></script>



<!-- Modal Large Size -->
<transition name="fade">
<form class="form" @submit.prevent="guardarDelegado">
<?php //echo $baseHTML->modalHTML('modalPerfil','Perfil','GUARDAR','Ingrese sus datos personales y los Email de los contactos','frmPerfil',$frmPerfil,'iddelegado','Delegados','VguardarDelegado'); ?>
</form>
</transition>

<!-- Large Size -->
<form ref="formP" class="form" id="formConfirmar" @submit.prevent="confirmarEquipos">
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
	 <div class="modal-dialog modal-lg" role="document">
		  <div class="modal-content">
				<div class="modal-header">
					 <h4 class="modal-title" id="largeModalLabel">PRESENTAR EQUIPOS</h4>
				</div>
				<div class="modal-body">
					<h4>¿Esta seguro que desea Presentar los equipos que ya fueron eliminados, mantenidos y los nuevos?</h4>
				</div>
				<input type="hidden" value="confirmarEquipos" name="accion" id="accion" />
				<input type="hidden" value="<?php echo $confirmo; ?>" name="idcabecera" id="idcabecera" />
				<input type="hidden" value="8" name="refestados" id="refestados" />
				<div class="modal-footer">
					<button v-if="activeEquipos.length == 0" type="submit" class="btn bg-orange waves-effect">
						<i class="material-icons" id="guardarFormulario">assignment_turned_in</i>
						<span>PRESENTAR</span>
					</button>
					<button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
				</div>
		  </div>
	 </div>
</div>
</form>

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



<form class="form" @submit.prevent="insertarEquiposdelegados">
<script type="text/x-template" id="modal-template-equipo">

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

			  	<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<label class="form-label">Nombre</label>
						<div class="form-group">
							<div class="form-line">
								<input value="" type="text" class="form-control" id="nombre" name="nombre" require />
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<label class="form-label">Categoria</label>
						<slot name="categorias"></slot>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
						<label class="form-label">Division</label>
						<slot name="division"></slot>

					</div>


				</div>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<label class="form-label">Seleccione el Countrie para fusionarse de ser necesario</label>
						<select @click="pasar()" class="form-control" multiple id="rrfusioncountries" name="rrfusioncountries" require >
							<?php echo $cadRefCountries; ?>
						</select>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<label class="form-label">Countries Seleccionados</label>
						<select @click="quitar()" class="form-control" multiple id="fusioncountries" name="fusioncountries" require >

						</select>
					</div>
				</div>
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
			<button class="btn bg-grey waves-effect" @click="$emit('close')">
                CANCELAR
			  </button>
			  	<button type="button" class="btn bg-green waves-effect" @click="crearEquipo()">
					<i class="material-icons">send</i>
					<span>CREAR</span>
				</button>

            </slot>
          </div>
        </div>
      </div>
    </div>

  </transition>
</script>
</form>


<form class="form" @submit.prevent="insertarFusiones">
<script type="text/x-template" id="modal-template-equipo-fusion">

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


				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<label class="form-label">Seleccione el Countrie para fusionarse de ser necesario</label>
						<select @click="pasar()" class="form-control" multiple id="rrfusioncountries" name="rrfusioncountries" require >
							<?php echo $cadRefCountries; ?>
						</select>
					</div>

					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
						<label class="form-label">Countries Seleccionados</label>
						<select @click="quitar()" class="form-control" multiple id="fusioncountries" name="fusioncountries" require >

						</select>

					</div>
				</div>
            </slot>
          </div>
			 <input type="hidden" name="refequiposdelegados" id="refequiposdelegados" value="0" />
          <div class="modal-footer">
            <slot name="footer">
			<button class="btn bg-grey waves-effect" @click="$emit('close')">
                CANCELAR
			  </button>
			  	<button type="button" class="btn bg-green waves-effect" @click="insertarFusion()">
					<i class="material-icons">send</i>
					<span>CREAR</span>
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


  <!-- use the modal component, pass in the prop -->
  <modal2 v-if="showModalEquipo" @close="showModalEquipo = false" @recargarequiposnuevos="getAllEquiposNuevos">
    <!--
      you can use custom content here to overwrite
      default content
    -->
	<h3 slot="header">Crear Equipo</h3>
	<select class="form-control show-tick" id="refcategorias" name="refcategorias" require slot="categorias" @change="onChange()" v-model="key">
		<option v-for="itemC in lstCategorias" :value="itemC.idcategoria">{{ itemC.categoria }}</option>
	</select>

	<select class="form-control show-tick" id="refdivisiones" name="refdivisiones" require slot="division">
		<option v-for="item in lstDivisiones" :value="item.iddivision" :key="item.iddivision">{{ item.division }}</option>
	</select>

  </modal2>


  <!-- use the modal component, pass in the prop -->
  <modal3 v-if="showModalFusion" @close="showModalFusion = false" @recargarfusiones="getFusion">
    <!--
      you can use custom content here to overwrite
      default content
    -->
	<h3 slot="header">Cargar Fusion</h3>



	</modal3>


</main>




<script>


	$(document).ready(function(){
		function traerDivision(idcategoria) {
			$.ajax({
				data: {
						idcategoria: idcategoria,
						idtemporada: <?php echo ($ultimaTemporada - 1); ?>,
						accion: 'traerUltimaDivisionPorTemporadaCategoria'
				},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {
					$('#refdivisiones').html('');
				},
				success:  function (response) {
					$('#refdivisiones').html(response);

				}
			});
		}

	});
</script>




<script>
	const paramsGetDelegado = new URLSearchParams();
   paramsGetDelegado.append('accion','VtraerDelegadosPorId');
	paramsGetDelegado.append('iddelegado',<?php echo $_SESSION['usuaid_aif']; ?>);

	const paramsGetEquipos = new URLSearchParams();
   paramsGetEquipos.append('accion','traerEquiposPorCountriesConFusion');
	paramsGetEquipos.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);
	paramsGetEquipos.append('idtemporada',<?php echo  $ultimaTemporada; ?>);

	const paramsGetEquiposEliminados = new URLSearchParams();
    paramsGetEquiposEliminados.append('accion','traerEquiposdelegadosEliminadosPorCountrie');
	paramsGetEquiposEliminados.append('idtemporada',<?php echo  $ultimaTemporada; ?>);
	paramsGetEquiposEliminados.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);

	const paramsGetEquiposNuevos = new URLSearchParams();
    paramsGetEquiposNuevos.append('accion','traerEquiposdelegadosPorCountrie');
	paramsGetEquiposNuevos.append('idtemporada',<?php echo  $ultimaTemporada; ?>);
	paramsGetEquiposNuevos.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);
	paramsGetEquiposNuevos.append('nuevo',1);

	const paramsGetEquiposQuedan = new URLSearchParams();
    paramsGetEquiposQuedan.append('accion','traerEquiposdelegadosPorCountrie');
	paramsGetEquiposQuedan.append('idtemporada',<?php echo  $ultimaTemporada; ?>);
	paramsGetEquiposQuedan.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);
	paramsGetEquiposQuedan.append('nuevo',0);

	const paramsCrearEquipo = new URLSearchParams();
    paramsCrearEquipo.append('accion','insertarEquiposdelegados');
	paramsCrearEquipo.append('idtemporada',<?php echo  $ultimaTemporada; ?>);
	paramsCrearEquipo.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);
	paramsCrearEquipo.append('idusuario',<?php echo  $idusuario; ?>);
	paramsCrearEquipo.append('refcategorias',0);
	paramsCrearEquipo.append('refdivisiones',0);
	paramsCrearEquipo.append('nombre','');
	paramsCrearEquipo.append('refcountries','');

	const paramsGetEliminarEquipoPasivo = new URLSearchParams();
    paramsGetEliminarEquipoPasivo.append('accion','');
	paramsGetEliminarEquipoPasivo.append('id',0);
	paramsGetEliminarEquipoPasivo.append('idtemporada',<?php echo  $ultimaTemporada; ?>);
	paramsGetEliminarEquipoPasivo.append('idusuario',<?php echo  $idusuario; ?>);

	const paramsMantenerEquipo = new URLSearchParams();
	paramsMantenerEquipo.append('accion','mantenerEquipoPasivo');
	paramsMantenerEquipo.append('id',0);
	paramsMantenerEquipo.append('idtemporada',<?php echo  $ultimaTemporada; ?>);
	paramsMantenerEquipo.append('idusuario',<?php echo  $idusuario; ?>);
	paramsMantenerEquipo.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);

	const paramsGeneral = new URLSearchParams();
	paramsGeneral.append('accion','verFusion');
	paramsGeneral.append('idequipodelegado',0);
	paramsGeneral.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);

	const paramsCategoria = new URLSearchParams();
	paramsCategoria.append('accion','traerUltimaDivisionPorTemporadaCategoria');
	paramsCategoria.append('idcategoria',1);
	paramsCategoria.append('idtemporada',<?php echo  ($ultimaTemporada - 1); ?>);

	const paramsCat = new URLSearchParams();
	paramsCat.append('accion','traerCategorias');

	const paramsGetFusion = new URLSearchParams();
	paramsGetFusion.append('accion','VtraerFusionPorEquiposDelegados');
	paramsGetFusion.append('idequipodelegado',0);

	const paramsCrearFusion = new URLSearchParams();
	paramsCrearFusion.append('accion','insertarFusionEquipos');

	paramsCrearFusion.append('refcountries','');




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


	Vue.component('modal2', {

		template: '#modal-template-equipo',
		methods: {
			pasar () {
				$('#rrfusioncountries option:selected').remove().appendTo('#fusioncountries');

			},
			quitar () {
				$('#fusioncountries option:selected').remove().appendTo('#rrfusioncountries');
			},
			crearEquipo () {

				$("#fusioncountries option").each(function(){
				// Marcamos cada valor como seleccionado
					$("#fusioncountries option[value="+this.value+"]").prop("selected",true);
				});
				paramsCrearEquipo.set('nombre',$('#nombre').val());
				paramsCrearEquipo.set('refcategorias',$('#refcategorias').val());
				paramsCrearEquipo.set('refdivisiones',$('#refdivisiones').val());
				paramsCrearEquipo.set('refcountries',$('#fusioncountries').val());
				paramsCrearEquipo.set('nuevo',1);


				axios.post('../../ajax/ajax.php', paramsCrearEquipo)
				.then(res => {

					if (res.data.error == '') {
						this.$swal("Ok!", res.data.mensaje, "success")
						this.$emit('recargarequiposnuevos', this.activeEquiposNuevos)
						this.$emit('close')

					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}

				});
			}

		}
	})


	Vue.component('modal3', {
		template: '#modal-template-equipo-fusion',
		methods: {
			pasar () {
				$('#rrfusioncountries option:selected').remove().appendTo('#fusioncountries');

			},
			quitar () {
				$('#fusioncountries option:selected').remove().appendTo('#rrfusioncountries');
			},
			insertarFusion () {

				$("#fusioncountries option").each(function(){
				// Marcamos cada valor como seleccionado
					$("#fusioncountries option[value="+this.value+"]").prop("selected",true);
				});

				//paramsCrearFusion.set('refequiposdelegados', $('#refequiposdelegados').val() );
				paramsCrearFusion.set('refcountries',$('#fusioncountries').val());


				axios.post('../../ajax/ajax.php', paramsCrearFusion)
				.then(res => {

					if (res.data.error == '') {
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
			activeEquipos: {},
			activeEquiposEliminados: {},
			activeEquiposNuevos: {},
			activeEquiposMantenidos: {},
			lstCategorias: {},
			items: [],
			lstDivisiones: {},
			showModal: false,
			lstFusion: {},
			showModalEquipo: false,
			showModalFusion: false

		},
		mounted () {
			this.getDelegado()
			this.getAllEquipos()
			this.getAllEquiposEliminados()
			this.getAllEquiposNuevos()
			this.getAllEquiposQuedan()
			this.getUltimaDivision()
			this.getCategorias()
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

			},
			getFusion : function(id) {
				this.showModalFusion = true
				paramsGetFusion.set('idequipodelegado',id)
				paramsCrearFusion.set('refequiposdelegados',id)

				$('#refequiposdelegados').val(id)

				$('#fusioncountries').html('');

				axios.post('../../ajax/ajax.php', paramsGetFusion)
				.then(res => {

					//this.lstFusion = res.data.datos
					this.items = res.data.datos
	            var total = 0;
	            for (var i = 0; i < this.items.length; i++) {
						$('#fusioncountries').append('<option value="' + this.items[i].idcountrie + '" selected>' + this.items[i].countrie + '</option>')
	            }


				});
			},
			getUltimaDivision () {
				axios.post('../../ajax/ajax.php', paramsCategoria)
				.then(res => {

					this.lstDivisiones = res.data.datos

				});
			},
			onChange() {
				paramsCategoria.set('idcategoria',this.key);

				axios.post('../../ajax/ajax.php', paramsCategoria)
				.then(res => {

					this.lstDivisiones = res.data.datos

				});
			},
			getCategorias () {
				axios.post('../../ajax/ajax.php', paramsCat)
				.then(res => {

					this.lstCategorias = res.data.datos

				});
			},
			getDelegado () {
					axios.post('../../ajax/ajax.php',paramsGetDelegado)
					.then(res => {

						this.activeDelegados = res.data.datos[0]
					})
			},
			guardarDelegado (e) {
				axios.post('../../ajax/ajax.php', new FormData(e.target))
				.then(res => {

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}

				});

			},
			confirmarEquipos (e) {
				axios.post('../../ajax/ajax.php', new FormData(e.target))
				.then(res => {

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
						setTimeout(function(){
							window.location.href = 'modificar.php';
						}, 2000);
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}

				});
			},
			presentar (evt) {
				$('#refestados').val(8)
				evt.preventDefault();
         	document.getElementById("guardarFormulario").click();
			},
			getAllEquipos () {
					axios.post('../../ajax/ajax.php',paramsGetEquipos)
					.then(res => {

						this.activeEquipos = res.data.datos
					})
			},
			getAllEquiposEliminados () {
					axios.post('../../ajax/ajax.php',paramsGetEquiposEliminados)
					.then(res => {

						this.activeEquiposEliminados = res.data.datos
					})
			},
			verFusion : function(id) {
				paramsGeneral.set('idequipodelegado',id);
				paramsGeneral.set('accion','verFusion');

				axios.post('../../ajax/ajax.php',paramsGeneral)
				.then(res => {
					this.$swal("Ok!", 'Fusion: ' + res.data.datos[0], "success")

				})
			},
			verFusionDelegados : function(id) {
				paramsGeneral.set('idequipodelegado',id);
				paramsGeneral.set('accion','traerFusionPorEquiposDelegados');

				axios.post('../../ajax/ajax.php',paramsGeneral)
				.then(res => {

					this.$swal("Ok!", res.data.datos[0], "success")

				})
			},
			eliminarEquipoPasivo : function(equi){

				paramsGetEliminarEquipoPasivo.set('id',equi.idequipo);
				paramsGetEliminarEquipoPasivo.set('accion','eliminarEquipoPasivo');

				axios.post('../../ajax/ajax.php',paramsGetEliminarEquipoPasivo)
				.then(res => {

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")

						this.getAllEquiposEliminados()
						this.getAllEquipos()
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}
				})
			},
			mantenerEquipoPasivo : function(equi){

				paramsMantenerEquipo.set('id',equi.idequipo);

				axios.post('../../ajax/ajax.php',paramsMantenerEquipo)
				.then(res => {

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")

						this.getAllEquiposQuedan()
						this.getAllEquipos()
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}
				})
			},


			eliminarEquipoDelegado : function(equi){

				paramsGetEliminarEquipoPasivo.set('id',equi.idequipo);
				paramsGetEliminarEquipoPasivo.set('accion','eliminarEquipoPasivo');

				axios.post('../../ajax/ajax.php',paramsGetEliminarEquipoPasivo)
				.then(res => {

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")

						this.getAllEquiposEliminados()
						this.getAllEquipos()
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}
				})
			},
			eliminarEquiposDelegadoDefinitivo : function(equi){

				paramsGetEliminarEquipoPasivo.set('id',equi.idequipodelegado);
				paramsGetEliminarEquipoPasivo.set('accion','eliminarEquiposdelegados');

				axios.post('../../ajax/ajax.php',paramsGetEliminarEquipoPasivo)
				.then(res => {

					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
						this.getAllEquiposEliminados()
						this.getAllEquiposNuevos()
						this.getAllEquiposQuedan()
						this.getAllEquipos()
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}
				})
			},
			getAllEquiposNuevos () {

				axios.post('../../ajax/ajax.php',paramsGetEquiposNuevos)
				.then(res => {

					this.activeEquiposNuevos = res.data.datos

				})
			},
			getAllEquiposQuedan () {

				axios.post('../../ajax/ajax.php',paramsGetEquiposQuedan)
				.then(res => {

					this.activeEquiposMantenidos = res.data.datos

				})
			}


		}
	})
</script>
</body>
<?php } ?>
</html>

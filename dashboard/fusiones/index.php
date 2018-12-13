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

////////////////////////////		 verifico que no entre por url de otro countrie /////////////////////////
$idequipodelegado = $_GET['id'];

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
										<h4>Fusion de Equipos</h4>
										<form class="form" id="formCountryEquiposMantenidos">
										<table class="table table-bordered table-striped table-hover highlight">
											<thead>
												<tr>

												</tr>
												<th>Countrie</th>
												<th>Categoria</th>
												<th>Division</th>
												<th>Equipo</th>
												<th>Estado Actual</th>
												<th>Acciones</th>
											</thead>
											<tbody>
												<tr v-for="fusion in fusiones" :key="fusion.idfusionequipo">
													<td>{{ fusion.countriepadre }}</td>
													<td>{{ fusion.categoria }}</td>
													<td>{{ fusion.division }}</td>
													<td>{{ fusion.nombre }}</td>
													<td style="font-size:22px;">
														<div v-if="fusion.idestado == 1">
															<span class="label label-primary">{{ fusion.estado }}</span>
														</div>
														<div v-else-if="fusion.idestado == 3">
															<span class="label label-success">{{ fusion.estado }}</span>
														</div>
														<div v-else-if="fusion.idestado == 5">
															<span class="label label-warning">{{ fusion.estado }}</span>
														</div>
														<div v-else="fusion.idestado == 3">
															<span class="label label-danger">{{ fusion.estado }}</span>
														</div>
													</td>
													<td>
														<div v-if="fusion.idestado == 1 || fusion.idestado == 5">
															<button style="margin-top:3px;" type='button' class='btn bg-green waves-effect' @click="modificarEstadoFusion(fusion.idfusionequipo, 3)">
																<i class="material-icons">done</i>
																<span>Aprobar</span>
															</button>
															<button style="margin-top:3px;" type='button' class='btn bg-red waves-effect' @click="modificarEstadoFusion(fusion.idfusionequipo, 4)">
																<i class="material-icons">clear</i>
																<span>Rechazar</span>
															</button>
															<button style="margin-top:3px;" type='button' class='btn bg-blue waves-effect' @click="modificarEstadoFusion(fusion.idfusionequipo, 4)">
																<i class="material-icons">update</i>
																<span>Marcar en Curso</span>
															</button>
														</div>
														
													</td>
												</tr>
											</tbody>
										</table>
										</form>
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
<?php echo $baseHTML->modalHTML('modalPerfil','Perfil','GUARDAR','Ingrese sus datos personales y los Email de los contactos','frmPerfil',$frmPerfil,'iddelegado','Delegados','VguardarDelegado'); ?>
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


	});
</script>




<script>
	const paramsGetDelegado = new URLSearchParams();
    paramsGetDelegado.append('accion','VtraerDelegadosPorId');
	paramsGetDelegado.append('iddelegado',<?php echo $_SESSION['usuaid_aif']; ?>);
	


	const paramsGeneral = new URLSearchParams();
	paramsGeneral.append('accion','traerFusionesPorEquipo');
	paramsGeneral.append('idequipodelegado',<?php echo $idequipodelegado; ?>);
	paramsGeneral.append('idcountrie',<?php echo $_SESSION['idclub_aif']; ?>);
	

	const paramsAcciones = new URLSearchParams();
	paramsAcciones.append('accion','cambiarEstadoFusion');
	paramsAcciones.append('idfusionequipo',0);
	paramsAcciones.append('refestados',0);


	

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
			activeClass: 'waves-effect',
			errorMensaje: '',
			successMensaje: '',
			activeDelegados: {},
			fusiones: {},
			showModal: false
		},
		mounted () {
			this.getDelegado()
			this.getFusiones()
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
			getDelegado () {
				axios.post('../../ajax/ajax.php',paramsGetDelegado)
				.then(res => {
                    
					this.activeDelegados = res.data.datos[0]
				})
			},
			getFusiones () {
				axios.post('../../ajax/ajax.php',paramsGeneral)
				.then(res => {
                    
					this.fusiones = res.data.datos
				})
			},
			modificarEstadoFusion (id, estado) {

				paramsAcciones.set('idfusionequipo', id);
				paramsAcciones.set('refestados', estado);

				axios.post('../../ajax/ajax.php',paramsAcciones)
				.then(res => {
                    
					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")

						this.getFusiones()

					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}
				})
			}


		}
	})
</script>
</body>
<?php } ?>
</html>



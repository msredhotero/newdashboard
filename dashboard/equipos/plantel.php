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

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a>';

$club = $serviciosReferencias->traerNombreCountryPorId($_SESSION['idclub_aif']);

$permiteRegistrar = 1;

$habilitado = 1;

$idequipo = $_GET['id'];

//////////////             validar que no entren por la url    ///////////////////////


///////////////////             fin                 ///////////////////////////////////


$resTemporadas = $serviciosReferencias->traerUltimaTemporada(); 

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);    
} else {
    $ultimaTemporada = 0;   
}

//die(var_dump($ultimaTemporada));

$resEquipos = $serviciosReferencias->traerEquiposdelegadosPorCountrieFinalizadoPorEquipo($_SESSION['idclub_aif'],$ultimaTemporada, $idequipo);

$categoria = mysql_result($resEquipos,0,'categoria');
$idcategoria = mysql_result($resEquipos,0,'refcategorias');

$division = mysql_result($resEquipos,0,'division');
$equipo = mysql_result($resEquipos,0,'nombre');
$estado = mysql_result($resEquipos,0,'estado');

$idusuario = $_SESSION['usuaid_aif'];

$confirmo = $serviciosReferencias->existeCabeceraConfirmacion($ultimaTemporada, $_SESSION['idclub_aif']);

$idEstado = $serviciosReferencias->devolverIdEstado("dbcabeceraconfirmacion",$confirmo,"idcabeceraconfirmacion");

if (($idEstado == 0) || ($idEstado == 1)) {
	header('Location: index.php');
}

$resCabecera = $serviciosReferencias->traerCabeceraconfirmacionPorId($confirmo);

$estado = mysql_result($resCabecera,0,'estado');

$lblEstado = '';

switch ($idEstado) {
	case (2):
		$lblEstado = 'label-warning';
		break;
	case (3):
		$lblEstado = 'label-success';
		break;
	case (4):
		$lblEstado = 'label-danger';
		break;
}




$tabla 			= "dbdelegados";

$lblCambio	 	= array("refusuarios","email1","email2","email3","email4");
$lblreemplazo	= array("Usuario","Email de Contacto 1","Email de Contacto 2","Email de Contacto 3","Email de Contacto 4");


$resModelo 	= $serviciosReferencias->traerUsuariosPorId($_SESSION['usuaid_aif']);
$cadRef 	= $serviciosFunciones->devolverSelectBox($resModelo,array(5),'');

$refdescripcion = array(0 => $cadRef);
$refCampo 	=  array("refusuarios");

$frmPerfil 	= $serviciosFunciones->camposTabla("insertarDelegados" ,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);


////////////////////////////		 verifico si existe alguna fusion donde no se confirmaron los countries /////////////////////////
$verificarFusion = $serviciosReferencias->traerEstadosFusionesAceptadasPorCountrie($_SESSION['idclub_aif']);

////////////////////////////// 				FIN				  /////////////////////////


$resTipoJugador		=	$serviciosReferencias->traerTipojugadores();
$cadRefTipoJug		=	$serviciosFunciones->devolverSelectBox($resTipoJugador,array(1),'');


/////////////////////////      BUSCO SI TIENE ALGUNA FUSION   ////////////////////////

$resFusiones = $serviciosReferencias->traerFusionPorEquiposCountrie($idequipo, $_SESSION['idclub_aif']);
$cadCountries = $_SESSION['idclub_aif'];
if (mysql_num_rows($resFusiones) > 0) {
	while ($row = mysql_fetch_array($resFusiones)) {
		$cadCountries .= ','.$row['refcountries'];
	}
}


/////////////////////////		FIN 						/////////////////////////
/*
$lstJugadoresPorCountries = $serviciosReferencias->traerJugadoresPorCountries($cadCountries);
$cadRefJugadores 	= $serviciosFunciones->devolverSelectBox($lstJugadoresPorCountries,array(1,2),' - ');
*/

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

	<!-- CSS file -->
	<link rel="stylesheet" href="../../css/easy-autocomplete.min.css">
	<link rel="stylesheet" href="../../css/easy-autocomplete.themes.min.css">
	

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
								Equipo: <?php echo $equipo; ?>
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
										
										<h3>Equipos Generados - ESTADO: <span class="label <?php echo $lblEstado; ?>"><?php echo $estado; ?></span></h3>
										<p>Recuerde que el plantel del equipo se deberá cargar </p>
										<div class="alert bg-indigo animated shake">
											<strong>Importante!</strong> Toda la información será confirmada por la Asociación. Imprimir y firmar la lista de Equipos
										</div>

									</div>
									
								</div>

								<div class="row">
									<div class="col-lg-6 col-md-6">
										<div class="card">
											<div class="header">
											<div class="card-image">
												<img src="../../imagenes/6.jpg">
												<span class="card-title"><?php echo $equipo; ?></span>
											</div>
											</div>

											<div class="body">
												<h4 style="border-bottom: 2px solid #555; transition: .3s ease-in-out;"><b>PLANTEL</b></h4>
												<div class="lstPlantel">

												</div>
											</div>
											
											<div class="card-action">
												<a href="javascript:void(0)"><?php echo $categoria; ?></a>
												<a href="javascript:void(0)"><?php echo $division; ?></a>
											</div>
										</div>
									</div>
									<div class="col-lg-6 col-md-6">
										<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="alert bg-red animated shake">
												<strong>Importante!</strong> Los jugadores deben cumplir esta regla para ingresar: <span class="regla">{{ activeDefinicion }}</span>
											</div>
										</div>
										</div>
										<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 form-control-label">
												<label for="buscarlbl">Buscar Jugador:</label>
											</div>
											<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
					                        	<div style="position: relative; height: 80px;">
					                                
					                                <input id="round" class="countrie" style="widows:100%;"/>
					                            </div>
					                            <div id="selction-ajax"></div>
					                            
					                        
											</div>
										</div>
										</div>
										<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 form-control-label">
												<label for="buscarlbl">Tipo Jugador:</label>
											</div>
											<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
												<select class="form-control show-tick" data-live-search="true" id="reftipojugadores" name="reftipojugadores">
			                                        <?php echo $cadRefTipoJug; ?>
			                                    </select>
											</div>
										</div>
										</div>
										<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="col-lg-4 col-md-4 col-sm-4 col-xs-5 form-control-label">
												<label for="buscarlbl">Solicitar Habilitación:</label>
											</div>

											<div class="col-lg-8 col-md-8 col-sm-8 col-xs-7">
												<div class='switch'>
												<label><input type='checkbox' id="habilita" name="habilita"/><span class='lever switch-col-green'></span></label>
												</div>
											</div>
										</div>
										</div>
										<div class="row">
										<div class="col-lg-12 col-md-12">
											<div class="alert bg-orange animated bounce delay-5s">
												<i class="material-icons">assignment_late</i> <strong>Aclaración!</strong> La habilitación la autorizará la asociación
											</div>
										</div>
										</div>
										
									</div>
								</div>

								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
									<form class="form" id="formJugadores">
									
									</form>
										
									</div>
								</div>
								<hr>
								
								<div class="button-demo">
									<button v-if="confirmado == 2" type="button" class="btn bg-brown waves-effect imprimir">
										<i class="material-icons">print</i>
										<span>IMPRIMIR LISTA DE BUENA FE</span>
									</button>

									<button type="button" class="btn bg-teal waves-effect imprimir">
										<i class="material-icons">done</i>
										<span>FINALIZAR</span>
									</button>
									
								</div>
								
							
							<div>
							
							</form>
							</div>							
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card">
					<div class="header">
						<h2>
							SELECCIONE LA IMAGEN DEL ESCUDO DEL EQUIPO PARA SUBIR
						</h2>
						<ul class="header-dropdown m-r--5">
							<li class="dropdown">
								<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									<i class="material-icons">more_vert</i>
								</a>
								<ul class="dropdown-menu pull-right">
									
								</ul>
							</li>
						</ul>
					</div>
					<div class="body">
						<form action="subir.php" id="frmFileUpload" class="dropzone" method="post" enctype="multipart/form-data">
							<div class="dz-message">
								<div class="drag-icon-cph">
									<i class="material-icons">touch_app</i>
								</div>
								<h3>Arrastre y suelte una imagen aqui o haga click y busque una imagen en su ordenador.</h3>
								
							</div>
							<div class="fallback">
								<input name="file" type="file" id="archivos" />
								
							</div>
						</form>
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

<script src="../../js/jquery.easy-autocomplete.min.js"></script> 

<script src="../../js/pages/ui/tooltips-popovers.js"></script>

<!-- Dropzone Plugin Js -->
<script src="../../plugins/dropzone/dropzone.js"></script>

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
		function traerImagen() {
			$.ajax({
				data:  {id: <?php echo $idequipo; ?>, 
						accion: 'traerImgenEquipo'},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {
						
				},
				success:  function (response) {

					$(".thumbnail img").attr("src",response);
						
				}
			});
		}

		Dropzone.prototype.defaultOptions.dictFileTooBig = "Este archivo es muy grande ({{filesize}}MiB). Peso Maximo: {{maxFilesize}}MiB.";

		Dropzone.options.frmFileUpload = {
			maxFilesize: 2,
			addRemoveLinks: true,
			acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
			accept: function(file, done) {
				done();
			},
			init: function() {
				this.on('success', function( file, resp ){
					traerImagen();
					swal("Correcto!", resp.replace("1", ""), "success");
				});

				this.on('error', function( file, resp ){
					swal("Error!", resp.replace("1", ""), "warning");
				});
			}
		};

		$('.imprimir').click(function() {
			window.open("../../reportes/rptEquiposCountriesDelegados.php?idcountrie=" + <?php echo $_SESSION['idclub_aif']; ?> ,'_blank');	
		});

		function eliminarJugadorDePlantel(id) {
			$.ajax({
				data:  {id: id, 
						accion: 'eliminarConectorDefinitivamenteDelegado'},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {
						
				},
				success:  function (response) {
					traerJugadoresPlantel();
						
				}
			});
		}


		function showConfirmMessage(id) {
		    swal({
		        title: "¿Desea Eliminar al jugador del Plantel?",
		        text: "Solo eliminara el jugador del plantel, no eliminara al jugador de la base de datos!",
		        type: "warning",
		        showCancelButton: true,
		        confirmButtonColor: "#DD6B55",
		        confirmButtonText: "Si!",
		        closeOnConfirm: false
		    }, function () {
		    	eliminarJugadorDePlantel(id);
		        swal("Eliminado!", "El jugador fue eliminado con exito.", "success");
		    });
		}


		$(document).on('click', '.varEliminarJugador', function(e){

			  if (!isNaN($(this).attr("id"))) {
				
				showConfirmMessage($(this).attr("id"));

			  } else {
				alert("Error, vuelva a realizar la acción.");	
			  }
		});//fin del boton eliminar

		function traerJugadoresPlantel() {
			
			$.ajax({
				data:  {id: <?php echo $idequipo; ?>, 
						reftemporadas: <?php echo $ultimaTemporada; ?>, 
						accion: 'traerConectorActivosPorEquiposDelegado'},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {
				
				},
				success:  function (response) {
					
					$('.lstPlantel').html(response);
					
				}
			});	
		}

		traerJugadoresPlantel();

		function agregarJugador(refjugadores, reftipojugadores, refequipos, refcountries, refcategorias, reftemporada) {
		
			$.ajax({
				data:  {refjugadores: refjugadores, 
						reftipojugadores: reftipojugadores, 
						refequipos: refequipos, 
						reftemporada: reftemporada,
						refcountries: refcountries,
						refcategorias: refcategorias,
						habilita: $('#habilita').prop('checked') ? 1 : 0,
						accion: 'insertarConectorAjax'},
				url:   '../../ajax/ajax.php',
				type:  'post',
				beforeSend: function () {
				
				},
				success:  function (response) {
					if (response == '') {
						traerJugadoresPlantel();
					} else {
						swal("Error!", response, "error");
					}
					
				}
			});	
		}
		
		$(document).on('click', '.agregarJugador', function(e){
			agregarJugador($(this).attr("id"), $('#reftipojugadores').val(), <?php echo $idequipo; ?>, <?php echo $_SESSION['idclub_aif']; ?>, <?php echo $idcategoria; ?>, <?php echo $ultimaTemporada; ?>);
			
		});//fin del boton modificar


		var options = {

		  url: function(phrase) {
			return "../../json/jugadoresPorEquipos.php?countrie='<?php echo $cadCountries; ?>'";
		  },
		
		  getValue: function(element) {
			return element.name;
		  },
		
		  ajaxSettings: {
			dataType: "json",
			method: "GET",
			data: {
			  dataType: "json"
			}
		  },
		
		  preparePostData: function(data) {
			data.phrase = $("#round").val();
			return data;
		  },
		  
		  list: {
				onClickEvent: function() {
					var value = $("#round").getSelectedItemData().id;
		
					$('#selction-ajax').html('<button type="button" id="' + value + '" class="btn bg-green waves-effect agregarJugador"> \
													<i class="material-icons">add</i> \
													<span>CARGAR</span> \
												</button>');
				},
				
				match: {
					enabled: true
				}
		  },
		  theme: "round",
		  requestDelay: 100
		};
		
		$("#round").easyAutocomplete(options);
		

	});
</script>




<script>
	const paramsGetDelegado = new URLSearchParams();
    paramsGetDelegado.append('accion','VtraerDelegadosPorId');
	paramsGetDelegado.append('iddelegado',<?php echo $_SESSION['usuaid_aif']; ?>);

	const paramsBasico = new URLSearchParams();
    paramsBasico.append('accion','traerJugadoresPorCountries');
	paramsBasico.append('lstcountries',<?php echo $cadCountries; ?>);

	
	const paramsGeneral = new URLSearchParams();
	paramsGeneral.append('accion', 'traerDefinicionesPorTemporadaCategoriaTipoJugador');
	paramsGeneral.append('resTemporada', <?php echo $ultimaTemporada; ?>);
	paramsGeneral.append('resCategoria', <?php echo $idcategoria; ?>);
	paramsGeneral.append('resTipoJugador', 1);


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
			activeJugadores: {},
			activeDefinicion: [],
			showModal: false,
			confirmado: <?php echo $idEstado; ?>,
			verificarFusion: <?php echo $verificarFusion; ?>	
			
		},
		mounted () {
			this.getDelegado()
			this.getDefinicion()
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
			getDefinicion () {
				axios.post('../../ajax/ajax.php',paramsGeneral)
				.then(res => {
					
					this.activeDefinicion = res.data.datos[0]
				})
			}
		}
	})
</script>
</body>
<?php } ?>
</html>



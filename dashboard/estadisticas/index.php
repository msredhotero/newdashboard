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
$serviciosReferenciasRemoto 	= new ServiciosReferencias();

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

$partido = $serviciosArbitros->traerPartidosPorArbitrosPartido($_SESSION['idarbitro_aif'],$id);
$partidoAux = $serviciosArbitros->traerPartidosPorArbitrosPartido($_SESSION['idarbitro_aif'],$id);

$resultado = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($id, $_SESSION['idarbitro_aif']);


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
	$golesvisitante = mysql_result($resultado,0,'golesvisitante');
	$amarillas = mysql_result($resultado,0,'amarillas');
	$expulsados = mysql_result($resultado,0,'expulsados');
	$informados = mysql_result($resultado,0,'informados');
	$dobleamarillas = mysql_result($resultado,0,'dobleamarillas');

} else {
	//si todavia no cargue el partido
	$idPlanilla = $serviciosArbitros->insertarPlanillasarbitrosCorto($id,$_SESSION['idarbitro_aif']);

	//die(var_dump($idPlanilla));
	$resultadolocal = 0;
	$resultadovisitante = 0;
	$goleslocal = 0;
	$golesvisitante = 0;
	$amarillas = 0;
	$expulsados = 0;
	$informados = 0;
	$dobleamarillas = 0;

	$resultado = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($id, $_SESSION['idarbitro_aif']);
}

// Ruta del directorio donde están los archivos
$path  = '../../arbitros/'.$_SESSION['idarbitro_aif'].'/'.$id;

if (!file_exists('../../arbitros/'.$_SESSION['idarbitro_aif'].'/')) {
	mkdir('../../arbitros/'.$_SESSION['idarbitro_aif'], 0777);
}

if (!file_exists($path)) {
	mkdir($path, 0777);
}
// Arreglo con todos los nombres de los archivos
$files = array_diff(scandir($path), array('.', '..'));

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

$refEstadoPlanilla = $serviciosArbitros->traerEstadosPorIn('1,2');
$cadEP = $serviciosFunciones->devolverSelectBoxActivo($refEstadoPlanilla,array(1),'', mysql_result($resultado,0,'refestados'));

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
									<?php echo $formulario; ?>
								</div>
								<div class="row">
									<div class="col-xs-6 col-md-6 col-lg-6">
										<h4>Planilla Cargada</h4>
										<a href="javascript:void(0);" class="thumbnail">
											<img class="img-responsive">
										</a>
										<div id="example1"></div>

									</div>
								</div>
								<div class="row">
									<div class="col-xs-12 col-md-12 col-lg-12">
										<div class="alert alert-info">
											<p>Para continuar debe cambiar el estado a FINALIZADO y GUARDAR.</p>
										</div>
									</div>
									<div class="col-xs-12 col-md-12 col-lg-12">
										<?php
										if (mysql_result($resultado,0,'refestados') == 2) {
										?>
										<button type="button" class="btn btn-warning waves-effect btnEstadistica">
											<i class="material-icons">insert_chart</i>
											<span>CARGAR DETALLE DE ESTADISTICA</span>
										</button>
										<?php
										} else {
										?>
											<?php
											if (count($files)> 0) {
											?>
											<button type="submit" class="btn btn-info waves-effect btnGuardar">
												<i class="material-icons">save</i>
												<span>GUARDAR</span>
										   </button>
											<?php
											} else {
											?>
											<div class="alert alert-warning infoPlanilla">
												<p>Debe cargar la planilla para GUARDAR y continuar con la carga de la estadistica del partido.</p>
											</div>
											<button type="submit" class="btn btn-info waves-effect btnGuardar" style="display:none;">
												<i class="material-icons">save</i>
												<span>GUARDAR</span>
										   </button>
										<?php } ?>
										<?php } ?>


							   	</div>
								</div>

							</form>

						</div>
					</div>
				</div>
			</div>


		</div>

		<?php
		if (mysql_result($resultado,0,'refestados') == 1) {
		?>
		<div class="row clearfix subirImagen">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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
								<ul class="dropdown-menu pull-right">
									<li><a href="javascript:void(0);">Action</a></li>
									<li><a href="javascript:void(0);">Another action</a></li>
									<li><a href="javascript:void(0);">Something else here</a></li>
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
								<h3>Arrastre y suelte una imagen O PDF aqui o haga click y busque una imagen en su ordenador.</h3>

							</div>
							<div class="fallback">

								<input name="file" type="file" id="archivos" />
								<input type="hidden" id="idjugador" name="idjugador" value="<?php echo mysql_result($resResultado,0,'idjugador'); ?>" />
								<input type="hidden" id="iddocumentacion" name="iddocumentacion" value="<?php echo $idDocumentacion; ?>" />


							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>



		</div>

	</div>




</section>


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




<script>


	function traerImagen() {
		$.ajax({
			data:  {idfixture: <?php echo $id; ?>,
					idarbitro: <?php echo $_SESSION['idarbitro_aif']; ?>,
					accion: 'traerArchivoPlanillaPorArbitroFixture'},
			url:   '../../ajax/ajax.php',
			type:  'post',
			beforeSend: function () {

			},
			success:  function (response) {
				var cadena = response.datos.type.toLowerCase();

				if (response.datos.type != '') {
					if (cadena.indexOf("pdf") > -1) {
						PDFObject.embed(response.datos.imagen, "#example1");
						$('#example1').show();
						$(".thumbnail").hide();
					} else {
						$(".thumbnail img").attr("src",response.datos.imagen);
						$(".thumbnail").show();
						$('#example1').hide();
					}
				}

			}
		});
	}

	traerImagen();




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
				traerImagen();
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

	$(document).ready(function(){

		$('#goleslocal').prop('required',true);
		$('#golesvisitante').prop('required',true);
		$('#amarillas').prop('required',true);
		$('#expulsados').prop('required',true);
		$('#informados').prop('required',true);
		$('#dobleamarillas').prop('required',true);
		$('#observaciones').prop('required',false);

		$('.frmNuevo').submit(function(e){

			e.preventDefault();
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

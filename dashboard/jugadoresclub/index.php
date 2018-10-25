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

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a><a class="navbar-brand" href="index.php">Jugadores Por Club</a>';


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


$resPermiteRegistrar = $serviciosReferencias->traerVigenciasoperacionesPorModuloVigencias(2,date('Y-m-d'));

if (mysql_num_rows($resPermiteRegistrar)>0) {
	$permiteRegistrar = 1;
} else {
	$permiteRegistrar = 0;
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
	$habilitado = 0;
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
$cadRef2j 	= $serviciosFunciones->devolverSelectBox($resCountries,array(1),'');

$resUsua = $serviciosUsuario->traerUsuarioId($_SESSION['usuaid_aif']);
$cadRef3j 	= $serviciosFunciones->devolverSelectBox($resUsua,array(3),'');

$refdescripcion2 = array(0 => $cadRefj,1 => $cadRef2j,2 => $cadRef3j);
$refCampo2 	=  array("reftipodocumentos","refcountries","refusuarios");

$formularioJugador 	= $serviciosFunciones->camposTabla("insertarJugadorespre" ,$tabla2,$lblCambio2,$lblreemplazo2,$refdescripcion2,$refCampo2);
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

$lstNuevosJugadores = $serviciosFunciones->camposTablaView($cabeceras,$serviciosReferencias->traerJugadoresprePorCountries($refClub),9);



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
<main id="app">
<section class="content" style="margin-top:-10px;">

	<div class="container-fluid">
		<div class="row clearfix">

			<form id="formjugadoresclub" method="POST" role="form">
        	<div class="row">

			<?php 
				$country = '';
				$fecha = '';
				$cadCabecera = '';
				$primero = 0;
				while ($row = mysql_fetch_array($resJugadoresPorCountries)) {
					if ($country != $refClub)  {
						
						
						$cadCabecera .= '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="card">
							<div class="header">
								<h2>
									JUGADORES CARGADOS
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
							</div><div class="table-responsive">
										<table class="table table-bordered table-striped">
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
										<tbody>';
										
						$primero = 1;
						$country = $refClub;	
					}
					
					$cadCabecera .= "<tr>
										<td>".$row['apellido']."</td>
										<td>".$row['nombres']."</td>
										<td>".$row['nrodocumento']."</td>
										<td><input class='form-control' type='text' name='numeroserielote".$row['idjugador']."' id='numeroserielote".$row['idjugador']."' value='".$row['numeroserielote']."'/></td>
										<td><input class='form-control' type='checkbox' name='fechabaja".$row['idjugador']."' id='fechabaja".$row['idjugador']."' ".($row['fechabaja'] == 'Si' ? 'checked' : '')."/></td>
										<td><input class='form-control' type='checkbox' name='articulo".$row['idjugador']."' id='articulo".$row['idjugador']."'  ".($row['articulo'] == 'Si' ? 'checked' : '')."/></td>
										
										<td>";
					if ($permiteRegistrar == 1) {
						
						$cadCabecera .=			"<button type='button' class='btn btn-primary guardarJugadorClubSimple' id='".$row['idjugador']."'>Guardar</button>";
					}
					$cadCabecera .= "</td>
									</tr>";
			
				}
				
				$cadCabecera .= '</tbody></table></div></div>
				</div>';
				
				echo $cadCabecera;
			?>
            </div>

            <div class="row" style="padding: 25px;">
            	<div class="panel panel-primary">
				  <div class="panel-heading">Jugadores Nuevos</div>
				  <div class="panel-body"><?php echo str_replace('example','example1', $lstNuevosJugadores); ?></div>
				</div>
            	
            	<div class="col-md-6">
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

            </div>

            <div class='row' style="margin-left:25px; margin-right:25px;">
                <div class='alert'>
                
                </div>
                <div id='load'>
                
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                <ul class="list-inline" style="margin-top:15px;">
                	
                    <li>
                        <button type="button" class="btn btn-danger" id="btnImprimir" style="margin-left:0px;">Imprimir</button>
                    </li>
                    <?php if ($habilitado == 0) { ?>
                    <li>
                        <button type="button" class="btn btn-success cerrar" id="btnAbrir" style="margin-left:0px;">Abrir</button>
                    </li>
                    <?php } else { ?>
                    <li>
                        <button type="button" class="btn btn-warning cerrar" id="btnCerrar" style="margin-left:0px;">Cerrar</button>
                    </li>
                    <?php } ?>
                    <li>
                    	<button type="button" data-toggle="modal" data-target="#myModal3" class="btn btn-success" id="agregarContacto"><span class="glyphicon glyphicon-plus"></span> Agregar Jugador</button>
                    </li>
                    <li>
                        <button type="button" class="btn btn-info" id="btnExcel1" style="margin-left:0px;" onClick="location.href = 'http://www.aif.org.ar/wp-content/uploads/2017/12/buenafe.xlsx'"><span class="glyphicon glyphicon-save"></span> Lista de Buena Fe/Altas de equipos</button>
                    </li>
                    <li>
                        <button type="button" class="btn btn-info" id="btnExcel2" style="margin-left:0px;" onClick="location.href = 'http://www.aif.org.ar/wp-content/uploads/2016/09/buenafemo.xlsx'"><span class="glyphicon glyphicon-save"></span> Modificaciones de Lista de Buena Fe/Altas de equipos</button>
                    </li>
                    <li>
                        <button type="button" class="btn btn-danger" id="btnCondicionJugador" style="margin-left:0px;">Reporte Condicion de Jugadores</button>
                    </li>
                </ul>
                </div>
            </div>
            <input type="hidden" id="refcountries" name="refcountries" value="<?php echo $refClub; ?>"/>
            </form>

		</div>
	</div>


</section>

<?php echo $baseHTML->cargarArchivosJS('../../'); ?>


<!-- Modal Large Size -->
<transition name="fade">
<form class="form" @submit.prevent="guardarDelegado">
<?php echo $baseHTML->modalHTML('modalPerfil','Perfil','GUARDAR','Ingrese sus datos personales y los Email de los contactos','frmPerfil',$frmPerfil,'iddelegado','Delegados','VguardarDelegado'); ?>
</form>
</transition>

</main>
<!-- VUE JS -->
<script src="https://cdn.jsdelivr.net/npm/vue"></script>

<!-- axios -->
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>



<script>
	$(document).ready(function(){
		$('#menuPerfil').click(function() {
			$('#modalPerfil').modal();
		});

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
	});
</script>

<script>
	const paramsGetDelegado = new URLSearchParams();
	paramsGetDelegado.append('accion','VtraerDelegadosPorId');
	paramsGetDelegado.append('iddelegado',1);

	const app = new Vue({
		el: "#app",
		data: {
			errorMensaje: '',
			successMensaje: '',
			activeDelegados: {}
			
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

			},
			getDelegado () {
				axios.post('../ajax/ajax.php',paramsGetDelegado)
				.then(res => {
					
					//this.$refs['ref_nombres'].value = res.data.datos[0].nombres
					this.activeDelegados = res.data.datos[0]
				})
			},
			guardarDelegado (e) {
				axios.post('../ajax/ajax.php', new FormData(e.target))
				.then(res => {
					this.setMensajes(res)
					
				});

				
			}
		}
	})
</script>
</body>
<?php } ?>
</html>



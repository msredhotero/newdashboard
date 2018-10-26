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

$lstNuevosJugadores = $serviciosReferencias->traerJugadoresprePorCountries($refClub);



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
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

	<!-- axios -->
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

	<script src="https://unpkg.com/vue-swal"></script>

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
										<li><a href="javascript:void(0);">Action</a></li>
										<li><a href="javascript:void(0);">Another action</a></li>
										<li><a href="javascript:void(0);">Something else here</a></li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="body table-responsive">
							<div class="row clearfix">
								<div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
									<label for="email_address_2">Buscar:</label>
								</div>
								<div class="col-lg-10 col-md-10 col-sm-8 col-xs-7">
									<div class="form-group">
										<div class="form-line">
											<input type="text" id="buscar" class="form-control" placeholder="Ingrese los datos de la busqueda" v-model="busqueda" v-on:keyup.enter="buscarJugadoresPorClub" />
										</div>
									</div>
								</div>
							</div>
							<table class="table table-bordered table-striped table-hover" id="example">
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
									<td><input class='form-control' type='text' name='numeroserielote' id='numeroserielote' :value="jugador.numeroserielote" v-model="jugador.numeroserielote"/></td>
									<td>
									<div class='switch'>
										<label><input type='checkbox' v-model="jugador.fechabajacheck" v-bind:id="jugador.id"/><span class='lever switch-col-green'></span></label>
									</div>
									
									</div>
									</td>
									<td>
									<div class='switch'>
										<label><input type='checkbox' v-model="jugador.articulocheck" v-bind:id="jugador.id"/><span class='lever switch-col-green'></span></label>
									</div>
									
									</td>
									
									<td>
									<button type='button' class='btn btn-primary guardarJugadorClubSimple' id=''>Guardar</button>
									</td>
								</tr>
			
								</tbody>
							</table>
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
                                <li>
                                    <a href="javascript:void(0);" data-toggle="cardloading" data-loading-effect="timer" data-loading-color="lightBlue">
                                        <i class="material-icons">loop</i>
                                    </a>
                                </li>
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
                        <div class="body table-responsive">
						<table class="table table-bordered table-striped table-hover js-basic-example dataTable" id="example">
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
										<button type="button" class="btn bg-amber btn-circle waves-effect waves-circle waves-float">
											<i class="material-icons">create</i>
										</button>
									</td>
									<td align="center">
										<button type="button" class="btn bg-red btn-circle waves-effect waves-circle waves-float">
											<i class="material-icons">delete</i>
										</button>
									</td>
								</tr>

							<?php } ?>
							</tbody>
						</table>
						<?php //echo str_replace('example','example1', $lstNuevosJugadores); ?>
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
								<ul class="dropdown-menu pull-right">
									<li><a href="javascript:void(0);" class=" waves-effect waves-block">Action</a></li>
									<li><a href="javascript:void(0);" class=" waves-effect waves-block">Another action</a></li>
									<li><a href="javascript:void(0);" class=" waves-effect waves-block">Something else here</a></li>
								</ul>
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
							<button type="button" class="btn btn-danger" id="btnImprimir" style="margin-left:0px;">Imprimir</button>
						<?php if ($habilitado == 0) { ?>
							<button type="button" class="btn btn-success cerrar" id="btnAbrir" style="margin-left:0px;">Abrir</button>
						<?php } else { ?>
							<button type="button" class="btn btn-warning cerrar" id="btnCerrar" style="margin-left:0px;">Cerrar</button>
						<?php } ?>
							<button type="button" data-toggle="modal" data-target="#myModal3" class="btn btn-success" id="agregarContacto"><span class="glyphicon glyphicon-plus"></span> Agregar Jugador</button>
							<button type="button" class="btn btn-info" id="btnExcel1" style="margin-left:0px;" onClick="location.href = 'http://www.aif.org.ar/wp-content/uploads/2017/12/buenafe.xlsx'"><span class="glyphicon glyphicon-save"></span> Lista de Buena Fe/Altas de equipos</button>
							<button type="button" class="btn btn-info" id="btnExcel2" style="margin-left:0px;" onClick="location.href = 'http://www.aif.org.ar/wp-content/uploads/2016/09/buenafemo.xlsx'"><span class="glyphicon glyphicon-save"></span> Modificaciones de Lista de Buena Fe/Altas de equipos</button>
							<button type="button" class="btn btn-danger" id="btnCondicionJugador" style="margin-left:0px;">Reporte Condicion de Jugadores</button>
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
            </form>

		</div>
	</div>


</section>


<?php echo $baseHTML->cargarArchivosJS('../../'); ?>
<!-- Wait Me Plugin Js -->
<script src="../../plugins/waitme/waitMe.js"></script>

<!-- Custom Js -->
<script src="../../js/pages/cards/colored.js"></script>

<script src="../../plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="../../plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="../../js/pages/tables/jquery-datatable.js"></script>
<!-- Modal Large Size -->
<transition name="fade">
<form class="form" @submit.prevent="guardarDelegado">
<?php //echo $baseHTML->modalHTML('modalPerfil','Perfil','GUARDAR','Ingrese sus datos personales y los Email de los contactos','frmPerfil',$frmPerfil,'iddelegado','Delegados','VguardarDelegado'); ?>
</form>
</transition>

</main>





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
	paramsGetDelegado.append('iddelegado',<?php echo $_SESSION['usuaid_aif']; ?>);

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
			busqueda: ''		
			
		},
		mounted () {
			this.getJugadoresPorClub(this.busqueda),
			this.getPaginasJC(this.busqueda),
			this.getActivePagina()
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
					this.setMensajes(res)
					
				});

				
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



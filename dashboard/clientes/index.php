<?php

session_start();

if (!isset($_SESSION['usua_predio']))
{
	header('Location: ../../error.php');
} else {


include ('../../includes/funcionesUsuarios.php');
include ('../../includes/funcionesHTML.php');
include ('../../includes/funciones.php');
include ('../../includes/funcionesReferencias.php');
include ('../../includes/base.php');

$serviciosUsuario = new ServiciosUsuarios();
$serviciosHTML = new ServiciosHTML();
$serviciosFunciones = new Servicios();
$serviciosReferencias 	= new ServiciosReferencias();
$baseHTML = new BaseHTML();

$fecha = date('Y-m-d');

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_predio'],"Clientes",$_SESSION['refroll_predio'],'');

$configuracion = $serviciosReferencias->traerConfiguracion();

$tituloWeb = mysql_result($configuracion,0,'sistema');

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a>';


/////////////////////////   DATOS   ///////////////////////////////////////////////////////
/////////////////////// Opciones pagina ///////////////////////////////////////////////
$singular = "Cliente";

$plural = "Clientes";

$eliminar = "eliminarClientes";

$insertar = "insertarClientes";

//////////////////////// Fin opciones ////////////////////////////////////////////////


/////////////////////// Opciones para la creacion del formulario  /////////////////////
$tabla 			= "dbclientes";

$lblCambio	 	= array("nrodocumento","fechanacimiento","telefono","direccion");
$lblreemplazo	= array("Nro Documento","Fecha Nacimiento","Teléfono","dirección");


$cadRef 	= '';

$refdescripcion = array();
$refCampo 	=  array();
//////////////////////////////////////////////  FIN de los opciones //////////////////////////




/////////////////////// Opciones para la creacion del view  apellido,nombre,nrodocumento,fechanacimiento,direccion,telefono,email/////////////////////
$cabeceras 		= "	<th>Apellido</th>
					<th>Nombre</th>
					<th>Nro Documento</th>
					<th>Fecha Nacimiento</th>
					<th>Dirección</th>
					<th>Teléfono</th>
					<th>Email</th>";

//////////////////////////////////////////////  FIN de los opciones //////////////////////////




$formulario 	= $serviciosFunciones->camposTabla($insertar ,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);

$lstCargados 	= $serviciosFunciones->camposTablaView($cabeceras,$serviciosReferencias->traerClientes(),7);



/////////////////////////        FIN DATOS  ////////////////////////////////////////////////


$arCSS = array();

$arJS = array();

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

    <?php echo $baseHTML->cargarArchivosCSS('../../',$arCSS); ?>
	
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
    <?php echo $baseHTML->cargarSECTION($_SESSION['usua_predio'], $_SESSION['nombre_predio'], str_replace('..','../dashboard',$resMenu),'../../'); ?>

    <section class="content" style="margin-top:-10px;">
        <div class="container-fluid">
 
			<div class="row clearfix">
				<main id="app" class="container">
					<transition name="fade">
					<div class="row clearfix">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="card">
							<div class="header">
								<h2>
									CLIENTES CARGADOS
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
								<div class="table-responsive">
									<table class="table table-bordered table-striped">
										<thead>
											<tr>
												<th>Apellido</th>
												<th>Nombre</th>
												<th>Nro Documento</th>
												<th>Fecha Nacimiento</th>
												<th>Dirección</th>
												<th>Teléfono</th>
												<th>Email</th>
												<th>Editar</th>
												<th>Eliminar</th>
											</tr>
										</thead>

										<tbody>
											<tr v-for="cliente in datos" :key="cliente.idcliente">
												<td>{{cliente.apellido}}</td>
												<td>{{cliente.nombre}}</td>
												<td>{{cliente.nrodocumento}}</td>
												<td>{{cliente.fechanacimiento}}</td>
												<td>{{cliente.direccion}}</td>
												<td>{{cliente.telefono}}</td>
												<td>{{cliente.email}}</td>
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
										</tbody>
									</table>
									</div>
								</div>
							</div>
						</div>
					</div>
					</transition>
				</main>
			</div>
        </div>
    </section>

    <?php echo $baseHTML->cargarArchivosJS('../../',$arJS); ?>

	<!-- VUE JS -->
	<script src="https://cdn.jsdelivr.net/npm/vue"></script>

	<!-- axios -->
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script>
		const app = new Vue({
			el: "#app",
			data: {
				showAddModal: false,
				showEditModal: false,
				showDeleteModal: false,
				errorMensaje: '',
				successMensaje: '',
				datos: [],
				activeDato: {}
			},
			mounted () {
				this.getAllDatos()
			},
			computed: {
				displayAddModal () {},
				displayEditModal () {},
				displayDeleteModal () {}
			},
			methods: {
				toggleModal () {},
				setMensajes () {},
				getAllDatos () {
					axios.get('../../ajax/ajax.php?accion=VtraerClientes')
					.then(res => {
						//console.log(res)
						this.datos = res.data.datos
					})
				},
				createDato () {},
				getDato () {},
				updateDato () {},
				deleteDato () {}	
			}
		})
    </script>
</body>
<?php } ?>
</html>

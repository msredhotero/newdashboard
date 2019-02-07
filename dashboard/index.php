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
$singular = "Socio";

$plural = "Socio";

$eliminar = "eliminarOrdenes";

$insertar = "insertarDelegados";

//$tituloWeb = "Gestión: Talleres";
//////////////////////// Fin opciones ////////////////////////////////////////////////


/////////////////////// Opciones para la creacion del formulario  /////////////////////
if ($_SESSION['idroll_aif'] == 4) {
	$club = $serviciosReferencias->traerNombreCountryPorId($_SESSION['idclub_aif']);

	$cantidadEquipos = $serviciosReferencias->traerEquiposPorCountries($_SESSION['idclub_aif']);

	$tabla 			= "dbdelegados";

	$lblCambio	 	= array("refusuarios","email1","email2","email3","email4");
	$lblreemplazo	= array("Usuario","Email de Contacto 1","Email de Contacto 2","Email de Contacto 3","Email de Contacto 4");


	$resModelo 	= $serviciosReferencias->traerUsuariosPorId($_SESSION['usuaid_aif']);
	$cadRef 	= $serviciosFunciones->devolverSelectBox($resModelo,array(5),'');

	$refdescripcion = array(0 => $cadRef);
	$refCampo 	=  array("refusuarios");

	$frmPerfil 	= $serviciosFunciones->camposTabla($insertar ,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);
}

if ($_SESSION['idroll_aif'] == 5) {
	$resJugador = $serviciosReferencias->traerJugadoresPorEmail($_SESSION['email_aif']);

	$tabla 			= "dbjugadores";

	$lblCambio	 	= array("reftipodocumentos","nrodocumento","fechanacimiento","fechaalta","fechabaja","refcountries");
	$lblreemplazo	= array("Tipo Documento","Nro Documento","Fecha Nacimiento","Fecha Alta","Fecha Baja","Countries");


	$resTipoDoc 	= $serviciosReferencias->traerTipodocumentosPorId(mysql_result($resJugador,0,'reftipodocumentos'));
	$cadRef 	= $serviciosFunciones->devolverSelectBox($resTipoDoc,array(1),'');

	$resCountries 	= $serviciosReferencias->traerCountriesPorId(mysql_result($resJugador,0,'refcountries'));
	$cadRef2 	= $serviciosFunciones->devolverSelectBox($resCountries,array(6),'');

	$refdescripcion = array(0 => $cadRef,1 => $cadRef2);
	$refCampo 	=  array("reftipodocumentos","refcountries");

	$idTabla = 'idjugador';

	$id = mysql_result($resJugador,0,'idjugador');

	$frm 	= $serviciosFunciones->camposTablaVer($id, $idTabla,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);
}

///////////////////////////              fin                   ////////////////////////

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

    <!-- VUE JS -->
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

    <!-- axios -->
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script src="https://unpkg.com/vue-swal"></script>

    <script src="../components/mensajes.js"></script>

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
    <?php echo $baseHTML->cargarSECTION($_SESSION['usua_aif'], $_SESSION['nombre_aif'], str_replace('..','../dashboard',$resMenu),'../'); ?>
    <main id="app">
    <section class="content" style="margin-top:-35px;">

        <div class="container-fluid">
            <div class="row clearfix">
					<?php if ($_SESSION['idroll_aif'] == 4) { ?>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="info-box bg-green hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">home</i>
                        </div>
                        <div class="content">
                            <div class="text">CLUB</div>
                            <div class="number" style="font-size:1.6em;"><?php echo $club; ?></div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box bg-deep-orange hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">security</i>
                        </div>
                        <div class="content">
                            <div class="text">EQUIPOS</div>
                            <div class="number"><?php echo mysql_num_rows($cantidadEquipos); ?></div>
                        </div>
                    </div>
                </div>
				<?php } ?>
				<?php if ($_SESSION['idroll_aif'] == 5) { ?>
					<div class="row">


				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="card ">
						<div class="header bg-blue">
							<h2>
								<?php echo strtoupper($plural); ?>
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
						<div class="body table-responsive">
							<form class="form" id="formCountry">

								<div class="row">
									<?php echo $frm; ?>
								</div>
							</form>
							</div>
						</div>
					</div>
				</div>
			</div>

				<?php } ?>
            </div>
        </div>


    </section>




    <!-- Modal Large Size -->
	 <?php if ($_SESSION['idroll_aif'] == 4) { ?>
    <transition name="fade">
    <form class="form" @submit.prevent="guardarDelegado">
    <?php echo $baseHTML->modalHTML('modalPerfil','Perfil','GUARDAR','Ingrese sus datos personales y los Email de los contactos','frmPerfil',$frmPerfil,'iddelegado','Delegados','VguardarDelegado'); ?>
    </form>
    </transition>
	<?php } ?>
    </main>

    <?php echo $baseHTML->cargarArchivosJS('../'); ?>


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

		const app = new Vue({
			el: "#app",
			data () {
                return {
                    activeDelegados: {},
                    errorMensaje: '',
                    successMensaje: ''
                }

			},
			mounted () {
				this.getDelegado()
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
                        //this.setMensajes(res)

                        if (!res.data.error) {
                            this.$swal("Ok!", res.data.mensaje, "success")
                        } else {
                            this.$swal("Error!", res.data.mensaje, "error")
                        }

                    });


                }
			}
		})
    </script>

</body>
<?php } ?>
</html>

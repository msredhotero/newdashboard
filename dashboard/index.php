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

$idSocioNuevo = 0;

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
	// determino si es un socio nuevo o vejo
	$determinaTipoSocio = $serviciosReferencias->determinaSocioNuevoViejo($_SESSION['email_aif']);

	if ($determinaTipoSocio['valor'] == 2) {
		// socio viejo
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
	} else {

		if ($determinaTipoSocio['valor'] == 1) {
			// idjugadorpre
			$idSocioNuevo = mysql_result($determinaTipoSocio['datos'],0,0);
			// socio nuevo
			$resJugador = $serviciosReferencias->traerJugadoresprePorIdNuevo($idSocioNuevo);

			$tabla 			= "dbjugadorespre";

			$lblCambio	 	= array("reftipodocumentos","nrodocumento","fechanacimiento","fechaalta","fechabaja","refcountries","numeroserielote");
			$lblreemplazo	= array("Tipo Documento","Nro Documento","Fecha Nacimiento","Fecha Alta","Fecha Baja","Countries",'Nro. Serie Lote');


			$resTipoDoc 	= $serviciosReferencias->traerTipodocumentosPorId(mysql_result($resJugador,0,'reftipodocumentos'));
			$cadRef 	= $serviciosFunciones->devolverSelectBox($resTipoDoc,array(1),'');

			$resCountries 	= $serviciosReferencias->traerCountriesPorId(mysql_result($resJugador,0,'refcountries'));
			$cadRef2 	= $serviciosFunciones->devolverSelectBox($resCountries,array(6),'');

			$refdescripcion = array(0 => $cadRef,1 => $cadRef2);
			$refCampo 	=  array("reftipodocumentos","refcountries");

			$idTabla = 'idjugadorpre';

			$frm 	= $serviciosFunciones->camposTablaVer($idSocioNuevo, $idTabla,$tabla,$lblCambio,$lblreemplazo,$refdescripcion,$refCampo);

			$arDocumentaciones = $serviciosReferencias->devolverEstadoDocumentaciones($idSocioNuevo,$determinaTipoSocio['valor']);

			$arDocumentacionesFase2 = $serviciosReferencias->devolverEstadoDocumentacionesFase2($idSocioNuevo,$determinaTipoSocio['valor']);

			//die(var_dump($arDocumentaciones));
		}
	}
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
	<script src="../../js/vue.min.js"></script>

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
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 <?php echo $arDocumentaciones['colorEstadoFoto']; ?> hover-zoom-effect btnFoto">
							<div class="icon">
								<i class="material-icons">face</i>
							</div>
							<div class="content">
								<div class="text">PERFIL</div>
								<div class="number"><?php echo strtoupper( str_replace('Finalizado','entregado', $arDocumentaciones['estadoFoto'])); ?></div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 <?php echo $arDocumentaciones['colorEstadoDocFrente']; ?> hover-zoom-effect">
							<div class="icon">
								<i class="material-icons">account_box</i>
							</div>
							<div class="content">
								<div class="text">DOC. FRENTE</div>
								<div class="number"><?php echo strtoupper( str_replace('Finalizado','entregado', $arDocumentaciones['estadoDocFrente'])); ?></div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 <?php echo $arDocumentaciones['colorEstadoDocDorsal']; ?> hover-zoom-effect">
							<div class="icon">
								<i class="material-icons">account_box</i>
							</div>
							<div class="content">
								<div class="text">DOC. DORSAL</div>
								<div class="number"><?php echo strtoupper( str_replace('Finalizado','entregado', $arDocumentaciones['estadoDocDorsal'])); ?></div>
							</div>
						</div>
					</div>

				</div>
				<?php
					if (($arDocumentaciones['idEstadoFoto'] == 3) && ($arDocumentaciones['idEstadoDocFrente'] == 3) && ($arDocumentaciones['idEstadoDocDorsal'] == 3)) {
				?>
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 <?php echo $arDocumentacionesFase2['colorEstadoEscritura']; ?> hover-zoom-effect">
							<div class="icon">
								<i class="material-icons">chrome_reader_mode</i>
							</div>
							<div class="content">
								<div class="text">ESCRITURA</div>
								<div class="number"><?php echo strtoupper( str_replace('Finalizado','entregado', $arDocumentacionesFase2['estadoEscritura'])); ?></div>
							</div>
						</div>
					</div>

					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 <?php echo $arDocumentacionesFase2['colorEstadoExpensa']; ?> hover-zoom-effect">
							<div class="icon">
								<i class="material-icons">attach_money</i>
							</div>
							<div class="content">
								<div class="text">EXPENSAS</div>
								<div class="number"><?php echo strtoupper( str_replace('Finalizado','entregado', $arDocumentacionesFase2['estadoExpensa'])); ?></div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 <?php echo $arDocumentacionesFase2['colorEstadoPartida']; ?> hover-zoom-effect">
							<div class="icon">
								<i class="material-icons">description</i>
							</div>
							<div class="content">
								<div class="text">PARTIDA</div>
								<div class="number"><?php echo strtoupper( str_replace('Finalizado','entregado', $arDocumentacionesFase2['estadoPartida'])); ?></div>
							</div>
						</div>
					</div>


				</div>

				<?php } ?>
				<?php if ($determinaTipoSocio['valor'] == 2) { ?>
				<div class="row">
					<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
						<div class="info-box-3 bg-blue hover-zoom-effect">
							<div class="icon">
								<i class="material-icons">local_hospital</i>
							</div>
							<div class="content">
								<div class="text">EST. MEDICO</div>
								<div class="number">CARGADO</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
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
									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="alert alert-success" role="alert">
											<strong><span class="glyphicon glyphicon-info-sign"></span> PASO 1</strong> <br>PROCEDA A CARGAR SU FOTO PERSONAL. LA MISMA DEBE TENER UN FONDO LISO. PUEDE OPTAR POR TOMARSE UNA FOTO CON SU SMARTPHONE O SUBIR UNA EXISTENTE. (RECOMENDACIÓN ADICIONAL, SUBIR LA FOTO VISTIENDO LA CAMISETA DEL COUNTRY).<br>
											SUBA UNA FOTO DE SU DNI TARJETA DE AMBOS LADOS. PROCURE QUE TODOS LOS DATOS SEAN LEGIBLES Y QUE LA CAMARA ESTE LO MAS CERCA POSIBLE DENTRO DE LOS LIMITES DEL DNI. SAQUE LA FOTO CON EL CELULAR EN HORIZONTAL.<br>
											PARA SUBIR LA FOTO DEBE HACER CLICK EN MENU "ARCHIVOS".
										</div>
										<div class="row">
											<div class="button-demo">
											<?php
											if (($arDocumentaciones['idEstadoFoto'] == 1) && ($arDocumentaciones['idEstadoDocFrente'] == 1) && ($arDocumentaciones['idEstadoDocDorsal'] == 1)) {
											?>

												<button data-toggle="modal" data-target="#myModal3" type="button" class="btn bg-orange waves-effect" id="presentarfase1">
													<i class="material-icons">assignment_turned_in</i>
													<span>PRESENTAR DOCUMENTACION PRINCIPAL</span>
												</button>

											<?php
										} else {
											?>

											<?php
				            				if (($arDocumentaciones['idEstadoFoto']  == 1) || ($arDocumentaciones['idEstadoDocFrente'] == 1) || ($arDocumentaciones['idEstadoDocDorsal'] == 1)) {
				            			?>
												<button data-toggle="modal" data-target="#myModal3" type="button" class="btn bg-orange waves-effect" id="presentarfase1">
													<i class="material-icons">assignment_turned_in</i>
													<span>PRESENTAR DOCUMENTACION PRINCIPAL</span>
												</button>
				            			<?php
				            				}
											}
				            			?>

				            			<?php
				            				if (($arDocumentaciones['idEstadoFoto'] == 3) && ($arDocumentaciones['idEstadoDocFrente'] == 3) && ($arDocumentaciones['idEstadoDocDorsal'] == 3)) {
				            			?>
												<button type="button" class="btn bg-brown waves-effect" id="generarFicha">
													<i class="material-icons">assignment_turned_in</i>
													<span>Generar Ficha Jugador</span>
												</button>

				            			<?php
				            				}
				            			?>
											</div>
										</div>
									</div>

									<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
										<div class="alert alert-success" role="alert">
											<strong><span class="glyphicon glyphicon-info-sign"></span> PASO 2</strong> <br>DEBERA PRESENTAR LA SIGUIENTE DOCUMENTACION. ESCRITURA, EXPENSA Y EN EL CASO QUE CORRESPONDA PARTIDA DE NACIMIENTO/LIBRETA DE MATRIMONIO (ESTAS TRES EN FORMA ONLINE Y SEGUIDAMENTE SE DETALLAN LOS ESPACIOS PARA CARGAR LA MISMA) SE RECOMIENDA LA UTILIZACION DE LA APP CAMSCANNER PARA REALIZAR ESTOS PASOS.
										</div>
										<div class="row">
											<div class="button-demo">
											<?php
				            				if (($arDocumentaciones['idEstadoFoto'] == '3') && ($arDocumentaciones['idEstadoDocFrente'] == '3') && ($arDocumentaciones['idEstadoDocDorsal'] == '3')) {

													if (($arDocumentacionesFase2['idEstadoEscritura'] == '1') || ($arDocumentacionesFase2['idEstadoExpensa'] == '1') || ($arDocumentacionesFase2['idEstadoPartida'] == '1')) {
				            			?>

												<button data-toggle="modal" data-target="#myModal3" type="button" class="btn bg-orange waves-effect" id="presentarfase2">
													<i class="material-icons">assignment_turned_in</i>
													<span>PRESENTAR DOCUMENTACION EXTRA</span>
												</button>

											<?php
											} else {
											?>

											<?php
				            				if (($arDocumentacionesFase2['idEstadoEscritura']  == '4') || ($arDocumentacionesFase2['idEstadoExpensa'] == '4') || ($arDocumentacionesFase2['idEstadoPartida'] == '4')) {
				            			?>
												<button data-toggle="modal" data-target="#myModal3" type="button" class="btn bg-orange waves-effect" id="presentarfase2">
													<i class="material-icons">assignment_turned_in</i>
													<span>PRESENTAR DOCUMENTACION EXTRA</span>
												</button>
				            			<?php
					            				}
												}
											}
				            			?>


											</div>
										</div>
									</div>


								</div>

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

	 <!-- Modal -->
		<div class="modal fade" id="myModal3" tabindex="1" style="z-index:500000;" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog modal-lg" role="document">
		    <div class="modal-content">
		      <form class="form-inline formulario" role="form">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="myModalLabel">Estado Documentación</h4>
		      </div>
		      <div class="modal-body" id="resultadoPresentacion">

		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		        <input type="hidden" name="refcountries" id="refcountries" value="0"/>
		      </div>
		      </form>
		    </div>
		  </div>
		</div>




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
			<?php
			if ($_SESSION['idroll_aif'] == 5) {
				if ($determinaTipoSocio['valor'] == 1) {
			?>

			$('#btnFoto').click(function() {
				url = "foto/index.php";
				$(location).attr('href',url);
			});

			$('#generarFicha').click(function() {
				window.open("../reportes/rptAltaSocio.php?id=<?php echo $idSocioNuevo; ?>" ,'_blank');
			});

			function presentardocumentacion(id) {
				$.ajax({
					data:  {id: id,
							accion: 'presentardocumentacionCompleta'},
					url:   '../ajax/ajax.php',
					type:  'post',
					beforeSend: function () {

					},
					success:  function (response) {
							$('#resultadoPresentacion').html(response.mensaje);
							url = "index.php";
							if (response.error == 0) {
								$('#presentarfase1').hide();
								setInterval(function() {
							      $(location).attr('href',url);
							   },5000);﻿
							}
							//url = "index.php";
							//$(location).attr('href',url);

					}
				});
			}

			function presentardocumentacionAparte(id) {
				$.ajax({
					data:  {id: id,
							accion: 'presentardocumentacionAparte'},
					url:   '../ajax/ajax.php',
					type:  'post',
					beforeSend: function () {

					},
					success:  function (response) {
						$('#resultadoPresentacion').html(response.mensaje);
						url = "index.php";
						if (response.error == 0) {
							setInterval(function() {
						      $(location).attr('href',url);
						   },5000);﻿
						}
					}
				});
			}

			$('#presentarfase1').click(function() {
				presentardocumentacion(<?php echo $idSocioNuevo; ?>);
			});

			$('#presentarfase2').click(function() {
				presentardocumentacionAparte(<?php echo $idSocioNuevo; ?>);
			});

			<?php
				}
			}
			?>


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

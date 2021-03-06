<?php

require 'includes/funcionesUsuarios.php';

session_start();

$serviciosUsuario = new ServiciosUsuarios();


$ui = $_GET['token'];

$resActivacion = $serviciosUsuario->traerActivacionusuariosPorTokenFechas($ui);

$cadResultado = '';

if (mysql_num_rows($resActivacion) > 0) {
	$idusuario = mysql_result($resActivacion,0,'refusuarios');

	//pongo al usuario $activo
	$resUsuario = $serviciosUsuario->activarUsuario($idusuario);

	// concreto la activacion
	$resConcretar = $serviciosUsuario->eliminarActivacionusuarios(mysql_result($resActivacion,0,0));

	$cadResultado = 'Su usuario fue activado correctamente, ya puede iniciar sessión!!';
} else {

	$resToken = $serviciosUsuario->traerActivacionusuariosPorToken($ui);

	if (mysql_num_rows($resToken) > 0) {

		$cadResultado = 'La vigencia para darse de alta a caducado, haga click <a href="prolongar.php?token='.$ui.'">AQUI</a> para prolongar la activación';
	} else {
		$cadResultado = 'Esta clave de Activación es inexistente o su usuario ya fue actvado';
	}

}



?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Acceder | AIF</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Sweetalert Css -->
    <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
			  <a href="javascript:void(0);">Activar <b>AIF</b></a>
			  <small>Administración de Equipos, Countries, Jugadores y Datos Personales</small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_in" method="POST">
                     <h4><?php echo $cadResultado; ?></h4>

                     <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                           <a href="index.html">Iniciar Sessión!!</a>
                        </div>
                        <div class="col-xs-6 align-right">

                        </div>
                     </div>
               </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/examples/sign-in.js"></script>

    <!-- SweetAlert Plugin Js -->
    <script src="plugins/sweetalert/sweetalert.min.js"></script>

    <script src="js/pages/ui/dialogs.js"></script>


    <script type="text/javascript">

        $(document).ready(function(){



        });/* fin del document ready */

    </script>
</body>

</html>

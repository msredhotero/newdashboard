<?php

include ('../includes/funcionesUsuarios.php');
include ('../includes/funciones.php');
include ('../includes/funcionesHTML.php');
include ('../includes/funcionesReferencias.php');


$serviciosUsuarios  		= new ServiciosUsuarios();
$serviciosFunciones 		= new Servicios();
$serviciosHTML				= new ServiciosHTML();
$serviciosReferencias		= new ServiciosReferencias();


$accion = $_POST['accion'];

$resV['error'] = '';
$resV['mensaje'] = '';



switch ($accion) {
    case 'login':
        enviarMail($serviciosUsuarios);
        break;
	case 'entrar':
		entrar($serviciosUsuarios);
		break;
	case 'insertarUsuario':
        insertarUsuario($serviciosUsuarios);
        break;
	case 'modificarUsuario':
        modificarUsuario($serviciosUsuarios);
        break;
	case 'registrar':
		registrar($serviciosUsuarios);
        break;


		case 'insertarDelegados': 
		insertarDelegados($serviciosReferencias); 
		break; 
		case 'modificarDelegados': 
		modificarDelegados($serviciosReferencias); 
		break; 
		case 'eliminarDelegados': 
		eliminarDelegados($serviciosReferencias); 
		break; 
		case 'traerDelegados': 
		traerDelegados($serviciosReferencias); 
		break; 
		case 'traerDelegadosPorId': 
		traerDelegadosPorId($serviciosReferencias); 
		break; 
		case 'VinsertarDelegados': 
		insertarDelegados($serviciosReferencias); 
		break; 
		case 'VmodificarDelegados': 
		modificarDelegados($serviciosReferencias); 
		break; 
		case 'VeliminarDelegados': 
		eliminarDelegados($serviciosReferencias); 
		break; 
		case 'VtraerDelegados': 
		traerDelegados($serviciosReferencias); 
		break; 
		case 'VtraerDelegadosPorId': 
		VtraerDelegadosPorId($serviciosReferencias); 
		break; 
		case 'VguardarDelegado':
		VguardarDelegado($serviciosReferencias);
		break;

		case 'VtraerJugadoresClubPorCountrieActivos':
		VtraerJugadoresClubPorCountrieActivos($serviciosReferencias);
		break;
		case 'VtraerPaginasJugadoresPorClub':
		VtraerPaginasJugadoresPorClub($serviciosReferencias);
		break;
/* Fin */

}
/* Fin */

function VtraerPaginasJugadoresPorClub($serviciosReferencias) {
	$idclub = $_POST['idclub'];
	$busqueda = $_POST['busqueda'];

	$res = $serviciosReferencias->traerJugadoresClubPorCountrieActivos($idclub, $busqueda); 

	$ar = array(round(mysql_num_rows($res) / 10));

	$resV['datos'] = $ar; 
	
	header('Content-type: application/json'); 
	echo json_encode($resV); 
}

function VtraerJugadoresClubPorCountrieActivos($serviciosReferencias) {
	$idclub = $_POST['idclub'];
	$pagina = $_POST['pagina'];
	$cantidad = $_POST['cantidad'];
	$busqueda = $_POST['busqueda'];

	$res = $serviciosReferencias->traerJugadoresClubPorCountrieActivosPaginador($idclub, $pagina, $cantidad, $busqueda); 
	$ar = array(); 

	while ($row = mysql_fetch_assoc($res)) { 
		$arNuevo = array('apellido'=> utf8_encode($row['apellido']),
						'nombres'=>utf8_encode($row['nombres']),
						'nrodocumento'=>$row['nrodocumento'],
						'idjugador'=>$row['idjugador'],
						'fechabajacheck'=> ($row['fechabajacheck'] == '0' ? false : true),
						'articulocheck'=> ($row['articulocheck'] == '0' ? false : true),
						'numeroserielote' => $row['numeroserielote']
		);
			
		array_push($ar, $arNuevo); 

	} 
	
	$resV['datos'] = $ar; 
	
	//die(var_dump($resV));

	header('Content-type: application/json; charset=utf-8'); 
	echo json_encode($resV);
}


function insertarDelegados($serviciosReferencias) { 
	$refusuarios = $_POST['refusuarios']; 
	$apellidos = $_POST['apellidos']; 
	$nombres = $_POST['nombres']; 
	$direccion = $_POST['direccion']; 
	$localidad = $_POST['localidad']; 
	$cp = $_POST['cp']; 
	$telefono = $_POST['telefono']; 
	$celular = $_POST['celular']; 
	$fax = $_POST['fax']; 
	$email1 = $_POST['email1']; 
	$email2 = $_POST['email2']; 
	$email3 = $_POST['email3']; 
	$email4 = $_POST['email4']; 
	$res = $serviciosReferencias->insertarDelegados($refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4); 
	if ((integer)$res > 0) { 
	echo ''; 
	} else { 
	echo 'Huvo un error al insertar datos';	 
	} 
} 

function VguardarDelegado($serviciosReferencias) {
	
	$existe = $serviciosReferencias->existeDelegadoPorUsuario($_POST['refusuarios']);


	$refusuarios = $_POST['refusuarios']; 
	$apellidos = $_POST['apellidos']; 
	$nombres = $_POST['nombres']; 
	$direccion = $_POST['direccion']; 
	$localidad = $_POST['localidad']; 
	$cp = $_POST['cp']; 
	$telefono = $_POST['telefono']; 
	$celular = $_POST['celular']; 
	$fax = $_POST['fax']; 
	$email1 = $_POST['email1']; 
	$email2 = $_POST['email2']; 
	$email3 = $_POST['email3']; 
	$email4 = $_POST['email4']; 
	
	if ($existe > 0) {
		$id = $_POST['iddelegado']; 

		$res = $serviciosReferencias->modificarDelegados($id,$refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4); 
		
		if ($res) { 
			$resV['mensaje'] = 'Registro Modificado con exito!.'; 
			$resV['error'] = false; 
		} else { 
			$resV['error'] = true; 
			$resV['mensaje'] = 'No se pudo modificar el Registro!'; 
		} 
	} else {
		$res = $serviciosReferencias->insertarDelegados($refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4); 
		if ((integer)$res > 0) { 
			$resV['mensaje'] = 'Registro Cargado con exito!.'; 
			$resV['error'] = false;
		} else { 
			$resV['error'] = true; 
			$resV['mensaje'] = 'No se pudo cargar el Registro!'; 
		} 
	}
	
	
	header('Content-type: application/json'); 
	echo json_encode($resV); 
}

	function modificarDelegados($serviciosReferencias) { 
		$id = $_POST['id']; 
		$refusuarios = $_POST['refusuarios']; 
		$apellidos = $_POST['apellidos']; 
		$nombres = $_POST['nombres']; 
		$direccion = $_POST['direccion']; 
		$localidad = $_POST['localidad']; 
		$cp = $_POST['cp']; 
		$telefono = $_POST['telefono']; 
		$celular = $_POST['celular']; 
		$fax = $_POST['fax']; 
		$email1 = $_POST['email1']; 
		$email2 = $_POST['email2']; 
		$email3 = $_POST['email3']; 
		$email4 = $_POST['email4']; 
		$res = $serviciosReferencias->modificarDelegados($id,$refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4); 
		if ($res == true) { 
		echo ''; 
		} else { 
		echo 'Huvo un error al modificar datos'; 
		} 
	} 

	function eliminarDelegados($serviciosReferencias) { 
		$id = $_POST['id']; 
		$res = $serviciosReferencias->eliminarDelegados($id); 
		echo $res; 
	} 

	function VinsertarDelegados($serviciosReferencias) { 
		$refusuarios = $_POST['refusuarios']; 
		$apellidos = $_POST['apellidos']; 
		$nombres = $_POST['nombres']; 
		$direccion = $_POST['direccion']; 
		$localidad = $_POST['localidad']; 
		$cp = $_POST['cp']; 
		$telefono = $_POST['telefono']; 
		$celular = $_POST['celular']; 
		$fax = $_POST['fax']; 
		$email1 = $_POST['email1']; 
		$email2 = $_POST['email2']; 
		$email3 = $_POST['email3']; 
		$email4 = $_POST['email4']; 
		$res = $serviciosReferencias->insertarDelegados($refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4); 
		if ((integer)$res > 0) { 
			$resV['mensaje'] = 'Registro Cargado con exito!.'; 
		} else { 
			$resV['error'] = true; 
			$resV['mensaje'] = 'No se pudo cargar el Registro!'; 
		} 
		header('Content-type: application/json'); 
		echo json_encode($resV); 
	} 

	function VmodificarDelegados($serviciosReferencias) { 
		$id = $_POST['id']; 
		$refusuarios = $_POST['refusuarios']; 
		$apellidos = $_POST['apellidos']; 
		$nombres = $_POST['nombres']; 
		$direccion = $_POST['direccion']; 
		$localidad = $_POST['localidad']; 
		$cp = $_POST['cp']; 
		$telefono = $_POST['telefono']; 
		$celular = $_POST['celular']; 
		$fax = $_POST['fax']; 
		$email1 = $_POST['email1']; 
		$email2 = $_POST['email2']; 
		$email3 = $_POST['email3']; 
		$email4 = $_POST['email4']; 
		$res = $serviciosReferencias->modificarDelegados($id,$refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4); 
		if ($res) { 
		$resV['mensaje'] = 'Registro Modificado con exito!.'; 
		} else { 
		$resV['error'] = true; 
		$resV['mensaje'] = 'No se pudo modificar el Registro!'; 
		} 
		header('Content-type: application/json'); 
		echo json_encode($resV); 
	} 

	function VeliminarDelegados($serviciosReferencias) { 
		$id = $_POST['id']; 
		$res = $serviciosReferencias->eliminarDelegados($id); 
		if ($res) { 
		$resV['mensaje'] = 'Registro Eliminado con exito!.'; 
		} else { 
		$resV['error'] = true; 
		$resV['mensaje'] = 'No se pudo eliminar el Registro!'; 
		} 
		header('Content-type: application/json'); 
		echo json_encode($resV); 
	} 

	function VtraerDelegados($serviciosReferencias) { 
		$res = $serviciosReferencias->VtraerDelegados(); 
		$ar = array(); 
		while ($row = mysql_fetch_array($res)) { 
			array_push($ar, $row); 
		} 
		$resV['datos'] = $ar; 
		header('Content-type: application/json'); 
		echo json_encode($resV); 
	} 


	function VtraerDelegadosPorId($serviciosReferencias) { 

		$ar = array(); 


		$id = $_POST['iddelegado'];

		$res = $serviciosReferencias->traerDelegadosPorId($id); 

		while ($row = mysql_fetch_assoc($res)) { 
			array_push($ar, $row); 
		} 

		$resV['datos'] = $ar; 
		header('Content-type: application/json'); 
		echo json_encode($resV); 
	}

////////////////////////// FIN DE TRAER DATOS ////////////////////////////////////////////////////////////

//////////////////////////  BASICO  /////////////////////////////////////////////////////////////////////////

function toArray($query)
{
    $res = array();
    while ($row = @mysql_fetch_array($query)) {
        $res[] = $row;
    }
    return $res;
}


function entrar($serviciosUsuarios) {
	$email		=	$_POST['email'];
	$pass		=	$_POST['pass'];
	echo $serviciosUsuarios->loginUsuario($email,$pass);
}


function registrar($serviciosUsuarios) {
	$usuario			=	$_POST['usuario'];
	$password			=	$_POST['password'];
	$refroll			=	$_POST['refroll'];
	$email				=	$_POST['email'];
	$nombre				=	$_POST['nombrecompleto'];
	
	$res = $serviciosUsuarios->insertarUsuario($usuario,$password,$refroll,$email,$nombre);
	if ((integer)$res > 0) {
		echo '';	
	} else {
		echo $res;	
	}
}


function insertarUsuario($serviciosUsuarios) {
	$usuario			=	$_POST['usuario'];
	$password			=	$_POST['password'];
	$refroll			=	$_POST['refroles'];
	$email				=	$_POST['email'];
	$nombre				=	$_POST['nombrecompleto'];
	
	$res = $serviciosUsuarios->insertarUsuario($usuario,$password,$refroll,$email,$nombre);
	if ((integer)$res > 0) {
		echo '';	
	} else {
		echo $res;	
	}
}


function modificarUsuario($serviciosUsuarios) {
	$id					=	$_POST['id'];
	$usuario			=	$_POST['usuario'];
	$password			=	$_POST['password'];
	$refroll			=	$_POST['refroles'];
	$email				=	$_POST['email'];
	$nombre				=	$_POST['nombrecompleto'];
	
	echo $serviciosUsuarios->modificarUsuario($id,$usuario,$password,$refroll,$email,$nombre);
}


function enviarMail($serviciosUsuarios) {
	$email		=	$_POST['email'];
	$pass		=	$_POST['pass'];
	//$idempresa  =	$_POST['idempresa'];
	
	echo $serviciosUsuarios->login($email,$pass);
}


function devolverImagen($nroInput) {
	
	if( $_FILES['archivo'.$nroInput]['name'] != null && $_FILES['archivo'.$nroInput]['size'] > 0 ){
	// Nivel de errores
	  error_reporting(E_ALL);
	  $altura = 100;
	  // Constantes
	  # Altura de el thumbnail en píxeles
	  //define("ALTURA", 100);
	  # Nombre del archivo temporal del thumbnail
	  //define("NAMETHUMB", "/tmp/thumbtemp"); //Esto en servidores Linux, en Windows podría ser:
	  //define("NAMETHUMB", "c:/windows/temp/thumbtemp"); //y te olvidas de los problemas de permisos
	  $NAMETHUMB = "c:/windows/temp/thumbtemp";
	  # Servidor de base de datos
	  //define("DBHOST", "localhost");
	  # nombre de la base de datos
	  //define("DBNAME", "portalinmobiliario");
	  # Usuario de base de datos
	  //define("DBUSER", "root");
	  # Password de base de datos
	  //define("DBPASSWORD", "");
	  // Mime types permitidos
	  $mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png");
	  // Variables de la foto
	  $name = $_FILES["archivo".$nroInput]["name"];
	  $type = $_FILES["archivo".$nroInput]["type"];
	  $tmp_name = $_FILES["archivo".$nroInput]["tmp_name"];
	  $size = $_FILES["archivo".$nroInput]["size"];
	  // Verificamos si el archivo es una imagen válida
	  if(!in_array($type, $mimetypes))
		die("El archivo que subiste no es una imagen válida");
	  // Creando el thumbnail
	  switch($type) {
		case $mimetypes[0]:
		case $mimetypes[1]:
		  $img = imagecreatefromjpeg($tmp_name);
		  break;
		case $mimetypes[2]:
		  $img = imagecreatefromgif($tmp_name);
		  break;
		case $mimetypes[3]:
		  $img = imagecreatefrompng($tmp_name);
		  break;
	  }
	  
	  $datos = getimagesize($tmp_name);
	  
	  $ratio = ($datos[1]/$altura);
	  $ancho = round($datos[0]/$ratio);
	  $thumb = imagecreatetruecolor($ancho, $altura);
	  imagecopyresized($thumb, $img, 0, 0, 0, 0, $ancho, $altura, $datos[0], $datos[1]);
	  switch($type) {
		case $mimetypes[0]:
		case $mimetypes[1]:
		  imagejpeg($thumb, $NAMETHUMB);
			  break;
		case $mimetypes[2]:
		  imagegif($thumb, $NAMETHUMB);
		  break;
		case $mimetypes[3]:
		  imagepng($thumb, $NAMETHUMB);
		  break;
	  }
	  // Extrae los contenidos de las fotos
	  # contenido de la foto original
	  $fp = fopen($tmp_name, "rb");
	  $tfoto = fread($fp, filesize($tmp_name));
	  $tfoto = addslashes($tfoto);
	  fclose($fp);
	  # contenido del thumbnail
	  $fp = fopen($NAMETHUMB, "rb");
	  $tthumb = fread($fp, filesize($NAMETHUMB));
	  $tthumb = addslashes($tthumb);
	  fclose($fp);
	  // Borra archivos temporales si es que existen
	  //@unlink($tmp_name);
	  //@unlink(NAMETHUMB);
	} else {
		$tfoto = '';
		$type = '';
	}
	$tfoto = utf8_decode($tfoto);
	return array('tfoto' => $tfoto, 'type' => $type);	
}


?>
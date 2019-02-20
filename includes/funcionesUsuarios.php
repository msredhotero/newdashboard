<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosUsuarios {

function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function existeUsuarioPreRegistrado($email) {
	$sql = "select (case when activo=1 then 'Si' else 'No' end) as activo from dbusuarios where email = '".$email."'";
	$res = $this->query($sql,0);
	if (mysql_num_rows($res)>0) {
		return mysql_result($res,0,0);
	} else {
		return '';
	}
}

function login($usuario,$pass) {

	$sqlusu = "select * from dbusuarios where email = '".$usuario."'";

	$error = '';

	if (trim($usuario) != '' and trim($pass) != '') {

	$respusu = $this->query($sqlusu,0);

	if (mysql_num_rows($respusu) > 0) {


		$idUsua = mysql_result($respusu,0,0);
		$sqlpass = "select nombrecompleto,email,usuario,r.descripcion, r.idrol, u.refcountries
				from dbusuarios u
				inner join tbroles r on r.idrol = u.refroles
				where password = '".$pass."' and u.activo = 1 and idusuario = ".$idUsua;


		$resppass = $this->query($sqlpass,0);

		if (mysql_num_rows($resppass) > 0) {
			$error = '';
			} else {
				$error = 'Usuario o Password incorrecto';
			}

		}
		else

		{
			$error = 'Usuario o Password incorrecto';
		}

		if ($error == '') {
			//die(var_dump($error));
			session_start();
			$_SESSION['usua_aif'] = $usuario;
			$_SESSION['nombre_aif'] = mysql_result($resppass,0,0);
			$_SESSION['usuaid_aif'] = $idUsua;
			$_SESSION['email_aif'] = mysql_result($resppass,0,1);
			$_SESSION['idroll_aif'] = mysql_result($resppass,0,4);
			$_SESSION['refroll_aif'] = mysql_result($resppass,0,3);
			$_SESSION['idclub_aif'] = mysql_result($resppass,0,'refcountries');
			$_SESSION['club_aif'] = mysql_result($resppass,0,'refcountries');

			return 1;
		}

	}	else {
		$error = 'Usuario y Password son campos obligatorios';
	}


	return $error;

}

function loginFacebook($usuario) {

	$sqlusu = "select concat(apellido,' ',nombre),email,direccion,refroll from se_usuarios where email = '".$usuario."'";
	$error = '';


if (trim($usuario) != '') {

$respusu = $this->query($sqlusu,0);

	if (mysql_num_rows($respusu) > 0) {


		if ($error == '') {
			session_start();
			$_SESSION['usua_predio'] = $usuario;
			$_SESSION['nombre_predio'] = mysql_result($resppass,0,0);
			$_SESSION['email_predio'] = mysql_result($resppass,0,1);
			$_SESSION['refroll_predio'] = mysql_result($resppass,0,3);
			//$error = 'andube por aca'-$sqlusu;
		}

	}	else {
		$error = 'Usuario y Password son campos obligatorios';
	}

}

	return $error;

}




function loginUsuario($usuario,$pass) {

	$sqlusu = "select * from dbusuarios where email = '".$usuario."'";



if (trim($usuario) != '' and trim($pass) != '') {

	$respusu = $this->query($sqlusu,0);

	if (mysql_num_rows($respusu) > 0) {
		$error = '';

		$idUsua = mysql_result($respusu,0,0);
		$sqlpass = "select concat(apellido,' ',nombre),email,refroles from dbusuarios where password = '".$pass."' and IdUsuario = ".$idUsua;

		$resppass = $this->query($sqlpass,0);

			if (mysql_num_rows($resppass) > 0) {
				$error = '';

			} else {
				if (mysql_result($respusu,0,'activo') == 0) {
					$error = 'El usuario no fue activado, verifique su cuenta de email: '.$usuario;
				} else {
					$error = 'Usuario o Password incorrecto';
				}

			}

		}
		else

		{
			$error = 'Usuario o Password incorrecto';
		}

		if ($error == '') {
			session_start();
			$_SESSION['usua_predio'] = $usuario;
			$_SESSION['nombre_predio'] = mysql_result($resppass,0,0);
			$_SESSION['email_predio'] = mysql_result($resppass,0,1);
			$_SESSION['refroll_predio'] = mysql_result($resppass,0,2);
		}


	}	else {
		$error = 'Usuario y Password son campos obligatorios';
	}


	return $error;

}


function traerRoles() {
	$sql = "select * from tbroles";
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}

function traerRolesSimple() {
	$sql = "select * from tbroles where idrol <> 1";
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}


function traerUsuario($email) {
	$sql = "select idusuario,usuario,refroll,nombrecompleto,email,password from se_usuarios where email = '".$email."'";
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}

function traerUsuarios() {
	$sql = "select u.idusuario,u.usuario, u.password, r.descripcion, u.email , u.nombrecompleto, u.refroles
			from dbusuarios u
			inner join tbroles r on u.refroles = r.idrol
			order by nombrecompleto";
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}


function traerUsuariosSimple() {
	$sql = "select u.idusuario,u.usuario, u.password, r.descripcion, u.email , u.nombrecompleto, u.refroles
			from dbusuarios u
			inner join tbroles r on u.refroles = r.idrol
			where r.idrol <> 1
			order by nombrecompleto";
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}

function traerTodosUsuarios() {
	$sql = "select u.idusuario,u.usuario,u.nombrecompleto,u.refroll,u.email,u.password
			from se_usuarios u
			order by nombrecompleto";
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}

function traerUsuarioId($id) {
	$sql = "select idusuario,usuario,refroles,nombrecompleto,email,password from dbusuarios where idusuario = ".$id;
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al traer datos';
	} else {
		return $res;
	}
}

function existeUsuario($usuario) {
	$sql = "select * from dbusuarios where email = '".$usuario."'";
	$res = $this->query($sql,0);
	if (mysql_num_rows($res)>0) {
		return true;
	} else {
		return false;
	}
}

function traerJugadoresPorId($id) {
   $sql = "select idjugador,reftipodocumentos,nrodocumento,apellido,nombres,email,fechanacimiento,fechaalta,fechabaja,refcountries,observaciones from dbjugadores where idjugador =".$id;

   $res = $this->query($sql,0);
   return $res;
}

function traerJugadoresprePorIdNuevo($id) {
   $sql = "select idjugadorpre,reftipodocumentos,nrodocumento,apellido,nombres,email,DATE_FORMAT(fechanacimiento, '%d-%m-%Y') as fechanacimiento,DATE_FORMAT(fechaalta, '%d-%m-%Y') as fechaalta,refcountries,observaciones,refusuarios,numeroserielote,refestados from dbjugadorespre where idjugadorpre =".$id;
   $res = $this->query($sql,0);
   return $res;
}

function registrarSocio($email, $password,$idjugador, $tipo) {

   if ($tipo == 1) {
      $resJugador = $this->traerJugadoresprePorIdNuevo($idjugador);
   } else {
      $resJugador = $this->traerJugadoresPorId($idjugador);
   }


	$token = $this->GUID();
	$cuerpo = '';

	$fecha = date_create(date('Y').'-'.date('m').'-'.date('d'));
	date_add($fecha, date_interval_create_from_date_string('15 days'));
	$fechaprogramada =  date_format($fecha, 'Y-m-d');

	$cuerpo .= '<p>Antes que nada por favor no responda este mail ya que no recibirá respuesta.</p>';
	$cuerpo .= '<p>Recibimos su solicitud de alta como socio/jugador en la Asociación Intercountry de Fútbol Zona Norte. Para verificar(activar) tu casilla de correo por favor ingresá al siguiente link: <a href="http://www.saupureinconsulting.com.ar/aifzncountriesdesarrollo/activacion.php?token='.$token.'" target="_blank">AQUI</a>.</p>';
	$cuerpo .= '<p>Este link estara vigente hasta la fecha '.$fechaprogramada.', pasada esta fecha deberá solicitar mas tiempo para activar su cuenta.</p>';
	$cuerpo .= '<p>Una vez hecho esto, el personal administrativo se pondrá en contacto mediante esta misma via para notificarle si su estado de alta se encuentra aprobado, de no ser así se detallará la causa.</p>';

	$cuerpo .= '<p>Atte.</p>';
	$cuerpo .= '<p>AIFZN</p>';

   $apellido   = mysql_result($resJugador,0,'apellido');
   $nombre     = mysql_result($resJugador,0,'nombres');

	$sql = "INSERT INTO dbusuarios
				(idusuario,
				usuario,
				password,
				refroles,
				email,
				nombrecompleto,
				refcountries,
				activo)
			VALUES
				('',
				'".utf8_decode($apellido).' '.utf8_decode($nombre)."',
				'".utf8_decode($password)."',
				5,
				'".utf8_decode($email)."',
				'".utf8_decode($apellido).' '.utf8_decode($nombre)."',
				NULL,
				0)";

	if ($this->existeUsuario($email) == true) {
		return "Ya existe el usuario";
	}

	$res = $this->query($sql,1);

   if ($res == false) {
		return 'Error al insertar datos';
	} else {
		$this->insertarActivacionusuarios($res,$token,'','');

      if ($tipo == 1) {
         $resModJug = $this->actualizarUsuarioUusarioPre($idjugador, $email, $res);
      } else {
         // creo la relacion socio y usuarios por el email
         $resModJug = $this->modificarJugadorEmail($idjugador, $email);
      }



		$this->enviarEmail($email,'Alta de Usuario',utf8_decode($cuerpo));

		return $res;
	}
}

function actualizarUsuarioUusarioPre($id, $email, $idUsuario) {
	$sql = "UPDATE dbjugadorespre SET idusuario = ".$idUsuario.", email = '".$email."' where idjugadorpre =".$id;

	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al modificar datos';
	} else {
		return '';
	}
}



/* PARA Activacionusuarios */

function insertarActivacionusuarios($refusuarios,$token,$vigenciadesde,$vigenciahasta) {
$sql = "insert into dbactivacionusuarios(idactivacionusuario,refusuarios,token,vigenciadesde,vigenciahasta)
values ('',".$refusuarios.",'".utf8_decode($token)."',now(),ADDDATE(now(), INTERVAL 15 DAY))";
$res = $this->query($sql,1);
return $res;
}


function modificarActivacionusuarios($id,$refusuarios,$token,$vigenciadesde,$vigenciahasta) {
$sql = "update dbactivacionusuarios
set
refusuarios = ".$refusuarios.",token = '".($token)."',vigenciadesde = '".utf8_decode($vigenciadesde)."',vigenciahasta = '".utf8_decode($vigenciahasta)."'
where idactivacionusuario =".$id;
$res = $this->query($sql,0);
return $res;
}


function modificarActivacionusuariosConcretada($token) {
$sql = "update dbactivacionusuarios
set
vigenciadesde = 'NULL',vigenciahasta = 'NULL'
where token ='".$token."'";
$res = $this->query($sql,0);
return $res;
}


function modificarActivacionusuariosRenovada($refusuarios,$token,$vigenciadesde,$vigenciahasta) {
$sql = "update dbactivacionusuarios
set
vigenciadesde = now(),vigenciahasta = ADDDATE(now(), INTERVAL 15 DAY),token = '".($token)."'
where refusuarios =".$refusuarios;
$res = $this->query($sql,0);
return $res;
}


function eliminarActivacionusuarios($id) {
$sql = "delete from dbactivacionusuarios where idactivacionusuario =".$id;
$res = $this->query($sql,0);
return $res;
}

function eliminarActivacionusuariosPorUsuario($refusuarios) {
$sql = "delete from dbactivacionusuarios where refusuarios =".$refusuarios;
$res = $this->query($sql,0);
return $res;
}


function traerActivacionusuarios() {
$sql = "select
a.idactivacionusuario,
a.refusuarios,
a.token,
a.vigenciadesde,
a.vigenciahasta
from dbactivacionusuarios a
order by 1";
$res = $this->query($sql,0);
return $res;
}


function traerActivacionusuariosPorId($id) {
$sql = "select idactivacionusuario,refusuarios,token,vigenciadesde,vigenciahasta from dbactivacionusuarios where idactivacionusuario =".$id;
$res = $this->query($sql,0);
return $res;
}


function traerActivacionusuariosPorToken($token) {
$sql = "select idactivacionusuario,refusuarios,token,vigenciadesde,vigenciahasta from dbactivacionusuarios where token ='".$token."'";
$res = $this->query($sql,0);
return $res;
}


function traerActivacionusuariosPorTokenFechas($token) {
$sql = "select idactivacionusuario,refusuarios,token,vigenciadesde,vigenciahasta from dbactivacionusuarios where token ='".$token."' and now() between vigenciadesde and vigenciahasta ";
$res = $this->query($sql,0);
return $res;
}

function traerActivacionusuariosPorUsuarioFechas($usuario) {
$sql = "select idactivacionusuario,refusuarios,token,vigenciadesde,vigenciahasta from dbactivacionusuarios where refusuarios =".$usuario." and now() between vigenciadesde and vigenciahasta ";
$res = $this->query($sql,0);
return $res;
}


function activarUsuario($refusuario) {
	$sql = "update dbusuarios
	set
		activo = 1
	where idusuario =".$refusuario;
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al modificar datos';
	} else {
		return '';
	}
}

/* Fin */
/* /* Fin de la Tabla: dbactivacionusuarios*/

function enviarEmail($destinatario,$asunto,$cuerpo, $referencia='') {


	# Defina el número de e-mails que desea enviar por periodo. Si es 0, el proceso por lotes
	# se deshabilita y los mensajes son enviados tan rápido como sea posible.
   if ($referencia == '') {
      $referencia = 'aif@intercountryfutbol.com.ar';
   }
   # Defina el número de e-mails que desea enviar por periodo. Si es 0, el proceso por lotes
   # se deshabilita y los mensajes son enviados tan rápido como sea posible.
   define("MAILQUEUE_BATCH_SIZE",0);

   //para el envío en formato HTML
   //$headers = "MIME-Version: 1.0\r\n";

   // Cabecera que especifica que es un HMTL
   $headers  = 'MIME-Version: 1.0' . "\r\n";
   $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

   //dirección del remitente
   $headers .= utf8_decode("From: ASOCIACIÓN INTERCOUNTRY DE FÚTBOL ZONA NORTE <aif@intercountryfutbol.com.ar>\r\n");

	mail($destinatario,$asunto,$cuerpo,$headers);
}


function insertarUsuario($usuario,$password,$refroles,$email,$nombrecompleto) {
	$sql = "INSERT INTO dbusuarios
				(idusuario,
				usuario,
				password,
				refroles,
				email,
				nombrecompleto)
			VALUES
				('',
				'".($usuario)."',
				'".($password)."',
				".$refroles.",
				'".($email)."',
				'".($nombrecompleto)."')";
	if ($this->existeUsuario($email) == true) {
		return "Ya existe el usuario";
	}
	$res = $this->query($sql,1);
	if ($res == false) {
		return 'Error al insertar datos';
	} else {

		return $res;
	}
}


function modificarUsuario($id,$usuario,$password,$refroles,$email,$nombrecompleto) {
	$sql = "UPDATE dbusuarios
			SET
				usuario = '".utf8_decode($usuario)."',
				password = '".utf8_decode($password)."',
				email = '".utf8_decode($email)."',
				refroles = ".$refroles.",
				nombrecompleto = '".utf8_decode($nombrecompleto)."'
			WHERE idusuario = ".$id;
	$res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al modificar datos';
	} else {
		return '';
	}
}

function modificarJugadorEmail($id, $email) {
   $sql = "update dbjugadores set email = '".$email."' where idjugador = ".$id;
   $res = $this->query($sql,0);
	if ($res == false) {
		return 'Error al modificar datos';
	} else {
		return '';
	}
}



function query($sql,$accion) {



		require_once 'appconfig.php';

		$appconfig	= new appconfig();
		$datos		= $appconfig->conexion();
		$hostname	= $datos['hostname'];
		$database	= $datos['database'];
		$username	= $datos['username'];
		$password	= $datos['password'];

		$conex = mysql_connect($hostname,$username,$password) or die ("no se puede conectar".mysql_error());

		mysql_select_db($database);

		        $error = 0;
		mysql_query("BEGIN");
		$result=mysql_query($sql,$conex);
		if ($accion && $result) {
			$result = mysql_insert_id();
		}
		if(!$result){
			$error=1;
		}
		if($error==1){
			mysql_query("ROLLBACK");
			return false;
		}
		 else{
			mysql_query("COMMIT");
			return $result;
		}

	}

}

?>

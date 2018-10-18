<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosReferencias {

	function GUID()
	{
		if (function_exists('com_create_guid') === true)
		{
			return trim(com_create_guid(), '{}');
		}

		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}



	function traerCountriesPorId($id) {
		$sql = "select idcountrie,nombre,cuit,fechaalta,
			fechabaja,refposiciontributaria,latitud,longitud,activo,referencia,direccion,telefonoadministrativo,telefonocampo,email,localidad,codigopostal,refusuarios from dbcountries where idcountrie =".$id;
		$res = $this->query($sql,0);
		return $res;
	}

	function traerNombreCountryPorId($id) {
		$sql = "select nombre from dbcountries where idcountrie =".$id;
		$res = $this->query($sql,0);
		
		if (mysql_num_rows($res)>0) {
			return mysql_result($res,0,0);
		}

		return '';
	}


	function traerEquiposPorCountries($idCountrie) { 
		$sql = "select 
		e.idequipo,
		cou.nombre as countrie,
		e.nombre,
		cat.categoria,
		di.division,
		con.nombre as contacto,
		e.fechaalta,
		e.fachebaja,
		(case when e.activo=1 then 'Si' else 'No' end) as activo,
		e.refcountries,
		e.refcategorias,
		e.refdivisiones,
		e.refcontactos
		from dbequipos e 
		inner join dbcountries cou ON cou.idcountrie = e.refcountries 
		inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria 
		inner join tbcategorias cat ON cat.idtcategoria = e.refcategorias 
		inner join tbdivisiones di ON di.iddivision = e.refdivisiones 
		inner join dbcontactos con ON con.idcontacto = e.refcontactos 
		inner join tbtipocontactos ti ON ti.idtipocontacto = con.reftipocontactos 
		where cou.idcountrie = ".$idCountrie." and e.activo = 1
		order by 1"; 
		$res = $this->query($sql,0); 
		return $res; 
	} 
	
	
	/* PARA Usuarios */
	
	function insertarUsuarios($usuario,$password,$refroles,$email,$nombrecompleto,$refclientes,$activo) { 
	$sql = "insert into dbusuarios(idusuario,usuario,password,refroles,email,nombrecompleto,refclientes,activo) 
	values ('','".utf8_decode($usuario)."','".utf8_decode($password)."',".$refroles.",'".utf8_decode($email)."','".utf8_decode($nombrecompleto)."',".$refclientes.",".$activo.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarUsuarios($id,$usuario,$password,$refroles,$email,$nombrecompleto,$refclientes,$activo) { 
	$sql = "update dbusuarios 
	set 
	usuario = '".utf8_decode($usuario)."',password = '".utf8_decode($password)."',refroles = ".$refroles.",email = '".utf8_decode($email)."',nombrecompleto = '".utf8_decode($nombrecompleto)."',refclientes = ".$refclientes.",activo = ".$activo." 
	where idusuario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarUsuarios($id) { 
	$sql = "delete from dbusuarios where idusuario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerUsuarios() { 
	$sql = "select 
	u.idusuario,
	u.usuario,
	u.password,
	u.refroles,
	u.email,
	u.nombrecompleto,
	u.activo
	from dbusuarios u 
	inner join tbroles rol ON rol.idrol = u.refroles 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerUsuariosPorId($id) { 
	$sql = "select idusuario,usuario,password,refroles,email,nombrecompleto,activo from dbusuarios where idusuario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbusuarios*/
	
	
	/* PARA Configuracion */
	
	function insertarConfiguracion($razonsocial,$empresa,$sistema,$direccion,$telefono,$email) { 
	$sql = "insert into tbconfiguracion(idconfiguracion,razonsocial,empresa,sistema,direccion,telefono,email) 
	values ('','".utf8_decode($razonsocial)."','".utf8_decode($empresa)."','".utf8_decode($sistema)."','".utf8_decode($direccion)."','".utf8_decode($telefono)."','".utf8_decode($email)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarConfiguracion($id,$razonsocial,$empresa,$sistema,$direccion,$telefono,$email) { 
	$sql = "update tbconfiguracion 
	set 
	razonsocial = '".utf8_decode($razonsocial)."',empresa = '".utf8_decode($empresa)."',sistema = '".utf8_decode($sistema)."',direccion = '".utf8_decode($direccion)."',telefono = '".utf8_decode($telefono)."',email = '".utf8_decode($email)."' 
	where idconfiguracion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarConfiguracion($id) { 
	$sql = "delete from tbconfiguracion where idconfiguracion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerConfiguracion() { 
	$sql = "select 
	c.idconfiguracion,
	c.razonsocial,
	c.empresa,
	c.sistema,
	c.direccion,
	c.telefono,
	c.email
	from tbconfiguracion c 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerConfiguracionPorId($id) { 
	$sql = "select idconfiguracion,razonsocial,empresa,sistema,direccion,telefono,email from tbconfiguracion where idconfiguracion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: tbconfiguracion*/
	
	
	/* PARA Estados */
	
	function insertarEstados($estado) { 
	$sql = "insert into tbestados(idestado,estado) 
	values ('','".utf8_decode($estado)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarEstados($id,$estado) { 
	$sql = "update tbestados 
	set 
	estado = '".utf8_decode($estado)."' 
	where idestado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarEstados($id) { 
	$sql = "delete from tbestados where idestado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerEstados() { 
	$sql = "select 
	e.idestado,
	e.estado
	from tbestados e 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerEstadosPorId($id) { 
	$sql = "select idestado,estado from tbestados where idestado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: tbestados*/
	
	
	/* PARA Horarios */
	
	function insertarHorarios($hora) { 
	$sql = "insert into tbhorarios(idtbhorario,hora) 
	values ('',".$hora.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarHorarios($id,$hora) { 
	$sql = "update tbhorarios 
	set 
	hora = ".$hora." 
	where idtbhorario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarHorarios($id) { 
	$sql = "delete from tbhorarios where idtbhorario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerHorarios() { 
	$sql = "select 
	h.idtbhorario,
	h.hora
	from tbhorarios h 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerHorariosPorId($id) { 
	$sql = "select idtbhorario,hora from tbhorarios where idtbhorario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: tbhorarios*/
	
	
	/* PARA Meses */
	
	function insertarMeses($nombremes) { 
	$sql = "insert into tbmeses(mes,nombremes) 
	values ('','".utf8_decode($nombremes)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarMeses($id,$nombremes) { 
	$sql = "update tbmeses 
	set 
	nombremes = '".utf8_decode($nombremes)."' 
	where mes =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarMeses($id) { 
	$sql = "delete from tbmeses where mes =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerMeses() { 
	$sql = "select 
	m.mes,
	m.nombremes
	from tbmeses m 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerMesesPorId($id) { 
	$sql = "select mes,nombremes from tbmeses where mes =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: tbmeses*/
	
	


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
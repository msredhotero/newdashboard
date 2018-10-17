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


/* PARA Clientes */

function insertarClientes($apellido,$nombre,$nrodocumento,$fechanacimiento,$direccion,$telefono,$email) { 
	$sql = "insert into dbclientes(idcliente,apellido,nombre,nrodocumento,fechanacimiento,direccion,telefono,email) 
	values ('','".utf8_decode($apellido)."','".utf8_decode($nombre)."',".$nrodocumento.",'".utf8_decode($fechanacimiento)."','".utf8_decode($direccion)."','".utf8_decode($telefono)."','".utf8_decode($email)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarClientes($id,$apellido,$nombre,$nrodocumento,$fechanacimiento,$direccion,$telefono,$email) { 
	$sql = "update dbclientes 
	set 
	apellido = '".utf8_decode($apellido)."',nombre = '".utf8_decode($nombre)."',nrodocumento = ".$nrodocumento.",fechanacimiento = '".utf8_decode($fechanacimiento)."',direccion = '".utf8_decode($direccion)."',telefono = '".utf8_decode($telefono)."',email = '".utf8_decode($email)."' 
	where idcliente =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarClientes($id) { 
	$sql = "delete from dbclientes where idcliente =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerClientes() { 
	$sql = "select 
	c.idcliente,
	c.apellido,
	c.nombre,
	c.nrodocumento,
	c.fechanacimiento,
	c.direccion,
	c.telefono,
	c.email
	from dbclientes c 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerClientesPorId($id) { 
	$sql = "select idcliente,apellido,nombre,nrodocumento,fechanacimiento,direccion,telefono,email from dbclientes where idcliente =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbclientes*/
	
	
	/* PARA Noticiaimagenes */
	
	function insertarNoticiaimagenes($refnoticias,$imagen,$type) { 
	$sql = "insert into dbnoticiaimagenes(idnoticiaimagen,refnoticias,imagen,type) 
	values ('',".$refnoticias.",'".utf8_decode($imagen)."','".utf8_decode($type)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarNoticiaimagenes($id,$refnoticias,$imagen,$type) { 
	$sql = "update dbnoticiaimagenes 
	set 
	refnoticias = ".$refnoticias.",imagen = '".utf8_decode($imagen)."',type = '".utf8_decode($type)."' 
	where idnoticiaimagen =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarNoticiaimagenes($id) { 
	$sql = "delete from dbnoticiaimagenes where idnoticiaimagen =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerNoticiaimagenes() { 
	$sql = "select 
	n.idnoticiaimagen,
	n.refnoticias,
	n.imagen,
	n.type
	from dbnoticiaimagenes n 
	inner join dbnoticias not ON not.idnoticia = n.refnoticias 
	inner join tbcategorias ca ON ca.idcategoria = not.refcategorias 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerNoticiaimagenesPorId($id) { 
	$sql = "select idnoticiaimagen,refnoticias,imagen,type from dbnoticiaimagenes where idnoticiaimagen =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbnoticiaimagenes*/
	
	
	/* PARA Noticias */
	
	function insertarNoticias($refcategorias,$titulo,$noticia,$pie,$fechacreacion,$refusuarios) { 
	$sql = "insert into dbnoticias(idnoticia,refcategorias,titulo,noticia,pie,fechacreacion,refusuarios) 
	values ('',".$refcategorias.",'".utf8_decode($titulo)."','".utf8_decode($noticia)."','".utf8_decode($pie)."',".$fechacreacion.",".$refusuarios.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarNoticias($id,$refcategorias,$titulo,$noticia,$pie,$fechacreacion,$refusuarios) { 
	$sql = "update dbnoticias 
	set 
	refcategorias = ".$refcategorias.",titulo = '".utf8_decode($titulo)."',noticia = '".utf8_decode($noticia)."',pie = '".utf8_decode($pie)."',fechacreacion = ".$fechacreacion.",refusuarios = ".$refusuarios." 
	where idnoticia =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarNoticias($id) { 
	$sql = "delete from dbnoticias where idnoticia =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerNoticias() { 
	$sql = "select 
	n.idnoticia,
	n.refcategorias,
	n.titulo,
	n.noticia,
	n.pie,
	n.fechacreacion,
	n.refusuarios
	from dbnoticias n 
	inner join tbcategorias cat ON cat.idcategoria = n.refcategorias 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerNoticiasPorId($id) { 
	$sql = "select idnoticia,refcategorias,titulo,noticia,pie,fechacreacion,refusuarios from dbnoticias where idnoticia =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbnoticias*/
	
	
	/* PARA Usuarios */
	
	function insertarUsuarios($usuario,$password,$refroles,$email,$nombrecompleto) { 
	$sql = "insert into dbusuarios(idusuario,usuario,password,refroles,email,nombrecompleto) 
	values ('','".utf8_decode($usuario)."','".utf8_decode($password)."',".$refroles.",'".utf8_decode($email)."','".utf8_decode($nombrecompleto)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarUsuarios($id,$usuario,$password,$refroles,$email,$nombrecompleto) { 
	$sql = "update dbusuarios 
	set 
	usuario = '".utf8_decode($usuario)."',password = '".utf8_decode($password)."',refroles = ".$refroles.",email = '".utf8_decode($email)."',nombrecompleto = '".utf8_decode($nombrecompleto)."' 
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
	u.nombrecompleto
	from dbusuarios u 
	inner join tbroles rol ON rol.idrol = u.refroles 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerUsuariosPorId($id) { 
	$sql = "select idusuario,usuario,password,refroles,email,nombrecompleto from dbusuarios where idusuario =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbusuarios*/
	
	
	/* PARA Predio_menu */
	
	function insertarPredio_menu($url,$icono,$nombre,$Orden,$hover,$permiso) { 
	$sql = "insert into predio_menu(idmenu,url,icono,nombre,Orden,hover,permiso) 
	values ('','".utf8_decode($url)."','".utf8_decode($icono)."','".utf8_decode($nombre)."',".$Orden.",'".utf8_decode($hover)."','".utf8_decode($permiso)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarPredio_menu($id,$url,$icono,$nombre,$Orden,$hover,$permiso) { 
	$sql = "update predio_menu 
	set 
	url = '".utf8_decode($url)."',icono = '".utf8_decode($icono)."',nombre = '".utf8_decode($nombre)."',Orden = ".$Orden.",hover = '".utf8_decode($hover)."',permiso = '".utf8_decode($permiso)."' 
	where idmenu =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarPredio_menu($id) { 
	$sql = "delete from predio_menu where idmenu =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerPredio_menu() { 
	$sql = "select 
	p.idmenu,
	p.url,
	p.icono,
	p.nombre,
	p.Orden,
	p.hover,
	p.permiso
	from predio_menu p 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerPredio_menuPorId($id) { 
	$sql = "select idmenu,url,icono,nombre,Orden,hover,permiso from predio_menu where idmenu =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: predio_menu*/
	
	
	/* PARA Categorias */
	
	function insertarCategorias($categoria,$activo) { 
	$sql = "insert into tbcategorias(idcategoria,categoria,activo) 
	values ('','".utf8_decode($categoria)."',".$activo.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarCategorias($id,$categoria,$activo) { 
	$sql = "update tbcategorias 
	set 
	categoria = '".utf8_decode($categoria)."',activo = ".$activo." 
	where idcategoria =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarCategorias($id) { 
	$sql = "delete from tbcategorias where idcategoria =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerCategorias() { 
	$sql = "select 
	c.idcategoria,
	c.categoria,
	c.activo
	from tbcategorias c 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerCategoriasPorId($id) { 
	$sql = "select idcategoria,categoria,activo from tbcategorias where idcategoria =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: tbcategorias*/
	
	
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
	
	
	/* PARA Roles */
	
	function insertarRoles($descripcion,$activo) { 
	$sql = "insert into tbroles(idrol,descripcion,activo) 
	values ('','".utf8_decode($descripcion)."',".$activo.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarRoles($id,$descripcion,$activo) { 
	$sql = "update tbroles 
	set 
	descripcion = '".utf8_decode($descripcion)."',activo = ".$activo." 
	where idrol =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarRoles($id) { 
	$sql = "delete from tbroles where idrol =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerRoles() { 
	$sql = "select 
	r.idrol,
	r.descripcion,
	r.activo
	from tbroles r 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerRolesPorId($id) { 
	$sql = "select idrol,descripcion,activo from tbroles where idrol =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: tbroles*/



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
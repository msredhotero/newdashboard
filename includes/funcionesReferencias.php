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

	function sanear_string($string)
	{
	
		$string = trim($string);
	
		$string = str_replace(
			array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
			array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
			$string
		);
	
		$string = str_replace(
			array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
			array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
			$string
		);
	
		$string = str_replace(
			array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
			array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
			$string
		);
	
		$string = str_replace(
			array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
			array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
			$string
		);
	
		$string = str_replace(
			array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
			array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
			$string
		);
	
		$string = str_replace(
			array('ñ', 'Ñ', 'ç', 'Ç'),
			array('n', 'N', 'c', 'C',),
			$string
		);
	
	
	
		return $string;
	}

	function borrarDirecctorio($dir) {
        array_map('unlink', glob($dir."/*.*")); 
    
    }

	function borrarArchivos($directorio) {
        
        $res =  $this->borrarDirecctorio("./".$directorio);

        rmdir("./".$directorio);

        return '';
    }



	
function insertarConectorDelegado($reftemporadas, $refusuarios, $refjugadores,$reftipojugadores,$refequipos,$refcountries,$refcategorias,$esfusion,$activo) {
	$sql = "insert into dbconectordelegados(idconector,reftemporadas, refusuarios,refjugadores,reftipojugadores,refequipos,refcountries,refcategorias,esfusion,activo)
	values ('',".$refjugadores.",".$reftemporadas.",".$refusuarios.",".$reftipojugadores.",".$refequipos.",".$refcountries.",".$refcategorias.",".$esfusion.",".$activo.")";
	$res = $this->query($sql,1);
	return $res;
	}
	
	
	function modificarConectorDelegado($id,$reftemporadas, $refusuarios, $refjugadores,$reftipojugadores,$refequipos,$refcountries,$refcategorias,$esfusion,$activo) {
	$sql = "update dbconectordelegados
	set
	reftemporadas = ".$reftemporadas.",refusuarios = ".$refusuarios.", refjugadores = ".$refjugadores.",reftipojugadores = ".$reftipojugadores.",refequipos = ".$refequipos.",refcountries = ".$refcountries.",refcategorias = ".$refcategorias.",esfusion = ".$esfusion.",activo = ".$activo."
	where idconector =".$id;
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function eliminarConectorDelegado($id) {
	$sql = "update dbconectordelegados set activo = 0 where idconector =".$id;
	$res = $this->query($sql,0);
	return $res;
	}
	
	function eliminarTodosLosJugadoresDelegado($id) {
		$sql = "update dbconectordelegados set activo = 0 where refequipos =".$id;
		$res = $this->query($sql,0);
		return $res;
	}
	
	function eliminarConectorDefinitivamenteDelegado($id) {
	$sql = "delete from dbconectordelegados where idconector =".$id;
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	
	function eliminarConectorPorJugadorDelegado($id) {
	$sql = "update dbconectordelegados set activo = 0 where refjugadores =".$id;
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function traerConectorDelegado($refJugador, $reftemporadas, $refusuarios) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		concat(equ.idequipo, '- ',equ.nombre) as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento
		
	from
	dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = c.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where jug.idjugador = ".$refJugador." and c.reftemporadas = ".$reftemporadas." 
				and c.refusuarios = ".$refusuarios."
	order by 1";
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function traerConectorActivosDelegado($refJugador, $reftemporadas, $refusuarios) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		equ.nombre as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento
		
	from
	dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where jug.idjugador = ".$refJugador." and c.activo = 1 and c.reftemporadas = ".$reftemporadas." 
				and c.refusuarios = ".$refusuarios."
	order by 1";
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	
	function traerConectorCategoriasActivosDelegado($refCategorias, $reftemporadas, $refusuarios) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		equ.nombre as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento
		
	from
	dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where cat.idtcategoria = ".$refCategorias." and c.activo = 1 and c.reftemporadas = ".$reftemporadas." 
		and c.refusuarios = ".$refusuarios."
	order by 1";
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	
	function traerConectorTodosActivosDelegado($reftemporadas, $refusuarios) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		equ.nombre as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento
		
	from
	dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where c.activo = 1 and c.reftemporadas = ".$reftemporadas." 
		and c.refusuarios = ".$refusuarios."
	order by 1";
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function traerConectorActivosPorEquiposDelegado($refEquipos, $reftemporadas, $refusuarios) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		equ.nombre as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento,
		jug.fechanacimiento,
		tip.idtipojugador,
		year(now()) - year(jug.fechanacimiento) as edad,
		jug.fechabaja,
		jug.fechaalta
	
	from
	dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where equ.idequipo = ".$refEquipos." and c.activo = 1 and c.reftemporadas = ".$reftemporadas." 
				and c.refusuarios = ".$refusuarios."
	order by concat(jug.apellido,', ',jug.nombres)";
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function traerConectorActivosPorEquiposCategoriasDelegado($refEquipos, $idCategoria, $reftemporadas, $refusuarios) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		equ.nombre as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento,
		jug.fechanacimiento,
		tip.idtipojugador,
		year(now()) - year(jug.fechanacimiento) as edad,
		(case when jug.fechabaja = '0000-00-00' then '1900-01-01' else coalesce(jug.fechabaja,'1900-01-01') end) as fechabaja
		
	from
		dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where equ.idequipo = ".$refEquipos." and c.activo = 1 and c.refcategorias = ".$idCategoria." 
				and c.reftemporadas = ".$reftemporadas." 
				and c.refusuarios = ".$refusuarios."
	order by concat(jug.apellido,', ',jug.nombres)";
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function traerConectorActivosPorEquiposEdadesDelegado($refEquipos, $reftemporadas, $refusuarios) {
	$sql = "select 
		min(year(now()) - year(jug.fechanacimiento)) as edadMinima,
		max(year(now()) - year(jug.fechanacimiento)) as edadMaxima,
		count(*) as cantidadJugadores,
		round((max(year(now()) - year(jug.fechanacimiento)) + min(year(now()) - year(jug.fechanacimiento)))/2,2) as edadPromedio 
	from
		dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where equ.idequipo = ".$refEquipos." and c.activo = 1 
				and c.reftemporadas = ".$reftemporadas." 
				and c.refusuarios = ".$refusuarios;
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	
	function traerConectorActivosPorConectorDelegado($id) {
	$sql = "select 
		c.idconector,
		cat.categoria,
		equ.nombre as equipo,
		co.nombre as countrie,
		tip.tipojugador,
		(case when c.esfusion = 1 then 'Si' else 'No' end) as esfusion,
		(case when c.activo = 1 then 'Si' else 'No' end) as activo,
		c.refjugadores,
		c.reftipojugadores,
		c.refequipos,
		c.refcountries,
		c.refcategorias,
		concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
		jug.nrodocumento,
		jug.fechanacimiento,
		tip.idtipojugador,
		year(now()) - year(jug.fechanacimiento) as edad
		
	from
		dbconectordelegados c
			inner join
		dbjugadores jug ON jug.idjugador = c.refjugadores
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequipos equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		dbcontactos con ON con.idcontacto = equ.refcontactos
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where c.idconector =".$id."
	order by 1";
	$res = $this->query($sql,0);
	return $res;
	}


	function existeJugadoresclubPorClubJugador($idClub, $idJugador) { 

		$resTemporada = $this->traerUltimaTemporada();
		$temporada = mysql_result($resTemporada,0,1);

		$sql = "select 
		jc.idjugadorclub,
		j.apellido,
		j.nombres,
		j.nrodocumento,
		(case when jc.fechabaja=1 then 'Si' else 'No' end) as fechabaja,
		(case when jc.articulo=1 then 'Si' else 'No' end) as articulo,
		jc.numeroserielote,
		jc.temporada,
		jc.refcountries,
		jc.refjugadores
		from dbjugadoresclub jc
		inner join dbjugadores j on j.idjugador = jc.refjugadores
		inner join dbcountries c on c.idcountrie = jc.refcountries 
		where jc.refJugadores = ".$idJugador." and j.refcountries = ".$idClub." and jc.temporada = ".$temporada."
		order by 1"; 
		$res = $this->existeDevuelveId($sql);
		return $res; 
	} 

	function insertarJugadoresclub($refjugadores,$fechabaja,$articulo,$numeroserielote,$temporada,$refcountries) { 
		$sql = "insert into dbjugadoresclub(idjugadorclub,refjugadores,fechabaja,articulo,numeroserielote,temporada,refcountries) 
		values ('',".$refjugadores.",".$fechabaja.",".$articulo.",'".($numeroserielote)."',".$temporada.",".$refcountries.")"; 
		
		$res = $this->query($sql,1); 
		return $res; 
	} 

	function eliminarJugadoresclub($id) { 
		$sql = "delete from dbjugadoresclub where idjugadorclub =".$id; 
		$res = $this->query($sql,0); 
		return $res; 
	} 
	
	
	function modificarJugadoresclub($id,$refjugadores,$fechabaja,$articulo,$numeroserielote,$temporada,$refcountries) { 
		$sql = "update dbjugadoresclub 
		set 
		refjugadores = ".$refjugadores.",fechabaja = ".$fechabaja.",articulo = ".$articulo.",numeroserielote = '".($numeroserielote)."',temporada = ".$temporada.",refcountries = ".$refcountries." 
		where idjugadorclub =".$id; 
		$res = $this->query($sql,0); 
		return $res; 
	} 

	function traerUltimaTemporada() {
		$sql = "select
		t.idtemporadas,
		t.temporada
		from tbtemporadas t
		order by 1 desc
		limit 1";
		$res = $this->query($sql,0);
		return $res;
	}

	function traerCierrepadronesPorCountry($idcountry) {
		$sql = "select idcierrepadron,refcountries,refusuarios,fechacierre from tbcierrepadrones where refcountries =".$idcountry;
		$res = $this->query($sql,0);
		return $res;
	}

	function traerTipodocumentos() { 
		$sql = "select 
		t.idtipodocumento,
		t.tipodocumento
		from tbtipodocumentos t 
		order by 1"; 
		$res = $this->query($sql,0); 
		return $res; 
	} 

	function existeJugador($nroDocumento) {
		$sql = "select idjugador from dbjugadores where nrodocumento = ".$nroDocumento;
		$res = $this->query($sql,0);
		
		if (mysql_num_rows($res)>0) {
			return 1;   
		}
		return 0;
	}
	
	
	function existeJugadorPre($nroDocumento) {
		$sql = "select idjugadorpre from dbjugadorespre where nrodocumento = ".$nroDocumento;
		$res = $this->query($sql,0);
		
		if (mysql_num_rows($res)>0) {
			return 1;   
		}
		return 0;
	}

	
function insertarJugadorespre($reftipodocumentos,$nrodocumento,$apellido,$nombres,$email,$fechanacimiento,$fechaalta,$numeroserielote,$refcountries,$observaciones,$refusuarios) {
	$sql = "insert into dbjugadorespre(idjugadorpre,reftipodocumentos,nrodocumento,apellido,nombres,email,fechanacimiento,fechaalta,numeroserielote,refcountries,observaciones,refusuarios, refestados)
	values ('',".$reftipodocumentos.",".$nrodocumento.",'".strtoupper($apellido)."','".strtoupper($nombres)."','".($email)."','".($fechanacimiento)."','".($fechaalta)."','".($numeroserielote)."',".$refcountries.",'".($observaciones)."',".$refusuarios.",1)";
	$res = $this->query($sql,1);
	return $res;
	}
	
	
	function modificarJugadorespre($id,$reftipodocumentos,$nrodocumento,$apellido,$nombres,$email,$fechanacimiento,$fechaalta,$numeroserielote,$refcountries,$observaciones,$refusuarios) {
	$sql = "update dbjugadorespre
	set
	reftipodocumentos = ".$reftipodocumentos.",nrodocumento = ".$nrodocumento.",apellido = '".strtoupper($apellido)."',nombres = '".strtoupper($nombres)."',email = '".($email)."',fechanacimiento = '".utf8_decode($fechanacimiento)."',fechaalta = '".($fechaalta)."',numeroserielote = '".($numeroserielote)."',refcountries = ".$refcountries.",observaciones = '".($observaciones)."',refusuarios = ".$refusuarios."
	where idjugadorpre =".$id;
	$res = $this->query($sql,0);
	return $sql;
	}
	
	
	function modificarJugadorespreRegistro($id,$apellido,$nombres,$fechanacimiento,$observaciones) {
	$sql = "update dbjugadorespre
	set
	apellido = '".strtoupper($apellido)."',nombres = '".strtoupper($nombres)."',fechanacimiento = '".($fechanacimiento)."',observaciones = '".($observaciones)."'
	where idjugadorpre =".$id;
	$res = $this->query($sql,0);
	return $res;
	}

	function traerJugadoresprePorId($id) {
	$sql = "select idjugadorpre,reftipodocumentos,nrodocumento,apellido,nombres,email,fechanacimiento,fechaalta,refcountries,observaciones,refusuarios,numeroserielote,refestados from dbjugadorespre where idjugadorpre =".$id;
	$res = $this->query($sql,0);
	return $res;
	}
	
	function modificarJugadorespreRegistroNuevo($id,$apellido,$nombres,$fechanacimiento,$observaciones,$email) {
	$sql = "update dbjugadorespre
	set
	apellido = '".strtoupper($apellido)."',nombres = '".strtoupper($nombres)."',fechanacimiento = '".($fechanacimiento)."',observaciones = '".($observaciones)."',email = '".($email)."'
	where idjugadorpre =".$id;
	$res = $this->query($sql,0);
	return $res;
	}
	
	
	function eliminarJugadorespre($id) {
		
	$sql = "delete from dbjugadorespre where idjugadorpre =".$id;
	$res = $this->query($sql,0);
	return $res;
	}


	
	function traerJugadoresprePorCountries($refCountries) {
		$sql = "select
		j.idjugadorpre,
		td.tipodocumento,
		j.nrodocumento,
		j.apellido,
		j.nombres,
		j.email,
		j.fechanacimiento,
		j.fechaalta,
		j.numeroserielote,
		j.observaciones,
		j.refusuarios,
		j.refcountries,
		j.refestados
		from dbjugadorespre j
		inner join tbtipodocumentos td on td.idtipodocumento = j.reftipodocumentos
		left join dbjugadores jj on jj.nrodocumento = j.nrodocumento
		where   j.refcountries = ".$refCountries." and jj.idjugador is null
		order by j.apellido, j.nombres";
		$res = $this->query($sql,0);
		return $res;
	}

	function traerVigenciasoperacionesPorModuloVigencias($idModulo, $fecha) {
		$sql = "select idvigenciaoperacion,refmodulos,vigenciadesde,vigenciahasta,observaciones from dbvigenciasoperaciones where refmodulos =".$idModulo." and (('".$fecha."' between vigenciadesde and vigenciahasta) or ('".$fecha."' >= vigenciadesde and vigenciahasta is null))";
		$res = $this->query($sql,0);
		return $res;
	}
	
	function traerJugadoresClubPorCountrieActivos($idCountrie,$busqueda='') { 

		$resTemporada = $this->traerUltimaTemporada();
		$temporada = mysql_result($resTemporada,0,1);

		$sql = "select 
		j.idjugador,
		tip.tipodocumento,
		j.nrodocumento,
		j.apellido,
		j.nombres,
		j.email,
		date_format(j.fechanacimiento, '%d/%m/%Y') as fechanacimiento,
		j.fechaalta,
		j.fechabaja,
		cou.nombre as countrie,
		j.observaciones,
		j.reftipodocumentos,
		j.refcountries,
		(case when jc.fechabaja = 1 then 'Si' else 'No' end) as fechabaja,
		(case when jc.articulo = 1 then 'Si' else 'No' end) as articulo,
		(case when jc.fechabaja = 1 then true else false end) as fechabajacheck,
		(case when jc.articulo = 1 then true else false end) as articulocheck,
		coalesce( jc.numeroserielote,'') as numeroserielote,
		concat(j.apellido, ' ', j.nombres) as apyn
		from dbjugadores j 
		inner join tbtipodocumentos tip ON tip.idtipodocumento = j.reftipodocumentos 
		inner join dbcountries cou ON cou.idcountrie = j.refcountries 
		inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria 
		left join dbjugadoresclub jc 
		on jc.refcountries = cou.idcountrie and jc.refjugadores = j.idjugador and jc.temporada = ".$temporada."
		where j.refcountries = ".$idCountrie." and (j.fechabaja is null or j.fechabaja = '1900-01-01' or j.fechabaja = '0000-00-00' or j.fechabaja >= now())
		";
		if ($busqueda != '') {
			$sql .= " and concat(j.nrodocumento,' ', j.apellido, ' ', j.nombres) like '%".$busqueda."%'";
		}
		$sql .= " 
		order by concat(j.apellido, ' ', j.nombres) 
		"; 
		//die(var_dump($sql));
		$res = $this->query($sql,0); 
		return $res; 
	} 


	function traerJugadoresClubPorCountrieActivosPaginador($idCountrie, $pagina, $cantidad, $busqueda='') { 

		$resTemporada = $this->traerUltimaTemporada();
		$temporada = mysql_result($resTemporada,0,1);

		$sql = "select 
		j.idjugador,
		tip.tipodocumento,
		j.nrodocumento,
		j.apellido,
		j.nombres,
		j.email,
		date_format(j.fechanacimiento, '%d/%m/%Y') as fechanacimiento,
		j.fechaalta,
		j.fechabaja,
		cou.nombre as countrie,
		j.observaciones,
		j.reftipodocumentos,
		j.refcountries,
		(case when jc.fechabaja = 1 then 'Si' else 'No' end) as fechabaja,
		(case when jc.articulo = 1 then 'Si' else 'No' end) as articulo,
		(case when jc.fechabaja = 1 then true else false end) as fechabajacheck,
		(case when jc.articulo = 1 then true else false end) as articulocheck,
		coalesce( jc.numeroserielote,'') as numeroserielote,
		concat(j.apellido, ' ', j.nombres) as apyn
		from dbjugadores j 
		inner join tbtipodocumentos tip ON tip.idtipodocumento = j.reftipodocumentos 
		inner join dbcountries cou ON cou.idcountrie = j.refcountries 
		inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria 
		left 
		join dbjugadoresclub jc on jc.refcountries = cou.idcountrie and jc.refjugadores = j.idjugador and jc.temporada = ".$temporada."
		where j.refcountries = ".$idCountrie." 
			  and (j.fechabaja is null or j.fechabaja = '1900-01-01' or j.fechabaja = '0000-00-00' or j.fechabaja >= now())
			  ";
		if ($busqueda != '') {
			$sql .= " and concat(j.nrodocumento,' ', j.apellido, ' ', j.nombres) like '%".$busqueda."%'";
		}
		$sql .= " order by concat(j.apellido, ' ', j.nombres) 
		limit ".(($pagina - 1) * $cantidad).",".$cantidad; 
		
		$res = $this->query($sql,0); 
		return $res; 
	} 

	function traerCountriesPorId($id) {
		$sql = "select idcountrie,direccion,
			telefonoadministrativo,telefonocampo,email, 
			concat('../../archivos/countries/', idcountrie,'/',imagen) as imagen 
			from dbcountries where idcountrie =".$id;
		$res = $this->query($sql,0);
		return $res;
	}

	function modificarCountry($id, $direccion, $telefonoadministrativo,$telefonocampo,$email) {
		$sql = "update dbcountries 
					set direccion = '".$direccion."',
						telefonoadministrativo = '".$telefonoadministrativo."',
						telefonocampo = '".$telefonocampo."',
						email = '".$email."'
					where idcountrie =".$id;
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

	function modificarImagenCuontries($id, $imagen) {
		$sql = "update dbcountries set imagen = '".$imagen."' where idcountrie = ".$id;
		$res = $this->query($sql,0);

		echo $res;
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
		order by cat.idtcategoria, di.iddivision, e.nombre"; 
		$res = $this->query($sql,0); 
		return $res; 
	} 
	
	
	/* PARA Usuarios */
	
	function insertarUsuarios($usuario,$password,$refroles,$email,$nombrecompleto,$refclientes,$activo) { 
	$sql = "insert into dbusuarios(idusuario,usuario,password,refroles,email,nombrecompleto,refclientes,activo) 
	values ('','".($usuario)."','".($password)."',".$refroles.",'".($email)."','".($nombrecompleto)."',".$refclientes.",".$activo.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarUsuarios($id,$usuario,$password,$refroles,$email,$nombrecompleto,$refclientes,$activo) { 
	$sql = "update dbusuarios 
	set 
	usuario = '".($usuario)."',password = '".($password)."',refroles = ".$refroles.",email = '".($email)."',nombrecompleto = '".($nombrecompleto)."',refclientes = ".$refclientes.",activo = ".$activo." 
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


	
/* PARA Delegados */

function insertarDelegados($refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4) { 
	$sql = "insert into dbdelegados(iddelegado,refusuarios,apellidos,nombres,direccion,localidad,cp,telefono,celular,fax,email1,email2,email3,email4) 
	values ('',".$refusuarios.",'".($apellidos)."','".($nombres)."','".($direccion)."','".($localidad)."','".($cp)."','".($telefono)."','".($celular)."','".($fax)."','".($email1)."','".($email2)."','".($email3)."','".($email4)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarDelegados($id,$refusuarios,$apellidos,$nombres,$direccion,$localidad,$cp,$telefono,$celular,$fax,$email1,$email2,$email3,$email4) { 
	$sql = "update dbdelegados 
	set 
	refusuarios = ".$refusuarios.",apellidos = '".($apellidos)."',nombres = '".($nombres)."',direccion = '".($direccion)."',localidad = '".($localidad)."',cp = '".($cp)."',telefono = '".($telefono)."',celular = '".($celular)."',fax = '".($fax)."',email1 = '".($email1)."',email2 = '".($email2)."',email3 = '".($email3)."',email4 = '".($email4)."' 
	where iddelegado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarDelegados($id) { 
	$sql = "delete from dbdelegados where iddelegado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerDelegados() { 
	$sql = "select 
	d.iddelegado,
	d.refusuarios,
	d.apellidos,
	d.nombres,
	d.direccion,
	d.localidad,
	d.cp,
	d.telefono,
	d.celular,
	d.fax,
	d.email1,
	d.email2,
	d.email3,
	d.email4
	from dbdelegados d 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 

	function existeDelegadoPorUsuario($id) {
		$sql = "select iddelegado,refusuarios,apellidos from dbdelegados where refusuarios =".$id;
		
		return $this->existeDevuelveId($sql);

	}
	
	
	function traerDelegadosPorId($id) { 
	$sql = "select iddelegado,refusuarios,apellidos,nombres,direccion,localidad,cp,telefono,celular,fax,email1,email2,email3,email4 from dbdelegados where iddelegado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 


	function traerDelegadosPorUsuario($id) { 
		$sql = "select iddelegado,refusuarios,apellidos,nombres,direccion,localidad,cp,telefono,celular,fax,email1,email2,email3,email4 from dbdelegados where refusuarios =".$id; 
		$res = $this->query($sql,0); 
		return $res; 
		} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbdelegados*/

	function existeDevuelveId($sql) {

		$res = $this->query($sql,0);
		
		if (mysql_num_rows($res)>0) {
			return mysql_result($res,0,0);  
		}
		return 0;
	}
	
	
	/* PARA Configuracion */
	
	function insertarConfiguracion($razonsocial,$empresa,$sistema,$direccion,$telefono,$email) { 
	$sql = "insert into tbconfiguracion(idconfiguracion,razonsocial,empresa,sistema,direccion,telefono,email) 
	values ('','".($razonsocial)."','".($empresa)."','".($sistema)."','".($direccion)."','".($telefono)."','".($email)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarConfiguracion($id,$razonsocial,$empresa,$sistema,$direccion,$telefono,$email) { 
	$sql = "update tbconfiguracion 
	set 
	razonsocial = '".($razonsocial)."',empresa = '".($empresa)."',sistema = '".($sistema)."',direccion = '".($direccion)."',telefono = '".($telefono)."',email = '".($email)."' 
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
	values ('','".($estado)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarEstados($id,$estado) { 
	$sql = "update tbestados 
	set 
	estado = '".($estado)."' 
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
	values ('','".($nombremes)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarMeses($id,$nombremes) { 
	$sql = "update tbmeses 
	set 
	nombremes = '".($nombremes)."' 
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
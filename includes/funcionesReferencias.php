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
	

	function insertarFusionEquipos($refequipos, $refcountries, $refestados, $observacion) {
		$sql = "INSERT INTO dbfusionequipos
				(idfusionequipo,
				refequipos,
				refcountries,
				refestados,
				observacion)
				VALUES
				('',
				".$refequipos.",
				".$refcountries.",
				".$refestados.",
				'".$observacion."')";

		$res = $this->query($sql,1); 
		return $res; 
	}

	function eliminarFusionEquipos($idfusionequipo) {
		$sql = "delete from dbfusionequipos where idfusionequipo = ".$idfusionequipo;

		$res = $this->query($sql,0); 
		return $res; 
	}

	function traerCountriesMenosId($id) {
		$sql = "select idcountrie, nombre from dbcountries where idcountrie <> ".$id." order by trim(nombre)";
		$res = $this->query($sql,0); 
		return $res; 
	}

	function traerDefinicionesPorTemporadaCategoriaTipoJugador($idTemporada, $idCategoria, $idTipoJugador) {
		$sql = "select
					max(dct.cantmaxjugadores) as cantmaxjugadores, max(dctj.edadmaxima) as edadmaxima, max(dctj.edadminima) as edadminima, max((dctj.edadmaxima + dctj.edadminima) /2) as promedio
				from        dbdefinicionescategoriastemporadas dct
				inner
				join        dbdefinicionescategoriastemporadastipojugador dctj
				on          dct.iddefinicioncategoriatemporada = dctj.refdefinicionescategoriastemporadas
				where       dct.reftemporadas = ".$idTemporada." and refcategorias = ".$idCategoria." and reftipojugadores =".$idTipoJugador;
		$res = $this->query($sql,0); 
		return $res;    
	}

/* PARA Conectordelegados */

function insertarConectordelegados($reftemporadas,$refusuarios,$refjugadores,$reftipojugadores,$refequipos,$refcountries,$refcategorias,$esfusion,$activo,$refestados) { 
	$sql = "insert into dbconectordelegados(idconector,reftemporadas,refusuarios,refjugadores,reftipojugadores,refequipos,refcountries,refcategorias,esfusion,activo,refestados) 
	values ('',".$reftemporadas.",".$refusuarios.",".$refjugadores.",".$reftipojugadores.",".$refequipos.",".$refcountries.",".$refcategorias.",".$esfusion.",".$activo.",".$refestados.")"; 
	$res = $this->query($sql,1); 
	return $res; 
	} 
	
	
	function modificarConectordelegados($id,$reftemporadas,$refusuarios,$refjugadores,$reftipojugadores,$refequipos,$refcountries,$refcategorias,$esfusion,$activo,$refestados) { 
	$sql = "update dbconectordelegados 
	set 
	reftemporadas = ".$reftemporadas.",refusuarios = ".$refusuarios.",refjugadores = ".$refjugadores.",reftipojugadores = ".$reftipojugadores.",refequipos = ".$refequipos.",refcountries = ".$refcountries.",refcategorias = ".$refcategorias.",esfusion = ".$esfusion.",activo = ".$activo.",refestados = ".$refestados." 
	where idconector =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function eliminarConectordelegados($id) { 
	$sql = "delete from dbconectordelegados where idconector =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerConectordelegados() { 
	$sql = "select 
	c.idconector,
	c.reftemporadas,
	c.refusuarios,
	c.refjugadores,
	c.reftipojugadores,
	c.refequipos,
	c.refcountries,
	c.refcategorias,
	c.esfusion,
	c.activo,
	c.refestados
	from dbconectordelegados c 
	inner join jug ON jug. = c.refjugadores 
	inner join tip ON tip. = c.reftipojugadores 
	inner join equ ON equ. = c.refequipos 
	inner join cou ON cou. = c.refcountries 
	inner join cat ON cat. = c.refcategorias 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerConectordelegadosPorId($id) { 
	$sql = "select idconector,reftemporadas,refusuarios,refjugadores,reftipojugadores,refequipos,refcountries,refcategorias,esfusion,activo,refestados from dbconectordelegados where idconector =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbconectordelegados*/




	
/* PARA Equiposdelegados */

/* PARA Cabeceraconfirmacion */

function devolverIdEstado($tabla,$id, $idlbl) {
	$sql = "select refestados 
			from ".$tabla." 
			where ".$idlbl." = ".$id;
	
	$res = $this->existeDevuelveId($sql);
	return $res;
}

function existeCabeceraConfirmacion($reftemporadas,$refcountries) {
	$sql = "select idcabeceraconfirmacion 
			from dbcabeceraconfirmacion 
			where reftemporadas = ".$reftemporadas." and refcountries = ".$refcountries;
	
	$res = $this->existeDevuelveId($sql);
	return $res;
}

function insertarCabeceraconfirmacion($reftemporadas,$refcountries,$refestados,$usuacrea,$usuamodi) { 
	$sql = "insert into dbcabeceraconfirmacion(idcabeceraconfirmacion,reftemporadas,refcountries,refestados,fechacrea,fechamodi,usuacrea,usuamodi) 
	values ('',".$reftemporadas.",".$refcountries.",".$refestados.",'".date('Y-m-d')."','".date('Y-m-d')."','".utf8_decode($usuacrea)."','".utf8_decode($usuamodi)."')"; 
	$res = $this->query($sql,1); 
	return $res; 
} 
	
	
function modificarCabeceraconfirmacion($id,$reftemporadas,$refcountries,$refestados,$usuacrea,$usuamodi) { 
	$sql = "update dbcabeceraconfirmacion 
	set 
	reftemporadas = ".$reftemporadas.",refcountries = ".$refcountries.",refestados = ".$refestados.",fechamodi = '".date('Y-m-d')."',usuacrea = '".utf8_decode($usuacrea)."',usuamodi = '".utf8_decode($usuamodi)."' 
	where idcabeceraconfirmacion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
} 

function modificarCabeceraconfirmacionEstado($id,$refestados) { 
	$sql = "update dbcabeceraconfirmacion 
	set 
	refestados = ".$refestados." 
	where idcabeceraconfirmacion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
} 
	
	
function eliminarCabeceraconfirmacion($id) { 
	$sql = "delete from dbcabeceraconfirmacion where idcabeceraconfirmacion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
} 
	
	
function traerCabeceraconfirmacion() { 
	$sql = "select 
	c.idcabeceraconfirmacion,
	c.reftemporadas,
	c.refcountries,
	c.refestados,
	c.fechacrea,
	c.fechamodi,
	c.usuacrea,
	c.usuamodi
	from dbcabeceraconfirmacion c 
	order by 1"; 
	$res = $this->query($sql,0); 
	return $res; 
} 
	
	
function traerCabeceraconfirmacionPorId($id) { 
	$sql = "SELECT 
				cc.idcabeceraconfirmacion,
				cc.reftemporadas,
				cc.refcountries,
				cc.refestados,
				cc.fechacrea,
				cc.fechamodi,
				cc.usuacrea,
				cc.usuamodi,
				est.estado
			FROM
				dbcabeceraconfirmacion cc
			INNER JOIN
				tbestados est ON est.idestado = cc.refestados
			WHERE
				idcabeceraconfirmacion =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbcabeceraconfirmacion*/

function traerCategorias() {
	$sql = "select
	c.idtcategoria,
	c.categoria
	from tbcategorias c
	order by 1";
	$res = $this->query($sql,0);
	return $res;
}

function traerDivisiones() {
	$sql = "select
	d.iddivision,
	d.division
	from tbdivisiones d
	order by 1";
	$res = $this->query($sql,0);
	return $res;
}

	function devolverNuevoIdEquipo() {
		$sql = "select
				sum(r.idequipo) from (
						select max(idequipo) as idequipo from dbequipos
						union all
						select coalesce( max(idequipo),0) as idequipo from dbequiposdelegados
		) r";

		$res = $this->query($sql,0); 

		return mysql_result($res,0,0) + 1;
	}

	function insertarEquiposdelegados($reftemporadas,$refusuarios,$refcountries,$nombre,$refcategorias,$refdivisiones,$fechabaja,$activo,$refestados,$nuevo) { 
		$id = $this->devolverNuevoIdEquipo();
		
		$sql = "insert into dbequiposdelegados(idequipodelegado,idequipo,reftemporadas,refusuarios,refcountries,nombre,refcategorias,refdivisiones,fechabaja,activo,refestados, nuevo) 
		values ('',".$id.",".$reftemporadas.",".$refusuarios.",".$refcountries.",'".utf8_decode($nombre)."',".$refcategorias.",".$refdivisiones.",'".utf8_decode($fechabaja)."',".$activo.",".$refestados.",".$nuevo.")"; 
		$res = $this->query($sql,0); 
		return $id; 
	} 
	
	
	function modificarEquiposdelegados($id,$reftemporadas,$refusuarios,$refcountries,$nombre,$refcategorias,$refdivisiones,$fechabaja,$activo,$refestados) { 
		$sql = "update dbequiposdelegados 
		set 
		reftemporadas = ".$reftemporadas.",refusuarios = ".$refusuarios.",refcountries = ".$refcountries.",nombre = '".utf8_decode($nombre)."',refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones.",fechabaja = '".utf8_decode($fechabaja)."',activo = ".$activo.",refestados = ".$refestados." 
		where idequipo =".$id; 
		$res = $this->query($sql,0); 
		return $res; 
	} 
	
	
	function eliminarEquiposdelegados($id) { 
	$sql = "delete from dbequiposdelegados where idequipodelegado =".$id; 
	$res = $this->query($sql,0); 
	return $res; 
	} 
	
	
	function traerEquiposdelegadosPorCountrie($id, $idtemporada) { 
		$sql = "select 
		e.idequipo,
		cou.nombre as countrie,
		e.nombre,
		cat.categoria,
		di.division,
		e.fechabaja,
		(case when e.activo=1 then 'Si' else 'No' end) as activo,
		est.estado,
		e.refestados
		from dbequiposdelegados e 
		inner join dbcountries cou ON cou.idcountrie = e.refcountries 
		inner join tbcategorias cat ON cat.idtcategoria = e.refcategorias 
		inner join tbdivisiones di ON di.iddivision = e.refdivisiones 
		inner join tbestados est ON est.idestado = e.refestados
		where e.activo = 1 and e.nuevo = 1 and cou.idcountrie = ".$id." and e.reftemporadas = ".$idtemporada."
		order by 1"; 
		
		$res = $this->query($sql,0); 
		
		return $res; 
	} 


	function traerEquiposdelegadosPorCountrieFinalizado($id, $idtemporada) {
		$sql = "SELECT 
					ee.idequipo,
					cou.nombre AS countrie,
					ee.nombre,
					cat.categoria,
					di.division,
					ee.fachebaja,
					(CASE
						WHEN ee.activo = 1 THEN 'Si'
						ELSE 'No'
					END) AS activo,
					'Aceptado' as estado,
					cat.orden,
					ee.refdivisiones,
					'label-success' as label,
					3 as refestados
				FROM
					dbequipos ee
						LEFT JOIN
					dbequiposdelegados e ON ee.idequipo = e.idequipo and e.reftemporadas = ".$idtemporada."
						INNER JOIN
					dbcountries cou ON cou.idcountrie = ee.refcountries
						INNER JOIN
					tbcategorias cat ON cat.idtcategoria = ee.refcategorias
						INNER JOIN
					tbdivisiones di ON di.iddivision = ee.refdivisiones
				
				WHERE
					ee.activo = 1 AND cou.idcountrie = ".$id." and e.idequipo is null
					
				union all
				
				SELECT 
					e.idequipo,
					cou.nombre AS countrie,
					e.nombre,
					cat.categoria,
					di.division,
					e.fechabaja,
					(CASE
						WHEN e.activo = 1 THEN 'Si'
						ELSE 'No'
					END) AS activo,
					est.estado,
					cat.orden,
					e.refdivisiones,
					(CASE
						WHEN est.idestado = 1 THEN 'label-info'
						WHEN est.idestado = 2 THEN 'label-warning'
						WHEN est.idestado = 3 THEN 'label-success'
						WHEN est.idestado = 4 THEN 'label-danger'
					END) AS label,
					est.idestado as refestados
				FROM
					dbequiposdelegados e
						INNER JOIN
					dbcountries cou ON cou.idcountrie = e.refcountries
						INNER JOIN
					tbcategorias cat ON cat.idtcategoria = e.refcategorias
						INNER JOIN
					tbdivisiones di ON di.iddivision = e.refdivisiones
						INNER JOIN
					tbestados est ON est.idestado = e.refestados
				WHERE
					e.activo = 1 AND cou.idcountrie = ".$id."
			AND e.reftemporadas = ".$idtemporada." 
			order by 8,9"; 
		
		$res = $this->query($sql,0); 
		
		return $res; 
	}

	function traerEquiposFusionPorEquipo($idequipo, $idtemporada) {
		$sql = "SELECT 
					cc.nombre
				FROM
					dbfusionequipos f
						INNER JOIN
					dbcountries cc ON cc.idcountrie = f.refcountries
						INNER JOIN
					dbequiposdelegados ed ON ed.refcountries = f.refcountries
						AND ed.idequipo = ".$idequipo."
						AND ed.reftemporadas = ".$idtemporada;

		$res = $this->query($sql,0); 

		return $res; 
	}


	
	function traerEquiposdelegadosPorCountrieFinalizadoPorEquipo($id, $idtemporada, $idequipo) {
		$sql = "SELECT 
					ee.idequipo,
					cou.nombre AS countrie,
					ee.nombre,
					cat.categoria,
					di.division,
					ee.fachebaja,
					(CASE
						WHEN ee.activo = 1 THEN 'Si'
						ELSE 'No'
					END) AS activo,
					'Aceptado' as estado,
					cat.orden,
					ee.refdivisiones,
					'label-success' as label,
					3 as refestados,
					ee.refcategorias
				FROM
					dbequipos ee
						LEFT JOIN
					dbequiposdelegados e ON ee.idequipo = e.idequipo and e.reftemporadas = ".$idtemporada."
						INNER JOIN
					dbcountries cou ON cou.idcountrie = ee.refcountries
						INNER JOIN
					tbcategorias cat ON cat.idtcategoria = ee.refcategorias
						INNER JOIN
					tbdivisiones di ON di.iddivision = ee.refdivisiones
				
				WHERE
					ee.activo = 1 AND cou.idcountrie = ".$id." and e.idequipo is null
					and ee.idequipo = ".$idequipo."
					
				union all
				
				SELECT 
					e.idequipo,
					cou.nombre AS countrie,
					e.nombre,
					cat.categoria,
					di.division,
					e.fechabaja,
					(CASE
						WHEN e.activo = 1 THEN 'Si'
						ELSE 'No'
					END) AS activo,
					est.estado,
					cat.orden,
					e.refdivisiones,
					(CASE
						WHEN est.idestado = 1 THEN 'label-info'
						WHEN est.idestado = 2 THEN 'label-warning'
						WHEN est.idestado = 3 THEN 'label-success'
						WHEN est.idestado = 4 THEN 'label-danger'
					END) AS label,
					est.idestado as refestados,
					e.refcategorias
				FROM
					dbequiposdelegados e
						INNER JOIN
					dbcountries cou ON cou.idcountrie = e.refcountries
						INNER JOIN
					tbcategorias cat ON cat.idtcategoria = e.refcategorias
						INNER JOIN
					tbdivisiones di ON di.iddivision = e.refdivisiones
						INNER JOIN
					tbestados est ON est.idestado = e.refestados
				WHERE
					e.activo = 1 AND cou.idcountrie = ".$id."
					and e.idequipo = ".$idequipo."
			AND e.reftemporadas = ".$idtemporada." 
			order by 8,9"; 
		
		$res = $this->query($sql,0); 
		
		return $res; 
	}


	function traerEquiposdelegadosEliminadosPorCountrie($id, $idtemporada) { 
		$sql = "select 
		e.idequipodelegado,
		e.idequipo,
		cou.nombre as countrie,
		e.nombre,
		cat.categoria,
		di.division,
		e.fechabaja,
		(case when e.activo=1 then 'Si' else 'No' end) as activo,
		e.refestados,
		est.estado
		from dbequiposdelegados e 
		inner join dbcountries cou ON cou.idcountrie = e.refcountries 
		inner join tbcategorias cat ON cat.idtcategoria = e.refcategorias 
		inner join tbdivisiones di ON di.iddivision = e.refdivisiones 
		inner join tbestados est ON est.idestado = e.refestados
		where e.activo = 0 and cou.idcountrie = ".$id." and e.reftemporadas = ".$idtemporada."
		order by 1"; 
		
		$res = $this->query($sql,0); 
		
		return $res; 
	} 

	function eliminarEquipoPasivo($id, $idtemporada, $idusuario) {

		//verifico si ya existe el equipo eliminado
		
		$sql = "
		INSERT INTO dbequiposdelegados
					(idequipodelegado,
					idequipo,
					reftemporadas,
					refusuarios,
					refcountries,
					nombre,
					refcategorias,
					refdivisiones,
					fechabaja,
					activo,
					refestados)
		select
			'',
			idequipo,
			".$idtemporada.",
			".$idusuario.",
			refcountries,
			nombre,
			refcategorias,
			refdivisiones,
			fachebaja,
			0,
			1
		from dbequipos where idequipo = ".$id;

		$res = $this->query($sql,0); 
		
		return $res; 

	}
	
	
	function traerEquiposdelegadosPorId($id) { 
		$sql = "select idequipodelegado,idequipo,reftemporadas,refusuarios,refcountries,nombre,refcategorias,refdivisiones,fechabaja,activo,refestados from dbequiposdelegados where idequipodelegado =".$id; 
		$res = $this->query($sql,0); 
		return $res; 
	} 
	
	/* Fin */
	/* /* Fin de la Tabla: dbequiposdelegados*/


	
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
			concat('../../archivos/countries/', idcountrie,'/',imagen) as imagen , nombre
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

	function traerFusionPorEquiposCountrie($idequipo, $idcountrie) {
		$sql = "SELECT 
					ce.nombre AS countrypadre,
					e.nombre AS equipo,
					c.nombre AS countrie,
					cc.refcountries,
					cc.refequipos
				FROM
					dbequipos e
						INNER JOIN
					dbconector cc ON cc.refequipos = e.idequipo
						INNER JOIN
					dbcountries c ON c.idcountrie = cc.refcountries
						INNER JOIN
					dbcountries ce ON ce.idcountrie = e.refcountries
				WHERE
					cc.activo = 1 AND cc.esfusion = 1
						AND e.activo = 1
						AND c.idcountrie <> ce.idcountrie
						AND e.idequipo = ".$idequipo."
						AND ce.idcountrie = ".$idcountrie."
				GROUP BY ce.nombre , e.nombre , c.nombre , cc.refcountries , cc.refequipos";

		$res = $this->query($sql,0); 
		return $res; 
	}

	function traerEquiposPorCountriesConFusion($idCountrie) {
		$sql = "SELECT 
					e.idequipo,
					cou.nombre AS countrie,
					e.nombre,
					cat.categoria,
					di.division,
					con.nombre AS contacto,
					e.fechaalta,
					e.fachebaja,
					(CASE
						WHEN e.activo = 1 THEN 'Si'
						ELSE 'No'
					END) AS activo,
					e.refcountries,
					e.refcategorias,
					e.refdivisiones,
					e.refcontactos,
					coalesce(max(r.refcountries),0) as esfusion
				FROM
					dbequipos e
						INNER JOIN
					dbcountries cou ON cou.idcountrie = e.refcountries
						INNER JOIN
					tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria
						INNER JOIN
					tbcategorias cat ON cat.idtcategoria = e.refcategorias
						INNER JOIN
					tbdivisiones di ON di.iddivision = e.refdivisiones
						INNER JOIN
					dbcontactos con ON con.idcontacto = e.refcontactos
						INNER JOIN
					tbtipocontactos ti ON ti.idtipocontacto = con.reftipocontactos
						left join
					(SELECT 
						ce.nombre AS countrypadre,
						e.nombre AS equipo,
						c.nombre AS countrie,
						cc.refcountries,
						cc.refequipos
					FROM
						dbequipos e
							INNER JOIN
						dbconector cc ON cc.refequipos = e.idequipo
							INNER JOIN
						dbcountries c ON c.idcountrie = cc.refcountries
							INNER JOIN
						dbcountries ce ON ce.idcountrie = e.refcountries
					WHERE
						cc.activo = 1 AND cc.esfusion = 1
							AND e.activo = 1
							AND c.idcountrie <> ce.idcountrie
					GROUP BY ce.nombre , e.nombre , c.nombre , cc.refcountries , cc.refequipos) r
					on r.refequipos = e.idequipo
				WHERE
					cou.idcountrie = ".$idCountrie." AND e.activo = 1
				group by e.idequipo,
					cou.nombre ,
					e.nombre,
					cat.categoria,
					di.division,
					con.nombre ,
					e.fechaalta,
					e.fachebaja,
					e.activo,
					e.refcountries,
					e.refcategorias,
					e.refdivisiones,
					e.refcontactos
				ORDER BY cat.idtcategoria , di.iddivision , e.nombre";

		$res = $this->query($sql,0); 
		return $res; 
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
<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');



class ServiciosReferencias {


	function borrarArchivoJugadores($id,$directorio) {

		$sql    =   "delete from dbdocumentacionjugadorimagenes where iddocumentacionjugadorimagen =".$id;

		$res =  $this->borrarDirecctorio("./../../../../".$directorio);

		rmdir("./../../../../".$directorio);
		$this->query($sql,0);

		return '';
	}

	function eliminarFotoJugadoresID($refdocumentaciones, $refjugadorespre, $idAux=0) {
		$servidorCarpeta = 'aifzn';

      $sql        =   "select concat('".$servidorCarpeta."/data','/',s.iddocumentacionjugadorimagen) as archivo, s.iddocumentacionjugadorimagen
                         from dbdocumentacionjugadorimagenes s
                         where s.refdocumentaciones =".$refdocumentaciones." and (s.idjugador =".$refjugadorespre." or s.refjugadorespre=".$idAux.")";
      $resImg     =   $this->query($sql,0);

      if (mysql_num_rows($resImg)>0) {
         $res        =   $this->borrarArchivoJugadores(mysql_result($resImg,0,1),mysql_result($resImg,0,0));
      } else {
         $res = true;
      }
      if ($res != '') {
         return 'Error al eliminar datos';
      } else {
         return 'Se elimino la imagen correctamente';
      }
 }


	/* PARA Documentacionjugadorimagenes */

	function insertarDocumentacionjugadorimagenes($refdocumentaciones,$refjugadorespre,$imagen,$type,$refestados, $idjugador) {
		$sql = "insert into dbdocumentacionjugadorimagenes(iddocumentacionjugadorimagen,refdocumentaciones,refjugadorespre,imagen,type,refestados, idjugador)
		values ('',".$refdocumentaciones.",".$refjugadorespre.",'".($imagen)."','".($type)."',".$refestados.",".$idjugador.")";

		$res = $this->query($sql,1);
		return $res;
	}


	function modificarDocumentacionjugadorimagenes($id,$refdocumentaciones,$refjugadorespre,$imagen,$type,$refestados) {
		$sql = "update dbdocumentacionjugadorimagenes
		set
		refdocumentaciones = ".$refdocumentaciones.",refjugadorespre = ".$refjugadorespre.",imagen = '".($imagen)."',type = '".($type)."',refestados = ".$refestados."
		where iddocumentacionjugadorimagen =".$id;

		$res = $this->query($sql,0);
		return $res;
	}


	function modificarDocumentacionjugadorimagenesIDjugador($refjugadorespre,$idjugador) {
		$sql = "update dbdocumentacionjugadorimagenes
		set
		idjugador = ".$idjugador."
		where refjugadorespre =".$refjugadorespre;

		$res = $this->query($sql,0);
		return $res;
	}


	function modificarEstadoDocumentacionjugadorimagenesPorJugadorDocumentacion($idjugador,$iddocumentacion,$refestados) {
		$sql = "update dbdocumentacionjugadorimagenes
		set
		refestados = ".$refestados."
		where refjugadorespre =".$idjugador." and refdocumentaciones =".$iddocumentacion;

		$res = $this->query($sql,0);
		return $res;
	}


	function modificarEstadoDocumentacionjugadorimagenesPorId($id,$refestados) {
		$sql = "update dbdocumentacionjugadorimagenes
		set
		refestados = ".$refestados."
		where iddocumentacionjugadorimagen =".$id;

		$res = $this->query($sql,0);
		return $res;
	}


	function eliminarDocumentacionjugadorimagenes($id) {
		$sql = "delete from dbdocumentacionjugadorimagenes where iddocumentacionjugadorimagen =".$id;
		$res = $this->query($sql,0);
		return $res;
	}


	function traerDocumentacionjugadorimagenes() {
		$sql = "select
		d.iddocumentacionjugadorimagen,
		d.refdocumentaciones,
		d.refjugadorespre,
		d.imagen,
		d.type,
		d.refestados
		from dbdocumentacionjugadorimagenes d
		order by 1";

		$res = $this->query($sql,0);
		return $res;
	}


	function traerDocumentacionjugadorimagenesPorId($id) {
		$sql = "select iddocumentacionjugadorimagen,refdocumentaciones,refjugadorespre,imagen,type,refestados from dbdocumentacionjugadorimagenes where iddocumentacionjugadorimagen =".$id;

		$res = $this->query($sql,0);
		return $res;
	}



	function traerDocumentacionjugadorimagenesPorJugadorDocumentacion($idJugador, $idDocumentacion, $idJugadorPre=0) {
		$sql = "select
		                dj.iddocumentacionjugadorimagen,dj.refdocumentaciones,dj.refjugadorespre,dj.imagen,dj.type,dj.refestados, e.estado, concat('data','/',dj.iddocumentacionjugadorimagen) as archivo
		            from dbdocumentacionjugadorimagenes dj
		            inner join tbestados e ON e.idestado = dj.refestados
		        where (dj.idjugador =".$idJugador." or dj.refjugadorespre = ".$idJugadorPre.") and dj.refdocumentaciones = ".$idDocumentacion;

		$res = $this->query($sql,0);
		return $res;
	}


	function traerDocumentacionjugadorimagenesPorJugadorDocumentacionID($idJugador, $idDocumentacion, $idJugadorPre=0) {
		$sql = "select
		                dj.iddocumentacionjugadorimagen,dj.refdocumentaciones,dj.refjugadorespre,dj.imagen,dj.type,dj.refestados, e.estado, concat('data','/',dj.iddocumentacionjugadorimagen) as archivo
		            from dbdocumentacionjugadorimagenes dj
		            inner join tbestados e ON e.idestado = dj.refestados
		        where (dj.idjugador =".$idJugador." or dj.refjugadorespre = ".$idJugadorPre.") and dj.refdocumentaciones = ".$idDocumentacion;

		$res = $this->query($sql,0);
		return $res;
	}


	/* Fin */
	/* /* Fin de la Tabla: dbdocumentacionjugadorimagenes*/


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

		$string = str_replace(
			array(' '),
			array(''),
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

	function generarPlantelTemporadaAnterior($idtemporada, $idcountrie, $idequipo) {
		$sql = "SELECT
					    '',
					    ".$idtemporada." as reftemporadas,
					    '' as refusuarios,
					c.refjugadores,
					c.reftipojugadores,
					c.refequipos,
					c.refcountries,
					c.refcategorias,
					c.esfusion,
					c.activo,
					1 as refestados,
					0 as habilitacionpendiente,
					0 as refjugadorespre
					FROM
					    dbconector c
		where	c.refequipos = ".$idequipo." and c.refcountries = ".$idcountrie." and c.activo = 1";
		//die(var_dump($sql));
		$res = $this->query($sql,0);

		$habilitacionpendiente = 0;

		while ($row = mysql_fetch_array($res)) {

			$vEdad = $this->verificaEdadCategoriaJugador($row['refjugadores'], $row['refcategorias'], $row['reftipojugadores']);

			//$vEdadMenor = $this->verificaEdadCategoriaJugadorMenor($row['refjugadores'], $row['refcategorias'], $row['reftipojugadores']);

			if ($vEdad == 1) {
				$habilitacionpendiente = 0;

				$sqlInsert = "INSERT INTO dbconectordelegados
						(idconector,
						reftemporadas,
						refusuarios,
						refjugadores,
						reftipojugadores,
						refequipos,
						refcountries,
						refcategorias,
						esfusion,
						activo,
						refestados,
						habilitacionpendiente,
						refjugadorespre)
						values ('',
						".$row['reftemporadas'].",
						'',
						".$row['refjugadores'].",
						".$row['reftipojugadores'].",
						".$row['refequipos'].",
						".$row['refcountries'].",
						".$row['refcategorias'].",
						0,
						1,
						".$row['refestados'].",
						".$habilitacionpendiente.",
						".$row['refjugadorespre'].")";
				//die(var_dump($sqlInsert));
				$resI = $this->query($sqlInsert,1);
			}
		}

		// elimino los jugadores de los countries donde no se acepto la fusion
		$resGetAllFusiones = $this->traerFusionPorIdEquipos($idequipo);

		if (mysql_num_rows($resGetAllFusiones) > 0) {
			while ($rowFu = mysql_fetch_array($resGetAllFusiones)) {
				if ($rowFu['idestado'] != 3) {
					// elimino
					$resEliminar = $this->eliminarConectordelegadosPorCountrie($idequipo, $rowFu['idcountrie']);
				}
			}
		}

		return $res;
	}


	function generarPlantelTemporadaAnteriorExcepciones($idtemporada, $idcountrie, $idequipo) {
		$sql = "SELECT
					    '',
					    ".$idtemporada." as reftemporadas,
					    '' as refusuarios,
					c.refjugadores,
					c.reftipojugadores,
					c.refequipos,
					c.refcountries,
					c.refcategorias,
					c.esfusion,
					c.activo,
					1 as refestados,
					0 as habilitacionpendiente,
					0 as refjugadorespre,
					jug.nrodocumento,
					c.idconector,
					concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
					year(now()) - year(jug.fechanacimiento) as edad
					FROM
					    dbconector c
					inner join dbjugadores jug ON jug.idjugador = c.refjugadores
					left join dbconectordelegados cc
					ON c.refjugadores = cc.refjugadores and c.refequipos = cc.refequipos
		where	c.refequipos = ".$idequipo." and c.refcountries = ".$idcountrie."
				and c.activo = 1 and cc.idconector is null";
		//die(var_dump($sql));
		$res = $this->query($sql,0);

		$habilitacionpendiente = 0;

		$ar = array();

		while ($row = mysql_fetch_array($res)) {

			$vEdad = $this->verificaEdadCategoriaJugador($row['refjugadores'], $row['refcategorias'], $row['reftipojugadores']);

			$vEdadMenor = $this->verificaEdadCategoriaJugadorMenor($row['refjugadores'], $row['refcategorias'], $row['reftipojugadores']);

			if (($vEdad == 0) && ($vEdadMenor == 1)) {
				array_push($ar, array('refjugadores' => $row['refjugadores'], 'reftipojugadores' => $row['reftipojugadores'], 'nrodocumento' => $row['nrodocumento'], 'nombrecompleto' => $row['nombrecompleto'], 'edad' => $row['edad'], 'idconector' => $row['idconector']));
			}
		}
		return $ar;
	}



		function generarPlantelTemporadaAnteriorExcepcionesTodos($idtemporada, $idcountrie, $idequipo) {
			$sql = "SELECT
						    '',
						    ".$idtemporada." as reftemporadas,
						    '' as refusuarios,
						c.refjugadores,
						c.reftipojugadores,
						c.refequipos,
						c.refcountries,
						c.refcategorias,
						c.esfusion,
						c.activo,
						1 as refestados,
						0 as habilitacionpendiente,
						0 as refjugadorespre,
						jug.nrodocumento,
						c.idconector,
						concat(jug.apellido,', ',jug.nombres) as nombrecompleto,
						year(now()) - year(jug.fechanacimiento) as edad
						FROM
						    dbconector c
						inner join dbjugadores jug ON jug.idjugador = c.refjugadores

			where	c.refequipos = ".$idequipo." and c.refcountries = ".$idcountrie."
					and c.activo = 1";
			//die(var_dump($sql));
			$res = $this->query($sql,0);

			$habilitacionpendiente = 0;

			$ar = array();

			while ($row = mysql_fetch_array($res)) {

				$vEdad = $this->verificaEdadCategoriaJugador($row['refjugadores'], $row['refcategorias'], $row['reftipojugadores']);

				$vEdadMenor = $this->verificaEdadCategoriaJugadorMenor($row['refjugadores'], $row['refcategorias'], $row['reftipojugadores']);

				if (($vEdad == 0) && ($vEdadMenor == 1)) {
					array_push($ar, array('refjugadores' => $row['refjugadores'], 'reftipojugadores' => $row['reftipojugadores'], 'nrodocumento' => $row['nrodocumento'], 'nombrecompleto' => $row['nombrecompleto'], 'edad' => $row['edad'], 'idconector' => $row['idconector']));
				}
			}
			return $ar;
		}


	function traerJugadoresPorCountriesBaja($idCountries) {
	    $sql = "select
	            j.nrodocumento,
	            concat(j.apellido,', ',j.nombres) as apyn,
	            j.email,
	            j.fechanacimiento,
	            j.observaciones,
	            j.fechabaja
	            from        dbjugadores j
	            inner
	            join        dbcountries cc
	            on          cc.idcountrie = j.refcountries
	            where       cc.idcountrie in (".$idCountries.") and (j.fechabaja <> '1900-01-01' and j.fechabaja <> '0000-00-00')
	            order by concat(j.apellido,', ',j.nombres)";
	    $res = $this->query($sql,0);
	    return $res;
	}


	function traerReferente($idcountrie) {
		$sql = "select
					coalesce(u.email,'') as email
				from        dbcountries c
				left
				join        dbusuarios u
				on          u.idusuario = c.refusuarios
		where       c.idcountrie = ".$idcountrie;
		$res = $this->query($sql,0);

		if (mysql_num_rows($res)>0) {
			return mysql_result($res,0,0);
		}
		return 'aif@intercountryfutbol.com.ar';
	}

	function traerReferentePorNrodocumento($nrodocumento) {
		$sql = "select
						coalesce(u.email,'') as email
					from        dbjugadorespre j
					left
					join        dbcountries c
					on          j.refcountries = c.idcountrie
					left
					join        dbusuarios u
					on          u.idusuario = c.refusuarios
				where       j.nrodocumento = ".$nrodocumento;

		$res = $this->query($sql,0);

		if (mysql_num_rows($res)>0) {
			return mysql_result($res,0,0);
		}

		return 'aif@intercountryfutbol.com.ar';
	}



	function traerEncargadoPorCountries($idcountrie) {
		$sql = "select email, idusuario from dbusuarios where refcountries = ".$idcountrie;
		$resUsuario = $this->query($sql,0);

		$email = mysql_result($resUsuario,0,0);
		$idusuario = mysql_result($resUsuario,0,1);

		$sqlDelegados = "select email1,email2,email3,email4 from dbdelegados where refusuarios = ".$idusuario;
		$resDelegado = $this->query($sqlDelegados,0);

		$email1 = '';
		$email2 = '';
		$email3 = '';
		$email4 = '';

		if (mysql_num_rows($resDelegado) > 0) {
			// empiezo a enviar emails a los que esten agregados
			if (mysql_result($resDelegado,0,'email1') != '') {
				$email1 = mysql_result($resDelegado,0,'email1');
			}
			if (mysql_result($resDelegado,0,'email2') != '') {
				$email2 = mysql_result($resDelegado,0,'email2');
			}
			if (mysql_result($resDelegado,0,'email3') != '') {
				$email3 = mysql_result($resDelegado,0,'email3');
			}
			if (mysql_result($resDelegado,0,'email4') != '') {
				$email4 = mysql_result($resDelegado,0,'email4');
			}
		}

		$arEncargado = array('idusuario'=> $idusuario, 'email' => $email,'email1' => $email1,'email2' => $email2,'email3' => $email3,'email4' => $email4);

		return $arEncargado;

	}


	function traerDefinicionesPorTemporadaCategoria($idTemporada, $idCategoria) {
	    $sql = "select
	                max(dct.cantmaxjugadores) as cantmaxjugadores, max(dctj.edadmaxima) as edadmaxima, max(dctj.edadminima) as edadminima, max((dctj.edadmaxima + dctj.edadminima) /2) as promedio
	            from        dbdefinicionescategoriastemporadas dct
	            inner
	            join        dbdefinicionescategoriastemporadastipojugador dctj
	            on          dct.iddefinicioncategoriatemporada = dctj.refdefinicionescategoriastemporadas
	            where       dct.reftemporadas = ".$idTemporada." and refcategorias = ".$idCategoria;
	    $res = $this->query($sql,0);
	    return $res;
	}


	function traerConectorActivosPorEquipos($refEquipos) {
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
		    dbconector c
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
		order by concat(jug.apellido,', ',jug.nombres)";

		$res = $this->query($sql,0);
		return $res;
	}


	function traerConectorActivosPorEquiposEdades($refEquipos) {
		$sql = "select
		    min(year(now()) - year(jug.fechanacimiento)) as edadMinima,
		    max(year(now()) - year(jug.fechanacimiento)) as edadMaxima,
		    count(*) as cantidadJugadores,
		    round((max(year(now()) - year(jug.fechanacimiento)) + min(year(now()) - year(jug.fechanacimiento)))/2,2) as edadPromedio
		from
		    dbconector c
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
		    where equ.idequipo = ".$refEquipos." and c.activo = 1";
		$res = $this->query($sql,0);
		return $res;
	}


	function verificarEdadAnioManual($refjugador, $anio) {
	    $sql = "select DATE_FORMAT(fechanacimiento, '%Y') as fechanacimiento from dbjugadores where idjugador =".$refjugador;
	    $res = $this->query($sql,0);

	    $fechactual = $anio;
	    $edadJuagador = mysql_result($res,0,'fechanacimiento');

	    $edad = $fechactual - $edadJuagador;

	    return $edad;
	}


	function verificaEdadCategoriaJugadorAnioManual($refjugador, $refcategoria, $tipoJugador, $anio) {
	    //## falta chocar contra una temporada
	    $edad = $this->verificarEdadAnioManual($refjugador, $anio);

	    $sql = "SELECT
	                count(*) as verificado
	            FROM
	                dbdefinicionescategoriastemporadastipojugador dc
	                    INNER JOIN
	                (SELECT
	                    iddefinicioncategoriatemporada
	                FROM
	                    dbdefinicionescategoriastemporadas ct
	                WHERE
	                    ct.refcategorias = ".$refcategoria."
	                ORDER BY iddefinicioncategoriatemporada DESC
	                LIMIT 1) c
	                on c.iddefinicioncategoriatemporada = dc.refdefinicionescategoriastemporadas
	                where dc.reftipojugadores = ".$tipoJugador." and ".$edad." between dc.edadminima and dc.edadmaxima";
	    $res = $this->query($sql,0);

	    return mysql_result($res,0,0);
	}


	function traerJugadoresdocumentacionPorJugadorValores($idJugador) {
	$sql = "select
	            r.refdocumentaciones,
	            r.descripcion,
	            r.obligatoria,
	            (case when r.valor = 1 then 'Si' else 'No' end) as valor,
	            (case when coalesce(r.contravalor,0) = 1 then 'Si' else 'No' end) as contravalor,
	            r.refjugadores,
	            r.idjugadordocumentacion,
	            r.observaciones,
	            coalesce(r.contravalordesc,'') as contravalordesc
	            from
	            (
	            SELECT
	                j.refdocumentaciones,
	                doc.descripcion,
	                (CASE
	                    WHEN doc.obligatoria = 1 THEN 'Si'
	                    ELSE 'No'
	                END) AS obligatoria,
	                j.valor,
	                (SELECT
	                        v.habilita
	                    FROM
	                        tbvaloreshabilitacionestransitorias v
	                    inner join dbjugadoresvaloreshabilitacionestransitorias vh
	                    on v.idvalorhabilitaciontransitoria = vh.refvaloreshabilitacionestransitorias
	                    WHERE
	                        refdocumentaciones = doc.iddocumentacion and vh.refjugadores = jug.idjugador) AS contravalor,
	                (SELECT
	                        v.descripcion
	                    FROM
	                        tbvaloreshabilitacionestransitorias v
	                    inner join dbjugadoresvaloreshabilitacionestransitorias vh
	                    on v.idvalorhabilitaciontransitoria = vh.refvaloreshabilitacionestransitorias
	                    WHERE
	                        refdocumentaciones = doc.iddocumentacion and vh.refjugadores = jug.idjugador) AS contravalordesc,
	                j.refjugadores,
	                j.idjugadordocumentacion,
	                j.observaciones
	            FROM
	                dbjugadoresdocumentacion j
	                    INNER JOIN
	                dbjugadores jug ON jug.idjugador = j.refjugadores
	                    INNER JOIN
	                tbdocumentaciones doc ON doc.iddocumentacion = j.refdocumentaciones
	            WHERE
	                j.refjugadores = ".$idJugador."
	                ) as r";
	$res = $this->query($sql,0);
	return $res;
	}



	function traerJugadoresmotivoshabilitacionestransitoriasPorJugadorDeportiva($idJugador, $reftemporada, $refcategoria, $refequipos) {
	$sql = "select
	j.iddbjugadormotivohabilitaciontransitoria,
	tem.temporada,
	doc.descripcion as documentacion,
	mot.descripcion as motivos,
	equ.nombre as equipo,
	cat.categoria,
	j.reftemporadas,
	j.refjugadores,
	j.refdocumentaciones,
	j.refmotivoshabilitacionestransitorias,
	j.refequipos,
	j.refcategorias,
	j.fechalimite,
	j.observaciones
	from dbjugadoresmotivoshabilitacionestransitorias j
	inner join tbtemporadas tem ON tem.idtemporadas = j.reftemporadas
	inner join dbjugadores jug ON jug.idjugador = j.refjugadores
	inner join tbdocumentaciones doc ON doc.iddocumentacion = j.refdocumentaciones
	inner join tbmotivoshabilitacionestransitorias mot ON mot.idmotivoshabilitacionestransitoria = j.refmotivoshabilitacionestransitorias
	inner join dbequipos equ ON equ.idequipo = j.refequipos
	inner join tbcategorias cat ON cat.idtcategoria = j.refcategorias
	where j.refjugadores = ".$idJugador." and mot.descripcion = 'Edad'
	      and j.reftemporadas = ".$reftemporada."
	      and j.refequipos = ".$refequipos."
	      and j.refcategorias = ".$refcategoria."
	      and (now() < j.fechalimite or j.fechalimite is null)
	order by 1";
	$res = $this->query($sql,0);
	return $res;
	}


	function traerJugadoresmotivoshabilitacionestransitoriasPorJugadorAdministrativaDocumentacion($idJugador, $idDocumentacion) {

		$resTemporadas = $this->traerUltimaTemporada();

		if (mysql_num_rows($resTemporadas)>0) {
		    $ultimaTemporada = mysql_result($resTemporadas,0,0);
		} else {
		    $ultimaTemporada = 0;
		}

		$sql = "select
		j.iddbjugadormotivohabilitaciontransitoria,
		tem.temporada,
		doc.descripcion as documentacion,
		mot.descripcion as motivos,
		equ.nombre as equipo,
		cat.categoria,
		j.reftemporadas,
		j.refjugadores,
		j.refdocumentaciones,
		j.refmotivoshabilitacionestransitorias,
		j.refequipos,
		j.refcategorias,
		j.fechalimite,
		j.observaciones
		from dbjugadoresmotivoshabilitacionestransitorias j
		inner join tbtemporadas tem ON tem.idtemporadas = j.reftemporadas
		inner join dbjugadores jug ON jug.idjugador = j.refjugadores
		inner join tbdocumentaciones doc ON doc.iddocumentacion = j.refdocumentaciones
		inner join tbmotivoshabilitacionestransitorias mot ON mot.idmotivoshabilitacionestransitoria = j.refmotivoshabilitacionestransitorias
		left join dbequipos equ ON equ.idequipo = j.refequipos
		left join tbcategorias cat ON cat.idtcategoria = j.refcategorias
		where j.refjugadores = ".$idJugador." and doc.descripcion <> 'Edad' and doc.iddocumentacion = ".$idDocumentacion." and tem.idtemporadas = ".$ultimaTemporada."
		order by 1";

		$res = $this->query($sql,0);
		return $res;
	}


	function traerEquiposPorCountriesActivosInactivos($idCountrie, $baja) {
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
		where cou.idcountrie = ".$idCountrie." and e.activo = ".$baja."
		order by 1";

		$res = $this->query($sql,0);
		return $res;
	}

	function traerEquiposPorEquipo($idEquipo) {
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
		where e.idequipo =".$idEquipo."
		order by 1";

		$res = $this->query($sql,0);
		return $res;
	}

	function traerTemporadasPorId($id) {
		$sql = "select idtemporadas,temporada from tbtemporadas where idtemporadas =".$id;
		$res = $this->query($sql,0);
		return $res;
	}

	function existeConectorJugadorEquipo($reftemporadas, $refJugador, $refEquipo) {
	    $sql = "select idconector from dbconectordelegados where refjugadores =".$refJugador." and refequipos = ".$refEquipo." and activo = 1 and reftemporadas = ".$reftemporadas;
	    $res = $this->query($sql,0);

	    if (mysql_num_rows($res)>0) {
	        return 1;
	    }
	    return 0;
	}

	function existeConectorJugadorEquipoNuevo($reftemporadas, $refJugador, $refEquipo) {
	    $sql = "select idconector from dbconectordelegados where refjugadorespre =".$refJugador." and refequipos = ".$refEquipo." and activo = 1 and reftemporadas = ".$reftemporadas;
	    $res = $this->query($sql,0);

	    if (mysql_num_rows($res)>0) {
	        return 1;
	    }
	    return 0;
	}

	/****** VERIFICO LA EDAD ******/////
	function verificarEdad($refjugador) {
	    $sql = "select DATE_FORMAT(fechanacimiento, '%Y') as fechanacimiento from dbjugadores where idjugador =".$refjugador;
	    $res = $this->query($sql,0);

	    $fechactual = date('Y');
	    $edadJuagador = mysql_result($res,0,'fechanacimiento');

	    $edad = $fechactual - $edadJuagador;

	    return $edad;
	}

	/****** VERIFICO LA EDAD ******/////
	function verificarEdadNuevo($refjugador) {
	    $sql = "select DATE_FORMAT(fechanacimiento, '%Y') as fechanacimiento from dbjugadorespre where idjugadorpre =".$refjugador;
	    $res = $this->query($sql,0);

	    $fechactual = date('Y');
	    $edadJuagador = mysql_result($res,0,'fechanacimiento');

	    $edad = $fechactual - $edadJuagador;

	    return $edad;
	}

	/******   COMPRUEBO SI PUEDO JUGAR EN ESA CATEGORIA Y TIPO DE JUGADOR, POR LA EDAD     *************/
	function verificaEdadCategoriaJugador($refjugador, $refcategoria, $tipoJugador) {
	    //## falta chocar contra una temporada
	    $edad = $this->verificarEdad($refjugador);

	    $sql = "SELECT
	                count(*) as verificado
	            FROM
	                dbdefinicionescategoriastemporadastipojugador dc
	                    INNER JOIN
	                (SELECT
	                    iddefinicioncategoriatemporada
	                FROM
	                    dbdefinicionescategoriastemporadas ct
	                WHERE
	                    ct.refcategorias = ".$refcategoria."
	                ORDER BY iddefinicioncategoriatemporada DESC
	                LIMIT 1) c
	                on c.iddefinicioncategoriatemporada = dc.refdefinicionescategoriastemporadas
	                where dc.reftipojugadores = ".$tipoJugador." and ".$edad." between dc.edadminima and dc.edadmaxima";
	    $res = $this->query($sql,0);

	    return mysql_result($res,0,0);
	}

	/******   COMPRUEBO SI PUEDO JUGAR EN ESA CATEGORIA Y TIPO DE JUGADOR, POR LA EDAD (Nuevo)     *************/
	function verificaEdadCategoriaJugadorNuevo($refjugador, $refcategoria, $tipoJugador) {
	    //## falta chocar contra una temporada
	    $edad = $this->verificarEdadNuevo($refjugador);

	    $sql = "SELECT
	                count(*) as verificado
	            FROM
	                dbdefinicionescategoriastemporadastipojugador dc
	                    INNER JOIN
	                (SELECT
	                    iddefinicioncategoriatemporada
	                FROM
	                    dbdefinicionescategoriastemporadas ct
	                WHERE
	                    ct.refcategorias = ".$refcategoria."
	                ORDER BY iddefinicioncategoriatemporada DESC
	                LIMIT 1) c
	                on c.iddefinicioncategoriatemporada = dc.refdefinicionescategoriastemporadas
	                where dc.reftipojugadores = ".$tipoJugador." and ".$edad." between dc.edadminima and dc.edadmaxima";
	    $res = $this->query($sql,0);

	    return mysql_result($res,0,0);
	}

	/******   COMPRUEBO SI PUEDO JUGAR EN ESA CATEGORIA Y TIPO DE JUGADOR, POR LA EDAD     *************/
	function verificaEdadCategoriaJugadorMenor($refjugador, $refcategoria, $tipoJugador) {
	    //## falta chocar contra una temporada
	    $edad = $this->verificarEdad($refjugador);

	    $sql = "SELECT
	                count(*) as verificado
	            FROM
	                dbdefinicionescategoriastemporadastipojugador dc
	                    INNER JOIN
	                (SELECT
	                    iddefinicioncategoriatemporada
	                FROM
	                    dbdefinicionescategoriastemporadas ct
	                WHERE
	                    ct.refcategorias = ".$refcategoria."
	                ORDER BY iddefinicioncategoriatemporada DESC
	                LIMIT 1) c
	                on c.iddefinicioncategoriatemporada = dc.refdefinicionescategoriastemporadas
	                where dc.reftipojugadores = ".$tipoJugador." and ".$edad." < dc.edadminima";
	    $res = $this->query($sql,0);

	    return mysql_result($res,0,0);
	}

	function traerTipojugadores() {
		$sql = "select
		t.idtipojugador,
		t.tipojugador,
		t.abreviatura
		from tbtipojugadores t
		order by 1";
		$res = $this->query($sql,0);
		return $res;
	}


	function traerJugadoresPorCountries($lstCountries) {
	    $sql = "select
	    		j.idjugador,
	            j.nrodocumento,
	            concat(j.apellido,', ',j.nombres) as apyn,
	            j.apellido,
	            j.nombres,
	            j.email,
	            j.fechanacimiento,
	            j.observaciones
	            from        dbjugadores j
	            inner
	            join        dbcountries cc
	            on          cc.idcountrie = j.refcountries
	            where       cc.idcountrie in (".$lstCountries.") and (j.fechabaja = '1900-01-01' or j.fechabaja = '0000-00-00' or j.fechabaja is null)
	            order by concat(j.apellido,', ',j.nombres)";
	    $res = $this->query($sql,0);
	    return $res;
	}

	function traerJugadoresPorId($id) {
		$sql = "select idjugador,reftipodocumentos,nrodocumento,apellido,nombres,email,fechanacimiento,fechaalta,fechabaja,refcountries,observaciones from dbjugadores where idjugador =".$id;

		$res = $this->query($sql,0);
		return $res;
	}

	function traerDocumentacionesPorId($id) {
		$sql = "select iddocumentacion,descripcion, (case when obligatoria = 1 then 'Si' else 'No' end) as obligatoria,observaciones from tbdocumentaciones where iddocumentacion =".$id;

		$res = $this->query($sql,0);
		return $res;
	}

	function cambiarEstadoTareas($idtarea=0, $refestado, $idpadre=0, $tablaMadre='') {
		if ($idpadre != 0) {
			switch ($tablaMadre) {
				case 'dbfusionequipos':
						$sql = "update dbtareas set refestados = ".$refestado." where id1 = ".$idpadre;
					break;

				default:
					# code...
					break;
			}
		} else {
			$sql = "update dbtareas set refestados = ".$refestado." where idtarea = ".$idtarea;
		}

		$res = $this->query($sql,0);
		return $res;
	}

	function cambiarEstadoFusion($id, $refestados) {
		$sql = "update dbfusionequipos
					set refestados = ".$refestados."
				where idfusionequipo = ".$id;

		$res = $this->query($sql,0);
		return $res;
	}


	function insertarFusionEquipos($refequipos, $refcountries, $refestados, $observacion, $viejo = 0) {
		$sql = "INSERT INTO dbfusionequipos
				(idfusionequipo,
				refequiposdelegados,
				refcountries,
				refestados,
				observacion,
				viejo)
				VALUES
				('',
				".$refequipos.",
				".$refcountries.",
				".$refestados.",
				'".$observacion."',
				".$viejo.")";

		$res = $this->query($sql,1);
		return $res;
	}

	function eliminarFusionEquipos($idfusionequipo) {
		$sql = "delete from dbfusionequipos where idfusionequipo = ".$idfusionequipo;

		$res = $this->query($sql,0);
		return $res;
	}

	function eliminarFusionEquiposPorEquipo($refequiposdelegados) {
		$sql = "delete from dbfusionequipos where refequiposdelegados = ".$refequiposdelegados;

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

function insertarConectordelegados($reftemporadas,$refusuarios,$refjugadores,$reftipojugadores,$refequipos,$refcountries,$refcategorias,$esfusion,$activo,$refestados,$habilitacionpendiente, $refjugadorespre) {
	$sql = "insert into dbconectordelegados(idconector,reftemporadas,refusuarios,refjugadores,reftipojugadores,refequipos,refcountries,refcategorias,esfusion,activo,refestados,habilitacionpendiente,refjugadorespre)
	values ('',".$reftemporadas.",'".$refusuarios."',".$refjugadores.",".$reftipojugadores.",".$refequipos.",".$refcountries.",".$refcategorias.",".$esfusion.",".$activo.",".$refestados.",".$habilitacionpendiente.",".$refjugadorespre.")";
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

	function eliminarConectordelegadosPorCountrie($refequipos, $refcountries) {
	$sql = "delete from dbconectordelegados where refequipos = ".$refequipos." and refcountries = ".$refcountries;
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

function traerUltimaDivisionPorTemporadaCategoria($idtemporada, $idcategoria) {
	$sql = "select v.iddivision, v.division
                from tbdivisiones v
                inner join dbtorneos t on t.refdivisiones = v.iddivision
                where t.reftemporadas = ".$idtemporada." and t.refcategorias = ".$idcategoria."
					 group by v.iddivision, v.division
					 order by v.iddivision desc
					 limit 1";
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
		$res = $this->query($sql,1);
		return $res;
	}


	function modificarEquiposdelegados($id,$reftemporadas,$refusuarios,$refcountries,$nombre,$refcategorias,$refdivisiones,$fechabaja,$activo,$refestados) {
		$sql = "update dbequiposdelegados
		set
		reftemporadas = ".$reftemporadas.",refusuarios = ".$refusuarios.",refcountries = ".$refcountries.",nombre = '".utf8_decode($nombre)."',refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones.",fechabaja = '".utf8_decode($fechabaja)."',activo = ".$activo.",refestados = ".$refestados."
		where idequipo =".$id;
		$res = $this->query($sql,0);
		return $res;
	}

	function modificarEquiposdelegadosEstado($id, $refestados) {
		$sql = "update dbequiposdelegados
		set
			refestados = ".$refestados."
		where idequipo =".$id;
		$res = $this->query($sql,0);
		return $res;
	}


	function eliminarEquiposdelegados($id) {
		$this->eliminarFusionEquiposPorEquipo($id);

		$sql = "delete from dbequiposdelegados where idequipodelegado =".$id;

		$res = $this->query($sql,0);
		return $res;
	}

	function traerEstadosFusionesAceptadasPorCountrie($idcountrie) {

		$sql = "select
					coalesce(min(fe.refestados),1) as idestado
				from dbfusionequipos fe
				inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
				where ed.refcountries = ".$idcountrie;

		$res = $this->existeDevuelveId($sql);
		return $res;

	}


	function traerEstadosFusionesAceptadasPorEquipo($idequiposdelegados ,$idcountrie) {

		$sql = "select
					coalesce(min(fe.refestados),1) as idestado
				from dbfusionequipos fe
				inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
				where fe.refequiposdelegados = ".$idequiposdelegados." and ed.refcountries = ".$idcountrie;

		$res = $this->existeDevuelveId($sql);
		return $res;

	}

	function traerEstadosFusionesPorEquipo($idequiposdelegados, $idcountrie) {
		$sql = "select
					(case when coalesce(min(fe.refestados),1) = 3 then 3 else 0 end) as idestado

				from dbfusionequipos fe
				inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
				where fe.refequiposdelegados = ".$idequiposdelegados." and ed.refcountries = ".$idcountrie;

		$res = $this->existeDevuelveId($sql);
		return $res;
	}


	function traerFusionesPorEquipo($idequiposdelegados ,$idcountrie) {
		//$idcountrie = 63;
		$sql = "select
					fe.idfusionequipo,
					cp.nombre as countriepadre,
					cat.categoria,
					di.division,
					ed.nombre,
					est.estado,
					est.idestado
				from dbfusionequipos fe
				inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
				inner join dbcountries cp on cp.idcountrie = ed.refcountries
				inner join tbcategorias cat on cat.idtcategoria = ed.refcategorias
				inner join tbdivisiones di on di.iddivision = ed.refdivisiones
				inner join tbestados est on est.idestado = fe.refestados
				where fe.idfusionequipo = ".$idequiposdelegados." and fe.refcountries = ".$idcountrie;

		$res = $this->query($sql,0);
		return $res;

	}

	function traerFusionesPorCoutriePadre($idcountrie, $idtemporada) {

		$sql = "select
					fe.idfusionequipo,
					cp.nombre as countriepadre,
					cat.categoria,
					di.division,
					ed.nombre,
					est.estado,
					est.idestado,
                    fe.refcountries
				from dbfusionequipos fe
				inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
				inner join dbcountries cp on cp.idcountrie = ed.refcountries
				inner join tbcategorias cat on cat.idtcategoria = ed.refcategorias
				inner join tbdivisiones di on di.iddivision = ed.refdivisiones
				inner join tbestados est on est.idestado = fe.refestados
				where cp.idcountrie = ".$idcountrie." and ed.reftemporadas = ".$idtemporada;

		$res = $this->query($sql,0);
		return $res;

	}


	function traerEquiposdelegadosPorCountrie($id, $idtemporada, $nuevo) {
		$sql = "select
		e.idequipodelegado,
		e.idequipo,
		cou.nombre as countrie,
		e.nombre,
		cat.categoria,
		di.division,
		e.fechabaja,
		(case when e.activo=1 then 'Si' else 'No' end) as activo,
		est.estado,
		e.refestados,
		coalesce(max(fe.refequiposdelegados),0) as esfusion
		from dbequiposdelegados e
		inner join dbcountries cou ON cou.idcountrie = e.refcountries
		inner join tbcategorias cat ON cat.idtcategoria = e.refcategorias
		inner join tbdivisiones di ON di.iddivision = e.refdivisiones
		inner join tbestados est ON est.idestado = e.refestados
		left join dbfusionequipos fe ON fe.refequiposdelegados = e.idequipodelegado
		where e.activo = 1 and e.nuevo = ".$nuevo." and cou.idcountrie = ".$id." and e.reftemporadas = ".$idtemporada."
		group by e.idequipodelegado,
		e.idequipo,
		cou.nombre,
		e.nombre,
		cat.categoria,
		di.division,
		e.fechabaja,
		e.activo,
		est.estado,
		e.refestados
		order by e.nombre";

		$res = $this->query($sql,0);

		return $res;
	}


	function traerEquiposdelegadosPorCountrieFinalizado($id, $idtemporada) {
		$sql = "SELECT
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
						WHEN est.idestado = 7 THEN 'label-success'
						WHEN est.idestado = 5 THEN 'label-warning'
						WHEN est.idestado = 6 THEN 'label-info'
						WHEN est.idestado = 8 THEN 'label-warning'
					END) AS label,
					est.idestado as refestados,
                    coalesce(max(fe.refcountries),0) as esfusion,
                    e.idequipodelegado,
                    (select
					(case when coalesce(min(fe.refestados),1) = 3 then 3 else 0 end) as idestado
					from dbfusionequipos fe
					inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
					where fe.refequiposdelegados = e.idequipodelegado and ed.refcountries = ".$id.") as fusion,
					(CASE
						WHEN e.nuevo = 1 THEN 'Si'
						ELSE 'No'
					END) AS nuevo,
					e.idequipodelegado
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
						left JOIN
					dbfusionequipos fe ON fe.refequiposdelegados = e.idequipodelegado
				WHERE
					(e.nuevo = 1 or (e.activo = 1 and e.nuevo = 0))  AND cou.idcountrie = ".$id."
			AND e.reftemporadas = ".$idtemporada."
            group by e.idequipo,
					cou.nombre,
					e.nombre,
					cat.categoria,
					di.division,
					e.fechabaja,
					e.activo,
					est.estado,
					cat.orden,
					e.refdivisiones,
					est.idestado,
					est.idestado,
					e.idequipodelegado
			order by 8,9";

		$res = $this->query($sql,0);

		return $res;
	}


	function traerEquiposdelegadosPorCountrieFinalizadoBaja($id, $idtemporada) {
		$sql = "SELECT
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
                    coalesce(max(fe.refcountries),0) as esfusion,
                    e.idequipodelegado,
                    (select
					(case when coalesce(min(fe.refestados),1) = 3 then 3 else 0 end) as idestado
					from dbfusionequipos fe
					inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
					where fe.refequiposdelegados = e.idequipodelegado and ed.refcountries = ".$id.") as fusion,
					(CASE
						WHEN e.nuevo = 1 THEN 'Si'
						ELSE 'No'
					END) AS nuevo
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
						left JOIN
					dbfusionequipos fe ON fe.refequiposdelegados = e.idequipodelegado
				WHERE
					e.activo = 0 AND cou.idcountrie = ".$id."
			AND e.reftemporadas = ".$idtemporada."
            group by e.idequipo,
					cou.nombre,
					e.nombre,
					cat.categoria,
					di.division,
					e.fechabaja,
					e.activo,
					est.estado,
					cat.orden,
					e.refdivisiones,
					est.idestado,
					est.idestado
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

	function traerEquiposFusionPorEquipoDelegado($idequipodelegado) {
		$sql = "SELECT
					cc.nombre, est.estado, (case when f.viejo = 1 then 'Mantiene' else 'Nuevo' end) as viejo
				FROM
					dbfusionequipos f
						INNER JOIN
					dbcountries cc ON cc.idcountrie = f.refcountries
						INNER JOIN
					tbestados est on est.idestado = f.refestados
					where f.refequiposdelegados = ".$idequipodelegado;

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
					ee.refcategorias,
					(select
					(case when coalesce(min(fe.refestados),1) = 3 then 3 else 0 end) as idestado
					from dbfusionequipos fe
					inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
					where fe.refequiposdelegados = e.idequipodelegado and ed.refcountries = ".$id.") as fusion
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
					e.refcategorias,
					(select
					(case when coalesce(min(fe.refestados),1) = 3 then 3 else 0 end) as idestado
					from dbfusionequipos fe
					inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
					where fe.refequiposdelegados = e.idequipodelegado and ed.refcountries = ".$id.") as fusion
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
					refestados,
					nuevo)
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
			1,
			0
		from dbequipos where idequipo = ".$id;

		$res = $this->query($sql,0);

		return $res;

	}


	function mantenerEquipoPasivo($id, $idtemporada, $idusuario, $idcountrie) {

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
					refestados,
					nuevo)
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
			1,
			8,
			0
		from dbequipos where idequipo = ".$id;

		$res = $this->query($sql,1);

		if ($res > 0) {
			$resFusion = $this->traerFusionPorEquiposCountrie($id, $idcountrie);
			while ($row = mysql_fetch_array($resFusion)) {
				$this->insertarFusionEquipos($res, $row['idcountrie'], 1, '',1);
			}
		}

		return $res;

	}


	function traerEquiposdelegadosPorId($id) {
		$sql = "select idequipodelegado,idequipo,reftemporadas,refusuarios,refcountries,nombre,refcategorias,refdivisiones,fechabaja,activo,refestados from dbequiposdelegados where idequipodelegado =".$id;
		$res = $this->query($sql,0);
		return $res;
	}

	function traerEquiposdelegadosPorEquipoTemporada($idequipo, $reftemporada) {
		$sql = "select ed.idequipodelegado,ed.idequipo,ed.reftemporadas,ed.refusuarios,
		ed.refcountries,ed.nombre,ed.refcategorias,ed.refdivisiones,
		ed.fechabaja,ed.activo,ed.refestados,
		c.categoria,
		d.division
		from dbequiposdelegados ed
		inner join tbcategorias c ON c.idtcategoria = ed.refcategorias
		inner join tbdivisiones d ON d.iddivision = ed.refdivisiones
		where ed.idequipo =".$idequipo." and ed.reftemporadas = ".$reftemporada;
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


	function traerConectorActivosPorEquiposDelegado($refEquipos, $reftemporadas, $refusuarios='') {
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
		jug.fechaalta,
		(case when c.habilitacionpendiente = 1 then 'Si' else 'No' end) as habilitacionpendiente

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
		dbequiposdelegados equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where (equ.idequipo = ".$refEquipos." and c.activo = 1 and c.reftemporadas = ".$reftemporadas.")

	order by concat(jug.apellido,', ',jug.nombres)";
	$res = $this->query($sql,0);
	//die(var_dump($sql));
	return $res;
	}



		function traerConectorActivosPorEquiposDelegadoSinExcepcion($refEquipos, $reftemporadas, $refusuarios='') {
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
			jug.fechaalta,
			(case when c.habilitacionpendiente = 1 then 'Si' else 'No' end) as habilitacionpendiente

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
			dbequiposdelegados equ ON equ.idequipo = c.refequipos
				inner join
			tbdivisiones di ON di.iddivision = equ.refdivisiones
				inner join
			tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
				inner join
			tbcategorias cat ON cat.idtcategoria = c.refcategorias
			where (equ.idequipo = ".$refEquipos." and c.activo = 1 and c.reftemporadas = ".$reftemporadas.")
					and c.habilitacionpendiente = 0
		order by concat(jug.apellido,', ',jug.nombres)";
		$res = $this->query($sql,0);
		//die(var_dump($sql));
		return $res;
	}


	function traerConectorActivosPorEquiposDelegadoNuevo($refEquipos, $reftemporadas, $refusuarios='') {
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
		jug.fechaalta,
		(case when c.habilitacionpendiente = 1 then 'Si' else 'No' end) as habilitacionpendiente

	from
	dbconectordelegados c
			inner join
		dbjugadorespre jug ON jug.idjugadorpre = c.refjugadorespre
			inner join
		tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
			inner join
		dbcountries co ON co.idcountrie = jug.refcountries
			inner join
		tbtipojugadores tip ON tip.idtipojugador = c.reftipojugadores
			inner join
		dbequiposdelegados equ ON equ.idequipo = c.refequipos
			inner join
		tbdivisiones di ON di.iddivision = equ.refdivisiones
			inner join
		tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
			inner join
		tbcategorias cat ON cat.idtcategoria = c.refcategorias
		where (equ.idequipo = ".$refEquipos." and c.activo = 1 and c.reftemporadas = ".$reftemporadas.")

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
		$sql = "select idcierrepadron,refcountries,refusuarios,fechacierre, current_date() - fechacierre as resto, current_date() - cast('2019-01-01' as date) as dias  from tbcierrepadrones where refcountries =".$idcountry;
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

	function traerTipodocumentosPorId($id) {
		$sql = "select
		t.idtipodocumento,
		t.tipodocumento
		from tbtipodocumentos t
		where t.idtipodocumento = ".$id."
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


	function traerJugadoresPorNroDocumento($nrodocumento) {
		$sql = "select
					j.idjugador,
					tip.tipodocumento,
					j.nrodocumento,
					j.apellido,
					j.nombres,
					j.email,
					j.fechanacimiento,
					j.fechaalta,
					j.fechabaja,
					cou.nombre as country,
					j.observaciones,
					j.reftipodocumentos,
					j.refcountries
			from dbjugadores j
			inner join tbtipodocumentos tip ON tip.idtipodocumento = j.reftipodocumentos
			inner join dbcountries cou ON cou.idcountrie = j.refcountries
			inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria
			where j.nrodocumento = ".$nrodocumento."
		order by 1";

		$res = $this->query($sql,0);
		return $res;
	}

	function traerJugadoresPorEmail($email) {
		$sql = "select
					j.idjugador,
					tip.tipodocumento,
					j.nrodocumento,
					j.apellido,
					j.nombres,
					j.email,
					j.fechanacimiento,
					j.fechaalta,
					(case when j.fechabaja = '1900-01-01' then ''
							when j.fechabaja = '0000-00-00' then ''
							when j.fechabaja is null then ''
							when j.fechabaja is not null then j.fechabaja end) fechabaja,
					cou.nombre as country,
					j.observaciones,
					j.reftipodocumentos,
					j.refcountries
			from dbjugadores j
			inner join tbtipodocumentos tip ON tip.idtipodocumento = j.reftipodocumentos
			inner join dbcountries cou ON cou.idcountrie = j.refcountries
			inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria
			where j.email = '".$email."'
		order by 1";

		$res = $this->query($sql,0);
		return $res;
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

	function traerJugadoresprePorIdNuevo($id) {
	$sql = "select idjugadorpre,reftipodocumentos,nrodocumento,apellido,nombres,email,DATE_FORMAT(fechanacimiento, '%d-%m-%Y') as fechanacimiento,DATE_FORMAT(fechaalta, '%d-%m-%Y') as fechaalta,refcountries,observaciones,refusuarios,numeroserielote,refestados from dbjugadorespre where idjugadorpre =".$id;
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
		where   j.refcountries in (".$refCountries.") and jj.idjugador is null
		order by j.apellido, j.nombres";
		$res = $this->query($sql,0);
		return $res;
	}

	function traerVigenciasoperacionesPorModuloVigenciasNuevo($idModulo, $fecha) {
		$sqlExistente = "select idvigenciaoperacion,refmodulos,vigenciadesde,vigenciahasta,observaciones from dbvigenciasoperaciones order by idvigenciaoperacion desc limit 1";

		$resExistente = $this->query($sqlExistente,0);

		if (mysql_num_rows($resExistente)>0) {
			$sql = "select idvigenciaoperacion,refmodulos,vigenciadesde,vigenciahasta,observaciones from dbvigenciasoperaciones where refmodulos =".$idModulo." and (('".$fecha."' between vigenciadesde and vigenciahasta) or ('".$fecha."' >= vigenciadesde and vigenciahasta is null)) and idvigenciaoperacion = ".mysql_result($resExistente,0,0);

		} else {
			$sql = "select idvigenciaoperacion,refmodulos,vigenciadesde,vigenciahasta,observaciones from dbvigenciasoperaciones where refmodulos =".$idModulo." and (('".$fecha."' between vigenciadesde and vigenciahasta) or ('".$fecha."' >= vigenciadesde and vigenciahasta is null)) and idvigenciaoperacion = 0";
		}

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
		coalesce( jc.numeroserielote,coalesce( jp.numeroserielote,'')) as numeroserielote,
		concat(j.apellido, ' ', j.nombres) as apyn
		from dbjugadores j
		inner join tbtipodocumentos tip ON tip.idtipodocumento = j.reftipodocumentos
		inner join dbcountries cou ON cou.idcountrie = j.refcountries
		inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria
		left
		join dbjugadoresclub jc on jc.refcountries = cou.idcountrie and jc.refjugadores = j.idjugador and jc.temporada = ".$temporada."
		left
		join dbjugadorespre jp on jp.nrodocumento = j.nrodocumento
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
		$temporada = (integer)mysql_result($resTemporada,0,1);

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
		coalesce( jc.numeroserielote,coalesce( jp.numeroserielote,'')) as numeroserielote,
		concat(j.apellido, ' ', j.nombres) as apyn,
		coalesce( jc.numeroserielote,coalesce( jp.numeroserielote,'')) as marcalote
		from dbjugadores j
		inner join tbtipodocumentos tip ON tip.idtipodocumento = j.reftipodocumentos
		inner join dbcountries cou ON cou.idcountrie = j.refcountries
		inner join tbposiciontributaria po ON po.idposiciontributaria = cou.refposiciontributaria
		left
		join dbjugadoresclub jc on jc.refcountries = cou.idcountrie and jc.refjugadores = j.idjugador and jc.temporada = ".$temporada."
		left
		join dbjugadorespre jp on jp.nrodocumento = j.nrodocumento
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

	function traerFusionPorEquiposDelegados($idequipodelegado) {
		$sql = "SELECT
					    cc.idcountrie,
					    cc.nombre AS countrie,
					    est.idestado,
					    est.estado,
					    (CASE
					        WHEN viejo = 1 THEN 'Antiguo'
					        ELSE 'Nuevo'
					    END) AS viejo
					FROM
					    dbcountries cc
					        INNER JOIN
					    dbfusionequipos fe ON fe.refcountries = cc.idcountrie
					        INNER JOIN
					    tbestados est ON est.idestado = fe.refestados
					WHERE
					    fe.refequiposdelegados = ".$idequipodelegado;

		$res = $this->query($sql,0);
		return $res;
	}

	function traerFusionPorIdEquipos($idequipo) {
		$sql = "SELECT
					    cc.idcountrie,
					    cc.nombre AS countrie,
					    est.idestado,
					    est.estado,
					    (CASE
					        WHEN viejo = 1 THEN 'Antiguo'
					        ELSE 'Nuevo'
					    END) AS viejo
					FROM
					    dbcountries cc
					        INNER JOIN
					    dbfusionequipos fe ON fe.refcountries = cc.idcountrie
					        INNER JOIN
					    tbestados est ON est.idestado = fe.refestados
						 	INNER JOIN
						dbequiposdelegados ed ON ed.idequipodelegado = fe.refequiposdelegados
					WHERE
					    ed.idequipo = ".$idequipo;

		$res = $this->query($sql,0);
		return $res;
	}

	function traerFusionPorCountrie($id) {
		$sql = "select
					cc.idcountrie,
					cc.nombre as countriefusion ,
					ccp.nombre as countriepadre,
					ed.nombre,
					cat.categoria,
					d.division,
					est.idestado,
					est.estado,
					(case when viejo = 1 then 'Antiguo' else 'Nuevo' end) as viejo,
					fe.idfusionequipo
				from dbcountries cc
				inner join dbfusionequipos fe on fe.refcountries = cc.idcountrie
				inner join tbestados est ON est.idestado = fe.refestados
				inner join dbequiposdelegados ed ON ed.idequipodelegado = fe.refequiposdelegados
				inner join tbcategorias cat ON cat.idtcategoria = ed.refcategorias
				inner join tbdivisiones d ON d.iddivision = ed.refdivisiones
				inner join dbcountries ccp ON ccp.idcountrie = ed.refcountries
				where cc.idcountrie = ".$id;

		$res = $this->query($sql,0);
		return $res;
	}


	function traerFusionPorCountrieFusion($id, $idfusionequipo) {
		$sql = "select
					cc.idcountrie,
					cc.nombre as countriefusion ,
					ccp.nombre as countriepadre,
					ed.nombre,
					cat.categoria,
					d.division,
					est.idestado,
					est.estado,
					(case when viejo = 1 then 'Antiguo' else 'Nuevo' end) as viejo
				from dbcountries cc
				inner join dbfusionequipos fe on fe.refcountries = cc.idcountrie
				inner join tbestados est ON est.idestado = fe.refestados
				inner join dbequiposdelegados ed ON ed.idequipodelegado = fe.refequiposdelegados
				inner join tbcategorias cat ON cat.idtcategoria = ed.refcategorias
				inner join tbdivisiones d ON d.iddivision = ed.refdivisiones
				inner join dbcountries ccp ON ccp.idcountrie = ed.refcountries
				where cc.idcountrie = ".$id." and fe.idfusionequipo = ".$idfusionequipo;

		$res = $this->query($sql,0);
		return $res;
	}

	function traerFusionPorEquiposCountrie($idequipo, $idcountrie) {
		$sql = "SELECT
					ce.nombre AS countrypadre,
					e.nombre AS equipo,
					c.nombre AS countrie,
					cc.refequipos,
					c.idcountrie
				FROM
					dbequipos e
						INNER JOIN
					dbconector cc ON cc.refequipos = e.idequipo
						INNER JOIN
					dbjugadores jug on jug.idjugador = cc.refjugadores
						INNER JOIN
					dbcountries c ON c.idcountrie = jug.refcountries
						INNER JOIN
					dbcountries ce ON ce.idcountrie = e.refcountries
				WHERE
					cc.activo = 1
						AND e.activo = 1
						AND c.idcountrie <> ce.idcountrie
						AND e.idequipo = ".$idequipo."
						AND ce.idcountrie = ".$idcountrie."
				GROUP BY ce.nombre , e.nombre , c.nombre , cc.refequipos";

		$res = $this->query($sql,0);
		return $res;
	}

	function traerEquiposPorCountriesConFusion($idCountrie, $idtemporada) {
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
					dbequiposdelegados ed ON ed.idequipo = e.idequipo and ed.reftemporadas = ".$idtemporada."
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
						dbjugadores jug on jug.idjugador = cc.refjugadores
							INNER JOIN
						dbcountries c ON c.idcountrie = jug.refcountries
							INNER JOIN
						dbcountries ce ON ce.idcountrie = e.refcountries
					WHERE
						cc.activo = 1
							AND e.activo = 1
							AND c.idcountrie <> ce.idcountrie
					GROUP BY ce.nombre , e.nombre , c.nombre , cc.refcountries , cc.refequipos) r
					on r.refequipos = e.idequipo
				WHERE
					cou.idcountrie = ".$idCountrie." AND e.activo = 1 and ed.idequipodelegado is null
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
	/* Fin de la Tabla: dbusuarios*/



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
	refusuarios = ".$refusuarios.",apellidos = '".$apellidos."',nombres = '".$nombres."',direccion = '".$direccion."',localidad = '".$localidad."',cp = '".$cp."',telefono = '".$telefono."',celular = '".$celular."',fax = '".$fax."',email1 = '".$email1."',email2 = '".$email2."',email3 = '".$email3."',email4 = '".$email4."'
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

	function enviarMailAdjuntoEquipos($id, $email) {
		require('../reportes/fpdf.php');

		$idCountries		=	$id;

		$resTemporadas = $this->traerUltimaTemporada();

		if (mysql_num_rows($resTemporadas)>0) {
		    $ultimaTemporada = mysql_result($resTemporadas,0,0);
		} else {
		    $ultimaTemporada = 0;
		}

		/////////////////////////////  fin parametross  ///////////////////////////


		$resDatos = $this->traerEquiposdelegadosPorCountrieFinalizado($idCountries, $ultimaTemporada);

		$resDatosBaja = $this->traerEquiposdelegadosPorCountrieFinalizadoBaja($idCountries, $ultimaTemporada);

		$resCountrie = $this->traerCountriesPorId($idCountries);

		$nombre 	= mysql_result($resCountrie,0,'nombre');

		$numFusion = 0;

		$pdf = new FPDF();


		function Footer($pdf)
		{

		$pdf->SetY(-10);

		$pdf->SetFont('Arial','I',10);

		$pdf->Cell(0,10,'Firma: ______________________________________________  -  Pagina '.$pdf->PageNo()." - Fecha: ".date('Y-m-d'),0,0,'C');
		}


		$cantidadJugadores = 0;
		#Establecemos los márgenes izquierda, arriba y derecha:
		//$pdf->SetMargins(2, 2 , 2);

		#Establecemos el margen inferior:
		$pdf->SetAutoPageBreak(false,1);



			$pdf->AddPage();
			/***********************************    PRIMER CUADRANTE ******************************************/

			$pdf->Image('../imagenes/logoparainformes.png',2,2,40);

			/***********************************    FIN ******************************************/



			//////////////////// Aca arrancan a cargarse los datos de los equipos  /////////////////////////


			$pdf->SetFillColor(183,183,183);
			$pdf->SetFont('Arial','B',12);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetY(25);
			$pdf->SetX(5);
			$pdf->Cell(200,5,'Padron de Equipos Temporada 2019 - Club: '.utf8_decode($nombre),1,0,'C',true);
			$pdf->Ln();
			$pdf->SetX(5);
			$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
			$pdf->SetFont('Arial','',10);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetX(5);

			$pdf->SetFont('Arial','',12);
			$pdf->Cell(5,5,'',1,0,'C',true);
			$pdf->Cell(60,5,'EQUIPO',1,0,'C',true);
			$pdf->Cell(60,5,'CATEGORIA',1,0,'C',true);
			$pdf->Cell(60,5,'DIVISION',1,0,'C',true);

			$cantPartidos = 0;
			$i=0;

			$contadorY1 = 44;
			$contadorY2 = 44;

			$arFusiones = array();
		while ($rowE = mysql_fetch_array($resDatos)) {
			$i+=1;
			$cantPartidos += 1;

			$resFusion = $this->traerEquiposFusionPorEquipoDelegado($rowE['idequipodelegado']);

		   if (mysql_num_rows($resFusion)>0) {
		      $numFusion += 1;
		      while ($rowF = mysql_fetch_array($resFusion)) {
		         array_push($arFusiones, array('num' => $numFusion, 'club'=> $rowF[0]));
		      }

		   }

			if ($i > 50) {
				Footer($pdf);
				$pdf->AddPage();
				$pdf->Image('../imagenes/logoparainformes.png',2,2,40);
				$pdf->SetFont('Arial','B',10);
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetY(25);
				$pdf->SetX(5);
				$pdf->Cell(200,5,utf8_decode($nombre),1,0,'C',true);
				$pdf->SetFont('Arial','',10);
				$pdf->Ln();
				$pdf->SetX(5);

				$i=0;

				$pdf->SetFont('Arial','',12);
				$pdf->Cell(5,5,'',1,0,'C',true);
				$pdf->Cell(60,5,'EQUIPO',1,0,'C',true);
				$pdf->Cell(60,5,'CATEGORIA',1,0,'C',true);
				$pdf->Cell(60,5,'DIVISION',1,0,'C',true);

			}


			$pdf->Ln();
			$pdf->SetX(5);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(5,5,$cantPartidos,1,0,'C',false);
			if (mysql_num_rows($resFusion)>0) {
		      $pdf->Cell(60,5,utf8_decode($rowE['nombre']).' ('.$numFusion.')',1,0,'C',false);
		   } else {
		      $pdf->Cell(60,5,utf8_decode($rowE['nombre']),1,0,'C',false);
		   }
			$pdf->Cell(60,5,utf8_decode($rowE['categoria']),1,0,'C',false);
			$pdf->Cell(60,5,utf8_decode($rowE['division']),1,0,'C',false);


			$contadorY1 += 4;

			//$pdf->SetY($contadorY1);


		}

		$pdf->SetX(5);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(200,5,'Equipos dados de Baja',0,0,'C',false);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetX(5);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,5,'',1,0,'C',true);
		$pdf->Cell(60,5,'EQUIPO',1,0,'C',true);
		$pdf->Cell(60,5,'CATEGORIA',1,0,'C',true);
		$pdf->Cell(60,5,'DIVISION',1,0,'C',true);

		$cantPartidos = 0;


		$contadorY1 = 44;
		$contadorY2 = 44;
		while ($rowE = mysql_fetch_array($resDatosBaja)) {
		$i+=1;
		$cantPartidos += 1;

		if ($i > 50) {
		   Footer($pdf);
		   $pdf->AddPage();
		   $pdf->Image('../imagenes/logoparainformes.png',2,2,40);
		   $pdf->SetFont('Arial','B',10);
		   $pdf->Ln();
		   $pdf->Ln();
		   $pdf->SetY(25);
		   $pdf->SetX(5);
		   $pdf->Cell(200,5,utf8_decode($nombre),1,0,'C',true);
		   $pdf->SetFont('Arial','',10);
		   $pdf->Ln();
		   $pdf->SetX(5);

		   $i=0;

		   $pdf->SetFont('Arial','',12);
		   $pdf->Cell(5,5,'',1,0,'C',true);
		   $pdf->Cell(60,5,'EQUIPO',1,0,'C',true);
		   $pdf->Cell(60,5,'CATEGORIA',1,0,'C',true);
		   $pdf->Cell(60,5,'DIVISION',1,0,'C',true);

		}


		$pdf->Ln();
		$pdf->SetX(5);
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(5,5,$cantPartidos,1,0,'C',false);
		$pdf->Cell(60,5,utf8_decode($rowE['nombre']),1,0,'C',false);
		$pdf->Cell(60,5,utf8_decode($rowE['categoria']),1,0,'C',false);
		$pdf->Cell(60,5,utf8_decode($rowE['division']),1,0,'C',false);


		$contadorY1 += 4;

		//$pdf->SetY($contadorY1);


		}


		$pdf->Ln();


		$pdf->SetX(5);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(200,5,'FUSIONES',0,0,'C',false);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetX(5);
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,5,'',1,0,'C',true);
		$pdf->Cell(60,5,'EQUIPO',1,0,'C',true);
		$pdf->Cell(60,5,'COUNTRIES',1,0,'C',true);
		$pdf->Cell(60,5,'FUSION',1,0,'C',true);

		$cantPartidos = 0;

		$contadorY1 = 44;
		$contadorY2 = 44;

		//die(var_dump($arFusiones));
		foreach ($arFusiones as $valor) {
		   // code...
		   $i+=1;
		   $cantPartidos += 1;

		   if ($i > 50) {
		      Footer($pdf);
		      $pdf->AddPage();
		      $pdf->Image('../imagenes/logoparainformes.png',2,2,40);
		      $pdf->SetFont('Arial','B',10);
		      $pdf->Ln();
		      $pdf->Ln();
		      $pdf->SetY(25);
		      $pdf->SetX(5);
		      $pdf->Cell(200,5,utf8_decode($nombre),1,0,'C',true);
		      $pdf->SetFont('Arial','',10);
		      $pdf->Ln();
		      $pdf->SetX(5);

		      $i=0;

		      $pdf->SetFont('Arial','',12);
		      $pdf->Cell(5,5,'',1,0,'C',true);
		      $pdf->Cell(60,5,'EQUIPO',1,0,'C',true);
		      $pdf->Cell(60,5,'COUNTRIES',1,0,'C',true);
				$pdf->Cell(60,5,'FUSION',1,0,'C',true);

		   }


		   $pdf->Ln();
		   $pdf->SetX(5);
		   $pdf->SetFont('Arial','',10);
		   $pdf->Cell(5,5,'',1,0,'C',false);
		   $pdf->Cell(60,5,$valor['num'],1,0,'C',false);
		   $pdf->Cell(60,5,utf8_decode($valor['club']),1,0,'C',false);
			$pdf->Cell(60,5,$valor['viejo'],1,0,'C',false);

		   $contadorY1 += 4;

		   //$pdf->SetY($contadorY1);


		}

		$pdf->Ln();
		$pdf->Ln();


		Footer($pdf);



		$nombreTurno = "EQUIPOSCLUB.pdf";

		$pdf->Output($nombreTurno,'F');

		require_once('AttachMailer.php');

		$ruta = "https://saupureinconsulting.com.ar/aifzncountriesdesarrollo/ajax/";
		$mi_archivo = $nombreTurno;
		$mi_nombre = "AIF";
		$mi_email = $email;
		$email_to = $this->traerReferente($idCountries);
		$mi_titulo = "Este es un correo con archivo adjunto";
		$mi_mensaje = "Esta es el cuerpo de mensaje.";

		$ruta_completa = $ruta.$mi_archivo;

		$mailer = new AttachMailer($mi_email, $email_to, "Presenta equipos", "Lista de los equipos confirmados");
		$mailer->attachFile($ruta_completa);
		$mailer->send() ? "Enviado": "Problema al enviar";

		$devuelve = $this->mail_attachment($mi_archivo, $ruta, $email_to, $mi_email, $mi_nombre, $mi_titulo, $mi_mensaje);


	}

	function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $subject, $message) {

		$ruta_completa = $path.$filename;

		$content = chunk_split(base64_encode(file_get_contents($ruta_completa)));
		$uid= md5(uniqid(time()));
		$bound="--".$uid."\r\n";
		$last_bound="--".$uid."--\r\n";
		$header = "From: ".$from_name." <".$from_mail.">\r\n";
		$header .= "MIME-Version: 1.0\n";
		$header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"\r\n";
		$header .= "This is a multi-part message in MIME format.\r\n";
		$header .= $bound;
		$header .= "Content-type:text/plain; charset=utf-8\r\n";
		$header .= "Content-Transfer-Encoding: 7bit\r\n";
		$header .= $message."\r\n";
		$header .= $bound;
		$header .= "Content-Type: application/pdf; name=\"".$ruta_completa."\"\r\n";
		$header .= "Content-Transfer-Encoding: base64\r\n";
		$header .= "Content-Disposition: attachment; filename=\"".$filename."\"\r\n";
		$header .= $content."\r\n";
		$header .= $last_bound;

		if (mail($mailto, $subject, "", $header)) {
			return "Correo enviado";
		} else {
			return "ERROR en el envio";
		}
	}


	function enviarEmail($destinatario,$asunto,$cuerpo, $referencia='') {

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

	    //ruta del mensaje desde origen a destino
	    $headers .= "Return-path: ".$destinatario."\r\n";

	    //direcciones que recibirán copia oculta
	    $headers .= "Bcc: ".$referencia."\r\n";

	    mail($destinatario,$asunto,$cuerpo,$headers);
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

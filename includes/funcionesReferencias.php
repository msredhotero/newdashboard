<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosReferencias {

	function permiteImprimirPadron($idclub) {
		$resTemporadas = $this->traerUltimaTemporada();

		if (mysql_num_rows($resTemporadas)>0) {
		    $ultimaTemporada = mysql_result($resTemporadas,0,1);
		} else {
		    $ultimaTemporada = 0;
		}

		$sql = "SELECT
				    count(*) as cantidad
				FROM
				    dbjugadoresclub
				WHERE
				    temporada = ".$ultimaTemporada." AND refcountries = ".$idclub." and (numeroserielote = '' or numeroserielote is null) and fechabaja = 0 and articulo = 0";
		$res = $this->query($sql,0);

		return $res;
	}

   function traerEstadoEstudioMedico($id) {
      $sql = "SELECT
                   idjugadordocumentacion
               FROM
                   dbjugadoresdocumentacion
               WHERE
                   refjugadores = ".$id."
                       AND refdocumentaciones = 5
                       and valor = 1";

      $res = $this->query($sql,0);

      if (mysql_num_rows($res) > 0) {
         return array('estadoEstudioMedico'=> 'ENTREGADO', 'colorEstudioMedico' => 'bg-green');
      }
      return array('estadoEstudioMedico'=> 'NO ENTREGADO', 'colorEstudioMedico' => 'bg-red');
   }

	function presentardocumentacionFase1($id) {

		$resJugador = $this->traerJugadoresprePorId($id);

		$emailReferente = $this->traerReferentePorNrodocumentopre(mysql_result($resJugador, 0, 'nrodocumento'));

		$sql = "select refestados,refdocumentaciones from dbdocumentacionjugadorimagenes where refjugadorespre = ".$id." and refdocumentaciones in (1,2,99)";
		$resDocumentaciones = $this->query($sql,0);

		$cantidad = 0;

		if (mysql_num_rows($resDocumentaciones) == 3) {
			while ($row = mysql_fetch_array($resDocumentaciones)) {
				if (($row['refestados'] == 1) || ($row['refestados'] == 4)) {
					$this->modificarEstadoDocumentacionjugadorimagenesPorJugadorDocumentacion($id,$row['refdocumentaciones'], 2);
				}
			}


			//** creo la notificacion **//
			$mensaje = 'Se presento una documentacion';
			$idpagina = 1;
			$autor = mysql_result($resJugador, 0, 'apellido').' '.mysql_result($resJugador, 0, 'nombres');
			$destinatario = $emailReferente;
			$id1 = $id;
			$id2 = 0;
			$id3 = 0;
			$icono = 'glyphicon glyphicon-eye-open';
			$estilo = 'alert alert-success';
			$fecha = date('Y-m-d H:i:s');
			$url = "altasocios/modificar.php?id=".$id;

			$res = $this->insertarNotificaciones($mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url);
			//** fin notificaion      **//

			$this->enviarEmail($emailReferente,$mensaje,$autor, $referencia='');

			return array('error'=>0, 'mensaje' => 'La documentacion fue enviada correctamente para su posterior revision, cualquier notificacion sera enviada por email.');

			//echo 'La documentacion fue enviada correctamente para su posterior revision, cualquier notificacion sera enviada por email.';
		} else {
			return array('error'=>1, 'mensaje' => 'Falta cargar datos para poder presentar la documentacion.');
			//echo 'Falta cargar datos para poder presentar la documentacion';
		}

	}

	function presentardocumentacionAparte($id) {
		$resJugador = $this->traerJugadoresprePorId($id);

		$emailReferente = $this->traerReferentePorNrodocumentopre(mysql_result($resJugador, 0, 'nrodocumento'));

		$sql = "select refestados,refdocumentaciones from dbdocumentacionjugadorimagenes where refjugadorespre = ".$id." and refdocumentaciones in (4,6,9)";
		$resDocumentaciones = $this->query($sql,0);

		$cantidad = 0;

		while ($row = mysql_fetch_array($resDocumentaciones)) {
			if ($row['refestados'] == 1) {
				$this->modificarEstadoDocumentacionjugadorimagenesPorJugadorDocumentacion($id,$row['refdocumentaciones'], 2);
				$cantidad += 1;
			}
		}


		if ($cantidad > 0) {
			//** creo la notificacion **//
			$mensaje = 'Se presento la documentación extra';
			$idpagina = 1;
			$autor = mysql_result($resJugador, 0, 'apellido').' '.mysql_result($resJugador, 0, 'nombres');
			$destinatario = $emailReferente;
			$id1 = $id;
			$id2 = 0;
			$id3 = 0;
			$icono = 'glyphicon glyphicon-eye-open';
			$estilo = 'alert alert-success';
			$fecha = date('Y-m-d H:i:s');
			$url = "altasocios/modificar.php?id=".$id;

			$res = $this->insertarNotificaciones($mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url);
			//** fin notificaion      **//

			$this->enviarEmail($emailReferente,$mensaje,$autor, $referencia='');

			return array('error'=>0, 'mensaje' => 'La documentacion fue enviada correctamente para su posterior revision, cualquier notificacion sera enviada por email.');

		} else {
			return array('error'=>1, 'mensaje' => 'Ya Presento toda la la documentación Extra o posee documentacion Rechazada.');
			//echo 'Ya Presento toda la la documentación Extra o posee documentacion Rechazada';
		}


	}



   function presentardocumentacionFase1Viejo($id) {

		$resJugador = $this->traerJugadoresPorId($id);

		$emailReferente = $this->traerReferentePorNrodocumento(mysql_result($resJugador, 0, 'nrodocumento'));

		$sql = "select refestados,refdocumentaciones, iddocumentacionjugadorimagen from dbdocumentacionjugadorimagenes where idjugador = ".$id." and refdocumentaciones in (1,2,99)";
		$resDocumentaciones = $this->query($sql,0);

		$cantidad = 0;

		if (mysql_num_rows($resDocumentaciones) == 3) {
			while ($row = mysql_fetch_array($resDocumentaciones)) {
				if (($row['refestados'] == 1) || ($row['refestados'] == 4)) {
					$this->modificarEstadoDocumentacionjugadorimagenesPorId($row['iddocumentacionjugadorimagen'], 2);
				}
			}


			//** creo la notificacion **//
			$mensaje = 'Se presento una documentacion';
			$idpagina = 1;
			$autor = mysql_result($resJugador, 0, 'apellido').' '.mysql_result($resJugador, 0, 'nombres');
			$destinatario = $emailReferente;
			$id1 = $id;
			$id2 = 0;
			$id3 = 0;
			$icono = 'glyphicon glyphicon-eye-open';
			$estilo = 'alert alert-success';
			$fecha = date('Y-m-d H:i:s');
			$url = "jugadores/documentaciones.php?id=".$id;

			$res = $this->insertarNotificaciones($mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url);
			//** fin notificaion      **//

			$this->enviarEmail($emailReferente,$mensaje,$autor, $referencia='');

			return array('error'=>0, 'mensaje' => 'La documentacion fue enviada correctamente para su posterior revision, cualquier notificacion sera enviada por email.');

			//echo 'La documentacion fue enviada correctamente para su posterior revision, cualquier notificacion sera enviada por email.';
		} else {
			return array('error'=>1, 'mensaje' => 'Falta cargar datos para poder presentar la documentacion.');
			//echo 'Falta cargar datos para poder presentar la documentacion';
		}

	}

	function presentardocumentacionAparteViejo($id) {
		$resJugador = $this->traerJugadoresPorId($id);

		$emailReferente = $this->traerReferentePorNrodocumento(mysql_result($resJugador, 0, 'nrodocumento'));

		$sql = "select refestados,refdocumentaciones, iddocumentacionjugadorimagen from dbdocumentacionjugadorimagenes where idjugador = ".$id." and refdocumentaciones in (4,6,9)";
		$resDocumentaciones = $this->query($sql,0);

		$cantidad = 0;

		while ($row = mysql_fetch_array($resDocumentaciones)) {
			if ($row['refestados'] == 1) {
				$this->modificarEstadoDocumentacionjugadorimagenesPorId($row['iddocumentacionjugadorimagen'], 2);
				$cantidad += 1;
			}
		}


		if ($cantidad > 0) {
			//** creo la notificacion **//
			$mensaje = 'Se presento la documentación extra';
			$idpagina = 1;
			$autor = mysql_result($resJugador, 0, 'apellido').' '.mysql_result($resJugador, 0, 'nombres');
			$destinatario = $emailReferente;
			$id1 = $id;
			$id2 = 0;
			$id3 = 0;
			$icono = 'glyphicon glyphicon-eye-open';
			$estilo = 'alert alert-success';
			$fecha = date('Y-m-d H:i:s');
			$url = "jugadores/documentaciones.php?id=".$id;

			$res = $this->insertarNotificaciones($mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url);
			//** fin notificaion      **//

			$this->enviarEmail($emailReferente,$mensaje,$autor, $referencia='');

			return array('error'=>0, 'mensaje' => 'La documentacion fue enviada correctamente para su posterior revision, cualquier notificacion sera enviada por email.');

		} else {
			return array('error'=>1, 'mensaje' => 'Ya Presento toda la la documentación Extra o posee documentacion Rechazada.');
			//echo 'Ya Presento toda la la documentación Extra o posee documentacion Rechazada';
		}


	}

	function insertarNotificaciones($mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url) {
		$sql = "insert into dbnotificaciones(idnotificacion,mensaje,idpagina,autor,destinatario,id1,id2,id3,icono,estilo,fecha,url,leido)
		values ('','".($mensaje)."',".$idpagina.",'".($autor)."','".($destinatario)."',".$id1.",".$id2.",".$id3.",'".($icono)."','".($estilo)."','".($fecha)."','".($url)."',0)";
		$res = $this->query($sql,1);
		return $res;
	}

	function devolverEstadoDocumentaciones($id, $tipo) {
		$foto1 = '';
		$foto2 = '';
		$foto3 = '';

		// traer foto
		if ($tipo == 2) {
			$resFoto = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($id,1);
			$resFotoDocumento = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($id,2);
			$resFotoDocumentoDorso = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($id,99);
		} else {
			$resFoto = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion(0,1,$id);
			$resFotoDocumento = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion(0,2,$id);
			$resFotoDocumentoDorso = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion(0,99,$id);
		}

		if (mysql_num_rows($resFoto) > 0) {
			$estadoFoto = mysql_result($resFoto, 0,'estado');
			$idEstadoFoto = mysql_result($resFoto, 0,'refestados');
			$foto1 = mysql_result($resFoto, 0,'imagen');
		} else {
			$estadoFoto = 'Sin carga';
			$idEstadoFoto = 0;
			$foto1 = '';
		}

		$spanFoto = '';

		switch ($idEstadoFoto) {
			case 0:
				$spanFoto = 'bg-light-blue';
				break;
			case 1:
				$spanFoto = 'bg-blue';
				break;
			case 2:
				$spanFoto = 'bg-amber';
				break;
			case 3:
				$spanFoto = 'bg-green';
				break;
			case 4:
				$spanFoto = 'bg-red';
				break;
		}



		// traer imagen


		if (mysql_num_rows($resFotoDocumento) > 0) {
			$estadoNroDoc = mysql_result($resFotoDocumento, 0,'estado');
			$idEstadoNroDoc = mysql_result($resFotoDocumento, 0,'refestados');
			$foto2 = mysql_result($resFotoDocumento, 0,'imagen');
		} else {
			$estadoNroDoc = 'Sin carga';
			$idEstadoNroDoc = 0;
			$foto2= '';
		}


		$spanNroDoc = '';
		switch ($idEstadoNroDoc) {
			case 0:
				$spanNroDoc = 'bg-light-blue';
				break;
			case 1:
				$spanNroDoc = 'bg-blue';
				break;
			case 2:
				$spanNroDoc = 'bg-amber';
				break;
			case 3:
				$spanNroDoc = 'bg-green';
				break;
			case 4:
				$spanNroDoc = 'bg-red';
				break;
		}



		if (mysql_num_rows($resFotoDocumentoDorso) > 0) {
			$estadoNroDocDorso = mysql_result($resFotoDocumentoDorso, 0,'estado');
			$idEstadoNroDocDorso = mysql_result($resFotoDocumentoDorso, 0,'refestados');
			$foto3 = mysql_result($resFotoDocumentoDorso, 0,'imagen');
		} else {
			$estadoNroDocDorso = 'Sin carga';
			$idEstadoNroDocDorso = 0;
			$foto3 = '';
		}


		$spanNroDocDorso = '';
		switch ($idEstadoNroDocDorso) {
			case 0:
				$spanNroDocDorso = 'bg-light-blue';
				break;
			case 1:
				$spanNroDocDorso = 'bg-blue';
				break;
			case 2:
				$spanNroDocDorso = 'bg-amber';
				break;
			case 3:
				$spanNroDocDorso = 'bg-green';
				break;
			case 4:
				$spanNroDocDorso = 'bg-red';
				break;
		}

		$ar = array('imagenFoto' => $foto1,
						'estadoFoto' => $estadoFoto,
						'idEstadoFoto' => $idEstadoFoto,
						'colorEstadoFoto' => $spanFoto,
						'imagenDocFrente' => $foto2,
						'estadoDocFrente' => $estadoNroDoc,
						'idEstadoDocFrente' => $idEstadoNroDoc,
						'colorEstadoDocFrente' => $spanNroDoc,
						'imagenDocDorsal' => $foto3,
						'estadoDocDorsal' => $estadoNroDocDorso,
						'idEstadoDocDorsal' => $idEstadoNroDocDorso,
						'colorEstadoDocDorsal' => $spanNroDocDorso);

		/*******-------------------------------------------------------*/

		return $ar;
	}


	function devolverEstadoDocumentacionesFase2($id, $tipo) {
		$foto1 = '';
		$foto2 = '';
		$foto3 = '';

		// traer foto
		if ($tipo == 2) {
			$resEscritura = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($id,4);
			$resExpensa = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($id,6);
			$resPartida = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion($id,9);
		} else {
			$resEscritura = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion(0,4,$id);
			$resExpensa = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion(0,6,$id);
			$resPartida = $this->traerDocumentacionjugadorimagenesPorJugadorDocumentacion(0,9,$id);
		}

		if (mysql_num_rows($resEscritura) > 0) {
			$estadoEscritura = mysql_result($resEscritura, 0,'estado');
			$idEstadoEscritura = mysql_result($resEscritura, 0,'refestados');
			$escritura1 = mysql_result($resEscritura, 0,'imagen');
		} else {
			$estadoEscritura = 'Sin carga';
			$idEstadoEscritura = 0;
			$escritura1 = '';
		}

		$spanEscritura = '';

		switch ($idEstadoEscritura) {
			case 0:
				$spanEscritura = 'bg-light-blue';
				break;
			case 1:
				$spanEscritura = 'bg-blue';
				break;
			case 2:
				$spanEscritura = 'bg-amber';
				break;
			case 3:
				$spanEscritura = 'bg-green';
				break;
			case 4:
				$spanEscritura = 'bg-red';
				break;
		}



		// traer imagen


		if (mysql_num_rows($resExpensa) > 0) {
			$estadoExpensa = mysql_result($resExpensa, 0,'estado');
			$idEstadoExpensa = mysql_result($resExpensa, 0,'refestados');
			$expensa2 = mysql_result($resExpensa, 0,'imagen');
		} else {
			$estadoExpensa = 'Sin carga';
			$idEstadoExpensa = 0;
			$expensa2= '';
		}


		$spanExpensa = '';
		switch ($idEstadoExpensa) {
			case 0:
				$spanExpensa = 'bg-light-blue';
				break;
			case 1:
				$spanExpensa = 'bg-blue';
				break;
			case 2:
				$spanExpensa = 'bg-amber';
				break;
			case 3:
				$spanExpensa = 'bg-green';
				break;
			case 4:
				$spanExpensa = 'bg-red';
				break;
		}



		if (mysql_num_rows($resPartida) > 0) {
			$estadoPartida = mysql_result($resPartida, 0,'estado');
			$idEstadoPartida = mysql_result($resPartida, 0,'refestados');
			$partida3 = mysql_result($resPartida, 0,'imagen');
		} else {
			$estadoPartida = 'Sin carga';
			$idEstadoPartida = 0;
			$partida3 = '';
		}


		$spanPartida = '';
		switch ($idEstadoPartida) {
			case 0:
				$spanPartida = 'bg-light-blue';
				break;
			case 1:
				$spanPartida = 'bg-blue';
				break;
			case 2:
				$spanPartida = 'bg-amber';
				break;
			case 3:
				$spanPartida = 'bg-green';
				break;
			case 4:
				$spanPartida = 'bg-red';
				break;
		}

		$ar = array('imagenEscritura' => $escritura1,
						'estadoEscritura' => $estadoEscritura,
						'idEstadoEscritura' => $idEstadoEscritura,
						'colorEstadoEscritura' => $spanEscritura,
						'imagenExpensa' => $expensa2,
						'estadoExpensa' => $estadoExpensa,
						'idEstadoExpensa' => $idEstadoExpensa,
						'colorEstadoExpensa' => $spanExpensa,
						'imagenPartida' => $partida3,
						'estadoPartida' => $estadoPartida,
						'idEstadoPartida' => $idEstadoPartida,
						'colorEstadoPartida' => $spanPartida);

		/*******-------------------------------------------------------*/

		return $ar;
	}


function devolverImagen($name, $type, $nombrenuevo) {

    //if( $_FILES[$archivo]['name'] != null && $_FILES[$archivo]['size'] > 0 ){
    // Nivel de errores
      error_reporting(E_ALL);
      $altura = 500;
      // Constantes
      # Altura de el thumbnail en píxeles
      //define("ALTURA", 100);
      # Nombre del archivo temporal del thumbnail
      //define("NAMETHUMB", "/tmp/thumbtemp"); //Esto en servidores Linux, en Windows podría ser:
      //define("NAMETHUMB", "c:/windows/temp/thumbtemp"); //y te olvidas de los problemas de permisos
      $NAMETHUMB = "";
      # Servidor de base de datos
      //define("DBHOST", "localhost");
      # nombre de la base de datos
      //define("DBNAME", "portalinmobiliario");
      # Usuario de base de datos
      //define("DBUSER", "root");
      # Password de base de datos
      //define("DBPASSWORD", "");
      // Mime types permitidos
      $mimetypes = array("image/jpeg", "image/pjpeg", "image/gif", "image/png","image/jpg");
      // Variables de la foto
      $name = $name;
      $type = $type;
      $tmp_name = $name;
      //$size = $_FILES[$archivo]["size"];
      // Verificamos si el archivo es una imagen válida
      if(!in_array($type, $mimetypes))
        die("El archivo que subiste no es una imagen válida");
      // Creando el thumbnail
    if ($nombrenuevo == 'imagenTemp2') {
        //die($type);
    }
      switch($type) {
        case $mimetypes[0]:
        case $mimetypes[1]:
        case $mimetypes[4]:
          $img = imagecreatefromjpeg($tmp_name);
          $NAMETHUMB .= $nombrenuevo.".jpg";
          //die($img);
          break;
        case $mimetypes[2]:
          $img = imagecreatefromgif($tmp_name);
          $NAMETHUMB .= $nombrenuevo.".gif";
          break;
        case $mimetypes[3]:
          $img = imagecreatefrompng($tmp_name);
          $NAMETHUMB .= $nombrenuevo.".png";
          break;
      }

      if ($img) {
      $datos = getimagesize($tmp_name);

      $ratio = ($datos[1]/$altura);
      $ancho = round($datos[0]/$ratio);
      $thumb = imagecreatetruecolor($ancho, $altura);
      imagecopyresized($thumb, $img, 0, 0, 0, 0, $ancho, $altura, $datos[0], $datos[1]);
      switch($type) {
        case $mimetypes[0]:
        case $mimetypes[1]:
        case $mimetypes[4]:
          imagejpeg($thumb, $NAMETHUMB);
              break;
        case $mimetypes[2]:
          imagegif($thumb, $NAMETHUMB);
          break;
        case $mimetypes[3]:
          imagepng($thumb, $NAMETHUMB);
          break;
      }

      //die();


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
    /*
    } else {
        $tfoto = '';
        $type = '';
    }
    */
    $tfoto = utf8_decode($tfoto);
    //return array('tfoto' => $tfoto, 'type' => $NAMETHUMB);
    return $NAMETHUMB;

    } else {
        return 'No se pudo cargar correctamente la imagen';
    }
}


	function traerJugadoresprePorIdCompleto($id) {
		$sql = "select j.idjugadorpre,j.reftipodocumentos,j.nrodocumento,j.apellido,j.nombres,j.email,j.fechanacimiento,j.fechaalta,j.refcountries,j.observaciones,j.refusuarios,j.numeroserielote , cc.nombre as country, td.tipodocumento
		        from dbjugadorespre j
		        inner join dbcountries cc on cc.idcountrie = j.refcountries
		        inner join tbtipodocumentos td on td.idtipodocumento = j.reftipodocumentos
		        where idjugadorpre =".$id;

		$res = $this->query($sql,0);
		return $res;
	}

	function borrarArchivoJugadores($id,$directorio) {

		$sql    =   "delete from dbdocumentacionjugadorimagenes where iddocumentacionjugadorimagen =".$id;

		$res =  $this->borrarDirecctorio("./../../../../".$directorio);

		rmdir("./../../../../".$directorio);
		$this->query($sql,0);

		return '';
	}

	function eliminarFotoJugadoresID($refdocumentaciones, $refjugadorespre, $idAux=0) {
		$servidorCarpeta = 'aifzndesarrollo';

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

      // en caso de que sea un jugador viejo
      if ($refjugadorespre == 0 ) {
         $resJugador    = $this->traerJugadoresPorId($idjugador);

         if (mysql_num_rows($resJugador)>0) {
            $resJugadorPre = $this->traerJugadoresprePorNroDocumento(mysql_result($resJugador,0,'nrodocumento'));

            if (mysql_num_rows($resJugadorPre)>0) {
               $refjugadorespre = mysql_result($resJugadorPre,0,0);
            }
         }
      } else {

         // en caso de que sea un jugador nuevo
         if ($idjugador == 0 ) {
            $resJugador    = $this->traerJugadoresprePorId($refjugadorespre);

            if (mysql_num_rows($resJugador)>0) {
               $resJugadorPre = $this->traerJugadoresPorNroDocumento(mysql_result($resJugador,0,'nrodocumento'));

               if (mysql_num_rows($resJugadorPre)>0) {
                  $idjugador = mysql_result($resJugadorPre,0,0);
               }
            }
         }
      }



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
		if ($idJugadorPre == 0) {
         //die(var_dump($idJugadorPre));
			$sql = "select
			                dj.iddocumentacionjugadorimagen,dj.refdocumentaciones,dj.refjugadorespre,dj.imagen,dj.type,dj.refestados, e.estado, concat('data','/',dj.iddocumentacionjugadorimagen) as archivo
			            from dbdocumentacionjugadorimagenes dj
			            inner join tbestados e ON e.idestado = dj.refestados
			        where (dj.idjugador =".$idJugador.") and dj.refdocumentaciones = ".$idDocumentacion;
		} else {
			$sql = "select
			                dj.iddocumentacionjugadorimagen,dj.refdocumentaciones,dj.refjugadorespre,dj.imagen,dj.type,dj.refestados, e.estado, concat('data','/',dj.iddocumentacionjugadorimagen) as archivo
			            from dbdocumentacionjugadorimagenes dj
			            inner join tbestados e ON e.idestado = dj.refestados
			        where (dj.refjugadorespre = ".$idJugadorPre.") and dj.refdocumentaciones = ".$idDocumentacion;
		}


		$res = $this->query($sql,0);
		return $res;
	}

	function determinaSocioNuevoViejo($email) {
		$resTemporadas = $this->traerUltimaTemporada();

		if (mysql_num_rows($resTemporadas)>0) {
		    $ultimaTemporada = mysql_result($resTemporadas,0,1);
		} else {
		    $ultimaTemporada = 0;
		}

		$estadoSocio = 0;
		// socio Nuevo
		// tabla jugadorespre que esten dados de alta en dbusuarios y esten activos
		// que la fecha de alta sea del año corriente o aunque sea del mes de diciembre del año anterior
		$sql = "SELECT
					    jp.idjugadorpre, u.idusuario, jp.nrodocumento, c.nombre as club
					FROM
					    dbjugadorespre jp
					        INNER JOIN
					    dbusuarios u ON u.idusuario = jp.idusuario
					        AND u.activo = 1
					        inner join
						dbcountries c ON c.idcountrie = jp.refcountries
					where	year(jp.fechaalta) in (2019) and u.email = '".$email."'";

		$resJugadorPre = $this->query($sql,0);

		// socio viejo
		// tabla dbjugadores que esten dados de alta en dbusuarios y esten activos
		$sql = "SELECT
					    j.idjugador, u.idusuario, j.nrodocumento, c.nombre as club
					FROM
					    dbjugadores j
					        INNER JOIN
					    dbusuarios u ON j.email = u.email COLLATE utf8_spanish_ci
					        AND u.activo = 1
					        inner join
						dbcountries c ON c.idcountrie = j.refcountries
					where	u.email = '".$email."'";

		$resJugador = $this->query($sql,0);

		if (mysql_num_rows($resJugadorPre) > 0) {
			return array('valor' => 1, 'datos'=>$resJugadorPre);
		} else {
			if (mysql_num_rows($resJugador) > 0) {
				return array('valor' => 2, 'datos'=>$resJugador);
			} else {
				return array('valor' => 0, 'datos'=>null);
			}
		}
	}

	function traerDocumentacionjugadorimagenesPorJugadorDocumentacionID($idJugador, $idDocumentacion, $idJugadorPre=0) {
		if ($idJugador > 0) {
      $sql = "select
                          dj.iddocumentacionjugadorimagen,dj.refdocumentaciones,dj.refjugadorespre,dj.imagen,dj.type,dj.refestados, e.estado
                      from dbdocumentacionjugadorimagenes dj
                      inner join tbestados e ON e.idestado = dj.refestados
                  where (dj.idjugador =".$idJugador.") and dj.refdocumentaciones = ".$idDocumentacion;
       } else {
          $sql = "select
                          dj.iddocumentacionjugadorimagen,dj.refdocumentaciones,dj.refjugadorespre,dj.imagen,dj.type,dj.refestados, e.estado
                      from dbdocumentacionjugadorimagenes dj
                      inner join tbestados e ON e.idestado = dj.refestados
                  where (dj.refjugadorespre = ".$idJugadorPre.") and dj.refdocumentaciones = ".$idDocumentacion;
       }

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
			array(' ',"'"),
			array('',''),
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
					from        dbjugadores j
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

	function traerReferentePorNrodocumentopre($nrodocumento) {
		$sql = "select
						coalesce(u.email,'') as email
					from        dbjugadorespre j
					inner
					join        dbcountries c
					on          j.refcountries = c.idcountrie
					inner
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
		        left join
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
		        left join
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
		left join dbcontactos con ON con.idcontacto = e.refcontactos
		left join tbtipocontactos ti ON ti.idtipocontacto = con.reftipocontactos
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
		left join dbcontactos con ON con.idcontacto = e.refcontactos
		left join tbtipocontactos ti ON ti.idtipocontacto = con.reftipocontactos
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

	function traerEstadosFusionesAceptadasPorCountrieEquipo($idcountrie, $idequipo) {

		$sql = "select
					coalesce(min(fe.refestados),3) as idestado
				from dbfusionequipos fe
				inner join dbequiposdelegados ed on ed.idequipodelegado = fe.refequiposdelegados
				where ed.refcountries = ".$idcountrie." and ed.idequipo = ".$idequipo;

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
					(case when est.idestado = 3 then 'Iniciado' else est.estado end) as estado,
					cat.orden,
					e.refdivisiones,
					(CASE
						WHEN est.idestado = 1 THEN 'label-info'
						WHEN est.idestado = 2 THEN 'label-warning'
						WHEN est.idestado = 3 THEN 'label-warning'
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


	function traerJugadoresprePorNroDocumento($nroDocumento) {
		$sql = "select idjugadorpre,reftipodocumentos,nrodocumento,apellido,nombres,email,fechanacimiento,fechaalta,refcountries,observaciones,refusuarios from dbjugadorespre where nrodocumento =".$nroDocumento;

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

	function existeJugadorPreTemporada($nroDocumento) {
		$sql = "select idjugadorpre from dbjugadorespre where year(fechaalta) in (2019,2018) and nrodocumento = ".$nroDocumento;
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

   function existeUsuarioPreRegistrado($email) {
   	$sql = "select idjugadorpre from dbjugadorespre where email = '".$email."'";
   	$res = $this->query($sql,0);
   	if (mysql_num_rows($res)>0) {
   		return mysql_result($res,0,0);
   	} else {
   		return '';
   	}
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

		$sql = "select r.* from (select
		j.idjugador,
		tip.tipodocumento,
		j.nrodocumento,
		j.apellido,
		j.nombres,
		j.email,
		date_format(j.fechanacimiento, '%d/%m/%Y') as fechanacimiento,
		j.fechaalta,
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
		order by jc.articulo,jc.fechabaja,concat(j.apellido, ' ', j.nombres)) as r order by r.articulo,r.fechabaja,r.apyn
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


	function traerFusionPorEquiposDelegadosAceptados($idequipodelegado) {
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
					    fe.refequiposdelegados = ".$idequipodelegado." and fe.refestados = 3";

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

	function traerJugadoresDeUnaFusion($idfusion, $idtemporada, $idcountrie) {
		$sql = "SELECT
                j.apellido, j.nombres, j.nrodocumento, j.fechanacimiento
            FROM
                dbjugadores j
                    INNER JOIN
                dbconectordelegados cd ON j.idjugador = cd.refjugadores
                    INNER JOIN
                dbequiposdelegados ed ON ed.idequipo = cd.refequipos
                    INNER JOIN
                dbfusionequipos fe ON ed.idequipodelegado = fe.refequiposdelegados
                    AND j.refcountries = fe.refcountries
            WHERE
                j.refcountries = ".$idcountrie."
                    AND cd.reftemporadas = ".$idtemporada."
                    AND fe.idfusionequipo = ".$idfusion."
            GROUP BY j.apellido , j.nombres , j.nrodocumento , j.fechanacimiento
            union all

            SELECT
                j.apellido, j.nombres, j.nrodocumento, j.fechanacimiento
            FROM
                dbjugadorespre j
                    INNER JOIN
                dbconectordelegados cd ON j.idjugadorpre = cd.refjugadorespre
                    INNER JOIN
                dbequiposdelegados ed ON ed.idequipo = cd.refequipos
                    INNER JOIN
                dbfusionequipos fe ON ed.idequipodelegado = fe.refequiposdelegados
                    AND j.refcountries = fe.refcountries
            WHERE
                j.refcountries = ".$idcountrie."
                    AND cd.reftemporadas = ".$idtemporada."
                    AND fe.idfusionequipo = ".$idfusion."
            GROUP BY j.apellido , j.nombres , j.nrodocumento , j.fechanacimiento";

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



	function enviarMailAdjuntoPlantel($idequipo,$referente,$asunto,$cuerpo, $referencia) {
		require('../reportes/fpdf.php');

		$refEquipos		=	$idequipo;



		$resTemporadas = $this->traerUltimaTemporada();

		if (mysql_num_rows($resTemporadas)>0) {
		    $ultimaTemporada = mysql_result($resTemporadas,0,0);
		} else {
		    $ultimaTemporada = 0;
		}

		$reftemporadas = $ultimaTemporada;

		/////////////////////////////  fin parametross  ///////////////////////////
		$resEquipo = $this->traerEquiposdelegadosPorEquipoTemporada($refEquipos,$reftemporadas);
		$resEquipoAux = $this->traerEquiposdelegadosPorEquipoTemporada($refEquipos,$reftemporadas);


		$resDatos = $this->traerConectorActivosPorEquiposDelegado($refEquipos, $reftemporadas, $refusuarios='');

		$resDatosNuevo = $this->traerConectorActivosPorEquiposDelegadoNuevo($refEquipos, $reftemporadas, $refusuarios='');

		$excepciones = $this->generarPlantelTemporadaAnteriorExcepcionesTodos($reftemporadas, mysql_result($resEquipoAux,0,'refcountries'), $refEquipos);

		$nombre 	= mysql_result($resEquipoAux,0,'nombre');
		$categoria = mysql_result($resEquipoAux,0,'categoria');
		$division = mysql_result($resEquipoAux,0,'division');

		$resclub = $this->traerCountriesPorId(mysql_result($resEquipoAux,0,'refcountries'));

		$nombreclub= mysql_result($resclub,0,'nombre');

		$pdf = new FPDF();


		function Footer($pdf)
		{

		$pdf->SetY(-10);

		$pdf->SetFont('Arial','I',10);

		$pdf->Cell(0,10,'Firma presidente y/o secretario: ______________________________________________  -  Pagina '.$pdf->PageNo()." - Fecha: ".date('Y-m-d'),0,0,'C');
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
			$pdf->Cell(200,5,'Lista de Buena Fe Temporada 2019 - Equipo: '.($nombre),1,0,'C',true);
			$pdf->Ln();
		   $pdf->SetX(5);
			$pdf->Cell(200,5,'Categoria: '.(mysql_result($resEquipo,0,'categoria')).' - Division: '.(mysql_result($resEquipo,0,'division')),1,0,'C',true);
			$pdf->Ln();
			$pdf->SetX(5);
			$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
			$pdf->SetFont('Arial','',10);
			$pdf->Ln();
			$pdf->Ln();
			$pdf->SetX(5);

			$pdf->SetFont('Arial','',10);
		   $pdf->Ln();
			$pdf->SetX(5);

			$pdf->SetFont('Arial','',11);
			$pdf->Cell(5,5,'',1,0,'C',true);
			$pdf->Cell(73,5,'JUGADOR',1,0,'C',true);
			$pdf->Cell(20,5,'NRO. DOC.',1,0,'C',true);
			$pdf->Cell(20,5,'TIPO JUG.',1,0,'C',true);
		   $pdf->Cell(20,5,'FEC. NAC.',1,0,'C',true);
		   $pdf->Cell(12,5,'EDAD',1,0,'C',true);
		   $pdf->Cell(50,5,'CLUB',1,0,'C',true);

			$cantPartidos = 0;
			$i=0;

			$contadorY1 = 44;
			$contadorY2 = 44;

		   $arExcepciones = array();

		   function array_column(array $input, $columnKey, $indexKey = null) {
		        $array = array();
		        foreach ($input as $value) {
		            if ( !array_key_exists($columnKey, $value)) {
		                trigger_error("Key \"$columnKey\" does not exist in array");
		                return false;
		            }
		            if (is_null($indexKey)) {
		                $array[] = $value[$columnKey];
		            }
		            else {
		                if ( !array_key_exists($indexKey, $value)) {
		                    trigger_error("Key \"$indexKey\" does not exist in array");
		                    return false;
		                }
		                if ( ! is_scalar($value[$indexKey])) {
		                    trigger_error("Key \"$indexKey\" does not contain scalar value");
		                    return false;
		                }
		                $array[$value[$indexKey]] = $value[$columnKey];
		            }
		        }
		        return $array;
		    }

		while ($rowE = mysql_fetch_array($resDatos)) {
			$i+=1;


			if ($i > 32) {
				Footer($pdf);
				$pdf->AddPage();
				$pdf->Image('../imagenes/logoparainformes.png',2,2,40);
				$pdf->SetFont('Arial','B',10);
				$pdf->Ln();
				$pdf->Ln();
				$pdf->SetY(25);
				$pdf->SetX(5);
				$pdf->Cell(200,5,($nombre),1,0,'C',true);
				$pdf->SetFont('Arial','',10);
				$pdf->Ln();
				$pdf->SetX(5);

				$i=0;

				$pdf->SetFont('Arial','',11);
				$pdf->Cell(5,5,'',1,0,'C',true);
		      $pdf->Cell(73,5,'JUGADOR',1,0,'C',true);
		   	$pdf->Cell(20,5,'NRO. DOC.',1,0,'C',true);
		   	$pdf->Cell(20,5,'TIPO JUG.',1,0,'C',true);
		      $pdf->Cell(20,5,'FEC. NAC.',1,0,'C',true);
		      $pdf->Cell(12,5,'EDAD',1,0,'C',true);
		      $pdf->Cell(50,5,'CLUB',1,0,'C',true);

			}



		   /// veo si la habilitacion ya la tenia la temporada apsada //
		   $habTemporadaPasada = $this->verificaEdadCategoriaJugadorMenor($rowE['refjugadores'], $rowE['refcategorias'], $rowE['reftipojugadores']);

		   if (count($excepciones) > 0) {
		      $excepto = array_search($rowE['nrodocumento'], array_column($excepciones, 'nrodocumento'));
		   } else {
		      $excepto = false;
		   }
		   if ($excepto !== false) {
		      array_push($arExcepciones, array('nombrecompleto' => '** '.($rowE['nombrecompleto']),
		                                       'tipojugador' => $rowE['tipojugador'],
		                                       'nrodocumento' => $rowE['nrodocumento'],
		                                       'fechanacimiento' => $rowE['fechanacimiento'],
		                                       'edad' => $rowE['edad'],
		                                       'countrie' => substr( $rowE['countrie'],0,25)));
		   } else {
		      if ($rowE['habilitacionpendiente'] == 'Si') {
		         array_push($arExcepciones, array('nombrecompleto' => '* '.($rowE['nombrecompleto']),
		                                          'tipojugador' => $rowE['tipojugador'],
		                                          'nrodocumento' => $rowE['nrodocumento'],
		                                          'fechanacimiento' => $rowE['fechanacimiento'],
		                                          'edad' => $rowE['edad'],
		                                          'countrie' => substr( $rowE['countrie'],0,25)));
		      } else {

		         $cantPartidos += 1;

		         $pdf->Ln();
		      	$pdf->SetX(5);
		      	$pdf->SetFont('Arial','',10);
		      	$pdf->Cell(5,5,$cantPartidos,1,0,'C',false);

		         $pdf->SetFont('Arial','',9);
		         $pdf->Cell(73,5,utf8_decode($rowE['nombrecompleto']),1,0,'L',false);
		      	$pdf->Cell(20,5,($rowE['nrodocumento']),1,0,'C',false);
		      	$pdf->Cell(20,5,($rowE['tipojugador']),1,0,'L',false);
		         $pdf->Cell(20,5,($rowE['fechanacimiento']),1,0,'C',false);
		         $pdf->Cell(12,5,$rowE['edad'],1,0,'C',false);
		         $pdf->Cell(50,5,substr( $rowE['countrie'],0,25) ,1,0,'L',false);

		         $contadorY1 += 4;
		      }
		   }







			//$pdf->SetY($contadorY1);


		}


		$pdf->Ln();

		$pdf->SetX(5);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(200,5,'Jugadores Nuevos',0,0,'C',false);
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetFont('Arial','',10);
		$pdf->Ln();
		$pdf->SetX(5);

		$pdf->SetFont('Arial','',11);
		$pdf->Cell(5,5,'',1,0,'C',true);
		$pdf->Cell(73,5,'JUGADOR',1,0,'C',true);
		$pdf->Cell(20,5,'NRO. DOC.',1,0,'C',true);
		$pdf->Cell(20,5,'TIPO JUG.',1,0,'C',true);
		$pdf->Cell(20,5,'FEC. NAC.',1,0,'C',true);
		$pdf->Cell(12,5,'EDAD',1,0,'C',true);
		$pdf->Cell(50,5,'CLUB',1,0,'C',true);


		while ($rowE = mysql_fetch_array($resDatosNuevo)) {
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
				$pdf->Cell(200,5,($nombre),1,0,'C',true);
				$pdf->SetFont('Arial','',10);
				$pdf->Ln();
				$pdf->SetX(5);

				$i=0;

				$pdf->SetFont('Arial','',11);
				$pdf->Cell(5,5,'',1,0,'C',true);
		      $pdf->Cell(73,5,'JUGADOR',1,0,'C',true);
		   	$pdf->Cell(20,5,'NRO. DOC.',1,0,'C',true);
		   	$pdf->Cell(20,5,'TIPO JUG.',1,0,'C',true);
		      $pdf->Cell(20,5,'FEC. NAC.',1,0,'C',true);
		      $pdf->Cell(12,5,'EDAD',1,0,'C',true);
		      $pdf->Cell(50,5,'CLUB',1,0,'C',true);

			}


			$pdf->Ln();
			$pdf->SetX(5);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(5,5,$cantPartidos,1,0,'C',false);


		   if ($rowE['habilitacionpendiente'] == 'Si') {
		      array_push($arExcepciones, array('nombrecompleto' => '* '.($rowE['nombrecompleto']),
		                                       'tipojugador' => $rowE['tipojugador'],
		                                       'nrodocumento' => $rowE['nrodocumento'],
		                                       'fechanacimiento' => $rowE['fechanacimiento'],
		                                       'edad' => $rowE['edad'],
		                                       'countrie' => substr( $rowE['countrie'],0,25)));
		   } else {
		      $pdf->Cell(73,5,utf8_decode($rowE['nombrecompleto']),1,0,'L',false);
		   	$pdf->Cell(20,5,($rowE['nrodocumento']),1,0,'C',false);
		   	$pdf->Cell(20,5,($rowE['tipojugador']),1,0,'L',false);
		      $pdf->Cell(20,5,($rowE['fechanacimiento']),1,0,'C',false);
		      $pdf->Cell(12,5,$rowE['edad'],1,0,'C',false);
		      $pdf->Cell(50,5,substr( $rowE['countrie'],0,25) ,1,0,'L',false);
		   }






			$contadorY1 += 4;

			//$pdf->SetY($contadorY1);


		}


		$pdf->Ln();



		$pdf->SetX(5);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(200,5,'Excepciones Jugadores',0,0,'C',false);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetFont('Arial','',9);
		$pdf->SetX(5);
		$pdf->Cell(200,5,utf8_decode('* Jugadores con solicitud de excepción'),0,0,'L',false);
		$pdf->Ln();
		$pdf->SetX(5);
		$pdf->Cell(200,5,utf8_decode('** Jugadores con solicitud de excepción, generada desde la temporada pasada'),0,0,'L',false);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln();
		$pdf->SetX(5);

		$pdf->SetFont('Arial','',10);
		$pdf->Ln();
		$pdf->SetX(5);

		$pdf->SetFont('Arial','',11);
		$pdf->Cell(5,5,'',1,0,'C',true);
		$pdf->Cell(73,5,'JUGADOR',1,0,'C',true);
		$pdf->Cell(20,5,'NRO. DOC.',1,0,'C',true);
		$pdf->Cell(20,5,'TIPO JUG.',1,0,'C',true);
		$pdf->Cell(20,5,'FEC. NAC.',1,0,'C',true);
		$pdf->Cell(12,5,'EDAD',1,0,'C',true);
		$pdf->Cell(50,5,'CLUB',1,0,'C',true);

		foreach ($arExcepciones as $valor) {
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
				$pdf->Cell(200,5,($nombre),1,0,'C',true);
				$pdf->SetFont('Arial','',10);
				$pdf->Ln();
				$pdf->SetX(5);

				$i=0;

				$pdf->SetFont('Arial','',10);
				$pdf->Cell(5,5,'',1,0,'C',true);
		      $pdf->Cell(73,5,'JUGADOR',1,0,'C',true);
		   	$pdf->Cell(20,5,'NRO. DOC.',1,0,'C',true);
		   	$pdf->Cell(20,5,'TIPO JUG.',1,0,'C',true);
		      $pdf->Cell(20,5,'FEC. NAC.',1,0,'C',true);
		      $pdf->Cell(12,5,'EDAD',1,0,'C',true);
		      $pdf->Cell(50,5,'CLUB',1,0,'C',true);

			}


			$pdf->Ln();
			$pdf->SetX(5);
			$pdf->SetFont('Arial','',8);
			$pdf->Cell(5,5,$cantPartidos,1,0,'C',false);

		   $pdf->Cell(73,5,utf8_decode($valor['nombrecompleto']),1,0,'L',false);
			$pdf->Cell(20,5,($valor['nrodocumento']),1,0,'C',false);
			$pdf->Cell(20,5,($valor['tipojugador']),1,0,'L',false);
		   $pdf->Cell(20,5,($valor['fechanacimiento']),1,0,'C',false);
		   $pdf->Cell(12,5,$valor['edad'],1,0,'C',false);
		   $pdf->Cell(50,5,substr( $valor['countrie'],0,25) ,1,0,'L',false);

			$contadorY1 += 4;

			//$pdf->SetY($contadorY1);


		}

		Footer($pdf);
		$pdf->AddPage();
		$pdf->Image('../imagenes/logoparainformes.png',2,2,40);
		$pdf->SetFont('Arial','B',10);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetY(25);
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Lista de Buena Fe Temporada 2019 - Equipo: '.($nombre),1,0,'C',true);
		$pdf->Ln();
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Categoria: '.(mysql_result($resEquipo,0,'categoria')).' - Division: '.(mysql_result($resEquipo,0,'division')),1,0,'C',true);
		$pdf->Ln();
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetX(5);

		$resGetAllFusiones = $this->traerFusionPorIdEquipos($refEquipos);

		if (mysql_num_rows($resGetAllFusiones) > 0) {
		   while ($rowFu = mysql_fetch_array($resGetAllFusiones)) {
		      if ($rowFu['idestado'] == 3) {
		         $countrie = $rowFu['countrie'];
		         $pdf->Ln();
		      	$pdf->SetX(5);

		      	$pdf->SetFont('Arial','',10);
		      	//$pdf->Cell(5,5,'',1,0,'C',true);
		      	$pdf->Multicell(200, 5, utf8_decode('Por medio de la presente, '.$nombreclub.' acepta la solicitud de fusión presentada por '.$countrie.' para el equipo '.$nombre.' en la categoría '.$categoria.' y división '.$division.', obligándose a respetar la normativa prevista por el reglamento interno de torneos de la AIF.'), 0, 'L', false);
		      }
		   }
		}


		Footer($pdf);
		$pdf->AddPage();
		$pdf->Image('../imagenes/logoparainformes.png',2,2,40);
		$pdf->SetFont('Arial','B',10);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetY(25);
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Lista de Buena Fe Temporada 2019 - Equipo: '.($nombre),1,0,'C',true);
		$pdf->Ln();
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Categoria: '.(mysql_result($resEquipo,0,'categoria')).' - Division: '.(mysql_result($resEquipo,0,'division')),1,0,'C',true);
		$pdf->Ln();
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
		$pdf->SetFont('Arial','',10);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetX(5);

		$pdf->SetFont('Arial','',10);
		//$pdf->Cell(5,5,'',1,0,'C',true);
		$pdf->Multicell(200, 5, utf8_decode('Certifico que los arriba Inscriptos, detallados como pertenecientes al country al cual represento, son Socios-Propietarios de Lotes del Country (titulares, cónyugues, ascendientes, descendientes o yernos únicamente), y/o jugadores que se enmarcan dentro del artículo 2 incisos "a", "b" y "d" de vuestro reglamento de torneos, estando estatutariamente habilitados para representar a la Institución en competencias deportivas. Manifiesto conocer y aceptar en todas sus partes el Reglamento de los Torneos y el Reglamento del Tribunal de Disciplina, comprometiéndose el Country al que represento, a cumplir y hacer cumplir los derechos y obligaciones obrantes en los mismos y a comunicar a la Asociación, en forma inmediata, cualquier modificación en la condición o categoría de los socios-propietarios y/o familiares inscriptos en la presente lista.'), 0, 'L', false);


		Footer($pdf);



		$nombreTurno = "LISTADEBUENAFE.pdf";

		$pdf->Output($nombreTurno,'F');

		require_once('AttachMailer.php');

		$ruta = "https://saupureinconsulting.com.ar/aifzncountries/ajax/";
		$mi_archivo = $nombreTurno;
		$mi_nombre = "AIF";
		$mi_email = $referente;
		$email_to = $referente;
		$mi_titulo = "Este es un correo con archivo adjunto";
		$mi_mensaje = $cuerpo;

		$ruta_completa = $ruta.$mi_archivo;

		//$mailer = new AttachMailer($mi_email, $email_to, "Presenta equipos", "Lista de los equipos confirmados");
		//$mailer->attachFile($ruta_completa);
		//$mailer->send() ? "Enviado": "Problema al enviar";

		$conf['to'] = $email_to;
		$conf['from'] = $mi_email;
		$conf['subject'] = 'Presenta Lista de Buena Fe';
		$conf['content'] = $cuerpo;

		//$files[] = __FILE__; //  este script!
		$files['LISTADEBUENAFE.pdf'] = 'mime/type';

		if ($this->mailto($conf, $files, true))
		{
			// ok
			return 'ok - ';
		}


	}

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

		$pdf->Cell(0,10,'Firma presidente y/o secretario: ______________________________________________  -  Pagina '.$pdf->PageNo()." - Fecha: ".date('Y-m-d'),0,0,'C');
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
		         array_push($arFusiones, array('num' => $numFusion, 'club'=> $rowF[0], 'viejo'=> $row['viejo']));
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
			if ($rowE['nuevo'] == 'Si') {
		      if (mysql_num_rows($resFusion)>0) {
		         $pdf->Cell(60,5,'* '.utf8_decode($rowE['nombre']).' ('.$numFusion.')',1,0,'C',false);
		      } else {
		         $pdf->Cell(60,5,'* '.utf8_decode($rowE['nombre']),1,0,'C',false);
		      }
		   } else {
		      if (mysql_num_rows($resFusion)>0) {
		         $pdf->Cell(60,5,utf8_decode($rowE['nombre']).' ('.$numFusion.')',1,0,'C',false);
		      } else {
		         $pdf->Cell(60,5,utf8_decode($rowE['nombre']),1,0,'C',false);
		      }
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
		$pdf->Cell(200,5,'FUSIONES SOLICITADAS',0,0,'C',false);
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

		$pdf->Output('../ajax/'.$nombreTurno,'F');

		require_once('AttachMailer.php');

		$ruta = "https://www.saupureinconsulting.com.ar/aifzncountries/ajax/";
		$mi_archivo = $nombreTurno;
		$mi_nombre = "AIF";
		$mi_email = $email;
		$email_to = $this->traerReferente($idCountries);
		$mi_titulo = "Este es un correo con archivo adjunto";
		$mi_mensaje = "Esta es el cuerpo de mensaje.";

		$ruta_completa = $ruta.$mi_archivo;

		//echo $ruta_completa;

		//$mailer = new AttachMailer($mi_email, $email_to, "Presenta equipos", "Lista de los equipos confirmados");
		//$mailer->attachFile($ruta_completa);
		//$mailer->send() ? "Enviado": "Problema al enviar";

		$conf['to'] = $email_to;
		$conf['from'] = $mi_email;
		$conf['subject'] = 'Presenta equipos';
		$conf['content'] = 'Lista de los equipos confirmados';

		//$files[] = __FILE__; //  este script!
		$files['EQUIPOSCLUB.pdf'] = 'mime/type';

		if ($this->mailto($conf, $files, true))
		{
			// ok
			return 'ok - ';
		}

		//$devuelve = $this->mail_attachment($mi_archivo, $ruta, $email_to, $mi_email, $mi_nombre, $mi_titulo, $mi_mensaje);

		//return $devuelve;
	}

	function mailto($test = array(), $add = array(), $html = false)
	{
	    //
	    $test = array_merge(array(
	            'to' => null,
	            'from' => null,
	            'reply' => null,
	            'subject' => null,
	            'content' => null
	    ), $test);

	    // en sus marcas!
	    $head = array(
	            "to: $test[to]",
	            'X-Mailer: PHP/'.phpversion(),
	            'MIME-version: 1.0'
	    );

	    $hash = md5(uniqid('PHP'));
	    $mime = $html? 'html': 'plain';
	    $content = !$html?  // limpiamos??
	            strip_tags($test['content']): $test['content'];

	    if (isset($test['from']))
	    { // origen..
	        $head[] = "from: $test[from]";
	    }
	    if (isset($test['reply']))
	    {// respuesta?
	        $head[] = "reply-to: $test[reply]";
	    }

	    // header mixto...
	    $head[] = 'content-type: multipart/mixed; boundary="mix-'.$hash.'"';

	    // body mixto...
	    $body[] = "--mix-$hash";
	    $body[] = 'content-Type: multipart/alternative; boundary="alt-'.$hash.'"';

	    $body[] = "--alt-$hash";
	    $body[] = 'content-type: text/'.$mime.'; charset="iso-8859-1"';
	    $body[] = 'content-transfer-encoding: 7bit';

	    $body[] = null; // xS
	    $body[] = $content;
	    $body[] = null;

	    $body[] = "--alt-$hash--";

	    if (!empty($add) && is_array($add))
	    {
	        foreach ($add as $key => $val)
	        { // adjuntamos...!
	            $file = is_numeric($key)? $val: $key;
	            $key = !is_numeric($key)? $val: null;

	            if (is_file($file))
	            {
	                $name = is_file($file)? basename($file): urlencode($file);
	                $mime = // establecemos tipo MIME... ?
	                        preg_match('/^[a-z]+\/[a-z0-9\+-]+$/i', $key)?
	                        $key: 'application/octet-stream';

	                $body[]="--mix-$hash";
	                $body[] = 'content-type: '.$mime.'; name="'.$name.'"';
	                $body[] = 'content-transfer-encoding: base64';
	                $body[] = 'content-disposition: attachment';

	                $body[]= null;
	                $body[]= // agregamos correctamente?
	                        chunk_split(base64_encode(file_get_contents($file)));
	                $body[]= null;
	            }
	        }
	    }
	    $body[] = "--mix-$hash--";

	    if (mail($test['to'], $test['subject'], join("\n", $body), join("\n", $head)))
	    { // ... ok!?
	        return true;
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


   function traerFixturePorId($id) {
      $sql = "select idfixture,reftorneos,reffechas,refconectorlocal,refconectorvisitante,refarbitros,juez1,juez2,refcanchas,fecha,hora,refestadospartidos,calificacioncancha,puntoslocal,puntosvisita,goleslocal,golesvisitantes,observaciones,publicar, (case when esresaltado=1 then 'Si' else 'No' end) as esresaltado,(case when esdestacado=1 then 'Si' else 'No' end) as esdestacado from dbfixture where idfixture =".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerFixtureDetallePorId($idFixture) {
      $sql = "select
      f.idfixture,
      el.nombre as equipolocal,
      f.puntoslocal,
      f.puntosvisita,
      ev.nombre as equipovisitante,
      ca.categoria,
      arb.nombrecompleto as arbitro,
      f.juez1,
      f.juez2,
      can.nombre as canchas,
      fec.fecha,
      f.fecha,
      f.hora,
      est.descripcion as estado,
      f.calificacioncancha,
      f.goleslocal,
      f.golesvisitantes,
      f.observaciones,
      f.publicar,
      ti.tipotorneo,
      te.temporada,
      di.division,
      f.refcanchas,
      f.reftorneos,
      f.reffechas,
      f.refconectorlocal,
      f.refconectorvisitante,
      f.refestadospartidos,
      f.refarbitros,
      (case when tor.respetadefiniciontipojugadores = 1 then 'Si' else 'No' end) as respetadefiniciontipojugadores,
      (case when tor.respetadefinicionhabilitacionestransitorias = 1 then 'Si' else 'No' end) as respetadefinicionhabilitacionestransitorias,
      (case when tor.respetadefinicionsancionesacumuladas = 1 then 'Si' else 'No' end) as respetadefinicionsancionesacumuladas,
      (case when tor.acumulagoleadores = 1 then 'Si' else 'No' end) as acumulagoleadores,
      (case when tor.acumulatablaconformada = 1 then 'Si' else 'No' end) as acumulatablaconformada,
      tor.descripcion

      from dbfixture f
      inner join dbtorneos tor ON tor.idtorneo = f.reftorneos
      inner join tbtipotorneo ti ON ti.idtipotorneo = tor.reftipotorneo
      inner join tbtemporadas te ON te.idtemporadas = tor.reftemporadas
      inner join tbcategorias ca ON ca.idtcategoria = tor.refcategorias
      inner join tbdivisiones di ON di.iddivision = tor.refdivisiones
      inner join tbfechas fec ON fec.idfecha = f.reffechas
      inner join dbequipos el ON el.idequipo = f.refconectorlocal
      inner join dbequipos ev ON ev.idequipo = f.refconectorvisitante
      left join dbarbitros arb ON arb.idarbitro = f.refarbitros
      left join tbcanchas can ON can.idcancha = f.refcanchas
      left join tbestadospartidos est ON est.idestadopartido = f.refestadospartidos
      where f.idfixture = ".$idFixture;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerFechasPorId($id) {
      $sql = "select idfecha,fecha from tbfechas where idfecha =".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerEquiposPorId($id) {
      $sql = "select idequipo,refcountries,nombre,refcategorias,refdivisiones,refcontactos,fechaalta,fachebaja,(case when activo = 1 then 'Si' else 'No' end) as activo from dbequipos where idequipo =".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerTorneosPorId($id) {
      $sql = "select idtorneo,descripcion,reftipotorneo,reftemporadas,refcategorias,refdivisiones,cantidadascensos,cantidaddescensos,
      (case when respetadefiniciontipojugadores = 1 then 'Si' else 'No' end) as respetadefiniciontipojugadores,
      (case when respetadefinicionhabilitacionestransitorias = 1 then 'Si' else 'No' end) as respetadefinicionhabilitacionestransitorias,
      (case when respetadefinicionsancionesacumuladas = 1 then 'Si' else 'No' end) as respetadefinicionsancionesacumuladas,
      (case when acumulagoleadores = 1 then 'Si' else 'No' end) as acumulagoleadores,
      (case when acumulatablaconformada = 1 then 'Si' else 'No' end) as acumulatablaconformada,
      observaciones,
      (case when activo = 1 then 'Si' else 'No' end) as activo,
      observacionesgenerales from dbtorneos where idtorneo =".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerFechasFixturePorTorneoEquipoLocal($idTorneo, $idEquipos) {
      $sql = "select
      f.idfixture,
      fec.fecha,
      f.reffechas
      from dbfixture f
      inner join dbtorneos tor ON tor.idtorneo = f.reftorneos
      inner join tbtipotorneo ti ON ti.idtipotorneo = tor.reftipotorneo
      inner join tbtemporadas te ON te.idtemporadas = tor.reftemporadas
      inner join tbcategorias ca ON ca.idtcategoria = tor.refcategorias
      inner join tbdivisiones di ON di.iddivision = tor.refdivisiones
      inner join tbfechas fec ON fec.idfecha = f.reffechas
      inner join dbequipos el ON el.idequipo = f.refconectorlocal
      left join dbarbitros arb ON arb.idarbitro = f.refarbitros
      left join tbcanchas can ON can.idcancha = f.refcanchas
      left join tbestadospartidos est ON est.idestadopartido = f.refestadospartidos
      where tor.idtorneo = ".$idTorneo." and f.refconectorlocal = ".$idEquipos." and f.refconectorlocal > 0
      order by f.reffechas";

      $res = $this->query($sql,0);
      return $res;
   }

   function traerFechasFixturePorTorneoEquipoVisitante($idTorneo, $idEquipos) {
      $sql = "select
      f.idfixture,
      fec.fecha,
      f.reffechas
      from dbfixture f
      inner join dbtorneos tor ON tor.idtorneo = f.reftorneos
      inner join tbtipotorneo ti ON ti.idtipotorneo = tor.reftipotorneo
      inner join tbtemporadas te ON te.idtemporadas = tor.reftemporadas
      inner join tbcategorias ca ON ca.idtcategoria = tor.refcategorias
      inner join tbdivisiones di ON di.iddivision = tor.refdivisiones
      inner join tbfechas fec ON fec.idfecha = f.reffechas
      inner join dbequipos ev ON ev.idequipo = f.refconectorvisitante
      left join dbarbitros arb ON arb.idarbitro = f.refarbitros
      left join tbcanchas can ON can.idcancha = f.refcanchas
      left join tbestadospartidos est ON est.idestadopartido = f.refestadospartidos
      where tor.idtorneo = ".$idTorneo." and f.refconectorvisitante = ".$idEquipos." and f.refconectorvisitante > 0
      order by f.reffechas";

      $res = $this->query($sql,0);
      return $res;
   }

   function traerDefinicionescategoriastemporadasPorTemporadaCategoria($idTemporada, $idCategoria) {
      $sql = "select
      d.iddefinicioncategoriatemporada,
      cat.categoria,
      tem.temporada,
      d.cantmaxjugadores,
      d.cantminjugadores,
      di.dia,
      d.hora,
      d.minutospartido,
      d.cantidadcambiosporpartido,
      d.conreingreso,
      d.observaciones,
      d.refcategorias,
      d.reftemporadas,
      d.refdias,
      (case when d.conreingreso = 1 then 'Si' else 'No' end) as reingreso
      from dbdefinicionescategoriastemporadas d
      inner join tbcategorias cat ON cat.idtcategoria = d.refcategorias
      inner join tbtemporadas tem ON tem.idtemporadas = d.reftemporadas
      inner join tbdias di ON di.iddia = d.refdias
      where cat.idtcategoria = ".$idCategoria." and tem.idtemporadas = ".$idTemporada;

      $res = $this->query($sql,0);
      return $res;
   }


   function traerConectorActivosPorEquiposCategorias($refEquipos, $idCategoria) {

      $refTemporada = $this->traerUltimaTemporada();

      if (mysql_num_rows($refTemporada)>0) {
      	$idTemporada = mysql_result($refTemporada,0,0);
      } else {
      	$idTemporada = 0;
      }

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
          (case when jug.fechabaja = '0000-00-00' then '1900-01-01' else coalesce(jug.fechabaja,'1900-01-01') end) as fechabaja,
       (case when ce.idexcepcionencancha is null then 1 else 2 end) as orden

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
              left join
          dbcontactos con ON con.idcontacto = equ.refcontactos
              inner join
          tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
              inner join
          tbcategorias cat ON cat.idtcategoria = c.refcategorias
              left JOIN
         dbexcepcionesencancha ce ON ce.refequipos = equ.idequipo
         and ce.refjugadores = jug.idjugador
         and ce.reftemporadas = ".$idTemporada."
          where equ.idequipo = ".$refEquipos." and c.activo = 1 and c.refcategorias = ".$idCategoria."
      order by orden, concat(jug.apellido,', ',jug.nombres)";

      $res = $this->query($sql,0);
      return $res;
   }

   function traerSancionesjugadoresPorFixtureEquipoTotales($idFixture, $idEquipo) {
      $sql = "select
      equ.nombre as equipo,
      coalesce((case when p.reftiposanciones = 1 then sum(p.cantidad) end),0) as amarillas,
      coalesce((case when p.reftiposanciones = 2 then sum(p.cantidad) end),0) as rojas,
      coalesce((case when p.reftiposanciones = 3 then sum(p.cantidad) end),0) as informados,
      coalesce((case when p.reftiposanciones = 4 then sum(p.cantidad) end),0) as dobleamarilla,
      coalesce((case when p.reftiposanciones = 5 then sum(p.cantidad) end),0) as cdtd
      from dbsancionesjugadores p
      inner join tbtiposanciones tip ON tip.idtiposancion = p.reftiposanciones
      inner join dbfixture fix ON fix.idfixture = p.reffixture
      inner join dbtorneos tor ON tor.idtorneo = fix.reftorneos
      inner join tbfechas fe ON fe.idfecha = fix.reffechas
      left join tbestadospartidos es ON es.idestadopartido = fix.refestadospartidos
      inner join dbequipos equ ON equ.idequipo = p.refequipos
      inner join dbcountries cou ON cou.idcountrie = equ.refcountries
      inner join tbcategorias cat ON cat.idtcategoria = p.refcategorias
      inner join tbdivisiones divi ON divi.iddivision = p.refdivisiones
      where fix.idfixture =".$idFixture." and equ.idequipo = ".$idEquipo;

      $res = $this->query($sql,0);
      return $res;
   }

   function existe($sql) {

       $res = $this->query($sql,0);

       if (mysql_num_rows($res)>0) {
           return 1;
       }
       return 0;
   }

   function traerEstadospartidos() {
      $sql = "select
      e.idestadopartido,
      e.descripcion,
      e.defautomatica,
      e.goleslocalauto,
      e.goleslocalborra,
      e.golesvisitanteauto,
      e.golesvisitanteborra,
      e.puntoslocal,
      e.puntosvisitante,
      e.finalizado,
      e.ocultardetallepublico,
      e.visibleparaarbitros
      ,e.contabilizalocal,e.contabilizavisitante
      from tbestadospartidos e
      order by 1";

      $res = $this->query($sql,0);
      return $res;
   }

   function traerCanchas() {
      $sql = "select
      c.idcancha,
      coalesce(cou.nombre,'Libre') as countrie,
      c.nombre,
      c.refcountries
      from tbcanchas c
      left join dbcountries cou ON cou.idcountrie = c.refcountries
      order by c.nombre";

      $res = $this->query($sql,0);
      return $res;
   }

   function traerArbitros() {
      $sql = "select
      a.idarbitro,
      a.nombrecompleto,
      a.telefonoparticular,
      a.telefonoceleluar,
      a.telefonolaboral,
      a.telefonofamiliar,
      a.email
      from dbarbitros a
      order by 1";

      $res = $this->query($sql,0);
      return $res;
   }

   function traerCambiosPorFixtureEquipo($idFixture, $idEquipo) {
   $sql = "select
   c.idcambio,
   c.refdorsalsale,
   c.refdorsalentra,
   c.reffixture,
   c.refequipos,
   c.refcategorias,
   c.refdivisiones,
   c.minuto
   from dbcambios c
   inner join dbfixture fix ON fix.idfixture = c.reffixture
   inner join dbequipos equ ON equ.idequipo = c.refequipos
   inner join tbcategorias cat ON cat.idtcategoria = c.refcategorias
   inner join tbdivisiones divi ON divi.iddivision = c.refdivisiones
   where c.reffixture = ".$idFixture." and c.refequipos = ".$idEquipo."
   order by 1";
   $res = $this->query($sql,0);
   return $res;
   }

   function traerEstadisticaPorFixtureJugadorCategoriaDivision($idJugador, $idFixture, $idCategoria, $idDivision) {

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
       coalesce(minj.minutos,-1) as minutosjugados,
       (case when coalesce(mj.idmejorjugador,0) > 0 then 'Si' else 'No' end) as mejorjugador,
       coalesce(gol.goles,0) as goles,
       coalesce(gol.encontra,0) as encontra,
       coalesce(pen.penalconvertido,0) as penalconvertido,
       coalesce(pen.penalerrado,0) as penalerrado,
       coalesce(pen.penalatajado,0) as penalatajado,
       coalesce(dor.numero,0) as dorsal
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
           left join
       dbcontactos con ON con.idcontacto = equ.refcontactos
           inner join
       tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
           inner join
       tbcategorias cat ON cat.idtcategoria = c.refcategorias
           inner join
       dbfixture fix ON fix.refconectorlocal = equ.idequipo
           left join
       dbmejorjugador mj
       ON  mj.reffixture = fix.idfixture
           and mj.refjugadores = jug.idjugador
           and mj.refcategorias = cat.idtcategoria
           and mj.refdivisiones = di.iddivision
           LEFT JOIN
       dbminutosjugados minj
       ON  minj.reffixture = fix.idfixture
           and minj.refjugadores = jug.idjugador
           and minj.refcategorias = cat.idtcategoria
           and minj.refdivisiones = di.iddivision
           LEFT JOIN
       dbgoleadores gol
       ON  gol.reffixture = fix.idfixture
           and gol.refjugadores = jug.idjugador
           and gol.refcategorias = cat.idtcategoria
           and gol.refdivisiones = di.iddivision
           LEFT JOIN
       dbpenalesjugadores pen
       ON  pen.reffixture = fix.idfixture
           and pen.refjugadores = jug.idjugador
           and pen.refcategorias = cat.idtcategoria
           and pen.refdivisiones = di.iddivision
           LEFT JOIN
       dbdorsales dor
       ON  dor.reffixture = fix.idfixture
           and dor.refjugadores = jug.idjugador
           and dor.refcategorias = cat.idtcategoria
           and dor.refdivisiones = di.iddivision
       where jug.idjugador = ".$idJugador." and fix.idfixture = ".$idFixture." and c.refcategorias = ".$idCategoria." and di.iddivision = ".$idDivision;
    $res = $this->query($sql,0);


    return $res;

}


function traerEstadisticaPorFixtureJugadorCategoriaDivisionVisitante($idJugador, $idFixture, $idCategoria, $idDivision) {


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
    coalesce(minj.minutos,-1) as minutosjugados,
    (case when coalesce(mj.idmejorjugador,0) > 0 then 'Si' else 'No' end) as mejorjugador,
    coalesce(gol.goles,0) as goles,
    coalesce(gol.encontra,0) as encontra,
    coalesce(pen.penalconvertido,0) as penalconvertido,
    coalesce(pen.penalerrado,0) as penalerrado,
    coalesce(pen.penalatajado,0) as penalatajado,
    coalesce(dor.numero,0) as dorsal
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
        left join
    dbcontactos con ON con.idcontacto = equ.refcontactos
        inner join
    tbposiciontributaria po ON po.idposiciontributaria = co.refposiciontributaria
        inner join
    tbcategorias cat ON cat.idtcategoria = c.refcategorias
        inner join
    dbfixture fix ON fix.refconectorvisitante = equ.idequipo
        left join
    dbmejorjugador mj
    ON  mj.reffixture = fix.idfixture
        and mj.refjugadores = jug.idjugador
        and mj.refcategorias = cat.idtcategoria
        and mj.refdivisiones = di.iddivision
        LEFT JOIN
    dbminutosjugados minj
    ON  minj.reffixture = fix.idfixture
        and minj.refjugadores = jug.idjugador
        and minj.refcategorias = cat.idtcategoria
        and minj.refdivisiones = di.iddivision
        LEFT JOIN
    dbgoleadores gol
    ON  gol.reffixture = fix.idfixture
        and gol.refjugadores = jug.idjugador
        and gol.refcategorias = cat.idtcategoria
        and gol.refdivisiones = di.iddivision
        LEFT JOIN
    dbpenalesjugadores pen
    ON  pen.reffixture = fix.idfixture
        and pen.refjugadores = jug.idjugador
        and pen.refcategorias = cat.idtcategoria
        and pen.refdivisiones = di.iddivision
        LEFT JOIN
    dbdorsales dor
    ON  dor.reffixture = fix.idfixture
        and dor.refjugadores = jug.idjugador
        and dor.refcategorias = cat.idtcategoria
        and dor.refdivisiones = di.iddivision
    where jug.idjugador = ".$idJugador." and fix.idfixture = ".$idFixture." and c.refcategorias = ".$idCategoria." and di.iddivision = ".$idDivision;
    $res = $this->query($sql,0);
    return $res;
}

function traerSancionesjugadoresPorJugadorConValor($idJugador, $idFixture, $idCategorias, $idDivision, $idTipoSancion) {
$sql = "select idsancionjugador,reftiposanciones,refjugadores,refequipos,reffixture,fecha,cantidad,refcategorias,refdivisiones,refsancionesfallos from dbsancionesjugadores where refjugadores =".$idJugador." and reffixture =".$idFixture." and refcategorias = ".$idCategorias." and refdivisiones =".$idDivision." and reftiposanciones =".$idTipoSancion;

$res = $this->query($sql,0);
if (mysql_num_rows($res)>0) {
     return mysql_result($res,0,'cantidad');
}

return 0;
}

function suspendidoPorDias($idJugador, $idTipoTorneo) {
    $sql = "select
            p.idsancionjugador
        from dbsancionesjugadores p
        inner join dbsancionesfallos sf ON sf.idsancionfallo = p.refsancionesfallos
        inner join tbtiposanciones tip ON tip.idtiposancion = p.reftiposanciones
        inner join dbjugadores jug ON jug.idjugador = p.refjugadores
        inner join tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
        inner join dbcountries co ON co.idcountrie = jug.refcountries
        inner join dbfixture fix ON fix.idfixture = p.reffixture
        inner join dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo = ".$idTipoTorneo."
        inner join tbfechas fe ON fe.idfecha = fix.reffechas
        inner join tbestadospartidos es ON es.idestadopartido = fix.refestadospartidos
        inner join dbequipos equ ON equ.idequipo = p.refequipos
        inner join dbcountries cou ON cou.idcountrie = equ.refcountries
        inner join tbcategorias cat ON cat.idtcategoria = p.refcategorias
        inner join tbdivisiones divi ON divi.iddivision = p.refdivisiones
        where jug.idjugador =".$idJugador." and ('".date('Y-m-d')."' between sf.fechadesde and sf.fechahasta and sf.fechadesde <> '1900-01-01')";

        $res = $this->query($sql,0);

        if (mysql_num_rows($res)>0) {
            return 1;
        }
        return 0;
}


function hayMovimientos($idJugador, $idFixture, $idTipoTorneo) {

    if (($idTipoTorneo == 1) || ($idTipoTorneo == 2)) {
        $sql = "SELECT
                coalesce(sf.cantidadfechas -  coalesce(sfc.cumplidas,0),0) as faltan
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallos sf ON sf.idsancionfallo = san.refsancionesfallos
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture." AND fix.fecha > san.fecha
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo in (1,2)
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos
                    left join
                (SELECT
                      fc.refsancionesfallos,
                      sj.refcategorias,
                      COALESCE(COUNT(*), 0) AS cumplidas
                  FROM
                      dbsancionesfechascumplidas fc
                          INNER JOIN
                      dbfixture fixf ON fixf.idfixture = fc.reffixture
                          INNER JOIN
                      dbtorneos torc ON torc.idtorneo = fixf.reftorneos
                  		inner join
                  	dbsancionesfallos sf ON sf.idsancionfallo = fc.refsancionesfallos
                  		inner join
                  	dbsancionesjugadores sj ON sj.idsancionjugador = sf.refsancionesjugadores
                  where fc.cumplida = 1
                  GROUP BY fc.refsancionesfallos , sj.refcategorias) sfc
                ON  sfc.refsancionesfallos = sf.idsancionfallo and sfc.refcategorias = san.refcategorias
            WHERE
                ju.idjugador = ".$idJugador."
                    AND tip.cumpletodascategorias = 1
                    AND (coalesce(sf.fechascumplidas,0) + coalesce(sfc.cumplidas,0)) < sf.cantidadfechas
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.fecha > san.fecha end)";
    } else {
        $sql = "SELECT
                coalesce(sf.cantidadfechas -  coalesce(sfc.cumplidas,0),0) as faltan
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallos sf ON sf.idsancionfallo = san.refsancionesfallos
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture." AND fix.fecha > san.fecha
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo = ".$idTipoTorneo."
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos
                    left join
                (SELECT
                      fc.refsancionesfallos,
                      sj.refcategorias,
                      COALESCE(COUNT(*), 0) AS cumplidas
                  FROM
                      dbsancionesfechascumplidas fc
                          INNER JOIN
                      dbfixture fixf ON fixf.idfixture = fc.reffixture
                          INNER JOIN
                      dbtorneos torc ON torc.idtorneo = fixf.reftorneos
                  		inner join
                  	dbsancionesfallos sf ON sf.idsancionfallo = fc.refsancionesfallos
                  		inner join
                  	dbsancionesjugadores sj ON sj.idsancionjugador = sf.refsancionesjugadores
                  where fc.cumplida = 1
                  GROUP BY fc.refsancionesfallos , sj.refcategorias) sfc
                ON  sfc.refsancionesfallos = sf.idsancionfallo and sfc.refcategorias = san.refcategorias
            WHERE
                ju.idjugador = ".$idJugador."
                    AND tip.cumpletodascategorias = 1
                    AND sf.fechascumplidas <> sf.cantidadfechas
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";
    }


    return $this->existeDevuelveId($sql);
}

function hayMovimientosAmarillasAcumuladas($idJugador, $idFixture, $idCategoria, $idTipoTorneo) {
    $sql = "SELECT
                coalesce(sf.cantidadfechas - sf.fechascumplidas,0) as faltan
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallosacumuladas sf ON sf.refsancionesjugadores = san.idsancionjugador
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture." AND fix.fecha > san.fecha
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and san.refcategorias = tor.refcategorias
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos and torv.reftipotorneo in (1,2)
            WHERE
                ju.idjugador = ".$idJugador."
                    AND tor.refcategorias = ".$idCategoria."
                    AND sf.generadaporacumulacion = 1
                    and sf.fechascumplidas = 0
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";


    return $this->existeDevuelveId($sql);
}

function traerSancionesjugadoresPorJugadorFixtureConValor($idJugador, $idFixture) {
    $sql = "select idsancionjugador,reftiposanciones,refjugadores,refequipos,reffixture,fecha,cantidad,refcategorias,refdivisiones,refsancionesfallos from dbsancionesjugadores where refjugadores =".$idJugador." and (refsancionesfallos is not null and refsancionesfallos <> 0) and reffixture =".$idFixture;

    $res = $this->query($sql,0);
    if (mysql_num_rows($res)>0) {
        return mysql_result($res,0,'idsancionjugador');
    }

    return 0;
}


function hayPendienteDeFallo($idJugador, $idFixture, $idTipoTorneo) {
    $sql = "SELECT
                coalesce(1,0) as faltan
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallos sf ON sf.idsancionfallo = san.refsancionesfallos
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture." AND fix.fecha > san.fecha
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos and torv.reftipotorneo = ".$idTipoTorneo."
                    left join
                (select fc.refsancionesfallos,torc.refcategorias, count(*) as cumplidas
                    from dbsancionesfechascumplidas fc
                    inner join dbfixture fixf on fixf.idfixture = fc.reffixture
                    inner join dbtorneos torc on torc.idtorneo = fixf.reftorneos
                    group by fc.refsancionesfallos,torc.refcategorias) sfc
                ON  sfc.refsancionesfallos = sf.idsancionfallo and sfc.refcategorias = san.refcategorias
            WHERE
                ju.idjugador = ".$idJugador."
                    AND sf.pendientesfallo = 1
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";

    return $this->existeDevuelveId($sql);
}

function estaFechaYaFueCumplida($idJugador, $idFixture) {
    $sql = "select * from dbsancionesfechascumplidas where reffixture = ".$idFixture." and refjugadores = ".$idJugador." and cumplida = 1";

    return $this->existe($sql);
}

function modificarFixturePorCancha($id,$refCanchas, $refArbitros, $juez1, $juez2, $calificacioncancha) {
$sql = "update dbfixture
set
refcanchas = ".($refCanchas == '' ? 'null' : $refCanchas).",
refarbitros = ".$refArbitros.",
juez1 = '".$juez1."',
juez2 = '".$juez2."',
calificacioncancha = ".($calificacioncancha == '' ? 'null' : $calificacioncancha)."
where idfixture =".$id;
$res = $this->query($sql,0);
return $res;
}

function existeFixturePorGoleadores($idJugador, $idFixture) {
    $sql = "select * from dbgoleadores where refjugadores =".$idJugador." and reffixture =".$idFixture;

    return $this->existeDevuelveId($sql);
}

function insertarGoleadores($refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$goles,$encontra) {
$sql = "insert into dbgoleadores(idgoleador,refjugadores,reffixture,refequipos,refcategorias,refdivisiones,goles,encontra)
values ('',".$refjugadores.",".$reffixture.",".$refequipos.",".$refcategorias.",".$refdivisiones.",".$goles.",".$encontra.")";
$res = $this->query($sql,1);
return $res;
}


function modificarGoleadores($id,$refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$goles,$encontra) {
$sql = "update dbgoleadores
set
refjugadores = ".$refjugadores.",reffixture = ".$reffixture.",refequipos = ".$refequipos.",refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones.",goles = ".$goles.",encontra = ".$encontra."
where idgoleador =".$id;
$res = $this->query($sql,0);
return $res;
}


function existeFixturePorPenalesJugador($idJugador, $idFixture) {
    $sql = "select * from dbpenalesjugadores where refjugadores =".$idJugador." and reffixture =".$idFixture;

    return $this->existeDevuelveId($sql);
}

function insertarPenalesjugadores($refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$penalconvertido,$penalerrado,$penalatajado) {
$sql = "insert into dbpenalesjugadores(idpenaljugador,refjugadores,reffixture,refequipos,refcategorias,refdivisiones,penalconvertido,penalerrado,penalatajado)
values ('',".$refjugadores.",".$reffixture.",".$refequipos.",".$refcategorias.",".$refdivisiones.",".$penalconvertido.",".$penalerrado.",".$penalatajado.")";
$res = $this->query($sql,1);
return $res;
}


function modificarPenalesjugadores($id,$refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$penalconvertido,$penalerrado,$penalatajado) {
$sql = "update dbpenalesjugadores
set
refjugadores = ".$refjugadores.",reffixture = ".$reffixture.",refequipos = ".$refequipos.",refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones.",penalconvertido = ".$penalconvertido.",penalerrado = ".$penalerrado.",penalatajado = ".$penalatajado."
where idpenaljugador =".$id;
$res = $this->query($sql,0);
return $res;
}


function existeFixturePorDorsalesJugador($idJugador, $idFixture) {
    $sql = "select * from dbdorsales where refjugadores =".$idJugador." and reffixture =".$idFixture;

    return $this->existeDevuelveId($sql);
}


function insertarDorsales($refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$numero) {
$sql = "insert into dbdorsales(iddorsal,refjugadores,reffixture,refequipos,refcategorias,refdivisiones,numero)
values ('',".$refjugadores.",".$reffixture.",".$refequipos.",".$refcategorias.",".$refdivisiones.",".$numero.")";
$res = $this->query($sql,1);
return $res;
}


function modificarDorsales($id,$refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$numero) {
$sql = "update dbdorsales
set
refjugadores = ".$refjugadores.",reffixture = ".$reffixture.",refequipos = ".$refequipos.",refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones.",numero = ".$numero."
where iddorsal =".$id;
$res = $this->query($sql,0);
return $res;
}


function existeFixturePorMinutosJugados($idJugador, $idFixture) {
    $sql = "select * from dbminutosjugados where refjugadores =".$idJugador." and reffixture =".$idFixture;

    return $this->existeDevuelveId($sql);
}

function insertarMinutosjugados($refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$minutos) {
$sql = "insert into dbminutosjugados(idminutojugado,refjugadores,reffixture,refequipos,refcategorias,refdivisiones,minutos)
values ('',".$refjugadores.",".$reffixture.",".$refequipos.",".$refcategorias.",".$refdivisiones.",".$minutos.")";
$res = $this->query($sql,1);
return $res;
}


function modificarMinutosjugados($id,$refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones,$minutos) {
$sql = "update dbminutosjugados
set
refjugadores = ".$refjugadores.",reffixture = ".$reffixture.",refequipos = ".$refequipos.",refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones.",minutos = ".$minutos."
where idminutojugado =".$id;
$res = $this->query($sql,0);
return $res;
}

function eliminarMejorjugadorPorJugadorFixture($idJugador, $idFixture) {
$sql = "delete from dbmejorjugador where refjugadores = ".$idJugador." and reffixture = ".$idFixture;
$res = $this->query($sql,0);
return $res;
}

function insertarMejorjugador($refjugadores,$reffixture,$refequipos,$refcategorias,$refdivisiones) {
$sql = "insert into dbmejorjugador(idmejorjugador,refjugadores,reffixture,refequipos,refcategorias,refdivisiones)
values ('',".$refjugadores.",".$reffixture.",".$refequipos.",".$refcategorias.",".$refdivisiones.")";
$res = $this->query($sql,1);
return $res;
}

function existeFixturePorSanciones($idJugador, $idTipoSancion, $idFixture) {
    $sql = "select * from dbsancionesjugadores where refjugadores =".$idJugador." and reffixture =".$idFixture." and reftiposanciones =".$idTipoSancion;

    return $this->existeDevuelveId($sql);
}

function insertarSancionesjugadores($reftiposanciones,$refjugadores,$refequipos,$reffixture,$fecha,$cantidad,$refcategorias,$refdivisiones,$refsancionesfallos) {
$sql = "insert into dbsancionesjugadores(idsancionjugador,reftiposanciones,refjugadores,refequipos,reffixture,fecha,cantidad,refcategorias,refdivisiones,refsancionesfallos)
values ('',".$reftiposanciones.",".$refjugadores.",".$refequipos.",".$reffixture.",'".utf8_decode($fecha)."',".$cantidad.",".$refcategorias.",".$refdivisiones.",".$refsancionesfallos.")";
$res = $this->query($sql,1);
return $res;
}

function modificarSancionesjugadoresSinAlterarFallo($id,$reftiposanciones,$refjugadores,$refequipos,$reffixture,$fecha,$cantidad,$refcategorias,$refdivisiones) {
$sql = "update dbsancionesjugadores
set
reftiposanciones = ".$reftiposanciones.",refjugadores = ".$refjugadores.",refequipos = ".$refequipos.",reffixture = ".$reffixture.",fecha = '".utf8_decode($fecha)."',cantidad = ".$cantidad.",refcategorias = ".$refcategorias.",refdivisiones = ".$refdivisiones."
where idsancionjugador =".$id;
$res = $this->query($sql,0);
return $res;
}

function eliminarMovimientosancionesPorSancionJugadorAcumuadasAmarillas($idSancionJugador) {
$sql = "delete from dbmovimientosanciones where refsancionesjugadores =".$idSancionJugador." and orden = 2";
$res = $this->query($sql,0);
return $res;
}

function eliminarSancionesfallosacumuladasPorIdSancionJugador($id) {

$sqlId = "select idsancionfalloacumuladas from dbsancionesfallosacumuladas where refsancionesjugadores =".$id;
$resId = $this->query($sqlId,0);

$sqlFechas = "delete from dbsancionesfechascumplidas where refsancionesfallosacumuladas =".mysql_result($resId,0,0);
$resEliminar = $this->query($sqlFechas,0);

$sql = "delete from dbsancionesfallosacumuladas where refsancionesjugadores =".$id;
$res = $this->query($sql,0);
return $res;
}

function eliminarSancionesjugadores($id) {

$sql = "delete from dbsancionesjugadores where idsancionjugador =".$id;
$res = $this->query($sql,0);
return $res;
}



// esta funcion me devuelve donde fue sancionado por ultima vez
function ultimaFechaSancionadoPorAcumulacionAmarillas($idTorneo, $idJugador, $idTipoTorneo) {

    $resTorneo = $this->traerTorneosPorId($idTorneo);

    $idTemporada = mysql_result($resTorneo, 0,'reftemporadas');
    $idCategoria = mysql_result($resTorneo, 0,'refcategorias');
    $iddivision  = mysql_result($resTorneo, 0,'refdivisiones');

    $sql    =   "select
                    max(coalesce(fixc.fecha,fix.fecha)) as reffechas
                from        dbsancionesjugadores sj
                inner
                join        dbfixture fix
                on          fix.idfixture = sj.reffixture
                inner
                join        dbsancionesfallosacumuladas ms
                on          ms.refsancionesjugadores = sj.idsancionjugador
                left
                join        dbsancionesfechascumplidas sc
                on          sc.refsancionesfallosacumuladas = ms.idsancionfalloacumuladas
                left
                join        dbfixture fixc
                on          fixc.idfixture = sc.reffixture
                inner
                join        dbtorneos tor
                on          tor.idtorneo = fix.reftorneos and tor.reftipotorneo in (1,2)
                where       ms.generadaporacumulacion = 1
                            and sj.refjugadores = ".$idJugador."
                            and tor.reftemporadas = ".(integer)$idTemporada."
                            and tor.refcategorias = ".(integer)$idCategoria."
                            and tor.refdivisiones = ".(integer)$iddivision." ";

    return $this->existeDevuelveId($sql);
}


//esta funcion me devuelve la fecha en la cual fue fallada la suspencion, no donde fue cumplida.
function ultimaFechaSancionadoPorAcumulacionAmarillasFallada($idTorneo, $idJugador, $idTipoTorneo) {
    $resTorneo = $this->traerTorneosPorId($idTorneo);

    $idTemporada = mysql_result($resTorneo, 0,'reftemporadas');
    $idCategoria = mysql_result($resTorneo, 0,'refcategorias');
    $iddivision  = mysql_result($resTorneo, 0,'refdivisiones');


    $sql    =   "select
                    max(ms.amarillas) as amarillas
                from        dbsancionesjugadores sj
                inner
                join        dbsancionesfallos sf
                on          sj.refsancionesfallos = sf.idsancionfallo
                inner
                join        dbfixture fix
                on          fix.idfixture = sj.reffixture
                inner
                join        dbsancionesfallosacumuladas ms
                on          ms.refsancionesjugadores = sj.idsancionjugador
                inner
                join        dbsancionesfechascumplidas sc
                on          sc.refsancionesfallosacumuladas = ms.idsancionfalloacumuladas
                inner
                join        dbfixture fixc
                on          fixc.idfixture = sc.reffixture
                inner
                join        dbtorneos tor
                on          tor.idtorneo = fix.reftorneos and tor.reftipotorneo in (1,2)
                where       ms.generadaporacumulacion = 1
                            and ms.fechascumplidas = 1
                            and sj.refjugadores = ".$idJugador."
                            and tor.reftemporadas = ".(integer)$idTemporada."
                            and tor.refcategorias = ".(integer)$idCategoria."
                            and tor.refdivisiones = ".(integer)$iddivision." ";

    return $this->existeDevuelveId($sql);
}


function traerRemanente($idJugador, $idTemporada, $idCategoria, $iddivision, $reffechaDesde, $reffechaHasta) {
    $sql = "select
                sum(coalesce((cantidad),0)) as cantidad
            from (
                select
                    1 as cantidad
                from        dbsancionesjugadores sj
                inner
                join        dbfixture fix
                on          fix.idfixture = sj.reffixture and (fix.fecha between '".$reffechaDesde."' and '".$reffechaHasta."') and sj.refjugadores = ".$idJugador."
                inner
                join        tbtiposanciones ts
                on          ts.idtiposancion = sj.reftiposanciones
                inner
                join        dbtorneos t
                on          t.reftemporadas = ".$idTemporada." and t.refcategorias = ".$idCategoria." and t.refdivisiones = ".$iddivision." and sj.refcategorias = t.refcategorias and t.idtorneo = fix.reftorneos and t.reftipotorneo in (1,2)
                where       ts.amonestacion = 1
                            and sj.cantidad > 0

                union all

                select
                    2 as cantidad
                from        dbsancionesjugadores sj
                inner
                join        dbsancionesfallos sf
                on          sj.refsancionesfallos = sf.idsancionfallo and sj.refjugadores = ".$idJugador."
                inner
                join        dbfixture fix
                on          fix.idfixture = sj.reffixture and (fix.fecha between '".$reffechaDesde."' and '".$reffechaHasta."')
                inner
                join        dbtorneos t
                on          t.reftemporadas = ".$idTemporada." and t.refcategorias = ".$idCategoria." and t.refdivisiones = ".$iddivision." and sj.refcategorias = t.refcategorias and t.idtorneo = fix.reftorneos and t.reftipotorneo in (1,2)
                where       sj.reftiposanciones = 4 or sf.amarillas = 2

                ) t";

    $res = $this->query($sql,0);

    if (mysql_num_rows($res)>0) {
        return mysql_result($res,0,0);
    }
    return 0;

}

//calculo para acumulacion de amarillas
function traerAmarillasAcumuladas($idTorneo, $idJugador, $refFecha, $idTipoTorneo) {
    $ultimaFecha = $this->ultimaFechaSancionadoPorAcumulacionAmarillas($idTorneo, $idJugador, $idTipoTorneo);

    $resTorneo = $this->traerTorneosPorId($idTorneo);

    $idTemporada = mysql_result($resTorneo, 0,'reftemporadas');
    $idCategoria = mysql_result($resTorneo, 0,'refcategorias');
    $iddivision  = mysql_result($resTorneo, 0,'refdivisiones');

    if ($ultimaFecha == 0) {
        $reffechaDesde = date('Y').'-01-01';
        $restoAmarillas = 0;
    } else {
        $reffechaDesde = $ultimaFecha;

        //calculo para vaeriguar si sobra una amarilla de la ultima sancion
        $restoAmarillas = (integer)$this->ultimaFechaSancionadoPorAcumulacionAmarillasFallada($idTorneo, $idJugador, $idTipoTorneo) - 1;

        $remanente = $this->traerRemanente($idJugador, $idTemporada, $idCategoria, $iddivision, date('Y').'-01-01', $reffechaDesde);

        if ($remanente == 5) {
            $restoAmarillas = 0;
        }
        if ($restoAmarillas < 0) {
            $restoAmarillas = 0;
        }
    }

    if (($idTipoTorneo == 1) || ($idTipoTorneo == 2)) {
        $idTipoTorneo = '1,2';
    } else {
        $idTipoTorneo = '3';
    }

    $sql = "select
                sum(coalesce((cantidad),0)) + ".$restoAmarillas." as cantidad
            from (
                select
                    1 as cantidad
                from        dbsancionesjugadores sj
                inner
                join        dbfixture fix
                on          fix.idfixture = sj.reffixture and fix.fecha > '".$reffechaDesde."' and sj.refjugadores = ".$idJugador."
                inner
                join        tbtiposanciones ts
                on          ts.idtiposancion = sj.reftiposanciones
                inner
                join        dbtorneos t
                on          t.reftemporadas = ".$idTemporada." and t.refcategorias = ".$idCategoria." and t.refdivisiones = ".$iddivision." and sj.refcategorias = t.refcategorias and t.idtorneo = fix.reftorneos and t.reftipotorneo in (".$idTipoTorneo.")
                where       ts.amonestacion = 1
                            and sj.cantidad > 0

                union all

                select
                    2 as cantidad
                from        dbsancionesjugadores sj
                inner
                join        dbsancionesfallos sf
                on          sj.refsancionesfallos = sf.idsancionfallo and sj.refjugadores = ".$idJugador."
                inner
                join        dbfixture fix
                on          fix.idfixture = sj.reffixture and fix.fecha > '".$reffechaDesde."'
                inner
                join        dbtorneos t
                on          t.reftemporadas = ".$idTemporada." and t.refcategorias = ".$idCategoria." and t.refdivisiones = ".$iddivision." and sj.refcategorias = t.refcategorias and t.idtorneo = fix.reftorneos and t.reftipotorneo in (".$idTipoTorneo.")
                where       sj.reftiposanciones = 4 or sf.amarillas = 2

                ) t";

    $res = $this->query($sql,0);

    if (mysql_num_rows($res)>0) {
        return mysql_result($res,0,0);
    }
    return 0;
}


function traerSancionesfallosacumuladasPorIdSancionJugador($idSancionJugador) {
$sql = "select idsancionfalloacumuladas,refsancionesjugadores,cantidadfechas,fechadesde,fechahasta,amarillas,fechascumplidas,pendientescumplimientos,pendientesfallo,generadaporacumulacion,observaciones from dbsancionesfallosacumuladas where refsancionesjugadores =".$idSancionJugador;
$res = $this->query($sql,0);
return $res;
}


function insertarSancionesfallosacumuladas($refsancionesjugadores,$cantidadfechas,$fechadesde,$fechahasta,$amarillas,$fechascumplidas,$pendientescumplimientos,$pendientesfallo,$generadaporacumulacion,$observaciones) {
$sql = "insert into dbsancionesfallosacumuladas(idsancionfalloacumuladas,refsancionesjugadores,cantidadfechas,fechadesde,fechahasta,amarillas,fechascumplidas,pendientescumplimientos,pendientesfallo,generadaporacumulacion,observaciones)
values ('',".$refsancionesjugadores.",".$cantidadfechas.",'".utf8_decode($fechadesde)."','".utf8_decode($fechahasta)."',".$amarillas.",".$fechascumplidas.",".$pendientescumplimientos.",".$pendientesfallo.",".$generadaporacumulacion.",'".utf8_decode($observaciones)."')";
$res = $this->query($sql,1);
return $res;
}


function sancionarPorAmarillasAcumuladas($idTorneo, $idJugador, $refFecha,$idFixture, $refequipos, $fecha,$refcategorias,$refdivisiones, $refSancionJugadores,$cantidadAmarillas) {

    //$cantidadAmarillas    =   $this->traerAmarillasAcumuladas($idTorneo, $idJugador, $refFecha);
    //$fechaNueva = (integer)$refFecha + 1;
    if ($cantidadAmarillas >=  5) {
        //determino si la fecha a sancionar ya fue sancionada
        $existe = $this->traerSancionesfallosacumuladasPorIdSancionJugador($refSancionJugadores);
        if (mysql_num_rows($existe)<1) {
            $fallo = $this->insertarSancionesfallosacumuladas($refSancionJugadores,1,'0000-00-00','0000-00-00',1,0,0,0,1,utf8_decode('Acumulación de la 5 amarilla'));
        }

        //determino si la fecha a sancionar ya fue sancionada
        //$exiteFechas = $this->existeMovimientoEnFechaPorCantidadFecha($refFecha+1, $idJugador);

        //busco la ultima fecha en caso de ser correcto
        //if (mysql_num_rows($exiteFechas)>0) {
        //  $reffechaNueva = $this->ultimaFechaSancionadoPorCantidadFechas($idJugador);
        //  if ($reffechaNueva >0) {
        //      $fechaNueva = $reffechaNueva + 1;
        //  }
        //}
        //inserto el movimiento con el orden 2, el orden 1 es para las expulsiones
        //$this->insertarMovimientosanciones($refSancionJugadores, $fechaNueva, $idFixture,0,0,2);

        //$this->modificarSancionesjugadoresFalladas($refSancionJugadores, $fallo);
        return 1;
    }
    return 0;
}


function eliminarCambiosPorFixture($idFixture) {
$sql = "delete from dbcambios where reffixture =".$idFixture;
$res = $this->query($sql,0);
return $res;
}

function insertarCambios($refdorsalsale,$refdorsalentra,$reffixture,$refequipos,$refcategorias,$refdivisiones,$minuto) {
$sql = "insert into dbcambios(idcambio,refdorsalsale,refdorsalentra,reffixture,refequipos,refcategorias,refdivisiones,minuto)
values ('',".$refdorsalsale.",".$refdorsalentra.",".$reffixture.",".$refequipos.",".$refcategorias.",".$refdivisiones.",".$minuto.")";
$res = $this->query($sql,1);
return $res;
}

function traerEstadospartidosPorId($id) {
$sql = "select idestadopartido,descripcion,
(case when defautomatica = 1 then 'Si' else 'No' end) as defautomatica,
goleslocalauto,
(case when goleslocalborra = 1 then 'Si' else 'No' end) as goleslocalborra,
golesvisitanteauto,
(case when golesvisitanteborra = 1 then 'Si' else 'No' end) as golesvisitanteborra,
puntoslocal,
puntosvisitante,
(case when finalizado = 1 then 'Si' else 'No' end) as finalizado,
(case when ocultardetallepublico = 1 then 'Si' else 'No' end) as ocultardetallepublico,
(case when visibleparaarbitros = 1 then 'Si' else 'No' end) as visibleparaarbitros,
contabilizalocal,
contabilizavisitante from tbestadospartidos where idestadopartido =".$id;
$res = $this->query($sql,0);
return $res;
}


function modificarFixturePorEstados($id,$refestadospartidos,$puntoslocal,$puntosvisita,$goleslocal,$golesvisitantes,$publicar) {
$sql = "update dbfixture
set
refestadospartidos = ".$refestadospartidos.",puntoslocal = ".$puntoslocal.",puntosvisita = ".$puntosvisita.",goleslocal = ".$goleslocal.",golesvisitantes = ".$golesvisitantes.",publicar = ".$publicar."
where idfixture =".$id;
$res = $this->query($sql,0);
return $res;
}

function modificaGoleadoresPorFixtureMasivo($idfixture, $idEquipo) {
    $sql    =   "update dbgoleadores set goles = 0, encontra = 0 where reffixture =".$idfixture." and refequipos = ".$idEquipo;
    $res = $this->query($sql,0);

    $sqlP   =   "update dbpenalesjugadores set penalconvertido = 0, penalerrado = 0, penalatajado = 0 where reffixture =".$idfixture." and refequipos = ".$idEquipo;
    $resP = $this->query($sqlP,0);

    return $res;
}


function traerInicidenciasPorFixtureEquipoDetalle($idFixture, $idEquipo) {
    $sql = "select
            r.apyn,
            r.nrodocumento,
            r.refjugadores,
            r.reffixture,
            r.refequipos,
            r.refcategorias,
            r.refdivisiones,
            sum(r.goles) as goles,
            sum(r.encontra) as encontra,
            max(r.amarilla) as amarillas,
            max(r.roja) as rojas,
            max(r.informado) as informados,
            max(r.cdtd) as cdtd,
            sum(r.pc) as pc,
            sum(r.pa) as pa,
            sum(r.pe) as pe,
            coalesce(dor.numero,0) as dorsal
            from (
            select
                concat(jug.apellido, ', ', jug.nombres) as apyn,
                jug.nrodocumento,
                p.refjugadores,
                p.reffixture,
                p.refequipos,
                p.refcategorias,
                p.refdivisiones,
                p.goles,
                p.encontra,
                0 as amarilla,
                0 as roja,
                0 as informado,
                0 as cdtd,
                0 as pc,
                0 as pa,
                0 as pe
                from dbgoleadores p
                inner join dbjugadores jug ON jug.idjugador = p.refjugadores
                inner join tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
                inner join dbcountries co ON co.idcountrie = jug.refcountries
                inner join dbfixture fix ON fix.idfixture = p.reffixture
                inner join dbtorneos tor ON tor.idtorneo = fix.reftorneos
                inner join tbfechas fe ON fe.idfecha = fix.reffechas
                left join tbestadospartidos es ON es.idestadopartido = fix.refestadospartidos
                inner join dbequipos equ ON equ.idequipo = p.refequipos
                inner join dbcountries cou ON cou.idcountrie = equ.refcountries
                inner join tbcategorias cat ON cat.idtcategoria = p.refcategorias
                inner join tbdivisiones divi ON divi.iddivision = p.refdivisiones
                where p.reffixture =".$idFixture." and p.refequipos =".$idEquipo." and (p.goles > 0 or p.encontra > 0)

                union all

                select
                concat(jug.apellido, ', ', jug.nombres) as apyn,
                jug.nrodocumento,
                p.refjugadores,
                p.reffixture,
                p.refequipos,
                p.refcategorias,
                p.refdivisiones,
                0 as goles,
                0 as encontra,
                0 as amarilla,
                0 as roja,
                0 as informado,
                0 as cdtd,
                p.penalconvertido as pc,
                p.penalatajado as pa,
                p.penalerrado as pe
                from dbpenalesjugadores p
                inner join dbjugadores jug ON jug.idjugador = p.refjugadores
                inner join tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
                inner join dbcountries co ON co.idcountrie = jug.refcountries
                inner join dbfixture fix ON fix.idfixture = p.reffixture
                inner join dbtorneos tor ON tor.idtorneo = fix.reftorneos
                inner join tbfechas fe ON fe.idfecha = fix.reffechas
                left join tbestadospartidos es ON es.idestadopartido = fix.refestadospartidos
                inner join dbequipos equ ON equ.idequipo = p.refequipos
                inner join dbcountries cou ON cou.idcountrie = equ.refcountries
                inner join tbcategorias cat ON cat.idtcategoria = p.refcategorias
                inner join tbdivisiones divi ON divi.iddivision = p.refdivisiones
                where p.reffixture =".$idFixture." and p.refequipos =".$idEquipo." and (p.penalconvertido > 0 or p.penalatajado > 0 or p.penalerrado > 0)


                union all

                select
                concat(jug.apellido, ', ', jug.nombres) as apyn,
                jug.nrodocumento,
                p.refjugadores,
                p.reffixture,
                p.refequipos,
                p.refcategorias,
                p.refdivisiones,
                0 as goles,
                0 as encontra,
                coalesce((case when p.reftiposanciones = 1 then 1 end),0) as amarilla,
                coalesce((case when p.reftiposanciones = 2 then 1 end),0) as roja,
                coalesce((case when p.reftiposanciones = 3 then 1 end),0) as informado,
                coalesce((case when p.reftiposanciones = 4 then 1 end),0) as cdtd,
                0 as pc,
                0 as pa,
                0 as pe
                from dbsancionesjugadores p
                inner join dbjugadores jug ON jug.idjugador = p.refjugadores
                inner join tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
                inner join dbcountries co ON co.idcountrie = jug.refcountries
                inner join dbfixture fix ON fix.idfixture = p.reffixture
                inner join dbtorneos tor ON tor.idtorneo = fix.reftorneos
                inner join tbfechas fe ON fe.idfecha = fix.reffechas
                left join tbestadospartidos es ON es.idestadopartido = fix.refestadospartidos
                inner join dbequipos equ ON equ.idequipo = p.refequipos
                inner join dbcountries cou ON cou.idcountrie = equ.refcountries
                inner join tbcategorias cat ON cat.idtcategoria = p.refcategorias
                inner join tbdivisiones divi ON divi.iddivision = p.refdivisiones
                where p.reffixture =".$idFixture." and p.refequipos =".$idEquipo." and p.reftiposanciones in (1,2,3,4) and p.cantidad >0
            ) as r
            left join dbdorsales dor
                ON  r.refjugadores = dor.refjugadores and
                    r.reffixture = dor.reffixture and
                    r.refequipos = dor.refequipos and
                    r.refcategorias = dor.refcategorias and
                    r.refdivisiones = dor.refdivisiones

            group by r.apyn,
            r.nrodocumento,
            r.refjugadores,
            r.reffixture,
            r.refequipos,
            r.refcategorias,
            r.refdivisiones,
            dor.numero";
$res = $this->query($sql,0);
return $res;
}


function hayMovimientosDevuelveId($idJugador, $idFixture, $idTipoTorneo) {
    $sql = "SELECT
                distinct san.refsancionesfallos
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallos sf ON sf.idsancionfallo = san.refsancionesfallos
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture."
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo in (1,2)
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos
                    left join
                (select fc.refsancionesfallos, coalesce(count(*),0) as cumplidas
                    from dbsancionesfechascumplidas fc where fc.cumplida = 1
                    group by fc.refsancionesfallos) sfc
                ON  sfc.refsancionesfallos = sf.idsancionfallo
            WHERE
                ju.idjugador = ".$idJugador."
                    AND tip.cumpletodascategorias = 1
                    AND (sf.fechascumplidas + coalesce( sfc.cumplidas,0)) < sf.cantidadfechas
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";

    return $this->existeDevuelveId($sql);
}


// echo el 22/05/2018
function hayMovimientosNuevo($idJugador, $idFixture, $idTipoTorneo) {

    if (($idTipoTorneo == 1) || ($idTipoTorneo == 2)) {
        $sql = "SELECT
                san.idsancionjugador, san.refcategorias
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallos sf ON sf.idsancionfallo = san.refsancionesfallos
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture." AND fix.fecha > san.fecha
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo in (1,2)
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos
                    left join
                (SELECT
                      fc.refsancionesfallos,
                      sj.refcategorias,
                      COALESCE(COUNT(*), 0) AS cumplidas
                  FROM
                      dbsancionesfechascumplidas fc
                          INNER JOIN
                      dbfixture fixf ON fixf.idfixture = fc.reffixture
                          INNER JOIN
                      dbtorneos torc ON torc.idtorneo = fixf.reftorneos
                  		inner join
                  	dbsancionesfallos sf ON sf.idsancionfallo = fc.refsancionesfallos
                  		inner join
                  	dbsancionesjugadores sj ON sj.idsancionjugador = sf.refsancionesjugadores
                  where fc.cumplida = 1
                  GROUP BY fc.refsancionesfallos , sj.refcategorias) sfc
                ON  sfc.refsancionesfallos = sf.idsancionfallo and sfc.refcategorias = san.refcategorias
            WHERE
                ju.idjugador = ".$idJugador."
                    AND tip.cumpletodascategorias = 1
                    AND (coalesce(sf.fechascumplidas,0) + coalesce(sfc.cumplidas,0)) < sf.cantidadfechas
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";
    } else {
        $sql = "SELECT
                coalesce(sf.cantidadfechas -  coalesce(sfc.cumplidas,0),0) as faltan
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallos sf ON sf.idsancionfallo = san.refsancionesfallos
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture." AND fix.fecha > san.fecha
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo = ".$idTipoTorneo."
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos
                    left join
                (SELECT
                      fc.refsancionesfallos,
                      sj.refcategorias,
                      COALESCE(COUNT(*), 0) AS cumplidas
                  FROM
                      dbsancionesfechascumplidas fc
                          INNER JOIN
                      dbfixture fixf ON fixf.idfixture = fc.reffixture
                          INNER JOIN
                      dbtorneos torc ON torc.idtorneo = fixf.reftorneos
                  		inner join
                  	dbsancionesfallos sf ON sf.idsancionfallo = fc.refsancionesfallos
                  		inner join
                  	dbsancionesjugadores sj ON sj.idsancionjugador = sf.refsancionesjugadores
                  where fc.cumplida = 1
                  GROUP BY fc.refsancionesfallos , sj.refcategorias) sfc
                ON  sfc.refsancionesfallos = sf.idsancionfallo and sfc.refcategorias = san.refcategorias
            WHERE
                ju.idjugador = ".$idJugador."
                    AND tip.cumpletodascategorias = 1
                    AND sf.fechascumplidas <> sf.cantidadfechas
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";
    }


    return $this->query($sql);
}


function hayMovimientosAmarillasAcumuladasDevuelveId($idJugador, $idFixture, $idCategoria, $idTipoTorneo) {
    $sql = "SELECT
                distinct san.idsancionjugador
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallosacumuladas sf ON sf.refsancionesjugadores = san.idsancionjugador
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture."
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and san.refcategorias = tor.refcategorias
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos and torv.reftipotorneo = ".$idTipoTorneo."

            WHERE
                ju.idjugador = ".$idJugador."
                    AND tor.refcategorias = ".$idCategoria."
                    AND sf.generadaporacumulacion = 1
                    and sf.fechascumplidas = 0
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";

    return $this->existeDevuelveId($sql);
}



function hayMovimientosAmarillasAcumuladasDevuelveIdAcumulado($idJugador, $idFixture, $idCategoria, $idTipoTorneo) {
    $sql = "SELECT
                distinct sf.idsancionfalloacumuladas
            FROM
                dbsancionesjugadores san
                    INNER JOIN
                dbsancionesfallosacumuladas sf ON sf.refsancionesjugadores = san.idsancionjugador
                    INNER JOIN
                dbjugadores ju ON ju.idjugador = san.refjugadores
                    INNER JOIN
                tbtiposanciones tip ON tip.idtiposancion = san.reftiposanciones
                    INNER JOIN
                dbfixture fix ON fix.idfixture = ".$idFixture."
                    INNER JOIN
                dbtorneos tor ON tor.idtorneo = fix.reftorneos and san.refcategorias = tor.refcategorias
                    INNER JOIN
                dbfixture fixv ON fixv.idfixture = san.reffixture
                    inner join
                dbtorneos torv ON torv.idtorneo = fixv.reftorneos and torv.reftipotorneo = ".$idTipoTorneo."

            WHERE
                ju.idjugador = ".$idJugador."
                    AND tor.refcategorias = ".$idCategoria."
                    AND sf.generadaporacumulacion = 1
                    and sf.fechascumplidas = 0
                    AND (case when torv.idtorneo <> tor.idtorneo then fix.reffechas >= 1 else fix.reffechas > fixv.reffechas end)";

    return $this->existeDevuelveId($sql);
}


function modificarSancionesfallosacumuladasPorSancionJugador($refsancionesjugadores) {
$sql = "update dbsancionesfallosacumuladas
set
fechascumplidas = 1
where refsancionesjugadores =".$refsancionesjugadores;
$res = $this->query($sql,0);
return $res;
}


function insertarSancionesfechascumplidas($reffixture,$refjugadores,$cumplida,$refsancionesfallos, $idTipoTorneo) {

    $sqlExiste = "select idsancionfechacumplida from dbsancionesfechascumplidas where reffixture =".$reffixture." and refjugadores =".$refjugadores;

    $resExiste = $this->existe($sqlExiste);
    // nuevo 22/05/2018
    $idCategoriaNuevo = 0;

    if ($resExiste == 0) {
        $resFix = $this->TraerFixturePorId($reffixture);

        $resTorneo  =   $this->traerTorneosPorId(mysql_result($resFix,0,'reftorneos'));

        $idCategoria    =   mysql_result($resTorneo,0,'refcategorias');

        $suspendidoCategorias       =   $this->hayMovimientos($refjugadores,$reffixture, $idTipoTorneo);

        $suspendidoCategoriasAA     =   $this->hayMovimientosAmarillasAcumuladas($refjugadores,$reffixture, $idCategoria, $idTipoTorneo);

        //primero sanciono por fecha desde y hasta
        if ($suspendidoCategorias != 0) {
            //busco el refsancionesfallos
            $refsancionesfallos = $this->hayMovimientosDevuelveId($refjugadores,$reffixture, $idTipoTorneo);
            $idAcumulado = 0;

            // funcion nueva 22/05/2018 para marcar como cumplido o no
            $refSancionNuevo = $this->hayMovimientosNuevo($refjugadores,$reffixture, $idTipoTorneo);
            $idCategoriaNuevo = mysql_result($refSancionNuevo,0,1);

            if ($idCategoria <> $idCategoriaNuevo) {
                $cumplida = 0;
            }
        } else {
            if ($suspendidoCategoriasAA != 0) {
                $refsancionesJugadores = $this->hayMovimientosAmarillasAcumuladasDevuelveId($refjugadores,$reffixture, $idCategoria, $idTipoTorneo);
                $idAcumulado         = $this->hayMovimientosAmarillasAcumuladasDevuelveIdAcumulado($refjugadores,$reffixture, $idCategoria, $idTipoTorneo);
                //hago cumplir la fecha
                $this->modificarSancionesfallosacumuladasPorSancionJugador($refsancionesJugadores);
                $refsancionesfallos = 0;
            }
        }

        $sql = "insert into dbsancionesfechascumplidas(idsancionfechacumplida,reffixture,refjugadores,cumplida,refsancionesfallos,refsancionesfallosacumuladas)
        values ('',".$reffixture.",".$refjugadores.",".$cumplida.",".$refsancionesfallos.",".$idAcumulado.")";
        $res = $this->query($sql,1);
        return $res;

    }
}


/* recordar poner buscar por temporada activa */
function traerSancionesJugadoresConFallosPorSancion($idFallo, $idTipoTorneo) {
    $sql = "select
            p.idsancionjugador,
            concat(jug.apellido, ', ', jug.nombres) as jugador,
            jug.nrodocumento,
            equ.nombre as equipo,
            p.fecha,
            tip.descripcion as tiposancion,
            p.cantidad,
            sf.cantidadfechas,
            DATE_FORMAT(sf.fechadesde, '%d/%m/%Y') as fechadesde,
            DATE_FORMAT(sf.fechahasta, '%d/%m/%Y') as fechahasta,
            sf.amarillas,
            sf.fechascumplidas,
            (case when sf.pendientescumplimientos = 1 then 'Si' else 'No' end) as pendientescumplimientos,
            (case when sf.pendientesfallo = 1 then 'Si' else 'No' end) as pendientesfallo,
            (case when sf.generadaporacumulacion = 1 then 'Si' else 'No' end) as generadaporacumulacion,
            sf.observaciones,
            p.reftiposanciones,
            p.refjugadores,
            p.refequipos,
            p.reffixture,
            p.refcategorias,
            p.refdivisiones,
            p.refsancionesfallos
        from dbsancionesjugadores p
        inner join dbsancionesfallos sf ON sf.idsancionfallo = p.refsancionesfallos
        inner join tbtiposanciones tip ON tip.idtiposancion = p.reftiposanciones
        inner join dbjugadores jug ON jug.idjugador = p.refjugadores
        inner join tbtipodocumentos ti ON ti.idtipodocumento = jug.reftipodocumentos
        inner join dbcountries co ON co.idcountrie = jug.refcountries
        inner join dbfixture fix ON fix.idfixture = p.reffixture
        inner join dbtorneos tor ON tor.idtorneo = fix.reftorneos and tor.reftipotorneo in (1,2)
        inner join tbfechas fe ON fe.idfecha = fix.reffechas
        inner join tbestadospartidos es ON es.idestadopartido = fix.refestadospartidos
        inner join dbequipos equ ON equ.idequipo = p.refequipos
        inner join dbcountries cou ON cou.idcountrie = equ.refcountries
        inner join tbcategorias cat ON cat.idtcategoria = p.refcategorias
        inner join tbdivisiones divi ON divi.iddivision = p.refdivisiones
        where p.idsancionjugador = ".$idFallo;

        $res = $this->query($sql,0);
        return $res;
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

<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosArbitros {

   function traerPartidosCargados($busqueda) {

      $cadWhere = '';

      if ($busqueda != '') {
         $cadWhere .= " and (f.idfixture like '%".$busqueda."%' or f.fecha like '%".$busqueda."%' or CONCAT(el.nombre, ' vs ', ev.nombre) like '%".$busqueda."%' or cat.categoria like '%".$busqueda."%' or di.division like '%".$busqueda."%' or e.descripcion like '%".$busqueda."%' )";
      }
      $sql = "SELECT
                   f.idfixture,
                   f.fecha AS fechajuego,
                   CONCAT(el.nombre, ' vs ', ev.nombre) partido,
                   fe.fecha,
                   cat.categoria,
                   di.division,
                   e.descripcion,
                   ar.imagen,
                   ar.imagen2
               FROM
                   dbfixture f
                       LEFT JOIN
                   tbestadospartidos e ON f.refestadospartidos = e.idestadopartido
                       INNER JOIN
                   dbequipos el ON el.idequipo = f.refconectorlocal
                       INNER JOIN
                   dbequipos ev ON ev.idequipo = f.refconectorvisitante
                       INNER JOIN
                   tbfechas fe ON fe.idfecha = f.reffechas
                       INNER JOIN
                   dbtorneos t ON t.idtorneo = f.reftorneos
                       INNER JOIN
                   tbcategorias cat ON cat.idtcategoria = t.refcategorias
                       INNER JOIN
                   tbdivisiones di ON di.iddivision = t.refdivisiones
                       LEFT JOIN
                   dbplanillasarbitros ar ON ar.reffixture = f.idfixture
               WHERE
                   (e.visibleparaarbitros = 1 or f.refestadospartidos is null)
                       AND (f.fecha BETWEEN DATE_ADD(NOW(), INTERVAL - 40000 DAY) AND DATE_ADD(NOW(), INTERVAL + 800 DAY))
                       AND f.refconectorlocal IS NOT NULL
                       AND f.refconectorvisitante IS NOT NULL
                       ".$cadWhere."
               order by f.idfixture";
               //die(var_dump($sql));
      $res = $this->query($sql,0);
      return $res;
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

   function traerEstadosPorIn($id) {
      $sql = "select idestado,estado from tbestados where idestado in (".$id.")";
      $res = $this->query($sql,0);
      return $res;
   }

   /* PARA Planillasarbitros */
   function actualizarArchivoPlanilla($id, $archivo, $type) {
      $sql = "update dbplanillasarbitros set imagen = '".$archivo."', type = '".$type."' where idplanillaarbitro = ".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function actualizarArchivoPlanillaComplemento($id, $archivo, $type) {
      $sql = "update dbplanillasarbitros set imagen2 = '".$archivo."', type2 = '".$type."' where idplanillaarbitro = ".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerPartidosPorArbitrosFechas($idarbitro) {
      $sql = "SELECT
             f.idfixture, f.fecha as fechajuego, concat( el.nombre, ' vs ' , ev.nombre) partido, fe.fecha, cat.categoria, di.division
         FROM
             dbfixture f
                 LEFT JOIN
             tbestadospartidos e ON f.refestadospartidos = e.idestadopartido
                 INNER JOIN
             dbequipos el ON el.idequipo = f.refconectorlocal
                 INNER JOIN
             dbequipos ev ON ev.idequipo = f.refconectorvisitante
         		inner join
         	tbfechas fe ON fe.idfecha = f.reffechas
         		inner join
         	dbtorneos t ON t.idtorneo = f.reftorneos
         		inner join
         	tbcategorias cat ON cat.idtcategoria = t.refcategorias
         		inner join
         	tbdivisiones di ON di.iddivision = t.refdivisiones
         WHERE
             (e.visibleparaarbitros = 1
                 OR f.refestadospartidos IS NULL)
                 AND (f.fecha BETWEEN DATE_ADD(NOW(), INTERVAL - 4 DAY) AND DATE_ADD(NOW(), INTERVAL + 5 DAY))
                 AND f.refconectorlocal IS NOT NULL
                 AND f.refconectorvisitante IS NOT NULL
                 and f.refarbitros = ".$idarbitro;

      $res = $this->query($sql,0);
      return $res;

   }


   function traerPartidosPorArbitrosPartido( $idfixture) {
      $sql = "SELECT
             f.idfixture, f.fecha as fechajuego, concat( el.nombre, ' vs ' , ev.nombre) partido, fe.fecha, cat.categoria, di.division, f.refestadospartidos
         FROM
             dbfixture f
                 LEFT JOIN
             tbestadospartidos e ON f.refestadospartidos = e.idestadopartido
                 INNER JOIN
             dbequipos el ON el.idequipo = f.refconectorlocal
                 INNER JOIN
             dbequipos ev ON ev.idequipo = f.refconectorvisitante
         		inner join
         	tbfechas fe ON fe.idfecha = f.reffechas
         		inner join
         	dbtorneos t ON t.idtorneo = f.reftorneos
         		inner join
         	tbcategorias cat ON cat.idtcategoria = t.refcategorias
         		inner join
         	tbdivisiones di ON di.iddivision = t.refdivisiones
         WHERE
             (e.visibleparaarbitros = 1
                 OR f.refestadospartidos IS NULL)
                 AND f.idfixture = ".$idfixture."
                 AND f.refconectorlocal IS NOT NULL
                 AND f.refconectorvisitante IS NOT NULL";

      $res = $this->query($sql,0);
      return $res;

   }


   function insertarPlanillasarbitrosCorto($reffixture,$refarbitros) {
      $sql = "insert into dbplanillasarbitros(idplanillaarbitro,reffixture,refarbitros,goleslocal,golesvisitante,amarillaslocal,expulsadoslocal,informadoslocal,dobleamarillaslocal,cantidadjugadoreslocal,amarillasvisitante,expulsadosvisitante,informadosvisitante,dobleamarillasvisitante,cantidadjugadoresvisitante,refestados,observaciones)
      values ('',".$reffixture.",".$refarbitros.",0,0,0,0,0,0,0,0,0,0,0,0,1,'Sin novedad')";

      $res = $this->query($sql,1);
      return $res;
   }

   function insertarPlanillasarbitros($reffixture,$refarbitros,$imagen,$type,$goleslocal,$golesvisitante,$amarillas,$expulsados,$informados,$dobleamarillas,$refestadospartidos,$observaciones,$imagen2,$type2) {
      $sql = "insert into dbplanillasarbitros(idplanillaarbitro,reffixture,refarbitros,imagen,type,goleslocal,golesvisitante,amarillas,expulsados,informados,dobleamarillas,refestadospartidos,observaciones,imagen2,type2)
      values ('',".$reffixture.",".$refarbitros.",'".$imagen."','".$type."',".$goleslocal.",".$golesvisitante.",".$amarillas.",".$expulsados.",".$informados.",".$dobleamarillas.",".$refestadospartidos.",'".$observaciones."','".$imagen2."','".$type2."')";
      $res = $this->query($sql,1);
      return $res;
   }


   function modificarPlanillasarbitros($id,$reffixture,$refarbitros,$goleslocal,$golesvisitante,$amarillaslocal,$expulsadoslocal,$informadoslocal,$dobleamarillaslocal,$cantidadjugadoreslocal,$amarillasvisitante,$expulsadosvisitante,$informadosvisitante,$dobleamarillasvisitante,$cantidadjugadoresvisitante,$refestadospartidos,$refestados,$observaciones) {

      $observaciones = $this->sanear_string($observaciones);

      $sql = "update dbplanillasarbitros
      set
      refarbitros = ".$refarbitros.",goleslocal = ".$goleslocal.",golesvisitante = ".$golesvisitante.",amarillaslocal = ".$amarillaslocal.",expulsadoslocal = ".$expulsadoslocal.",informadoslocal = ".$informadoslocal.",dobleamarillaslocal = ".$dobleamarillaslocal.",amarillasvisitante = ".$amarillasvisitante.",expulsadosvisitante = ".$expulsadosvisitante.",informadosvisitante = ".$informadosvisitante.",dobleamarillasvisitante = ".$dobleamarillasvisitante.",cantidadjugadoreslocal = ".$cantidadjugadoreslocal.",cantidadjugadoresvisitante = ".$cantidadjugadoresvisitante.",refestadospartidos = ".$refestadospartidos.",refestados = ".$refestados.",observaciones = '".$observaciones."'
      where reffixture =".$id;
      $res = $this->query($sql,0);
      return $res;
   }


   function eliminarPlanillasarbitros($id) {
      $sql = "delete from dbplanillasarbitros where idplanillaarbitro =".$id;
      $res = $this->query($sql,0);
      return $res;
   }


   function traerPlanillasarbitros() {
      $sql = "select
      p.idplanillaarbitro,
      p.reffixture,
      p.refarbitros,
      p.imagen,
      p.type,
      p.goleslocal,
      p.golesvisitante,
      p.amarillas,
      p.expulsados,
      p.informados,
      p.dobleamarillas,
      p.refestadospartidos,
      p.observaciones,
      p.imagen2,
      p.type2
      from dbplanillasarbitros p
      order by 1";
      $res = $this->query($sql,0);
      return $res;
   }

   function traerPlanillasarbitrosPorFixtureArbitro($id) {
      $sql = "select
      p.idplanillaarbitro,
      p.reffixture,
      p.refarbitros,
      p.imagen,
      p.type,
      p.goleslocal,
      p.golesvisitante,
      p.amarillaslocal,
      p.expulsadoslocal,
      p.informadoslocal,
      p.dobleamarillaslocal,
      p.cantidadjugadoreslocal,
      p.amarillasvisitante,
      p.expulsadosvisitante,
      p.informadosvisitante,
      p.dobleamarillasvisitante,
      p.cantidadjugadoresvisitante,
      p.refestadospartidos,
      p.observaciones,
      p.refestados,
      p.imagen2,
      p.type2
      from dbplanillasarbitros p
      where p.reffixture = ".$id;
      $res = $this->query($sql,0);
      return $res;
   }


   function traerPlanillasarbitrosPorId($id) {
      $sql = "select idplanillaarbitro,reffixture,refarbitros,imagen,type,goleslocal,golesvisitante,amarillas,expulsados,informados,dobleamarillas,refestadospartidos,observaciones,imagen2,type2 from dbplanillasarbitros where idplanillaarbitro =".$id;
      $res = $this->query($sql,0);
      return $res;
   }

   /* Fin */
   /* /* Fin de la Tabla: dbplanillasarbitros*/

   function traerFixturePorId($id) {
      $sql = "select idfixture,reftorneos,reffechas,refconectorlocal,refconectorvisitante,refarbitros,juez1,juez2,refcanchas,fecha,hora,refestadospartidos,calificacioncancha,puntoslocal,puntosvisita,goleslocal,golesvisitantes,observaciones,publicar, (case when esresaltado=1 then 'Si' else 'No' end) as esresaltado,(case when esdestacado=1 then 'Si' else 'No' end) as esdestacado from dbfixture where idfixture =".$id;

      $res = $this->query($sql,0);
      return $res;
   }

   function traerEstadospartidosArbitros() {
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
      where e.visibleparaarbitros = 1 and e.idestadopartido <> 16
      order by 1";
      $res = $this->query($sql,0);
      return $res;
   }

   function traerEstadospartidosArbitrosPorId($id) {
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
      where e.visibleparaarbitros = 1 and e.idestadopartido = ".$id."
      order by 1";
      $res = $this->query($sql,0);
      return $res;
   }

   function traerArbitrosPorId($id) {
      $sql = "select idarbitro,nombrecompleto,telefonoparticular,telefonoceleluar,telefonolaboral,telefonofamiliar,email from dbarbitros where idarbitro =".$id;
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

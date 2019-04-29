<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosArbitros {

   /* PARA Planillasarbitros */

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
                 AND (f.fecha BETWEEN DATE_ADD(NOW(), INTERVAL - 4 DAY) AND DATE_ADD(NOW(), INTERVAL + 1 DAY))
                 AND f.refconectorlocal IS NOT NULL
                 AND f.refconectorvisitante IS NOT NULL
                 and f.refarbitros = ".$idarbitro;

      $res = $this->query($sql,0);
      return $res;

   }


   function traerPartidosPorArbitrosPartido($idarbitro, $idfixture) {
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
                 AND f.refconectorvisitante IS NOT NULL
                 and f.refarbitros = ".$idarbitro;

      $res = $this->query($sql,0);
      return $res;

   }


   function insertarPlanillasarbitrosCorto($reffixture,$refarbitros) {
      $sql = "insert into dbplanillasarbitros(idplanillaarbitro,reffixture,refarbitros,resultadolocal,resultadovisitante,goleslocal,golesvisitante,amarillas,expulsados,informados,dobleamarillas)
      values ('',".$reffixture.",".$refarbitros.",0,0,0,0,0,0,0,0)";

      $res = $this->query($sql,1);
      return $res;
   }

   function insertarPlanillasarbitros($reffixture,$refarbitros,$imagen,$type,$resultadolocal,$resultadovisitante,$goleslocal,$golesvisitante,$amarillas,$expulsados,$informados,$dobleamarillas,$refestadospartidos,$observaciones) {
      $sql = "insert into dbplanillasarbitros(idplanillaarbitro,reffixture,refarbitros,imagen,type,resultadolocal,resultadovisitante,goleslocal,golesvisitante,amarillas,expulsados,informados,dobleamarillas,refestadospartidos,observaciones)
      values ('',".$reffixture.",".$refarbitros.",'".$imagen."','".$type."',".$resultadolocal.",".$resultadovisitante.",".$goleslocal.",".$golesvisitante.",".$amarillas.",".$expulsados.",".$informados.",".$dobleamarillas.",".$refestadospartidos.",'".$observaciones."')";
      $res = $this->query($sql,1);
      return $res;
   }


   function modificarPlanillasarbitros($id,$reffixture,$refarbitros,$imagen,$type,$resultadolocal,$resultadovisitante,$goleslocal,$golesvisitante,$amarillas,$expulsados,$informados,$dobleamarillas,$refestadospartidos,$observaciones) {
      $sql = "update dbplanillasarbitros
      set
      reffixture = ".$reffixture.",refarbitros = ".$refarbitros.",imagen = '".$imagen."',type = '".$type."',resultadolocal = ".$resultadolocal.",resultadovisitante = ".$resultadovisitante.",goleslocal = ".$goleslocal.",golesvisitante = ".$golesvisitante.",amarillas = ".$amarillas.",expulsados = ".$expulsados.",informados = ".$informados.",dobleamarillas = ".$dobleamarillas.",refestadospartidos = ".$refestadospartidos.",observaciones = '".$observaciones."'
      where idplanillaarbitro =".$id;
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
      p.resultadolocal,
      p.resultadovisitante,
      p.goleslocal,
      p.golesvisitante,
      p.amarillas,
      p.expulsados,
      p.informados,
      p.dobleamarillas,
      p.refestadospartidos,
      p.observaciones
      from dbplanillasarbitros p
      order by 1";
      $res = $this->query($sql,0);
      return $res;
   }

   function traerPlanillasarbitrosPorFixtureArbitro($id, $idarbitro) {
      $sql = "select
      p.idplanillaarbitro,
      p.reffixture,
      p.refarbitros,
      p.imagen,
      p.type,
      p.resultadolocal,
      p.resultadovisitante,
      p.goleslocal,
      p.golesvisitante,
      p.amarillas,
      p.expulsados,
      p.informados,
      p.dobleamarillas,
      p.refestadospartidos,
      p.observaciones
      from dbplanillasarbitros p
      where p.reffixture = ".$id." and p.refarbitros = ".$idarbitro;
      $res = $this->query($sql,0);
      return $res;
   }


   function traerPlanillasarbitrosPorId($id) {
      $sql = "select idplanillaarbitro,reffixture,refarbitros,imagen,type,resultadolocal,resultadovisitante,goleslocal,golesvisitante,amarillas,expulsados,informados,dobleamarillas,refestadospartidos,observaciones from dbplanillasarbitros where idplanillaarbitro =".$id;
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
      where e.visibleparaarbitros = 1
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

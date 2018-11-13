
<?php

/**
 * @Usuarios clase en donde se accede a la base de datos
 * @ABM consultas sobre las tablas de usuarios y usarios-clientes
 */

date_default_timezone_set('America/Buenos_Aires');

class ServiciosNotificaciones {

    function insertarNotificaciones($mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url) { 
        $sql = "insert into dbnotificaciones(mensaje,idpagina,autor,destinatario,id1,id2,id3,icono,estilo,fecha,url,leido) 
        values ('".($mensaje)."',".$idpagina.",'".($autor)."','".($destinatario)."',".$id1.",".$id2.",".$id3.",'".($icono)."','".($estilo)."','".($fecha)."','".($url)."',0)"; 
        $res = $this->query($sql,1); 
        return $res; 
    } 


    function modificarNotificaciones($id,$mensaje,$idpagina,$autor,$destinatario,$id1,$id2,$id3,$icono,$estilo,$fecha,$url) { 
        $sql = "update dbnotificaciones 
        set 
        mensaje = '".utf8_decode($mensaje)."',idpagina = ".$idpagina.",autor = '".utf8_decode($autor)."',destinatario = '".utf8_decode($destinatario)."',id1 = ".$id1.",id2 = ".$id2.",id3 = ".$id3.",icono = '".utf8_decode($icono)."',estilo = '".utf8_decode($estilo)."',fecha = '".utf8_decode($fecha)."',url = '".utf8_decode($url)."' 
        where idnotificacion =".$id; 
        $res = $this->query($sql,0); 
        return $res; 
    } 

    function marcarNotificacion($id) {
        $sql = "update dbnotificaciones 
        set 
        leido = 1
        where idnotificacion =".$id; 
        $res = $this->query($sql,0); 
        return $res; 
    }


    function eliminarNotificaciones($id) { 
        $sql = "delete from dbnotificaciones where idnotificacion =".$id; 
        $res = $this->query($sql,0); 
        return $res; 
    } 



    function traerNotificaciones() { 
        $sql = "select 
        n.idnotificacion,
        n.mensaje,
        n.idpagina,
        n.autor,
        n.destinatario,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.fecha,
        n.url,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido
        from dbnotificaciones n 
        order by n.leido, n.fecha desc"; 
        $res = $this->query($sql,0); 
        return $res; 
    } 

    function traerNotificacionesNoLeida() { 
        $sql = "select 
        count(*)
        from dbnotificaciones
        where leido = 0"; 
        $res = $this->query($sql,0); 
        if (mysql_num_rows($res)>0) {
            return mysql_result($res, 0,0);
        }
        return 0; 
    } 


    function traerNotificacionesPorUsuarios($email) { 
        $sql = "select 
        n.idnotificacion,
        n.mensaje,
        n.idpagina,
        n.autor,
        n.destinatario,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.fecha,
        n.url,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido
        from dbnotificaciones n 
        where n.destinatario = '".$email."'
        order by n.leido, n.fecha desc"; 
        $res = $this->query($sql,0); 
        return $res; 
    } 

    function traerNotificacionesGrid() {
        $sql = "select 
        n.idnotificacion,
        n.mensaje,
        n.autor,
        n.destinatario,
        n.fecha,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido,
        n.idpagina,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.url
        from dbnotificaciones n 
        order by n.leido, n.fecha desc"; 
        $res = $this->query($sql,0); 
        return $res; 
    }

    function traerNotificacionesPorUsuariosGrid($email) { 
        $sql = "select 
        n.idnotificacion,
        n.mensaje,
        n.autor,
        n.destinatario,
        n.fecha,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido,
        n.idpagina,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.url
        from dbnotificaciones n 
        where n.destinatario = '".$email."'
        order by n.leido, n.fecha desc"; 
        $res = $this->query($sql,0); 
        return $res; 
    } 

    function traerNotificacionesNoLeidaPorUsuarios($email) { 
        $sql = "select 
        count(*)
        from dbnotificaciones
        where leido = 0 and destinatario = '".$email."'"; 
        $res = $this->query($sql,0); 
        if (mysql_num_rows($res)>0) {
            return mysql_result($res, 0,0);
        }
        return 0; 
    } 


    function traerNotificacionesPorId($id) { 
        $sql = "select idnotificacion,mensaje,idpagina,autor,destinatario,id1,id2,id3,icono,estilo,fecha,url,(case when leido = 1 then 'Si' else 'No' end) as leido from dbnotificaciones where idnotificacion =".$id; 
        $res = $this->query($sql,0); 
        return $res; 
    } 

    function traerNotificacionesPorParametrosCompleto($idpagina,$id1, $id2, $id3) {
        $sql = "select
        n.idnotificacion,
        n.mensaje,
        n.idpagina,
        n.autor,
        n.destinatario,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.fecha,
        n.url,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido
        from dbnotificaciones n
        WHERE n.idpagina = ".$idpagina." or (n.id1 = ".$id1." or n.id2 = ".$id2." or n.id3 = ".$id3.")
        order by n.fecha desc";
        $res = $this->query($sql,0);
        return $res;
    }


    function traerNotificacionesPorParametrosTodos($idpagina,$id1, $id2, $id3) {
        $sql = "select
        n.idnotificacion,
        n.mensaje,
        n.idpagina,
        n.autor,
        n.destinatario,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.fecha,
        n.url,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido
        from dbnotificaciones n
        WHERE n.idpagina = ".$idpagina." and n.id1 = ".$id1." and n.id2 = ".$id2." and n.id3 = ".$id3."
        order by n.fecha desc";
        $res = $this->query($sql,0);
        return $res;
    }


    function traerNotificacionesPorParametrosDos($idpagina,$id1, $id2) {
        $sql = "select
        n.idnotificacion,
        n.mensaje,
        n.idpagina,
        n.autor,
        n.destinatario,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.fecha,
        n.url,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido
        from dbnotificaciones n
        WHERE n.idpagina = ".$idpagina." and n.id1 = ".$id1." and n.id2 = ".$id2."
        order by n.fecha desc";
        $res = $this->query($sql,0);
        return $res;
    }

    function traerNotificacionesPorParametrosUno($idpagina,$id1) {
        $sql = "select
        n.idnotificacion,
        n.mensaje,
        n.idpagina,
        n.autor,
        n.destinatario,
        n.id1,
        n.id2,
        n.id3,
        n.icono,
        n.estilo,
        n.fecha,
        n.url,
        (case when n.leido = 1 then 'Si' else 'No' end) as leido
        from dbnotificaciones n
        WHERE n.idpagina = ".$idpagina." and n.id1 = ".$id1."
        order by n.fecha desc";
        $res = $this->query($sql,0);
        return $res;
    }


    /* Fin */
    /* /* Fin de la Tabla: dbnotificaciones*/
    /* PARA Cierrepadrones */

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
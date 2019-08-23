<?php


session_start();

if (!isset($_SESSION['usua_aif']))
{
	header('Location: ../../error.php');
} else {


include ('../../includes/funciones.php');
include ('../../includes/funcionesUsuarios.php');
include ('../../includes/funcionesHTML.php');
include ('../../includes/funcionesReferencias.php');
include ('../../includes/base.php');

include ('../../includes/funcionesArbitros.php');

$serviciosFunciones 	= new Servicios();
$serviciosUsuario 		= new ServiciosUsuarios();
$serviciosHTML 			= new ServiciosHTML();
$serviciosReferencias 	= new ServiciosReferencias();
$baseHTML = new BaseHTML();

$serviciosArbitros 	= new ServiciosArbitros();

//*** SEGURIDAD ****/
include ('../../includes/funcionesSeguridad.php');
$serviciosSeguridad = new ServiciosSeguridad();
$serviciosSeguridad->seguridadRuta($_SESSION['refroll_aif'], '../estadisticas/');
//*** FIN  ****/

$fecha = date('Y-m-d');

//$resProductos = $serviciosProductos->traerProductosLimite(6);
$resMenu = $serviciosHTML->menu($_SESSION['nombre_aif'],"Estadisticas",$_SESSION['refroll_aif'],$_SESSION['email_aif']);

$configuracion = $serviciosReferencias->traerConfiguracion();

$tituloWeb = mysql_result($configuracion,0,'sistema');

$breadCumbs = '<a class="navbar-brand" href="../index.php">Dashboard</a>';

$id = $_POST['idfixture'];

$partido = $serviciosArbitros->traerPartidosPorArbitrosPartido($id);
$partidoAux = $serviciosArbitros->traerPartidosPorArbitrosPartido($id);

$resultado = $serviciosArbitros->traerPlanillasarbitrosPorFixtureArbitro($id);


/////////////////////// Opciones pagina ///////////////////////////////////////////////
$singular = "Pre-Estadistica";

$plural = "Pre-Estadistica";

$eliminar = "eliminarPlanillasarbitros";

$insertar = "insertarPlanillasarbitros";

$tituloWeb = "Gestión: AIF";
//////////////////////// Fin opciones ////////////////////////////////////////////////



$idFixture = $_POST['idfixture'];

$path  = '../../arbitros/'.$idFixture;

if (!file_exists($path)) {
	mkdir($path, 0777);
}

$pathPlanilla  = '../../arbitros/'.$idFixture.'/1';

if (!file_exists($pathPlanilla)) {
	mkdir($pathPlanilla, 0777);
}

$pathPlanillaComplemento  = '../../arbitros/'.$idFixture.'/2';

if (!file_exists($pathPlanillaComplemento)) {
	mkdir($pathPlanillaComplemento, 0777);
}
// Arreglo con todos los nombres de los archivos
$filesPlanilla = array_diff(scandir($pathPlanilla), array('.', '..'));
$filesComplemento = array_diff(scandir($pathPlanillaComplemento), array('.', '..'));


//die(var_dump($idFixture));
$resFix = $serviciosReferencias->TraerFixturePorId($idFixture);


$equipoLocal		=	mysql_result($resFix,0,'refconectorlocal');
$equipoVisitante	=	mysql_result($resFix,0,'refconectorvisitante');

$refFecha = mysql_result($resFix,0,'reffechas');
$refJugo = mysql_result($resFix,0,'fecha');
$resultadoA = mysql_result($resFix,0,'puntoslocal');
$resultadoB = mysql_result($resFix,0,'puntosvisita');

$equipoA = mysql_result($serviciosReferencias->traerEquiposPorId($equipoLocal),0,'nombre');
$equipoB = mysql_result($serviciosReferencias->traerEquiposPorId($equipoVisitante),0,'nombre');

$resTorneo	=	$serviciosReferencias->traerTorneosPorId(mysql_result($resFix,0,'reftorneos'));
$idTorneo = (integer)mysql_result($resTorneo,0,'idtorneo');
$idCategoria	=	mysql_result($resTorneo,0,'refcategorias');
$idDivisiones	=	mysql_result($resTorneo,0,'refdivisiones');


////////////  TRAIGO EL TIPO DE TORNEO  //////////////////////////////
$idTipoTorneo	= mysql_result($resTorneo,0,'reftipotorneo');
/////////////  FIN TIPO TORNEO ///////////////////////////////////////


//todas las fechas del torneo del equipo local (Fechas Local y Visitante)
$resTodasFechasL = $serviciosReferencias->traerFechasFixturePorTorneoEquipoLocal(mysql_result($resFix,0,'reftorneos'), $equipoLocal);
$resTodasFechasLV = $serviciosReferencias->traerFechasFixturePorTorneoEquipoVisitante(mysql_result($resFix,0,'reftorneos'), $equipoLocal);

//todas las fechas del torneo del equipo visitante
$resTodasFechasV = $serviciosReferencias->traerFechasFixturePorTorneoEquipoVisitante(mysql_result($resFix,0,'reftorneos'), $equipoVisitante);
$resTodasFechasVL = $serviciosReferencias->traerFechasFixturePorTorneoEquipoLocal(mysql_result($resFix,0,'reftorneos'), $equipoVisitante);

$resFecha	=	$serviciosReferencias->traerFechasPorId($refFecha);

///////////////   traigo la utima temporada  ///////////////////
$refTemporada = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($refTemporada)>0) {
	$idTemporada = mysql_result($refTemporada,0,0);
} else {
	$idTemporada = 0;
}
////////////////// fin  ////////////////////////////////////////

/////////////		traigo los minutos del partido   ////////////////
$resDefCategTemp		=	$serviciosReferencias->traerDefinicionescategoriastemporadasPorTemporadaCategoria($idTemporada, $idCategoria);

$minutos				=	mysql_result($resDefCategTemp,0,'minutospartido');
/////////////			fin				/////////////////////////////

$error = '';
$lblerror = '';


$resModCancha = $serviciosReferencias->modificarFixturePorCancha($idFixture, $_POST['refcanchas'], $_POST['refarbitros'], $_POST['juez1'], $_POST['juez2'], $_POST['calificacioncancha']);

//die(var_dump($resModCancha));
$numero = count($_POST);
	$tags = array_keys($_POST);// obtiene los nombres de las varibles
	$valores = array_values($_POST);// obtiene los valores de las varibles
	$cantEncontrados = 0;
	$cantidad = 1;
	$idEquipos = 0;

	$cadWhere = '';
	$cantEquipos = array();

	$golesRealesLocal 		= 0;
	$golesRealesVisitantes	= 0;
	$idsancion = 0;

	for($i=0;$i<$numero;$i++){


		/////////////////////			EQUIPO LOCAL				////////////////////////////////////////////////
		if (strpos($tags[$i],"goles") !== false) {

			$idJugador = str_replace("goles","",$tags[$i]);

			//////////////		logica GOLEADORES		///////////////////////////////////////////////////////
			$existeGoleadores = $serviciosReferencias->existeFixturePorGoleadores($idJugador, $idFixture);

			$golesRealesLocal += $_POST['goles'.$idJugador];
			$golesRealesVisitantes += $_POST['encontra'.$idJugador];

			if ($existeGoleadores == 0) {
				//inserto
				$serviciosReferencias->insertarGoleadores($idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['goles'.$idJugador], $_POST['encontra'.$idJugador]);
			} else {
				//modifico

				$serviciosReferencias->modificarGoleadores($existeGoleadores, $idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['goles'.$idJugador], $_POST['encontra'.$idJugador]);
			}
			//////////////			fin logica			/////////////////////////////////////////////////////////


			//////////////		logica PENALES		///////////////////////////////////////////////////////
			$existePenales = $serviciosReferencias->existeFixturePorPenalesJugador($idJugador, $idFixture);

			$golesRealesLocal += $_POST['penalesconvertidos'.$idJugador];

			if ($existePenales == 0) {
				if (($_POST['penalesconvertidos'.$idJugador] > 0) || ($_POST['penaleserrados'.$idJugador] > 0) || ($_POST['penalesatajados'.$idJugador] > 0)) {
				//inserto
					$serviciosReferencias->insertarPenalesjugadores($idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['penalesconvertidos'.$idJugador], $_POST['penaleserrados'.$idJugador], $_POST['penalesatajados'.$idJugador]);
				}
			} else {
				//modifico

				$serviciosReferencias->modificarPenalesjugadores($existePenales, $idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['penalesconvertidos'.$idJugador], $_POST['penaleserrados'.$idJugador], $_POST['penalesatajados'.$idJugador]);
			}
			//////////////			fin logica			/////////////////////////////////////////////////////////



			//////////////		logica DORSALES		///////////////////////////////////////////////////////
			$existeDorsal = $serviciosReferencias->existeFixturePorDorsalesJugador($idJugador, $idFixture);

			if ($existeDorsal == 0) {
				//inserto
				$serviciosReferencias->insertarDorsales($idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['dorsal'.$idJugador]);
			} else {
				//modifico

				$serviciosReferencias->modificarDorsales($existeDorsal, $idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['dorsal'.$idJugador]);
			}
			//////////////			fin logica			/////////////////////////////////////////////////////////





			//////////////		logica MINUTOS		///////////////////////////////////////////////////////
			$existeMinutos = $serviciosReferencias->existeFixturePorMinutosJugados($idJugador, $idFixture);

			if ($existeMinutos == 0) {
				//inserto
				$serviciosReferencias->insertarMinutosjugados($idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones, $_POST['minutos'.$idJugador]);
			} else {
				//modifico

				$serviciosReferencias->modificarMinutosjugados($existeMinutos, $idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones,$_POST['minutos'.$idJugador]);
			}
			//////////////			fin logica			/////////////////////////////////////////////////////////


			//////////////  mejor jugador //////////////
			// siempre lo borro a lo primero
			$serviciosReferencias->eliminarMejorjugadorPorJugadorFixture($idJugador, $idFixture);
			if (isset($_POST['mejorjugador'.$idJugador])) {

				$serviciosReferencias->insertarMejorjugador($idJugador, $idFixture, $equipoLocal, $idCategoria, $idDivisiones);
			}
			/////////////		FIN MEJOR JUGADOR		/////////////////////////////////////////////////////////



			/***********		AMARILLAS			*************************************************************/
			$existeAmarillas	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,1,$idFixture);
			if ($_POST['amaLrillas'.$idJugador] > 0) {
				if ($existeAmarillas == 0) {
					//inserto
					$idsancion = $serviciosReferencias->insertarSancionesjugadores(1,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['amaLrillas'.$idJugador], $idCategoria, $idDivisiones, 'NULL');


				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeAmarillas,1,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['amaLrillas'.$idJugador], $idCategoria, $idDivisiones);
				}
			} else {

				//analizo si en un supuesto caso anteriormente tenia una amarilla
				if ($existeAmarillas != 0) {
					$serviciosReferencias->eliminarMovimientosancionesPorSancionJugadorAcumuadasAmarillas($existeAmarillas);
					$serviciosReferencias->eliminarSancionesfallosacumuladasPorIdSancionJugador($existeAmarillas);
					$serviciosReferencias->eliminarSancionesjugadores($existeAmarillas);
				}
			}

			/***********		FIN					*************************************************************/



			/***********		ROJAS			*************************************************************/
			$existeRojas	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,2,$idFixture);

				if ($existeRojas == 0) {
					if ($_POST['roLjas'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(2,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['roLjas'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeRojas,2,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['roLjas'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/



			/***********		INFORMADOS			*************************************************************/
			$existeInformados	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,3,$idFixture);

				if ($existeInformados == 0) {
					if ($_POST['inforLmados'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(3,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['inforLmados'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeInformados,3,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['inforLmados'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/



			/***********		DOBLE AMARILLAS			*************************************************************/
			$existeDobleAmarillas	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,4,$idFixture);

			if ($existeDobleAmarillas == 0) {
				//inserto
				if ($_POST['dobleLamarilla'.$idJugador] > 0) {
					$idsancion = $serviciosReferencias->insertarSancionesjugadores(4,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['dobleLamarilla'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
				}

			} else {
				//modifico

				$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeDobleAmarillas,4,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['dobleLamarilla'.$idJugador], $idCategoria, $idDivisiones);
			}

			/***********		FIN					*************************************************************/



			/***********		CD TD			*************************************************************/
			$existeCDTD	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,5,$idFixture);


				if ($existeCDTD == 0) {
					if ($_POST['cdLtd'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(5,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['cdLtd'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeCDTD,5,$idJugador, $equipoLocal, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['cdLtd'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/


			/**************** miro si la acumulacion de amarillas hace que lo sancione **************************/
			/****** aca solo sanciono por cantidad de amarillas cargadas, no espero el fallo, si le ponen doble amarilla espero el fallo */
			$idsancion = $serviciosReferencias->existeFixturePorSanciones($idJugador, 1, $idFixture);
			if ($idsancion != 0) {
				//*****			calculo amarillas acumuladas ********/
				$cantidadAmarillas = $serviciosReferencias->traerAmarillasAcumuladas($idTorneo, $idJugador, $refFecha, $idTipoTorneo);
				//die(var_dump($cantidadAmarillas.'jugador:'.$idJugador));
				$acuAmarillasA = $serviciosReferencias->sancionarPorAmarillasAcumuladas($idTorneo, $idJugador, $refFecha, $idFixture, $equipoLocal, $fecha, $idCategoria, $idDivisiones, $idsancion, $cantidadAmarillas);
				//*****				fin							*****/
			}



			/**************** 					fin				*************************************************/

		}



		////********************		FIN EQUIPO LOCAL		*************************************************//////



		/////////////////////			EQUIPO VISITANTE				////////////////////////////////////////////////
		if (strpos($tags[$i],"gobles") !== false) {

			if (isset($valores[$i])) {

				$idJugador = str_replace("gobles","",$tags[$i]);

				$golesRealesLocal += $_POST['enbcontra'.$idJugador];
				$golesRealesVisitantes += $valores[$i];
				//////////////		logica GOLEADORES		///////////////////////////////////////////////////////
				$existeGoleadores = $serviciosReferencias->existeFixturePorGoleadores($idJugador, $idFixture);

				if ($existeGoleadores == 0) {

					if ($valores[$i] > 0) {
						//inserto
						$serviciosReferencias->insertarGoleadores($idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$valores[$i], $_POST['enbcontra'.$idJugador]);
					}

				} else {
					//modifico

					$serviciosReferencias->modificarGoleadores($existeGoleadores, $idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$valores[$i], $_POST['enbcontra'.$idJugador]);
				}
				//////////////			fin logica			/////////////////////////////////////////////////////////


				//////////////		logica PENALES		///////////////////////////////////////////////////////
				$existePenales = $serviciosReferencias->existeFixturePorPenalesJugador($idJugador, $idFixture);

				$golesRealesVisitantes += $_POST['penalesbconvertidos'.$idJugador];

				if ($existePenales == 0) {

					if (($_POST['penalesbconvertidos'.$idJugador] > 0) || ($_POST['penalesberrados'.$idJugador] > 0) || ($_POST['penalesbatajados'.$idJugador] > 0)) {
						//inserto
						$serviciosReferencias->insertarPenalesjugadores($idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$_POST['penalesbconvertidos'.$idJugador], $_POST['penalesberrados'.$idJugador], $_POST['penalesbatajados'.$idJugador]);
					}

				} else {
					//modifico

					$serviciosReferencias->modificarPenalesjugadores($existePenales, $idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$_POST['penalesbconvertidos'.$idJugador], $_POST['penalesberrados'.$idJugador], $_POST['penalesbatajados'.$idJugador]);
				}
				//////////////			fin logica			/////////////////////////////////////////////////////////



				//////////////		logica DORSALES		///////////////////////////////////////////////////////
				$existeDorsal = $serviciosReferencias->existeFixturePorDorsalesJugador($idJugador, $idFixture);

				if ($existeDorsal == 0) {
					//inserto
					$serviciosReferencias->insertarDorsales($idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$_POST['dorbsal'.$idJugador]);
				} else {
					//modifico

					$serviciosReferencias->modificarDorsales($existeDorsal, $idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$_POST['dorbsal'.$idJugador]);
				}
				//////////////			fin logica			/////////////////////////////////////////////////////////


				//////////////		logica MINUTOS		///////////////////////////////////////////////////////
				$existeMinutos = $serviciosReferencias->existeFixturePorMinutosJugados($idJugador, $idFixture);

				if ($existeMinutos == 0) {
					//inserto
					$serviciosReferencias->insertarMinutosjugados($idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones, $_POST['minbutos'.$idJugador]);
				} else {
					//modifico

					$serviciosReferencias->modificarMinutosjugados($existeMinutos, $idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones,$_POST['minbutos'.$idJugador]);
				}
				//////////////			fin logica			/////////////////////////////////////////////////////////


				//////////////  mejor jugador //////////////
				// siempre lo borro a lo primero
				$serviciosReferencias->eliminarMejorjugadorPorJugadorFixture($idJugador, $idFixture);
				if (isset($_POST['mejorbjugador'.$idJugador])) {

					$serviciosReferencias->insertarMejorjugador($idJugador, $idFixture, $equipoVisitante, $idCategoria, $idDivisiones);
				}

				/**************  fin 			**********************************************************************/

			/***********		AMARILLAS			*************************************************************/

			$existeAmarillas	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,1,$idFixture);

			if ($_POST['amaVrillas'.$idJugador] > 0) {
				if ($existeAmarillas == 0) {
					//inserto
					$idsancion = $serviciosReferencias->insertarSancionesjugadores(1,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['amaVrillas'.$idJugador], $idCategoria, $idDivisiones, 'NULL');



				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeAmarillas,1,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['amaVrillas'.$idJugador], $idCategoria, $idDivisiones);
				}
			} else {
				//analizo si en un supuesto caso anteriormente tenia una amarilla
				if ($existeAmarillas != 0) {
					$serviciosReferencias->eliminarMovimientosancionesPorSancionJugadorAcumuadasAmarillas($existeAmarillas);
					$serviciosReferencias->eliminarSancionesfallosacumuladasPorIdSancionJugador($existeAmarillas);
					$serviciosReferencias->eliminarSancionesjugadores($existeAmarillas);
				}
			}
			/***********		FIN					*************************************************************/



			/***********		ROJAS			*************************************************************/
			$existeRojas	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,2,$idFixture);


				if ($existeRojas == 0) {
					if ($_POST['roVjas'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(2,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['roVjas'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeRojas,2,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['roVjas'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/



			/***********		INFORMADOS			*************************************************************/
			$existeInformados	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,3,$idFixture);


				if ($existeInformados == 0) {
					if ($_POST['inforVmados'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(3,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['inforVmados'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeInformados,3,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['inforVmados'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/



			/***********		DOBLE AMARILLAS			*************************************************************/
			$existeDobleAmarillas	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,4,$idFixture);


				if ($existeDobleAmarillas == 0) {
					if ($_POST['dobleVamarilla'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(4,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['dobleVamarilla'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeDobleAmarillas,4,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['dobleVamarilla'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/



			/***********		CD TD			*************************************************************/
			$existeCDTD	=	$serviciosReferencias->existeFixturePorSanciones($idJugador,5,$idFixture);


				if ($existeCDTD == 0) {
					if ($_POST['cdVtd'.$idJugador] > 0) {
					//inserto
						$idsancion = $serviciosReferencias->insertarSancionesjugadores(5,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['cdVtd'.$idJugador], $idCategoria, $idDivisiones, 'NULL');
					}
				} else {
					//modifico

					$serviciosReferencias->modificarSancionesjugadoresSinAlterarFallo($existeCDTD,5,$idJugador, $equipoVisitante, $idFixture, mysql_result($resFix,0,'fecha'),$_POST['cdVtd'.$idJugador], $idCategoria, $idDivisiones);
				}

			/***********		FIN					*************************************************************/

			}


			/**************** miro si la acumulacion de amarillas hace que lo sancione **************************/
			$idsancion = $serviciosReferencias->existeFixturePorSanciones($idJugador, 1, $idFixture);
			if ($idsancion != 0) {
				//*****			calculo amarillas acumuladas ********/
				$cantidadAmarillas = $serviciosReferencias->traerAmarillasAcumuladas($idTorneo, $idJugador, $refFecha, $idTipoTorneo);
				//die(var_dump($cantidadAmarillas.'jugador:'.$idJugador));
				$acuAmarillasA = $serviciosReferencias->sancionarPorAmarillasAcumuladas($idTorneo, $idJugador, $refFecha, $idFixture, $equipoLocal, $fecha, $idCategoria, $idDivisiones, $idsancion, $cantidadAmarillas);
				//*****				fin							*****/
			}
			//*****				fin							*****/


			/**************** 					fin				*************************************************/
		}



		////********************		FIN EQUIPO VISITANTE		*************************************************//////

	}


///////////////////////  PARA LOS CAMBIOS                        ///////////////////////////
// BORRO TODOS LOS CAMBIOS Y LOS VUELVO A CARGAR
$serviciosReferencias->eliminarCambiosPorFixture($idFixture);

for ($i=1; $i<=7; $i++) {

	if (isset($_POST['salecambioLocal'.$i])) {
		$serviciosReferencias->insertarCambios($_POST['salecambioLocal'.$i], $_POST['entracambioLocal'.$i], $idFixture, $equipoLocal, $idCategoria, $idDivisiones, $_POST['minutocambioLocal'.$i]);
	}

	if (isset($_POST['salecambioVisitante'.$i])) {
		$serviciosReferencias->insertarCambios($_POST['salecambioVisitante'.$i], $_POST['entracambioVisitante'.$i], $idFixture, $equipoVisitante, $idCategoria, $idDivisiones, $_POST['minutocambioVisitante'.$i]);
	}


}





///////////////////////         FIN                             ////////////////////////////


///////////////////////  CALCULA SEGUN EL ESTADO DEL PARTIDO	////////////////////////////
$refEstadoPartido		=		$_POST['refestadospartidos'];

//calculo
$defAutomatica			= 0;

$golesLocalAuto			= 0;
$golesLocalBorra		= 0;

$golesvisitanteauto		= 0;
$golesvisitanteborra	= 0;

$puntosLocal			= 0;
$puntosVisitante		= 0;

$finalizado				= 0;

$ocultaDetallePublico	= 0;

$visibleParaArbitros	= 0;

$contabilizaLocal		= 0;
$contabilizaVisitante	= 0;

//variable para determinar si el partido va a continuar
$partidoSuspendidoCompletamente = 0;


if	($refEstadoPartido != 0) {


	$estadoPartido	=	$serviciosReferencias->traerEstadospartidosPorId($refEstadoPartido);

	$defAutomatica			= mysql_result($estadoPartido,0,'defautomatica');

	$golesLocalAuto			= mysql_result($estadoPartido,0,'goleslocalauto');
	$golesLocalBorra		= mysql_result($estadoPartido,0,'goleslocalborra');

	$golesvisitanteauto		= mysql_result($estadoPartido,0,'golesvisitanteauto');
	$golesvisitanteborra	= mysql_result($estadoPartido,0,'golesvisitanteborra');

	$puntosLocal			= mysql_result($estadoPartido,0,'puntoslocal');
	$puntosVisitante		= mysql_result($estadoPartido,0,'puntosvisitante');

	$finalizado				= mysql_result($estadoPartido,0,'finalizado');

	$ocultaDetallePublico	= mysql_result($estadoPartido,0,'ocultardetallepublico');

	$visibleParaArbitros	= mysql_result($estadoPartido,0,'visibleparaarbitros');

	$contabilizaLocal		= mysql_result($estadoPartido,0,'contabilizalocal');
	$contabilizaVisitante	= mysql_result($estadoPartido,0,'contabilizavisitante');

	// caso de ganado, perdido, empatado
	if (($defAutomatica == 'No') && ($finalizado == 'Si') && ($visibleParaArbitros == 'No')) {
		if (($golesRealesLocal > $golesRealesVisitantes) && (($puntosLocal == 0) || ($puntosLocal == 1))) {
			$error = "Error: El equipo local deberia ganar";
			$lblerror = "alert-danger";
		}

		if (($golesRealesLocal < $golesRealesVisitantes) && (($puntosVisitante == 0) || ($puntosVisitante == 1))) {
			$error = "Error: El equipo visitante deberia ganar";
			$lblerror = "alert-danger";
		}

		if (($golesRealesLocal == $golesRealesVisitantes) && ($puntosVisitante != $puntosLocal)) {
			$error = "Error: El partido deberia ser un empate";
			$lblerror = "alert-danger";
		}



		if ($error == '') {
			$lblerror = "alert-success";
			$error = "Ok: Se cargo correctamente";
			$serviciosReferencias->modificarFixturePorEstados($idFixture, $refEstadoPartido, $puntosLocal, $puntosVisitante, $golesRealesLocal, $golesRealesVisitantes, 1);
			if ($_SESSION['idroll_aif'] == 1) {
				$resEstados		= $serviciosReferencias->traerEstadospartidos();
				$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
			} else {
				$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
				$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
			}

			/****************				MARCO LA SANCION CUMPLIDA					*********************/

			/****************				FIN MARCO LA SANCION CUMPLIDA					*********************/
		} else {
			$resM = $serviciosReferencias->modificarFixturePorEstados($idFixture, 'NULL', $puntosLocal, $puntosVisitante, $golesRealesLocal, $golesRealesVisitantes, 0);

			if ($_SESSION['idroll_aif'] == 1) {
				$resEstados		= $serviciosReferencias->traerEstadospartidos();
				$cadEstados		= $serviciosFunciones->devolverSelectBox($resEstados,array(1),'');
			} else {
				$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
				$cadEstados		= $serviciosFunciones->devolverSelectBox($resEstados,array(1),'');
			}

		}

		$partidoSuspendidoCompletamente = 0;
	} else { // else del ganado, perdido, empatado
		// estados donde los partidos los define el estado como W.O. Local, Perdida de puntos a Ambos, Suspendido Finalizado
		if (($defAutomatica == 'Si') && ($finalizado == 'Si') && ($visibleParaArbitros == 'No')) {
			if ($golesLocalBorra == 'Si') {
				//borrar los goles cargados al local
				$serviciosReferencias->modificaGoleadoresPorFixtureMasivo($idFixture, $equipoLocal);
				//verifico si los goles superan la cantidad arbitrada
				if ($golesRealesVisitantes > $golesvisitanteauto) {
					$golesvisitanteauto = $golesRealesVisitantes;
				}
			}

			if ($golesvisitanteborra == 'Si') {
				//borrar los goles cargados al local
				$serviciosReferencias->modificaGoleadoresPorFixtureMasivo($idFixture, $equipoVisitante);
				//verifico si los goles superan la cantidad arbitrada
				if ($golesRealesLocal > $golesLocalAuto) {
					$golesLocalAuto = $golesRealesLocal;
				}
			}

			$serviciosReferencias->modificarFixturePorEstados($idFixture, $refEstadoPartido, $puntosLocal, $puntosVisitante, $golesLocalAuto, $golesvisitanteauto, 1);


			if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
				$resEstados		= $serviciosReferencias->traerEstadospartidos();
				$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
			} else {
				$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
				$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
			}

			$partidoSuspendidoCompletamente = 0;
		} else { // else del W.O. Local, Perdida de puntos a Ambos, Suspendido Finalizado

			// para los arbitros
			if (($defAutomatica == 'No') && ($finalizado == 'No') && ($visibleParaArbitros == 'Si') && (($puntosLocal > 0) || ($puntosVisitante > 0))) {
				if (($golesRealesLocal > $golesRealesVisitantes) && (($puntosLocal == 0) || ($puntosLocal == 1))) {
					$error = "Error: El equipo local deberia ganar";
					$lblerror = "alert-danger";
				}

				if (($golesRealesLocal < $golesRealesVisitantes) && (($puntosVisitante == 0) || ($puntosVisitante == 1))) {
					$error = "Error: El equipo visitante deberia ganar";
					$lblerror = "alert-danger";
				}

				if (($golesRealesLocal == $golesRealesVisitantes) && ($puntosVisitante != $puntosLocal)) {
					$error = "Error: El partido deberia ser un empate";
					$lblerror = "alert-danger";
				}



				if ($error == '') {
					$lblerror = "alert-success";
					$error = "Ok: Se cargo correctamente";
					$serviciosReferencias->modificarFixturePorEstados($idFixture, $refEstadoPartido, $puntosLocal, $puntosVisitante, $golesRealesLocal, $golesRealesVisitantes, 1);
					if ($_SESSION['idroll_aif'] == 1) {
						$resEstados		= $serviciosReferencias->traerEstadospartidos();
						$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
					} else {
						$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
						$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
					}

					/****************				MARCO LA SANCION CUMPLIDA					*********************/

					/****************				FIN MARCO LA SANCION CUMPLIDA					*********************/
				} else {
					$resM = $serviciosReferencias->modificarFixturePorEstados($idFixture, 'NULL', $puntosLocal, $puntosVisitante, $golesRealesLocal, $golesRealesVisitantes, 0);

					if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
						$resEstados		= $serviciosReferencias->traerEstadospartidos();
						$cadEstados		= $serviciosFunciones->devolverSelectBox($resEstados,array(1),'');
					} else {
						$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
						$cadEstados		= $serviciosFunciones->devolverSelectBox($resEstados,array(1),'');
					}

				}

				$partidoSuspendidoCompletamente = 0;
				// arbitros


			} else {
				// if para cuando un partido se suspende y no se carga nada
				if (($defAutomatica == 'No') && ($finalizado == 'No') && ($golesLocalAuto == 0) && ($golesvisitanteauto == 0)) {


					$partidoSuspendidoCompletamente = 1;

					$serviciosReferencias->modificarFixturePorEstados($idFixture, $refEstadoPartido, $puntosLocal, $puntosVisitante, $golesLocalAuto, $golesvisitanteauto, 1);

					if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
						$resEstados		= $serviciosReferencias->traerEstadospartidos();
						$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
					} else {
						$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
						$cadEstados		= $serviciosFunciones->devolverSelectBoxActivo($resEstados,array(1),'', $refEstadoPartido);
					}

				}
			}

		}
	}
} else { //else de si no selecciono un estado para el partido


	if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
		$resEstados		= $serviciosReferencias->traerEstadospartidos();
		$cadEstados		= $serviciosFunciones->devolverSelectBox($resEstados,array(1),'');
	} else {
		$resEstados		= $serviciosArbitros->traerEstadospartidosArbitros();
		$cadEstados		= $serviciosFunciones->devolverSelectBox($resEstados,array(1),'');
	}

}


///**** DETALLE DEL PARTIDO *****///

$detallePartidoLocal = $serviciosReferencias->traerInicidenciasPorFixtureEquipoDetalle($idFixture,$equipoLocal);

$arDetalleLocal = array();

while( $row = mysql_fetch_assoc( $detallePartidoLocal)){
    $arDetalleLocal[] = $row['refjugadores']; // Inside while loop
}

$detallePartidoVisit = $serviciosReferencias->traerInicidenciasPorFixtureEquipoDetalle($idFixture, $equipoVisitante);

$arDetalleVisit = array();

while( $row = mysql_fetch_assoc( $detallePartidoVisit)){
    $arDetalleVisit[] = $row['refjugadores']; // Inside while loop
}

//die(var_dump($arDetalleVisit));

///****   FIN   ****///////////////


//////*************************			CALCULO POR ACUMULADION DE AMARILLAS	*********************************************/////



//////*************************			FIN calculo de amarillas	*********************************************/////

///////////////////////				FIN							////////////////////////////

/////////////////////// Opciones de la pagina  ////////////////////

$lblTitulosingular	= "Estadistica";
$lblTituloplural	= "Estadisticas";
$lblEliminarObs		= "Si elimina la Estadistica se eliminara todo el contenido de este";
$accionEliminar		= "eliminarEstadisticas";

/////////////////////// Fin de las opciones /////////////////////



/////////////////////// Opciones para la creacion del view  /////////////////////
$cabeceras 		= "<th>Nombre</th>
				<th>DNI</th>
				<th>Equipo</th>
				<th>Fecha</th>
				<th>Goles</th>";

$cabeceras2 		= "<th>Nombre</th>
				<th>DNI</th>
				<th>Equipo</th>
				<th>Fecha</th>
				<th>Amarillas</th>";
//////////////////////////////////////////////  FIN de los opciones //////////////////////////



$resJugadoresA = $serviciosReferencias->traerConectorActivosPorEquiposCategorias($equipoLocal, $idCategoria);
$resJugadoresB = $serviciosReferencias->traerConectorActivosPorEquiposCategorias($equipoVisitante, $idCategoria);

$resFixDetalle	= $serviciosReferencias->traerFixtureDetallePorId($idFixture);

$refCanchas		=	$serviciosReferencias->traerCanchas();
if (mysql_result($resFixDetalle,0,'refcanchas') == '') {
	$cadCanchas	=	$serviciosFunciones->devolverSelectBox($refCanchas,array(1),'');
} else {
	$cadCanchas	=	$serviciosFunciones->devolverSelectBoxActivo($refCanchas,array(1),'',mysql_result($resFixDetalle,0,'refcanchas'));
}


$refArbitros	=	$serviciosReferencias->traerArbitros();
if (mysql_result($resFixDetalle,0,'refarbitros') == '') {
	$cadArbitros	=	$serviciosFunciones->devolverSelectBox($refArbitros,array(1),'');
} else {
	$cadArbitros	=	$serviciosFunciones->devolverSelectBoxActivo($refArbitros,array(1),'',mysql_result($resFixDetalle,0,'refarbitros'));
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title><?php echo $tituloWeb; ?></title>
    <!-- Favicon-->
    <link rel="icon" href="../../favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

	<?php echo $baseHTML->cargarArchivosCSS('../../'); ?>

	<link href="../../plugins/waitme/waitMe.css" rel="stylesheet" />
	<link href="../../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

	<!-- VUE JS -->
	<script src="../../js/vue.min.js"></script>

	<!-- axios -->
	<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

	<script src="https://unpkg.com/vue-swal"></script>

	<script src="../../js/image-compressor.js"></script>



   <link rel="stylesheet" href="../../css/chosen.css">


	<!-- Bootstrap Material Datetime Picker Css -->
    <link href="../../plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

	<!-- Dropzone Css -->
    <link href="../../plugins/dropzone/dropzone.css" rel="stylesheet">

    <style>
        .alert > i{ vertical-align: middle !important; }


	</style>


</head>



<body class="theme-red">

<!-- Page Loader -->
<div class="page-loader-wrapper">
	<div class="loader">
		<div class="preloader">
			<div class="spinner-layer pl-red">
				<div class="circle-clipper left">
					<div class="circle"></div>
				</div>
				<div class="circle-clipper right">
					<div class="circle"></div>
				</div>
			</div>
		</div>
		<p>Cargando...</p>
	</div>
</div>
<!-- #END# Page Loader -->
<!-- Overlay For Sidebars -->
<div class="overlay"></div>
<!-- #END# Overlay For Sidebars -->
<!-- Search Bar -->
<div class="search-bar">
	<div class="search-icon">
		<i class="material-icons">search</i>
	</div>
	<input type="text" placeholder="Ingrese palabras...">
	<div class="close-search">
		<i class="material-icons">close</i>
	</div>
</div>
<!-- #END# Search Bar -->
<!-- Top Bar -->
<?php echo $baseHTML->cargarNAV($breadCumbs); ?>
<!-- #Top Bar -->
<?php echo $baseHTML->cargarSECTION($_SESSION['usua_aif'], $_SESSION['nombre_aif'], $resMenu,'../../'); ?>
<main id="app">
<section class="content" style="margin-top:-75px;">

	<div class="container-fluid">
		<div class="row clearfix">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div class="card ">
					<div class="header bg-blue">
						<h2>
							Cargar Partido - <?php echo mysql_result($resFecha,0,1); ?>
						</h2>
						<ul class="header-dropdown m-r--5">
							<li class="dropdown">
								<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
									<i class="material-icons">more_vert</i>
								</a>
								<ul class="dropdown-menu pull-right">
									<li><a href="javascript:void(0);" @click="showModal = true">Realizar Consulta</a></li>
								</ul>
							</li>
						</ul>
					</div>
					<div class="body table-responsive">
						<div class="boxInfoLargoEstadisticas">

    	<div class="cuerpoBox" style="padding-right:10px;">

			<button type="button" class="btn btn-primary waves-effect btnVolver">
				<i class="material-icons">keyboard_backspace</i>
				<span>VOLVER</span>
			</button>
    		<form class="form-inline formulario" id="target" role="form" method="post" action="cargarestadisticas.php">
        	<div class="row">
                <div class="col-md-3">
                	<p>Descripción: <span style="color:#00F"><?php echo mysql_result($resFixDetalle,0,'descripcion'); ?></span></p>
                </div>

                <div class="col-md-3">
                	<p>Tipo Torneo: <span style="color:#00F"><?php echo mysql_result($resFixDetalle,0,'tipotorneo'); ?></span></p>
                </div>
                <div class="col-md-3">
                	<p>Temporadas: <span style="color:#00F"><?php echo mysql_result($resFixDetalle,0,'temporada'); ?></span></p>
                </div>

                <div class="col-md-3">
                	<p>Categorias: <span style="color:#00F"><?php echo mysql_result($resFixDetalle,0,'categoria'); ?></span></p>
                </div>

                <div class="col-md-3">
                	<p>Divisiones: <span style="color:#00F"><?php echo mysql_result($resFixDetalle,0,'division'); ?></span></p>
                </div>

                <div class="col-md-3">
                	<p>Resp.Def. Tipo Jugadores <?php if (mysql_result($resFixDetalle,0,'respetadefiniciontipojugadores') == 'Si') {
								?>
										<span style="color:#3C0;" class="glyphicon glyphicon-ok"></span>
								<?php
										} else {
								?>
										<span style="color:#F00;" class="glyphicon glyphicon-remove"></span>
								<?php
										}
								?>

					</p>
                </div>
                <div class="col-md-3">
                	<p>Resp.Def. Habilitaciones Trans.<?php if (mysql_result($resFixDetalle,0,'respetadefinicionhabilitacionestransitorias') == 'Si') {
								?>
										<span style="color:#3C0;" class="glyphicon glyphicon-ok"></span>
								<?php
										} else {
								?>
										<span style="color:#F00;" class="glyphicon glyphicon-remove"></span>
								<?php
										}
								?></p>
                </div>
                <div class="col-md-3">
                	<p>Resp.Def. Sanciones Acumuladas<?php if (mysql_result($resFixDetalle,0,'respetadefinicionsancionesacumuladas') == 'Si') {
								?>
										<span style="color:#3C0;" class="glyphicon glyphicon-ok"></span>
								<?php
										} else {
								?>
										<span style="color:#F00;" class="glyphicon glyphicon-remove"></span>
								<?php
										}
								?></p>
                </div>
                <div class="col-md-3">
                	<p>Acumula Goleadores<?php if (mysql_result($resFixDetalle,0,'acumulagoleadores') == 'Si') {
								?>
										<span style="color:#3C0;" class="glyphicon glyphicon-ok"></span>
								<?php
										} else {
								?>
										<span style="color:#F00;" class="glyphicon glyphicon-remove"></span>
								<?php
										}
								?></p>
                </div>
                <div class="col-md-3">
                	<p>Acumula Tabla Conformada<?php if (mysql_result($resFixDetalle,0,'acumulatablaconformada') == 'Si') {
								?>
										<span style="color:#3C0;" class="glyphicon glyphicon-ok"></span>
								<?php
										} else {
								?>
										<span style="color:#F00;" class="glyphicon glyphicon-remove"></span>
								<?php
										}
								?></p>
                </div>
				<div class="col-md-3">
                	<p>Arbitro: <select data-placeholder="selecione el Arbitro..." id="refarbitros" name="refarbitros" class="chosen-select" tabindex="2" style="width:210px;">
            								<option value=""></option>
											<?php echo $cadArbitros; ?>
                                            </select></p>
                </div>
                <div class="col-md-3">
                	<p>Cancha: <select data-placeholder="selecione la cancha..." id="refcanchas" name="refcanchas" class="chosen-select" tabindex="2" style="width:210px;">
            								<option value=""></option>
											<?php echo $cadCanchas; ?>
                                            </select></p>
                </div>

                <div class="col-md-6 col-xs-offset-6">

                </div>

                <div class="col-md-4">
                	<p>Juez 1: <input type="text" class="form-control" id="juez1" name="juez1" value="<?php echo mysql_result($resFixDetalle,0,'juez1'); ?>"/></p>
                </div>
                <div class="col-md-4">
                	<p>Juez 2: <input type="text" class="form-control" id="juez2" name="juez2" value="<?php echo mysql_result($resFixDetalle,0,'juez2'); ?>"/></p>
                </div>

                <div class="col-md-4">
                	<p>Calificación Cancha: <input type="number" class="form-control" id="calificacioncancha" name="calificacioncancha" value="<?php echo mysql_result($resFixDetalle,0,'calificacioncancha'); ?>"/></p>
                </div>

                <div class="col-md-6">
                	<p style="font-size:1.6em">Resultado <?php echo $equipoA; ?>: <span class="resultadoA"><?php echo (mysql_result($resFixDetalle,0,'goleslocal') == '' ? 0 : mysql_result($resFixDetalle,0,'goleslocal')); ?></span></p>
                </div>
                <div class="col-md-6">
                	<p style="font-size:1.6em">Resultado <?php echo $equipoB; ?>: <span class="resultadoB"><?php echo (mysql_result($resFixDetalle,0,'golesvisitantes') == '' ? 0 : mysql_result($resFixDetalle,0,'golesvisitantes')); ?></span></p>
                </div>


                <div class='row' style="margin-left:15px; margin-right:15px;">
                    <div class="form-group col-md-4" style="display:block">
                        <label for="reftipodocumentos" class="control-label" style="text-align:left">Estado Partido</label>
                        <div class="input-group col-md-12">
                            <select class="form-control" id="refestadospartidos" name="refestadospartidos">
                                <option value="0">-- Seleccionar --</option>
                                <?php echo $cadEstados; ?>
                            </select>
                        </div>
                    </div>
                </div>

                </div>

					 <div class='row' style="margin-left:15px; margin-right:15px;">
	                 <div class='alert'>

	                 </div>
	                 <div class='alert <?php echo $lblerror; ?>'>
	                 	<p><?php echo $error; ?></p>
	                 </div>
	                 <div id='load'>

	                 </div>
	             </div>

					 <div class="row">

	                 <div style="margin-left:5px;padding-left:10px; border-left:12px solid #0C0; border-bottom:1px solid #eee;border-top:1px solid #CCC; margin-right:5px;">
	                 <h4 style="color: #fff; background-color:#333; padding:6px;margin-left:-10px; margin-top:0;"><span class="glyphicon glyphicon-signal"></span> Datos Partido</h4>

	                 <!--		detalles del partido			---->

	                 <!--		detalles fin			---->




	 					 <table class="table table-striped table-bordered table-responsive" id="example">
	 					   <caption style="font-size:1.5em; font-style:italic;">Equipo Local: <?php echo $equipoA; ?></caption>
	 					     <thead>
	 					      <tr>
	 					            <th style="text-align:center">DRSL</th>
	 					            <th>Jugador</th>
	 					             <th>DNI</th>
	 					             <th style="text-align:center">GA</th>
	 					             <th style="text-align:center">GC</th>
	 					             <th style="text-align:center">MIN</th>
	 					             <th style="text-align:center">PC</th>
	 					             <th style="text-align:center">PA</th>
	 					             <th style="text-align:center">PE</th>
	 					             <th style="text-align:center">MJ</th>
	 					             <th style="text-align:center; background-color:#FF0;">A</th>
	 					             <th style="text-align:center; background-color:#F00;">E</th>
	 					             <th style="text-align:center; background-color:#09F;">I</th>
	 					             <th style="text-align:center; background-color:#0C0;">A+A</th>
	 					             <th style="text-align:center; background-color:#333; color:#FFF;">CDTD</th>

	 					             <?php

	 					      if ($_SESSION['idroll_aif'] == 1) {

	 					      ?>
	 					             <th style="text-align:center">Fallo</th>
	 					             <th style="text-align:center">Ver</th>
	 					             <?php
	 					      }
	 					      ?>
	 					         </tr>
	 					     </thead>
	 					     <tbody>
	 					      <?php

	 					      while ($row = mysql_fetch_array($resJugadoresA)) {
	 					         $estadisticas	= $serviciosReferencias->traerEstadisticaPorFixtureJugadorCategoriaDivision($row['refjugadores'],$idFixture, $idCategoria, $idDivisiones);

	 					         $sancionAmarilla		=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($row['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 1);

	 					         $sancionRoja			=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($row['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 2);

	 					         $sancionInformados		=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($row['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 3);

	 					         $sancionDobleAmarilla	=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($row['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 4);

	 					         $sancionCDTD			=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($row['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 5);

	 					         $suspendidoDias				=	$serviciosReferencias->suspendidoPorDias($row['refjugadores'], $idTipoTorneo);

	 					         $suspendidoCategorias		=	$serviciosReferencias->hayMovimientos($row['refjugadores'],$idFixture, $idTipoTorneo);
	 					         $suspendidoCategoriasAA		=	$serviciosReferencias->hayMovimientosAmarillasAcumuladas($row['refjugadores'],$idFixture, $idCategoria, $idTipoTorneo);

	 					         $falloA					=	$serviciosReferencias->traerSancionesjugadoresPorJugadorFixtureConValor($row['refjugadores'],$idFixture);

	 					         $yaCumpli				=	$serviciosReferencias->estaFechaYaFueCumplida($row['refjugadores'],$idFixture);

	 					   /* todo para saber si esta o no inhabilitado */
	 					         $cadCumpleEdad = '';
	 					         $errorDoc = 'FALTA';
	 					         $cadErrorDoc = '';
	 					         $habilitacion= 'INHAB.';
	 					         $transitoria= '';
	 					         $valorDocumentacion = 0;
	 					         $documentaciones = '';



	 					         $edad = $serviciosReferencias->verificarEdad($row['refjugadores']);

	 					         $cumpleEdad = $serviciosReferencias->verificaEdadCategoriaJugador($row['refjugadores'], $idCategoria, $row['idtipojugador']);

	 					         $documentaciones = $serviciosReferencias->traerJugadoresdocumentacionPorJugadorValores($row['refjugadores']);

	 					         if ($cumpleEdad == 1) {
	 					            $cadCumpleEdad = "CUMPLE";
	 					         } else {
	 					            // VERIFICO SI EXISTE ALGUNA HABILITACION TRANSITORIA
	 					            $habilitacionTransitoria = $serviciosReferencias->traerJugadoresmotivoshabilitacionestransitoriasPorJugadorDeportiva($row['refjugadores'], $idTemporada, $idCategoria, $equipoLocal);
	 					            if (mysql_num_rows($habilitacionTransitoria)>0) {
	 					               $cadCumpleEdad = "HAB. TRANS.";
	 					               $habilitacion= 'HAB.';
	 					            } else {
	 					               $cadCumpleEdad = "NO CUMPLE";
	 					            }
	 					         }

	 					         if (mysql_num_rows($documentaciones)>0) {
	 					            while ($rowH = mysql_fetch_array($documentaciones)) {
	 					               if (($rowH['valor'] == 'No') && ($rowH['contravalor'] == 'No')) {
	 					                  if ($rowH['obligatoria'] == 'Si') {
	 					                     $valorDocumentacion += 1;
	 					                     if (mysql_num_rows($serviciosReferencias->traerJugadoresmotivoshabilitacionestransitoriasPorJugadorAdministrativaDocumentacion($row['refjugadores'],$rowH['refdocumentaciones']))>0) {
	 					                        $valorDocumentacion -= 1;
	 					                     }
	 					                  }
	 					                  if ($rowH['contravalordesc'] == '') {
	 					                     $cadErrorDoc .= strtoupper($rowH['descripcion']).' - ';
	 					                  } else {
	 					                     $cadErrorDoc .= strtoupper($rowH['contravalordesc']).' - ';
	 					                  }
	 					               }
	 					            }
	 					            if ($cadErrorDoc == '') {
	 					               $cadErrorDoc = 'OK';
	 					               $errorDoc = 'OK';
	 					            } else {
	 					               $cadErrorDoc = substr($cadErrorDoc,0,-3);
	 					            }

	 					         } else {
	 					            $cadErrorDoc = 'FALTAN PRESENTAR TODAS LAS DOCUMENTACIONES';
	 					         }

	 					         if ($valorDocumentacion <= 0 && ($cadCumpleEdad == 'CUMPLE' || $cadCumpleEdad == "HAB. TRANS.")) {
	 					            if ($cadErrorDoc == 'FALTAN PRESENTAR TODAS LAS DOCUMENTACIONES') {
	 					               $habilitacion= 'INHAB.';
	 					            } else {
	 					               $habilitacion= 'HAB.';
	 					            }
	 					         } else {
	 					            $habilitacion= 'INHAB.';
	 					         }

	 					         /* fin todo para saber si esta o no inhabilitado */

	 					   if (!(($suspendidoDias == 0) && ($suspendidoCategorias == 0) && ($suspendidoCategoriasAA == 0) && ($yaCumpli == 0))) {

	 					      // si entro aca esta suspendido el jugador//

	 					      // cargo que la fecha no la cumplio
	 					      if ($finalizado == 'Si') {
	 					        if ($partidoSuspendidoCompletamente == 0) {
	 					           $serviciosReferencias->insertarSancionesfechascumplidas($idFixture,$row['refjugadores'],1,'', $idTipoTorneo);
	 					        } else {
	 					           $serviciosReferencias->insertarSancionesfechascumplidas($idFixture,$row['refjugadores'],0,'', $idTipoTorneo);
	 					        }
	 					      }

	 					 ?>

	 					    <tr class="<?php echo $row[0]; ?>">
	 					      <th style="background-color:#F00;">

	 					             </th>
	 					            <th style="background-color:#F00;">
	 					         <?php echo $row['nombrecompleto']; ?>
	 					             </th>
	 					             <th style="background-color:#F00;">
	 					         <?php echo $row['nrodocumento']; ?>
	 					             </th>

	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>
	 					             <th style="background-color:#F00;">

	 					             </th>

	 					              <?php


	 					            if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
	 					               if ($falloA > 0) {
	 					                  $resFallo = $serviciosReferencias->traerSancionesJugadoresConFallosPorSancion($falloA, $idTipoTorneo);

	 					                  $fallo	= '';

	 					                  $amarillas		=	mysql_result($resFallo,0,'amarillas');
	 					                  $cantidadfechas	=	mysql_result($resFallo,0,'cantidadfechas');
	 					                  $fechadesde		=	mysql_result($resFallo,0,'fechadesde');
	 					                  $fechahasta		=	mysql_result($resFallo,0,'fechahasta');
	 					                  $pendiente		=	mysql_result($resFallo,0,'pendientesfallo');
	 					                  $observaciones	=	mysql_result($resFallo,0,'observaciones');

	 					                  if ($amarillas > 0) {
	 					                     $fallo = 'Doble Amarilla';
	 					                  } else {
	 					                     if ($fechadesde != '00/00/0000') {
	 					                        $fallo = 'Dias: desde '.$fechadesde.' hasta '.$fechahasta;
	 					                     } else {
	 					                        if ($pendiente == 'Si') {
	 					                           $fallo = 'Pendiente';
	 					                        } else {
	 					                           $fallo = 'Cantidad de Fechas:'.$cantidadfechas;
	 					                        }
	 					                     }
	 					                  }
	 					            ?>
	 					                   <th style="text-align:center"><?php echo $fallo; ?></th>
	 					                   <th style="text-align:center"><a href="../fallos/modificarfechas.php?id=<?php echo $falloA; ?>">Ver</a></th>
	 					                   <input type="hidden" id="sancionJugadores<?php echo $row['refjugadores']; ?>" name="sancionJugadores<?php echo $row['refjugadores']; ?>" value="1"/>
	 					                   <?php
	 					               } else {

	 					            ?>
	 					                   <th style="text-align:center"></th>
	 					                   <th style="text-align:center"></th>
	 					                   <?php
	 					               }
	 					            }

	 					            ?>


	 					         </tr>

	 					 <?php
	 					   } else {

	 					   if (($suspendidoDias == 0) && ($suspendidoCategorias == 0) && ($suspendidoCategoriasAA == 0) && ($yaCumpli == 0)) {

	 					      if (in_array($row['refjugadores'], $arDetalleLocal)) {
	 					   ?>
	 					         <tr class="<?php echo $row[0]; ?>">
	 					           <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm dorsalEA" name="dorsal<?php echo $row['refjugadores']; ?>" id="dorsal<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticas,0,'dorsal'); ?>"/>
	 					                 </div>
	 					             </th>
	 					            <th>
	 					         <?php echo $row['nombrecompleto']; ?>
	 					             </th>
	 					             <th>
	 					         <?php echo $row['nrodocumento']; ?>
	 					             </th>

	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm golesEA" name="goles<?php echo $row['refjugadores']; ?>" id="goles<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticas,0,'goles'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm golescontraEA" name="encontra<?php echo $row['refjugadores']; ?>" id="encontra<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticas,0,'encontra'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm minutos" name="minutos<?php echo $row['refjugadores']; ?>" id="minutos<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php if (mysql_result($estadisticas,0,'minutosjugados')==-1) { echo $minutos; } else { echo mysql_result($estadisticas,0,'minutosjugados'); } ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm penalesconvertidosEA" name="penalesconvertidos<?php echo $row['refjugadores']; ?>" id="penalesconvertidos<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticas,0,'penalconvertido'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm penalesatajados" name="penalesatajados<?php echo $row['refjugadores']; ?>" id="penalesatajados<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticas,0,'penalatajado'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm penaleserrados" name="penaleserrados<?php echo $row['refjugadores']; ?>" id="penaleserrados<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticas,0,'penalerrado'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="checkbox" class="form-control input-sm mejor" id="mejorjugador<?php echo $row['refjugadores']; ?>" name="mejorjugador<?php echo $row['refjugadores']; ?>" <?php if (mysql_result($estadisticas,0,'mejorjugador')== 'Si') { echo 'checked'; } ?> style="width:30px;"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#FF0">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm amarillas" name="amaLrillas<?php echo $row['refjugadores']; ?>" id="amaLrillas<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionAmarilla; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#F00">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm rojas" name="roLjas<?php echo $row['refjugadores']; ?>" id="roLjas<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionRoja; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#09F">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm informados" name="inforLmados<?php echo $row['refjugadores']; ?>" id="inforLmados<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionInformados; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#0C0">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm dobleamarilla" name="dobleLamarilla<?php echo $row['refjugadores']; ?>" id="dobleLamarilla<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionDobleAmarilla; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#333; color:#FFF;">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm cdtd" name="cdLtd<?php echo $row['refjugadores']; ?>" id="cdLtd<?php echo $row['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionCDTD; ?>"/>
	 					                 </div>
	 					             </th>



	 					         <?php

	 					      if ($_SESSION['idroll_aif'] == 1) {
	 					         if ($falloA > 0) {
	 					            $resFallo = $serviciosReferencias->traerSancionesJugadoresConFallosPorSancion($falloA, $idTipoTorneo);

	 					            $fallo	= '';

	 					            $amarillas		=	mysql_result($resFallo,0,'amarillas');
	 					            $cantidadfechas	=	mysql_result($resFallo,0,'cantidadfechas');
	 					            $fechadesde		=	mysql_result($resFallo,0,'fechadesde');
	 					            $fechahasta		=	mysql_result($resFallo,0,'fechahasta');
	 					            $pendiente		=	mysql_result($resFallo,0,'pendientesfallo');
	 					            $observaciones	=	mysql_result($resFallo,0,'observaciones');

	 					            if ($amarillas > 0) {
	 					               $fallo = 'Doble Amarilla';
	 					            } else {
	 					               if ($fechadesde != '00/00/0000') {
	 					                  $fallo = 'Dias: desde '.$fechadesde.' hasta '.$fechahasta;
	 					               } else {
	 					                  if ($pendiente == 'Si') {
	 					                     $fallo = 'Pendiente';
	 					                  } else {
	 					                     $fallo = 'Cantidad de Fechas:'.$cantidadfechas;
	 					                  }
	 					               }
	 					            }
	 					      ?>
	 					             <th style="text-align:center"><?php echo $fallo; ?></th>
	 					             <th style="text-align:center"><a href="../sancionesfechascumplidas/index.php?id=<?php echo $falloA; ?>">Ver</a></th>
	 					             <?php
	 					         } else {

	 					      ?>
	 					             <th style="text-align:center"></th>
	 					             <th style="text-align:center"></th>
	 					             <?php
	 					         }
	 					         } //fin del detalle del partido
	 					      }
	 					      ?>

	 					         </tr>
	 					         <?php
	 					      /* else del suspendidos */
	 					      } else {

	 					   ?>


	 					         <?php } ?>
	 					         <?php
	 					         }
	 					         $goles = 0;
	 					      }
	 					   ?>
	 					     </tbody>
	 					 </table>

	                 </div>



	                 <hr>

	                 <div style="margin-left:5px;padding-left:10px;border-left:12px solid #C00; border-bottom:1px solid #eee; border-top:1px solid #CCC;margin-right:5px;">
	                 <h4 style="color: #fff; background-color:#333; padding:6px;margin-left:-10px; margin-top:0;"><span class="glyphicon glyphicon-signal"></span> Datos Partido</h4>

	 					 <table class="table table-striped table-bordered table-responsive" id="example2">
	 					   <caption style="font-size:1.5em; font-style:italic;">Equipo Visitante: <?php echo $equipoB; ?></caption>
	 					     <thead>
	 					      <tr>
	 					      <th style="text-align:center">DRSL</th>
	 					            <th>Jugador</th>
	 					             <th>DNI</th>
	 					             <th style="text-align:center">GA</th>
	 					             <th style="text-align:center">GC</th>
	 					             <th style="text-align:center">MIN</th>
	 					             <th style="text-align:center">PC</th>
	 					             <th style="text-align:center">PA</th>
	 					             <th style="text-align:center">PE</th>
	 					             <th style="text-align:center">MJ</th>
	 					             <th style="text-align:center; background-color:#FF0;">A</th>
	 					             <th style="text-align:center; background-color:#F00;">E</th>
	 					             <th style="text-align:center; background-color:#09F;">I</th>
	 					             <th style="text-align:center; background-color:#0C0;">A+A</th>
	 					             <th style="text-align:center; background-color:#333; color:#FFF;">CDTD</th>

	 					             <?php

	 					      if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {

	 					      ?>
	 					             <th style="text-align:center">Fallo</th>
	 					             <th style="text-align:center">Ver</th>
	 					             <?php
	 					      }
	 					      ?>
	 					         </tr>
	 					     </thead>
	 					     <tbody>
	 					      <?php

	 					      while ($rowB = mysql_fetch_array($resJugadoresB)) {
	 					         $estadisticasB = $serviciosReferencias->traerEstadisticaPorFixtureJugadorCategoriaDivisionVisitante($rowB['refjugadores'],$idFixture, $idCategoria, $idDivisiones);

	 					         $sancionAmarilla		=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($rowB['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 1);

	 					         $sancionRoja			=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($rowB['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 2);

	 					         $sancionInformados		=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($rowB['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 3);

	 					         $sancionDobleAmarilla	=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($rowB['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 4);

	 					         $sancionCDTD			=	$serviciosReferencias->traerSancionesjugadoresPorJugadorConValor($rowB['refjugadores'],$idFixture, $idCategoria, $idDivisiones, 5);

	 					         $suspendidoDiasB			=	$serviciosReferencias->suspendidoPorDias($rowB['refjugadores'], $idTipoTorneo);

	 					         $suspendidoCategoriasB		=	$serviciosReferencias->hayMovimientos($rowB['refjugadores'],$idFixture, $idTipoTorneo);
	 					         $suspendidoCategoriasAAB	=	$serviciosReferencias->hayMovimientosAmarillasAcumuladas($rowB['refjugadores'],$idFixture, $idCategoria, $idTipoTorneo);

	 					         $falloB					=	$serviciosReferencias->traerSancionesjugadoresPorJugadorFixtureConValor($rowB['refjugadores'],$idFixture);

	 					         $yaCumpliB				=	$serviciosReferencias->estaFechaYaFueCumplida($rowB['refjugadores'],$idFixture);

	 					   /* todo para saber si esta o no inhabilitado */
	 					         $cadCumpleEdad = '';
	 					         $errorDoc = 'FALTA';
	 					         $cadErrorDoc = '';
	 					         $habilitacion= 'INHAB.';
	 					         $transitoria= '';
	 					         $valorDocumentacion = 0;
	 					         $documentaciones = '';



	 					         $edad = $serviciosReferencias->verificarEdad($rowB['refjugadores']);

	 					         $cumpleEdad = $serviciosReferencias->verificaEdadCategoriaJugador($rowB['refjugadores'], $idCategoria, $rowB['idtipojugador']);

	 					         $documentaciones = $serviciosReferencias->traerJugadoresdocumentacionPorJugadorValores($rowB['refjugadores']);

	 					         if ($cumpleEdad == 1) {
	 					            $cadCumpleEdad = "CUMPLE";
	 					         } else {
	 					            // VERIFICO SI EXISTE ALGUNA HABILITACION TRANSITORIA
	 					            $habilitacionTransitoria = $serviciosReferencias->traerJugadoresmotivoshabilitacionestransitoriasPorJugadorDeportiva($rowB['refjugadores'], $idTemporada, $idCategoria, $equipoVisitante);
	 					            if (mysql_num_rows($habilitacionTransitoria)>0) {
	 					               $cadCumpleEdad = "HAB. TRANS.";
	 					               $habilitacion= 'HAB.';
	 					            } else {
	 					               $cadCumpleEdad = "NO CUMPLE";
	 					            }
	 					         }

	 					         if (mysql_num_rows($documentaciones)>0) {
	 					            while ($rowH = mysql_fetch_array($documentaciones)) {
	 					               if (($rowH['valor'] == 'No') && ($rowH['contravalor'] == 'No')) {
	 					                  if ($rowH['obligatoria'] == 'Si') {
	 					                     $valorDocumentacion += 1;
	 					                     if (mysql_num_rows($serviciosReferencias->traerJugadoresmotivoshabilitacionestransitoriasPorJugadorAdministrativaDocumentacion($rowB['refjugadores'],$rowH['refdocumentaciones']))>0) {
	 					                        $valorDocumentacion -= 1;
	 					                     }
	 					                  }
	 					                  if ($rowH['contravalordesc'] == '') {
	 					                     $cadErrorDoc .= strtoupper($rowH['descripcion']).' - ';
	 					                  } else {
	 					                     $cadErrorDoc .= strtoupper($rowH['contravalordesc']).' - ';
	 					                  }
	 					               }
	 					            }
	 					            if ($cadErrorDoc == '') {
	 					               $cadErrorDoc = 'OK';
	 					               $errorDoc = 'OK';
	 					            } else {
	 					               $cadErrorDoc = substr($cadErrorDoc,0,-3);
	 					            }

	 					         } else {
	 					            $cadErrorDoc = 'FALTAN PRESENTAR TODAS LAS DOCUMENTACIONES';
	 					         }

	 					         if ($valorDocumentacion <= 0 && ($cadCumpleEdad == 'CUMPLE' || $cadCumpleEdad == "HAB. TRANS.")) {
	 					            if ($cadErrorDoc == 'FALTAN PRESENTAR TODAS LAS DOCUMENTACIONES') {
	 					               $habilitacion= 'INHAB.';
	 					            } else {
	 					               $habilitacion= 'HAB.';
	 					            }
	 					         } else {
	 					            $habilitacion= 'INHAB.';
	 					         }

	 					         /* fin todo para saber si esta o no inhabilitado */

	 					   if (!(($suspendidoDiasB == 0) && ($suspendidoCategoriasB == 0) && ($suspendidoCategoriasAAB == 0) && ($yaCumpliB == 0))) {

	 					      /*
	 					      if ($rowB['refjugadores']== 5637) {
	 					        die('acaaaaaaaaaaaaaaaaaaa');
	 					      }*/
	 					      // cargo que la fecha no la cumplio
	 					      if ($finalizado == 'Si') {
	 					        if ($partidoSuspendidoCompletamente == 0) {
	 					           $serviciosReferencias->insertarSancionesfechascumplidas($idFixture,$rowB['refjugadores'],1,'', $idTipoTorneo);
	 					        } else {
	 					           $serviciosReferencias->insertarSancionesfechascumplidas($idFixture,$rowB['refjugadores'],0,'', $idTipoTorneo);
	 					        }
	 					      }
	 					 ?>

	 					 <tr class="<?php echo $rowB[0]; ?>">
	 					 <th style="background-color:#F00;">

	 					     </th>

	 					    <th style="background-color:#F00;">
	 					 <?php echo $rowB['nombrecompleto']; ?>
	 					     </th>
	 					     <th style="background-color:#F00;">
	 					 <?php echo $rowB['nrodocumento']; ?>
	 					     </th>

	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>
	 					     <th style="background-color:#F00;">

	 					     </th>

	 					     <?php

	 					 if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
	 					 if ($falloB > 0) {
	 					    $resFallo = $serviciosReferencias->traerSancionesJugadoresConFallosPorSancion($falloB, $idTipoTorneo);

	 					    $fallo	= '';

	 					    $amarillas		=	mysql_result($resFallo,0,'amarillas');
	 					    $cantidadfechas	=	mysql_result($resFallo,0,'cantidadfechas');
	 					    $fechadesde		=	mysql_result($resFallo,0,'fechadesde');
	 					    $fechahasta		=	mysql_result($resFallo,0,'fechahasta');
	 					    $pendiente		=	mysql_result($resFallo,0,'pendientesfallo');
	 					    $observaciones	=	mysql_result($resFallo,0,'observaciones');

	 					    if ($amarillas > 0) {
	 					       $fallo = 'Doble Amarilla';
	 					    } else {
	 					       if ($fechadesde != '00/00/0000') {
	 					          $fallo = 'Dias: desde '.$fechadesde.' hasta '.$fechahasta;
	 					       } else {
	 					          if ($pendiente == 'Si') {
	 					             $fallo = 'Pendiente';
	 					          } else {
	 					             $fallo = 'Cantidad de Fechas:'.$cantidadfechas;
	 					          }
	 					       }
	 					    }
	 					 ?>
	 					     <th style="text-align:center"><?php echo $fallo; ?></th>
	 					     <th style="text-align:center"><a href="../fallos/modificarfechas.php?id=<?php echo $falloB; ?>">Ver</a></th>
	 					     <?php
	 					 } else {

	 					 ?>
	 					     <th style="text-align:center"></th>
	 					     <th style="text-align:center"></th>
	 					     <?php
	 					 }
	 					 }

	 					 ?>


	 					 </tr>

	 					 <?php } else {
	 					   if (($suspendidoDiasB == 0) && ($suspendidoCategoriasB == 0) && ($suspendidoCategoriasAAB == 0) && ($yaCumpliB == 0)) {

	 					      if (in_array($rowB['refjugadores'], $arDetalleVisit)) {
	 					   ?>
	 					         <tr class="<?php echo $rowB[0]; ?>">
	 					      <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm dorsalEB" name="dorbsal<?php echo $rowB['refjugadores']; ?>" id="dorbsal<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticasB,0,'dorsal'); ?>"/>
	 					                 </div>
	 					             </th>

	 					            <th>
	 					         <?php echo $rowB['nombrecompleto']; ?>
	 					             </th>
	 					             <th>
	 					         <?php echo $rowB['nrodocumento']; ?>
	 					             </th>

	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm golesEB" name="gobles<?php echo $rowB['refjugadores']; ?>" id="gobles<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticasB,0,'goles'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm golescontraEB" name="enbcontra<?php echo $rowB['refjugadores']; ?>" id="enbcontra<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticasB,0,'encontra'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm minutosEB" name="minbutos<?php echo $rowB['refjugadores']; ?>" id="minbutos<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php if (mysql_result($estadisticasB,0,'minutosjugados')==-1) { echo $minutos; } else { echo mysql_result($estadisticasB,0,'minutosjugados'); } ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm penalesconvertidosEB" name="penalesbconvertidos<?php echo $rowB['refjugadores']; ?>" id="penalesbconvertidos<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticasB,0,'penalconvertido'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm penalesatajados" name="penalesbatajados<?php echo $rowB['refjugadores']; ?>" id="penalesbatajados<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticasB,0,'penalatajado'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm penaleserrados" name="penalesberrados<?php echo $rowB['refjugadores']; ?>" id="penalesberrados<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo mysql_result($estadisticasB,0,'penalerrado'); ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th>
	 					               <div align="center">
	 					                  <input type="checkbox" class="form-control input-sm mejor" id="mejorbjugador<?php echo $rowB['refjugadores']; ?>" name="mejorbjugador<?php echo $rowB['refjugadores']; ?>" <?php if (mysql_result($estadisticasB,0,'mejorjugador')== 'Si') { echo 'checked'; } ?> style="width:30px;"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#FF0">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm amarillas" name="amaVrillas<?php echo $rowB['refjugadores']; ?>" id="amaVrillas<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionAmarilla; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#F00">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm rojas" name="roVjas<?php echo $rowB['refjugadores']; ?>" id="roVjas<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionRoja; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#09F">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm informados" name="inforVmados<?php echo $rowB['refjugadores']; ?>" id="inforVmados<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionInformados; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#0C0">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm dobleamarilla" name="dobleVamarilla<?php echo $rowB['refjugadores']; ?>" id="dobleVamarilla<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionDobleAmarilla; ?>"/>
	 					                 </div>
	 					             </th>
	 					             <th style="text-align:center; background-color:#333; color:#FFF;">
	 					               <div align="center">
	 					                  <input type="text" class="form-control input-sm cdtd" name="cdVtd<?php echo $rowB['refjugadores']; ?>" id="cdVtd<?php echo $rowB['refjugadores']; ?>" style="width:45px;" value="<?php echo $sancionCDTD; ?>"/>
	 					                 </div>
	 					             </th>



	 					         <?php


	 					      if (($_SESSION['idroll_aif'] == 1) || ($_SESSION['idroll_aif'] == 2)) {
	 					         if ($falloB > 0) {
	 					            $resFallo = $serviciosReferencias->traerSancionesJugadoresConFallosPorSancion($falloB, $idTipoTorneo);

	 					            $fallo	= '';

	 					            $amarillas		=	mysql_result($resFallo,0,'amarillas');
	 					            $cantidadfechas	=	mysql_result($resFallo,0,'cantidadfechas');
	 					            $fechadesde		=	mysql_result($resFallo,0,'fechadesde');
	 					            $fechahasta		=	mysql_result($resFallo,0,'fechahasta');
	 					            $pendiente		=	mysql_result($resFallo,0,'pendientesfallo');
	 					            $observaciones	=	mysql_result($resFallo,0,'observaciones');

	 					            if ($amarillas > 0) {
	 					               $fallo = 'Doble Amarilla';
	 					            } else {
	 					               if ($fechadesde != '00/00/0000') {
	 					                  $fallo = 'Dias: desde '.$fechadesde.' hasta '.$fechahasta;
	 					               } else {
	 					                  if ($pendiente == 'Si') {
	 					                     $fallo = 'Pendiente';
	 					                  } else {
	 					                     $fallo = 'Cantidad de Fechas:'.$cantidadfechas;
	 					                  }
	 					               }
	 					            }
	 					      ?>
	 					             <th style="text-align:center"><?php echo $fallo; ?></th>
	 					             <th style="text-align:center"><a href="../fallos/modificarfechas.php?id=<?php echo $falloB; ?>">Ver</a></th>
	 					             <input type="hidden" id="sancionBJugadores<?php echo $rowB['refjugadores']; ?>" name="sancionBJugadores<?php echo $rowB['refjugadores']; ?>" value="1"/>
	 					             <?php
	 					         } else {

	 					      ?>
	 					                     <th style="text-align:center"></th>
	 					                     <th style="text-align:center"></th>
	 					             <?php
	 					         }
	 					         } //fin del detalle partido
	 					      }
	 					      ?>
	 					         </tr>
	 					         <?php
	 					      /* else del suspendidos */
	 					      } else {

	 					   ?>

	 					         <?php } ?>
	 					         <?php
	 					         }
	 					         $goles = 0;
	 					      }
	 					   ?>
	 					     </tbody>
	 					 </table>



	 				</div>

	             <div class="row" style="margin-left:15px; margin-right:15px;">
	                 <div class="col-md-12">
	                 <ul class="list-inline" style="margin-top:15px;">


	                     <li>
	                         <button type="button" class="btn btn-default volver">Volver</button>
	                     </li>
	                 </ul>
	                 </div>
	             </div>
	             <input type="hidden" id="accion" name="accion" value="insertarEstadisticaMasiva" />
	             <input type="hidden" id="idfixture" name="idfixture" value="<?php echo $idFixture; ?>" />
	             </form>
	     	</div>
			<div class="row clearfix subirImagen">
				<div class="col-xs-6 col-md-6 col-lg-6">
					<a href="javascript:void(0);" class="thumbnail">
						<img class="img-responsive">
					</a>
					<div id="example1"></div>

				</div>

			</div>
    </div>


</div><!-- fin del boxInfoLargoEstadisticas -->
					</div>
				</div>
			</div>


		</div>
	</div>



</section>


<?php
if (count($filesPlanilla)<1) {
?>
<!-- Modal -->
<div class="modal fade" id="myModalPlanilla" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Estado de la Planilla</h4>
			</div>
		<div class="modal-body">
			<h1>Aun no se cargo la imagen de la planilla</h1>
			<h3>Recuerde que debe cargar la planilla.</h3>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		</div>
		</div>

	</div>
</div>
<?php }  ?>

<?php
if (count($filesComplemento)<1) {
?>
<!-- Modal -->
<div class="modal fade" id="myModalComplemento" role="dialog">
	<div class="modal-dialog modal-lg">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Estado del Complemento</h4>
			</div>
		<div class="modal-body">
			<h1>Aun no se cargo la imagen del Complemento</h1>
			<h3>Recuerde que debe cargar el Complemento.</h3>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		</div>
		</div>

	</div>
</div>
<?php }  ?>


<?php echo $baseHTML->cargarArchivosJS('../../'); ?>
<!-- Wait Me Plugin Js -->
<script src="../../plugins/waitme/waitMe.js"></script>

<!-- Custom Js -->
<script src="../../js/pages/cards/colored.js"></script>

<!-- Bootstrap Material Datetime Picker Plugin Js -->
<script src="../../plugins/jquery-inputmask/jquery.inputmask.bundle.js"></script>

<!-- Dropzone Plugin Js -->
<script src="../../plugins/dropzone/dropzone.js"></script>


<form class="form" @submit.prevent="realizarConsulta">
<script type="text/x-template" id="modal-template">
  <transition name="modal">
    <div class="modal-mask">
      <div class="modal-wrapper">
        <div class="modal-container">

          <div class="modal-header">
            <slot name="header">
              default header
            </slot>
          </div>

          <div class="modal-body">
            <slot name="body">
			  <h4>Ingrese su consulta y en la brevedad se comunicarán con usted</h4>
			  	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<label class="form-label">Mensaje</label>
					<div class="form-group">
						<div class="form-line">
							<input type="text" class="form-control" id="mensaje" name="mensaje" />

						</div>
					</div>
				</div>
            </slot>
          </div>

          <div class="modal-footer">
            <slot name="footer">
			<button class="btn bg-grey waves-effect" @click="$emit('close')">
                CANCELAR
			  </button>
			  <button type="button" class="btn bg-green waves-effect" @click="enviarConsulta()">
					<i class="material-icons">send</i>
					<span>ENVIAR</span>
				</button>

            </slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</script>
</form>

  <!-- use the modal component, pass in the prop -->
  <modal v-if="showModal" @close="showModal = false">
    <!--
      you can use custom content here to overwrite
      default content
    -->
    <h3 slot="header">Realizar Consulta</h3>
  </modal>


</main>

<script type="text/javascript" src="../../DataTables/datatables.min.js"></script>
<script src="../../bootstrap/js/dataTables.bootstrap.js"></script>

<script src="../../js/fnFilterClear.js"></script>
<script src="../../js/jquery.number.js"></script>

<script type="text/javascript">

			$(function(){
				// Set up the number formatting.
				/*
				$('#goles3').number( true, 2 );
				$('#goles3').number( true, 2 );*/
				$('.golesEA').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.golesEB').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.golescontraEA').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.golescontraEB').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.dorsalEA').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.dorsalEB').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.minutos').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('<?php echo $minutos; ?>');
					}
				});

				$('.minutosEB').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('<?php echo $minutos; ?>');
					}
				});

				$('.penalesconvertidosEA').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.penalesconvertidosEB').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.penalesatajados').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.penaleserrados').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.amarillas').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.rojas').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.informados').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.dobleamarilla').on("keyup", function() {
					if ($(this).val() == '') {
						$(this).val('0');
					}
				});

				$('.golesEB').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.golescontraEB').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.golesEA').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.golescontraEA').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.amarillas').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.rojas').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.informados').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.dobleamarilla').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.cdtd').each(function(intIndex){
					$(this).number( true, 0 );
				});


				$('.golesEA').change(function(e) {

					var acumulado = 0;
					$('.golesEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.golescontraEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.penalesconvertidosEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});

					$('.resultadoA').html(acumulado);
				});


				$('.golescontraEB').change(function(e) {
					var acumulado = 0;
					$('.golesEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.golescontraEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.penalesconvertidosEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.resultadoA').html(acumulado);
				});


				$('.penalesconvertidosEA').change(function(e) {
					var acumulado = 0;
					$('.golesEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.golescontraEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.penalesconvertidosEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});

					$('.resultadoA').html(acumulado);
				});





				$('.golesEB').change(function(e) {
					var acumulado = 0;
					$('.golesEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.golescontraEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.penalesconvertidosEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.resultadoB').html(acumulado);
				});


				$('.golescontraEA').change(function(e) {
					var acumulado = 0;
					$('.golesEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.golescontraEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.penalesconvertidosEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.resultadoB').html(acumulado);
				});

				$('.penalesconvertidosEB').change(function(e) {
					var acumulado = 0;
					$('.golesEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.golescontraEA').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.penalesconvertidosEB').each(function(intIndex){
						acumulado += parseInt($(this).val());
					});
					$('.resultadoB').html(acumulado);
				});

				$('.minutos').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > <?php echo $minutos; ?>) {
							$(this).val(<?php echo $minutos; ?>);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.minutosEB').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > <?php echo $minutos; ?>) {
							$(this).val(<?php echo $minutos; ?>);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.amarillas').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > 1) {
							$(this).val(2);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.rojas').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > 1) {
							$(this).val(1);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.informados').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > 1) {
							$(this).val(1);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.dobleamarilla').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > 1) {
							$(this).val(1);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.cdtd').each(function(intIndex){
					$(this).number( true, 0 );
					$(this).change( function() {
						if ($(this).val() > 1) {
							$(this).val(1);
						}
						if ($(this).val() < 0) {
							$(this).val(0);
						}
					});
				});

				$('.penalesconvertidos').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.penalesatajados').each(function(intIndex){
					$(this).number( true, 0 );
				});

				$('.penaleserrados').each(function(intIndex){
					$(this).number( true, 0 );
				});

			});
		</script>


<script>


	function traerImagen() {
		$.ajax({
			data:  {idfixture: <?php echo $id; ?>,
					idarbitro: <?php echo $_SESSION['idarbitro_aif']; ?>,
					accion: 'traerArchivoPlanillaPorArbitroFixture'},
			url:   '../../ajax/ajax.php',
			type:  'post',
			beforeSend: function () {

			},
			success:  function (response) {
				var cadena = response.datos.type.toLowerCase();

				if (response.datos.type != '') {
					if (cadena.indexOf("pdf") > -1) {
						PDFObject.embed(response.datos.imagen, "#example1");
						$('#example1').show();
						$(".thumbnail").hide();
					} else {
						$(".thumbnail img").attr("src",response.datos.imagen);
						$(".thumbnail").show();
						$('#example1').hide();
					}
				}

			}
		});
	}

	traerImagen();


	$(document).ready(function(){

		$('.btnVolver').click(function() {
			url = "estadistica.php?id=<?php echo $id; ?>";
			$(location).attr('href',url);
		});

		$('.volver').click(function() {
			url = "estadistica.php?id=<?php echo $id; ?>";
			$(location).attr('href',url);
		});


	});
</script>




<script>


	Vue.component('modal', {
		template: '#modal-template',
		methods: {
			enviarConsulta () {

				paramsNotificacion.set('mensaje',$('#mensaje').val());

				axios.post('../../ajax/ajax.php', paramsNotificacion)
				.then(res => {
					//this.setMensajes(res)


					if (!res.data.error) {
						this.$swal("Ok!", res.data.mensaje, "success")
						this.$emit('close')
					} else {
						this.$swal("Error!", res.data.mensaje, "error")
					}

				});
			}
		}
	})

	const app = new Vue({
		el: "#app",
		data: {
			pag: 1,
			idclub: 5,
			activeClass: 'waves-effect',
			errorMensaje: '',
			successMensaje: '',
			activeDelegados: {},
			showModal: false

		},
		mounted () {
		},
		computed: {

		},
		methods: {

			setMensajes (res) {
				this.getDelegado()

				if (res.data.error) {
					this.errorMensaje = res.data.mensaje
				} else {
					this.successMensaje = res.data.mensaje
				}

				setTimeout(() => {
					this.errorMensaje = ''
					this.successMensaje = ''
				}, 3000);

			}
		}
	})
</script>



<script type="text/javascript">
$(document).ready(function(){

	<?php
	if (count($filesComplemento)<1) {
	?>
	$('#myModalComplemento').modal();
	<?php } ?>

	<?php
	if (count($filesPlanilla)<1) {
	?>
	$('#myModalPlanilla').modal();
	<?php } ?>



	$('#colapsarMenu').click();
	var minutosPartido = <?php echo $minutos; ?>;
	/*var table = $('#example dataTables_filter input');*/

	var table = $('#example').DataTable({
		"lengthMenu": [[30, 60 -1], [30, 60, "All"]],
		"order": [[ 1, "asc" ]],
		"language": {
			"emptyTable":     "No hay datos cargados",
			"info":           "Mostrar _START_ hasta _END_ del total de _TOTAL_ filas",
			"infoEmpty":      "Mostrar 0 hasta 0 del total de 0 filas",
			"infoFiltered":   "(filtrados del total de _MAX_ filas)",
			"infoPostFix":    "",
			"thousands":      ",",
			"lengthMenu":     "Mostrar _MENU_ filas",
			"loadingRecords": "Cargando...",
			"processing":     "Procesando...",
			"search":         "Buscar:",
			"zeroRecords":    "No se encontraron resultados",
			"paginate": {
				"first":      "Primero",
				"last":       "Ultimo",
				"next":       "Siguiente",
				"previous":   "Anterior"
			},
			"aria": {
				"sortAscending":  ": activate to sort column ascending",
				"sortDescending": ": activate to sort column descending"
			}
		  }
	} );


	var table2 = $('#example2').DataTable({
		"lengthMenu": [[30, 60 -1], [30, 60, "All"]],
		"order": [[ 1, "asc" ]],
		"language": {
			"emptyTable":     "No hay datos cargados",
			"info":           "Mostrar _START_ hasta _END_ del total de _TOTAL_ filas",
			"infoEmpty":      "Mostrar 0 hasta 0 del total de 0 filas",
			"infoFiltered":   "(filtrados del total de _MAX_ filas)",
			"infoPostFix":    "",
			"thousands":      ",",
			"lengthMenu":     "Mostrar _MENU_ filas",
			"loadingRecords": "Cargando...",
			"processing":     "Procesando...",
			"search":         "Buscar:",
			"zeroRecords":    "No se encontraron resultados",
			"paginate": {
				"first":      "Primero",
				"last":       "Ultimo",
				"next":       "Siguiente",
				"previous":   "Anterior"
			},
			"aria": {
				"sortAscending":  ": activate to sort column ascending",
				"sortDescending": ": activate to sort column descending"
			}
		  }
	} );


	function existeCambioSaleLocal(dorsal, lblEach, lblBuscar, lblValor) {
		nuevoSale = 0;
		$('.'+lblEach).each(function(intIndex){
			idCambio = $(this).attr("id");
			idCambio = idCambio.replace(lblBuscar, "");
			if ($(this).val()==dorsal) {
				nuevoSale = $('#'+lblValor+idCambio).val();
				return false;
			}
		});
		return nuevoSale;
	}

	function existeCambioEntraLocal(dorsal, minutosPartido, lblEach, lblBuscar, lblValor) {
		nuevoEntra = 0;
		$('.'+lblEach).each(function(intIndex){
			idCambio = $(this).attr("id");
			idCambio = idCambio.replace(lblBuscar, "");
			if ($(this).val()==dorsal) {
				nuevoEntra = minutosPartido - $('#'+lblValor+idCambio).val();
				return false;
			}
		});
		return nuevoEntra;
	}



	function calcularMinutos() {


		// para restarle los minutos jugados al que sale y entra
		/* localsale - salecambioLocal - minutocambioLocal */
		var cambio = 0;
		$('.dorsalEA').each(function(intIndex){
		    cambio = 0;
			idJugador = $(this).attr("id");
			idJugador = idJugador.replace("dorsal", "");
			if ($(this).val()==0) {
				$('#minutos'+idJugador).val(0);
			} else {

				if (existeCambioSaleLocal($(this).val(),'localsale','salecambioLocal','minutocambioLocal')>0) {
					$('#minutos'+idJugador).val(existeCambioSaleLocal($(this).val(),'localsale','salecambioLocal','minutocambioLocal'));
					cambio = 1;
				}

				if (existeCambioEntraLocal($(this).val(),minutosPartido,'localentra','entracambioLocal','minutocambioLocal')>0) {
					$('#minutos'+idJugador).val(existeCambioEntraLocal($(this).val(),minutosPartido,'localentra','entracambioLocal','minutocambioLocal'));
					cambio = 1;
				}

				if (cambio == 0) {
					$('#minutos'+idJugador).val(minutosPartido);
				}

			}
		});


		// para restarle los minutos jugados al que sale
		/* visitsale - salecambioVisitante - minutocambioVisitante */
		var cambioV = 0;
		$('.dorsalEB').each(function(intIndex){
		    cambioV = 0;
			idJugador = $(this).attr("id");
			idJugador = idJugador.replace("dorbsal", "");
			if ($(this).val()==0) {
				$('#minbutos'+idJugador).val(0);
			} else {

				if (existeCambioSaleLocal($(this).val(),'visitsale','salecambioVisitante','minutocambioVisitante')>0) {
					$('#minbutos'+idJugador).val(existeCambioSaleLocal($(this).val(),'visitsale','salecambioVisitante','minutocambioVisitante'));
					cambioV = 1;
				}

				if (existeCambioEntraLocal($(this).val(),minutosPartido,'visitentra','entracambioVisitante','minutocambioVisitante')>0) {
					$('#minbutos'+idJugador).val(existeCambioEntraLocal($(this).val(),minutosPartido,'visitentra','entracambioVisitante','minutocambioVisitante'));
					cambioV = 1;
				}

				if (cambioV == 0) {
					$('#minbutos'+idJugador).val(minutosPartido);
				}

			}
		});
	}

	$('#calcularMinutos').click(function(e) {
        calcularMinutos();
    });


	$('#cargamasiva').click(function(e) {
		table.fnFilter('Win');
      	table.fnFilter('Trident', 0);

      	// Remove all filtering
      	table.fnFilterClear();

		table2.fnFilter('Win');
      	table2.fnFilter('Trident', 0);

      	// Remove all filtering
      	table2.fnFilterClear();

		calcularMinutos();

		if (($('#refestadospartidos').val() == '') || ($('#refestadospartidos').val() == '0')) {
			alert('Atencion, debe seleccionar un estado para el partido');
		} else {
			$( "#target" ).submit();
		}

    });

	$(document).on('change','#example_filter input', function(e){

		var acumulado = 0;
		$('.golesEA').each(function(intIndex){
			acumulado += parseInt($(this).val());
		});
		$('.golescontraEB').each(function(intIndex){
			acumulado += parseInt($(this).val());
		});
		$('.penalesconvertidosEA').each(function(intIndex){
			acumulado += parseInt($(this).val());
		});

		$('.resultadoA').html(acumulado);

	});


	$(document).on('change','#example2_filter input', function(e){


		var acumuladoB = 0;
		$('.golesEB').each(function(intIndex){
			acumuladoB += parseInt($(this).val());
		});
		$('.golescontraEA').each(function(intIndex){
			acumuladoB += parseInt($(this).val());
		});
		$('.penalesconvertidosEB').each(function(intIndex){
			acumuladoB += parseInt($(this).val());
		});
		$('.resultadoB').html(acumuladoB);
	});

	function validarmasivo(goleslocalcalculado, golesvisitantecalculado, amarillaslocalcalculado, amarillasvisitantecalculado, expulsadoslocalcalculado, expulsadosvisitantecalculado, informadoslocalcalculado, informadosvisitantecalculado, dobleamarillaslocalcalculado, dobleamarillasvisitantecalculado, cantidadjugadoreslocalcalculado, cantidadjugadoresvisitantecalculado) {
		$.ajax({
			data:  {
				idfixture: <?php echo $id; ?>,
				/*goleslocal: <?php echo $goleslocal; ?>,
				golesvisitante: <?php echo $golesvisitante; ?>,
				amarillaslocal: <?php echo $amarillaslocal; ?>,
				amarillasvisitante: <?php echo $amarillasvisitante; ?>,
				expulsadoslocal: <?php echo $expulsadoslocal; ?>,
				expulsadosvisitante: <?php echo $expulsadosvisitante; ?>,
				informadoslocal: <?php echo $informadoslocal; ?>,
				informadosvisitante: <?php echo $informadosvisitante; ?>,
				dobleamarillaslocal: <?php echo $dobleamarillaslocal; ?>,
				dobleamarillasvisitante: <?php echo $dobleamarillasvisitante; ?>,
				cantidadjugadoreslocal: <?php echo $cantidadjugadoreslocal; ?>,
				cantidadjugadoresvisitante: <?php echo $cantidadjugadoresvisitante; ?>,*/
				goleslocalcalculado: goleslocalcalculado,
				golesvisitantecalculado: golesvisitantecalculado,
				amarillaslocalcalculado: amarillaslocalcalculado,
				amarillasvisitantecalculado: amarillasvisitantecalculado,
				expulsadoslocalcalculado: expulsadoslocalcalculado,
				expulsadosvisitantecalculado: expulsadosvisitantecalculado,
				informadoslocalcalculado: informadoslocalcalculado,
				informadosvisitantecalculado: informadosvisitantecalculado,
				dobleamarillaslocalcalculado: dobleamarillaslocalcalculado,
				dobleamarillasvisitantecalculado: dobleamarillasvisitantecalculado,
				cantidadjugadoreslocalcalculado: cantidadjugadoreslocalcalculado,
				cantidadjugadoresvisitantecalculado: cantidadjugadoresvisitantecalculado,
				accion: 'validarCargaMasiva'},
			url:   '../../ajax/ajax.php',
			type:  'post',
			beforeSend: function () {
				$('.validarmasivo').hide();
			},
			success:  function (response) {

				$('.validarmasivo').show();
				if (response.error) {
					swal("Error!", response.data, "warning");
				} else {
					swal({
					  title: response.data + ', Desea guardar el partido?',
					  text: "Una vez guardado finalizara su carga",
					  type: "success",
					  showCancelButton: true,
					  confirmButtonColor: "#28a745",
					  confirmButtonText: "Si, deseo guardar el partido",
					  cancelButtonText: "No!",
					  closeOnConfirm: false,
					  closeOnCancel: false
					},
					function(isConfirm) {
					  if (isConfirm) {
					    swal("Partido Cargado Correctamente!", "El partido se cargo de manera correcto.", "success");
					  } else {
					    swal("Partido Sin Cargar!", "El partido no fue guardado", "error");
					  }
					});

					//swal("Correcto!", response.data, "success");
				}


			}
		});
	}

	$('#validarmasivo').click(function() {
		var acumuladoGolesLocal = 0;

		$('.golesEA').each(function(intIndex){
			acumuladoGolesLocal += parseInt($(this).val());
		});
		$('.golescontraEB').each(function(intIndex){
			acumuladoGolesLocal += parseInt($(this).val());
		});
		$('.penalesconvertidosEA').each(function(intIndex){
			acumuladoGolesLocal += parseInt($(this).val());
		});

		var acumuladoGolesVisitante = 0;
		$('.golesEB').each(function(intIndex){
			acumuladoGolesVisitante += parseInt($(this).val());
		});
		$('.golescontraEA').each(function(intIndex){
			acumuladoGolesVisitante += parseInt($(this).val());
		});
		$('.penalesconvertidosEB').each(function(intIndex){
			acumuladoGolesVisitante += parseInt($(this).val());
		});

		var acumuladoDorsalesLocal = 0;
		$('.dorsalEA').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoDorsalesLocal += 1;
			}
		});

		var acumuladoDorsalesVisitante = 0;
		$('.dorsalEB').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoDorsalesVisitante += 1;
			}
		});

		var acumuladoAmarillasLocal = 0;
		$('#example .amarillas').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoAmarillasLocal += 1;
			}
		});

		var acumuladoAmarillasVisitante= 0;
		$('#example2 .amarillas').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoAmarillasVisitante += 1;
			}
		});


		var acumuladoRojasLocal = 0;
		$('#example .rojas').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoRojasLocal += 1;
			}
		});

		var acumuladoRojasVisitante = 0;
		$('#example2 .rojas').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoRojasVisitante += 1;
			}
		});


		var acumuladoInformadosLocal = 0;
		$('#example .informados').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoInformadosLocal += 1;
			}
		});

		var acumuladoInformadosVisitante = 0;
		$('#example2 .informados').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoInformadosVisitante += 1;
			}
		});


		var acumuladoDoblemarillaLocal = 0;
		$('#example .dobleamarilla').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoDoblemarillaLocal += 1;
			}
		});

		var acumuladoDoblemarillaVisitante = 0;
		$('#example2 .dobleamarilla').each(function(intIndex){
			if ($(this).val() > 0) {
				acumuladoDoblemarillaVisitante += 1;
			}
		});


		validarmasivo(acumuladoGolesLocal, acumuladoGolesVisitante, acumuladoAmarillasLocal, acumuladoAmarillasVisitante, acumuladoRojasLocal, acumuladoRojasVisitante, acumuladoInformadosLocal, acumuladoInformadosVisitante, acumuladoDoblemarillaLocal, acumuladoDoblemarillaVisitante, acumuladoDorsalesLocal, acumuladoDorsalesVisitante);


	});


	//al enviar el formulario
    $('#cargar').click(function(){

			//informaciï¿½n del formulario
		var formData = new FormData($(".formulario")[0]);
		var message = "";
		//hacemos la peticiï¿½n ajax
		$.ajax({
			url: '../../ajax/ajax.php',
			type: 'POST',
			// Form data
			//datos del formulario
			data: formData,
			//necesario para subir archivos via ajax
			cache: false,
			contentType: false,
			processData: false,
			//mientras enviamos el archivo
			beforeSend: function(){
				$("#load").html('<img src="../../imagenes/load13.gif" width="50" height="50" />');
			},
			//una vez finalizado correctamente
			success: function(data){

				if (data == '') {
					$(".alert").removeClass("alert-danger");
					$(".alert").removeClass("alert-info");
					$(".alert").addClass("alert-success");
					$(".alert").html('<strong>Ok!</strong> Se cargo exitosamente las <strong>Estadisticas</strong>. ');
					$(".alert").delay(3000).queue(function(){
						/*aca lo que quiero hacer
						  despuï¿½s de los 2 segundos de retraso*/
						$(this).dequeue(); //continï¿½o con el siguiente ï¿½tem en la cola

					});
					$("#load").html('');
					url = "estadisticas.php?id="+<?php echo $idFixture; ?>;
					$(location).attr('href',url);


				} else {
					$(".alert").removeClass("alert-danger");
					$(".alert").addClass("alert-danger");
					$(".alert").html('<strong>Error!</strong> '+data);
					$("#load").html('');
				}
			},
			//si ha ocurrido un error
			error: function(){
				$(".alert").html('<strong>Error!</strong> Actualice la pagina');
				$("#load").html('');
			}
		});

    });

});
</script>
<script src="../../js/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }


  </script>

</body>
<?php } ?>

</html>

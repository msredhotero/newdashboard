<?php

date_default_timezone_set('America/Buenos_Aires');

include ('../includes/funcionesUsuarios.php');
include ('../includes/funciones.php');
include ('../includes/funcionesHTML.php');
include ('../includes/funcionesReferencias.php');


$serviciosUsuarios  		= new ServiciosUsuarios();
$serviciosFunciones 		= new Servicios();
$serviciosHTML				= new ServiciosHTML();
$serviciosReferencias 			= new ServiciosReferencias();

$fecha = date('Y-m-d');

require('fpdf.php');

//$header = array("Hora", "Cancha 1", "Cancha 2", "Cancha 3");

////***** Parametros ****////////////////////////////////
$refEquipos		=	$_GET['idequipo'];



$resTemporadas = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);
} else {
    $ultimaTemporada = 0;
}

$reftemporadas = $ultimaTemporada;

/////////////////////////////  fin parametross  ///////////////////////////
$resEquipo = $serviciosReferencias->traerEquiposdelegadosPorEquipoTemporada($refEquipos,$reftemporadas);
$resEquipoAux = $serviciosReferencias->traerEquiposdelegadosPorEquipoTemporada($refEquipos,$reftemporadas);


$resDatos = $serviciosReferencias->traerConectorActivosPorEquiposDelegado($refEquipos, $reftemporadas, $refusuarios='');

$resDatosNuevo = $serviciosReferencias->traerConectorActivosPorEquiposDelegadoNuevo($refEquipos, $reftemporadas, $refusuarios='');

$excepciones = $serviciosReferencias->generarPlantelTemporadaAnteriorExcepcionesTodos($reftemporadas, mysql_result($resEquipoAux,0,'refcountries'), $refEquipos);

$nombre 	= mysql_result($resEquipoAux,0,'nombre');
$categoria = mysql_result($resEquipoAux,0,'categoria');
$division = mysql_result($resEquipoAux,0,'division');

$resclub = $serviciosReferencias->traerCountriesPorId(mysql_result($resEquipoAux,0,'refcountries'));

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
   $habTemporadaPasada = $serviciosReferencias->verificaEdadCategoriaJugadorMenor($rowE['refjugadores'], $rowE['refcategorias'], $rowE['reftipojugadores']);

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
         $pdf->Cell(73,5,($rowE['nombrecompleto']),1,0,'L',false);
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
      $pdf->Cell(73,5,($rowE['nombrecompleto']),1,0,'L',false);
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

$resGetAllFusiones = $serviciosReferencias->traerFusionPorIdEquipos($refEquipos);

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



$nombreTurno = "LISTA-DE-BUENA-FE-".$nombre.'-'.$fecha.".pdf";

$pdf->Output($nombreTurno,'I');


?>

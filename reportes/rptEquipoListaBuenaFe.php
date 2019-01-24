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

$resDatos = $serviciosReferencias->traerConectorActivosPorEquiposDelegado($refEquipos, $reftemporadas, $refusuarios='');

$nombre 	= mysql_result($resEquipo,0,'nombre');


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
	$pdf->Cell(200,5,'Lista de Buena Fe Temporada 2019 - Equipo: '.utf8_decode($nombre),1,0,'C',true);
	$pdf->Ln();
   $pdf->SetX(5);
	$pdf->Cell(200,5,'Categoria: '.utf8_decode(mysql_result($resEquipo,0,'categoria')).' - Division: '.utf8_decode(mysql_result($resEquipo,0,'division')),1,0,'C',true);
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
	$pdf->SetFont('Arial','',10);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);

   $pdf->SetFont('Arial','',9);
   $pdf->SetX(5);
	$pdf->Cell(200,5,utf8_decode('* Jugadores con solicitud de excepción'),0,0,'L',false);
   $pdf->Ln();
   $pdf->SetX(5);
	$pdf->Cell(200,5,utf8_decode('** Jugadores con solicitud de excepción, generada desde la temporada pasada'),0,0,'L',false);
	$pdf->SetFont('Arial','',10);
   $pdf->Ln();
	$pdf->SetX(5);

	$pdf->SetFont('Arial','',11);
	$pdf->Cell(5,5,'',1,0,'C',true);
	$pdf->Cell(90,5,'JUGADOR',1,0,'C',true);
	$pdf->Cell(30,5,'NRO. DOC.',1,0,'C',true);
	$pdf->Cell(40,5,'TIPO JUGADOR',1,0,'C',true);
   $pdf->Cell(25,5,'FECHA NAC.',1,0,'C',true);

	$cantPartidos = 0;
	$i=0;

	$contadorY1 = 44;
	$contadorY2 = 44;

   $arFusiones = array();
while ($rowE = mysql_fetch_array($resDatos)) {
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

		$pdf->SetFont('Arial','',11);
		$pdf->Cell(5,5,'',1,0,'C',true);
      $pdf->Cell(90,5,'JUGADOR',1,0,'C',true);
   	$pdf->Cell(30,5,'NRO. DOC.',1,0,'C',true);
   	$pdf->Cell(40,5,'TIPO JUGADOR',1,0,'C',true);
      $pdf->Cell(25,5,'FECHA NAC.',1,0,'C',true);

	}


	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->SetFont('Arial','',10);
	$pdf->Cell(5,5,$cantPartidos,1,0,'C',false);

   /// veo si la habilitacion ya la tenia la temporada apsada //
   $habTemporadaPasada = $serviciosReferencias->verificaEdadCategoriaJugadorMenor($rowE['refjugadores'], $rowE['refcategorias'], $rowE['reftipojugadores']);

   if ($habTemporadaPasada == 1) {
      $pdf->Cell(90,5,'** '.utf8_decode($rowE['nombrecompleto']),1,0,'L',false);
   } else {
      if ($rowE['habilitacionpendiente'] == 'Si') {
         $pdf->Cell(90,5,'* '.utf8_decode($rowE['nombrecompleto']),1,0,'L',false);
      } else {
         $pdf->Cell(90,5,utf8_decode($rowE['nombrecompleto']),1,0,'L',false);
      }
   }

	$pdf->Cell(30,5,($rowE['nrodocumento']),1,0,'C',false);
	$pdf->Cell(40,5,utf8_decode($rowE['tipojugador']),1,0,'L',false);
   $pdf->Cell(25,5,($rowE['fechanacimiento']),1,0,'C',false);


	$contadorY1 += 4;

	//$pdf->SetY($contadorY1);


}


$pdf->Ln();


Footer($pdf);



$nombreTurno = "LISTA-DE-BUENA-FE-".$nombre.'-'.$fecha.".pdf";

$pdf->Output($nombreTurno,'I');


?>

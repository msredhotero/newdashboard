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
$idCountries		=	$_GET['idcountrie'];

$resTemporadas = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);
} else {
    $ultimaTemporada = 0;
}

/////////////////////////////  fin parametross  ///////////////////////////


$resDatos = $serviciosReferencias->traerEquiposdelegadosPorCountrieFinalizado($idCountries, $ultimaTemporada);

$resDatosBaja = $serviciosReferencias->traerEquiposdelegadosPorCountrieFinalizadoBaja($idCountries, $ultimaTemporada);

$resCountrie = $serviciosReferencias->traerCountriesPorId($idCountries);

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
	$pdf->Cell(200,5,'Equipos Temporada 2019 - Club: '.utf8_decode($nombre),1,0,'C',true);
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

   $resFusion = $serviciosReferencias->traerEquiposFusionPorEquipoDelegado($rowE['idequipodelegado']);

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


Footer($pdf);



$nombreTurno = "EQUIPOS-CLUB-".$fecha.".pdf";

$pdf->Output($nombreTurno,'I');


?>

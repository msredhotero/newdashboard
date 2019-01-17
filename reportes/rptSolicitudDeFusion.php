<?php


session_start();


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
$idCountries		=	$_SESSION['idclub_aif'];

$idFusion      =  $_GET['id'];

$resTemporadas = $serviciosReferencias->traerUltimaTemporada();

if (mysql_num_rows($resTemporadas)>0) {
    $ultimaTemporada = mysql_result($resTemporadas,0,0);
} else {
    $ultimaTemporada = 0;
}

/////////////////////////////  fin parametross  ///////////////////////////


$resDatos = $serviciosReferencias->traerFusionPorCountrieFusion($idCountries, $idFusion);

$equipo     = mysql_result($resDatos,0,'nombre');
$club       = mysql_result($resDatos,0,'countriefusion');
$clubpadre  = mysql_result($resDatos,0,'countriepadre');
$categoria  = mysql_result($resDatos,0,'categoria');
$division   = mysql_result($resDatos,0,'division');

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
	$pdf->Cell(200,5,'SOLICITUD DE FUSION',1,0,'C',true);
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
	$pdf->SetFont('Arial','',10);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);

	$pdf->SetFont('Arial','',10);
	//$pdf->Cell(5,5,'',1,0,'C',true);
	$pdf->Multicell(200, 5, 'A partir de esta nota, el Club '.$club.' acepta la solicitud de fusion con el Equipo '.$equipo.' en la categoria '.$categoria.' y division '.$division.' que formaran parte del Club '.$clubpadre, 0, 'L', false);



Footer($pdf);



$nombreTurno = "SOLICITUD-FUSION-".$fecha.".pdf";

$pdf->Output($nombreTurno,'I');


?>

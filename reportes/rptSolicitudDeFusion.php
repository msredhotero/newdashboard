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

$resJugadores = $serviciosReferencias->traerJugadoresDeUnaFusion($idFusion, $ultimaTemporada, $idCountries);

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
	$pdf->Multicell(200, 5, utf8_decode('Por medio de la presente, '.$club.' acepta la solicitud de fusión presentada por '.$clubpadre.' para el equipo '.$equipo.' en la categoría '.$categoria.' y división '.$division.', obligándose a respetar la normativa prevista por el reglamento interno de torneos de la AIF.
Los jugadores de nuestra institución que formarán parte son los siguientes: '), 0, 'L', false);



   if (mysql_num_rows($resJugadores) > 0) {
      $pdf->Ln();
      $pdf->Ln();

      $pdf->SetX(5);

   	$pdf->SetFont('Arial','',12);
   	$pdf->Cell(5,5,'',1,0,'C',true);
   	$pdf->Cell(40,5,'APELLIDO',1,0,'C',true);
   	$pdf->Cell(40,5,'NOMBRE',1,0,'C',true);
   	$pdf->Cell(30,5,'NRO DOC',1,0,'C',true);
      $pdf->Cell(40,5,'FECHA NACIMIENTO',1,0,'C',true);

      $cantPartidos = 0;

      $contadorY1 = 44;
      $contadorY2 = 44;

      while ($row = mysql_fetch_array($resJugadores)) {
         $i+=1;
         $cantPartidos += 1;

         $pdf->Ln();
         $pdf->SetX(5);
         $pdf->SetFont('Arial','',10);
         $pdf->Cell(5,5,$cantPartidos,1,0,'C',false);
         $pdf->Cell(40,5,utf8_decode($rowE['apellido']),1,0,'L',false);
         $pdf->Cell(40,5,utf8_decode($rowE['nombres']),1,0,'L',false);
         $pdf->Cell(30,5,utf8_decode($rowE['nrodocumento']),1,0,'C',false);
         $pdf->Cell(40,5,utf8_decode($rowE['fechanacimiento']),1,0,'C',false);


         $contadorY1 += 4;

      }

   } else {
      $pdf->Ln();
      $pdf->SetX(5);
      $pdf->SetFont('Arial','',10);
      $pdf->Cell(200,5,'* Aun no se cargaron los Jugadores',0,0,'L',false);
   }

   $pdf->Ln();
	$pdf->Ln();
   $pdf->Ln();
	$pdf->Ln();
   $pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);

	$pdf->SetFont('Arial','',10);
	//$pdf->Cell(5,5,'',1,0,'C',true);
	$pdf->Multicell(200, 5, utf8_decode('Certifico que los arriba Inscriptos son Socios-Propietarios de Lotes del Country (titulares, cónyugues, ascendientes, descendientes o yernos únicamente), y/o jugadores que se enmarcan dentro del artículo 2 incisos "a", "b" y "d" de vuestro reglamento de torneos, estando estatutariamente habilitados para representar a la Institución en competencias deportivas. Manifiesto conocer y aceptar en todas sus partes el Reglamento de los Torneos y el Reglamento del Tribunal de Disciplina, comprometiéndose el Country al que represento, a cumplir y hacer cumplir los derechos y obligaciones obrantes en los mismos y a comunicar a la Asociación, en forma inmediata, cualquier modificación en la condición o categoría de los socios-propietarios y/o familiares inscriptos en la presente lista.                    '), 0, 'L', false);



Footer($pdf);



$nombreTurno = "SOLICITUD-FUSION-".$fecha.".pdf";

$pdf->Output($nombreTurno,'I');


?>

<?php

date_default_timezone_set('America/Buenos_Aires');

ini_set('max_execution_time', 1000);

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
$id		=	$_GET['id'];
/////////////////////////////  fin parametross  ///////////////////////////




$resSocio = $serviciosReferencias->traerJugadoresPorIdCompleto($id);

$resFoto = $serviciosReferencias->traerDocumentacionjugadorimagenesPorJugadorDocumentacionID($id,1);
if (mysql_num_rows($resFoto) > 0) {
	$urlImg1 = "../data/".mysql_result($resFoto,0,0)."/".mysql_result($resFoto,0,'imagen');
	$urlImgType1 = mysql_result($resFoto,0,'type');
} else {
	$urlImg1 = '';
	$urlImgType1 = '';
}


$resFotoDocumento = $serviciosReferencias->traerDocumentacionjugadorimagenesPorJugadorDocumentacionID($id,2);
if (mysql_num_rows($resFotoDocumento) > 0) {
	$urlImg2 = "../data/".mysql_result($resFotoDocumento,0,0)."/".mysql_result($resFotoDocumento,0,'imagen');
	$urlImgType2 = mysql_result($resFotoDocumento,0,'type');
} else {
	$urlImg2 = '';
	$urlImgType2 = '';
}

$resFotoDocumentoDorso = $serviciosReferencias->traerDocumentacionjugadorimagenesPorJugadorDocumentacionID($id,99);
if (mysql_num_rows($resFotoDocumentoDorso) > 0) {
	$urlImg3 = "../data/".mysql_result($resFotoDocumentoDorso,0,0)."/".mysql_result($resFotoDocumentoDorso,0,'imagen');
	$urlImgType3 = mysql_result($resFotoDocumentoDorso,0,'type');
} else {
	$urlImg3 = '';
	$urlImgType3 = '';
}

$pdf = new FPDF();

#Establecemos los márgenes izquierda, arriba y derecha: 
$pdf->SetMargins(2, 2 , 2); 

#Establecemos el margen inferior: 
$pdf->SetAutoPageBreak(true,1); 


	
	$pdf->AddPage('L','A4','mm');
	/***********************************    PRIMER CUADRANTE ******************************************/
	
	

	/***********************************    FIN ******************************************/

	//////////////////// Aca arrancan a cargarse los datos de los equipos  /////////////////////////

	
	$pdf->SetFillColor(183,183,183);
	$pdf->SetFont('Arial','U',18);

	$pdf->SetX(5);
	$pdf->Cell(50,5,mysql_result($resSocio,0,'nrodocumento'),0,0,'L',false);

	$pdf->Image('../imagenes/logoparainformes.png',5,10,40);
	
	$pdf->SetFont('Arial','',14);
	$pdf->SetXY(60,15);
	$pdf->Cell(120,5,'ASOCIACION INTERCOUNTRY DE FUTBOL ZONA NORTE',0,0,'C',false);
	$pdf->SetFont('Arial','U',10);

	$pdf->SetY(30);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetFont('Arial','',12);
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'APELLIDO: '.mysql_result($resSocio,0,'apellido'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'NOMBRE: '.mysql_result($resSocio,0,'nombres'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'TIPO Y NRO DE DOCUMENTO: '.mysql_result($resSocio,0,'tipodocumento').' '.mysql_result($resSocio,0,'nrodocumento'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'FECHA NACIMIENTO: '.mysql_result($resSocio,0,'fechanacimiento'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'COUNTRY: '.mysql_result($resSocio,0,'country'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'NRO DE LOTE: '.mysql_result($resSocio,0,'numeroserielote'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'EMAIL: '.mysql_result($resSocio,0,'email'),0,0,'L',false);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(180,5,'FECHA DE ALTA: '.mysql_result($resSocio,0,'fechaalta'),0,0,'L',false);

	if ($urlImg1 != '') {

		$res1 = $serviciosReferencias->devolverImagen(($urlImg1), $urlImgType1,'imagenTemp');
		$pdf->Image($res1,210,10,40,54);
		

		// El operador !== también puede ser usado. Puesto que != no funcionará como se espera
		// porque la posición de 'a' es 0. La declaración (0 != false) se evalúa a 
		// false.
		if ($pos !== false) {
			//die(var_dump($urlImgType1));

			//$pdf->Image($res1,210,10,40,54,'PNG');
		} else {
			//$pdf->Image($res1,210,10,40,54);
		}
	}

	if ($urlImg2 != '') {


		$res2 = $serviciosReferencias->devolverImagen(($urlImg2), $urlImgType2,'imagenTemp2');

		$pdf->Image($res2,190,80,70);


	}

	if ($urlImg3 != '') {
		$res3 = $serviciosReferencias->devolverImagen(($urlImg3), $urlImgType3,'imagenTemp3');

		$pdf->Image($res3,190,140,70);
	}

	$pdf->SetXY(20,150);
	$pdf->Cell(110,5,'Registre en el recuadro la firma a utilizar en la planilla del partido',0,0,'L',false);
	$pdf->Ln();
	$pdf->SetXY(20,160);
	$pdf->Cell(110,25,'',1,0,'L',false);





$nombreTurno = "ALTA-JUGADOR-".$fecha.".pdf";

$pdf->Output($nombreTurno,'D');


// Creamos un instancia de la clase ZipArchive
 //$zip = new ZipArchive();
// Creamos y abrimos un archivo zip temporal
 //$zip->open("Alta-Jugador.zip",ZipArchive::CREATE);
 // Añadimos un directorio
 //$dir = 'miDirectorio';
 //$zip->addEmptyDir($dir);
 // Añadimos un archivo en la raid del zip.
 //$zip->addFile($nombreTurno);
 //Añadimos un archivo dentro del directorio que hemos creado
 //$zip->addFile("imagen2.jpg",$dir."/mi_imagen2.jpg");
 // Una vez añadido los archivos deseados cerramos el zip.
 //$zip->close();

 // Creamos las cabezeras que forzaran la descarga del archivo como archivo zip.
 //header("Content-type: application/octet-stream");
 //header("Content-disposition: attachment; filename=Alta-Jugador.zip");
 // leemos el archivo creado
 //readfile('Alta-Jugador.zip');
 // Por último eliminamos el archivo temporal creado
 //unlink('Alta-Jugador.zip');//Destruye el archivo temporal

?>


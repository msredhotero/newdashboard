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
$idCountries		=	$_GET['refcountries1'];
/////////////////////////////  fin parametross  ///////////////////////////


$resDatos = $serviciosReferencias->traerJugadoresClubPorCountrieActivos($idCountries);

if ($_GET['bajas1'] == 'true') {
	$resDatosBaja = $serviciosReferencias->traerJugadoresPorCountriesBaja($idCountries);
} else {
	$resDatosBaja = $serviciosReferencias->traerJugadoresPorCountriesBaja(0);
}

$resCountrie = $serviciosReferencias->traerCountriesPorId($idCountries);

$lstNuevosJugadores = $serviciosReferencias->traerJugadoresprePorCountries($idCountries);
//echo $resEquipos;

$nombre 	= mysql_result($resCountrie,0,'nombre');



$pdf = new FPDF();


function Footer($pdf)
{

$pdf->SetY(-10);

$pdf->SetFont('Arial','I',8);

$pdf->Cell(0,10,'Firma Presidente o Secretario: ______________________________________________  -  Pagina '.$pdf->PageNo()." - Fecha: ".date('Y-m-d'),0,0,'C');
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
	$pdf->SetFont('Arial','B',10);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(5);
	$pdf->Cell(200,5,'Padron Socios Propietarios - '.utf8_decode($nombre),1,0,'C',true);
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->Cell(200,5,'Fecha: '.date('d-m-Y').' - Hora: '.date('H:i:s'),1,0,'C',true);
	$pdf->SetFont('Arial','',8);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);

	$pdf->SetFont('Arial','',8);
	$pdf->Cell(5,4,'',1,0,'C',false);
	$pdf->Cell(60,4,'Apellido y Nombre',1,0,'C',false);
	$pdf->Cell(15,4,'Nro. Doc.',1,0,'C',false);
	$pdf->Cell(15,4,'Fec. Nac',1,0,'C',false);
	$pdf->Cell(40,4,'Numero Socio/Lote',1,0,'C',false);
	$pdf->Cell(16,4,'Baja',1,0,'C',false);
	$pdf->Cell(30,4,'Art 2 Inciso D',1,0,'C',false);

	$cantPartidos = 0;
	$i=0;

	$contadorY1 = 44;
	$contadorY2 = 44;
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
		$pdf->SetFont('Arial','',8);
		$pdf->Ln();
		$pdf->SetX(5);

		$i=0;

		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,4,'',1,0,'C',false);
		$pdf->Cell(60,4,'Apellido y Nombre',0,0,'C',false);
		$pdf->Cell(15,4,'Nro. Doc.',0,0,'C',false);
		$pdf->Cell(15,4,'Fec. Nac',1,0,'C',false);
		$pdf->Cell(40,4,'Numero de Socio/Lote',1,0,'C',false);
		$pdf->Cell(16,4,'Baja',1,0,'C',false);
		$pdf->Cell(30,4,'Art 2 Inciso D',1,0,'C',false);

	}


	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(5,4,$cantPartidos,1,0,'C',false);
	$pdf->Cell(60,4,utf8_decode($rowE['apyn']),1,0,'L',false);
	$pdf->Cell(15,4,$rowE['nrodocumento'],1,0,'C',false);
	$pdf->Cell(15,4,$rowE['fechanacimiento'],1,0,'C',false);
	$pdf->Cell(40,4,$rowE['numeroserielote'],1,0,'C',false);
	$pdf->Cell(16,4,$rowE['fechabaja'],1,0,'C',false);
	$pdf->Cell(30,4,$rowE['articulo'],1,0,'C',false);






	$contadorY1 += 4;

	//$pdf->SetY($contadorY1);


}


$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','',7);
$pdf->MultiCell(190, 3, utf8_decode('Certifico que los arriba Inscriptos son Socios-Propietarios de Lotes del Country (titulares, cónyugues, ascendientes, descendientes o yernos únicamente), y/o jugadores que se enmarcan dentro del artículo 2 incisos "a", "b" y "d" de vuestro reglamento de torneos, estando estatutariamente habilitados para representar a la Institución en competencias deportivas. Manifiesto conocer y aceptar en todas sus partes el Reglamento de los Torneos y el Reglamento del Tribunal de Disciplina, comprometiéndose el Country al que represento, a cumplir y hacer cumplir los derechos y obligaciones obrantes en los mismos y a comunicar a la Asociación, en forma inmediata, cualquier modificación en la condición o categoría de los socios-propietarios y/o familiares inscriptos en la presente lista.'),0,'','');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','b',8);
$pdf->MultiCell(190, 4, utf8_decode('Nota: El Padrón deberá estar firmado por el Presidente y/o Secretario de la Institución, con sellos aclaratorios y certificación Bancaria o de un Escribano público acerca de las identidades de los Firmantes, adjuntando además un elemento probatorio del carácter de su función (fotocopia certificada del libro de Actas, certificación Bancaria u otras).'),0,'','');


Footer($pdf);

$pdf->AddPage();
//////////////////// Aca arrancan a cargarse los datos de los equipos  /////////////////////////

	$pdf->SetFillColor(183,183,183);
	$pdf->SetFont('Arial','B',10);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(5);
	$pdf->Cell(200,5,'Nuevos Socios Propietarios - '.utf8_decode($nombre),1,0,'C',true);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->SetFont('Arial','',8);
	$pdf->Cell(5,4,'',1,0,'C',false);
	$pdf->Cell(60,4,'Apellido y Nombre',1,0,'C',false);
	$pdf->Cell(15,4,'Nro. Doc.',1,0,'C',false);
	$pdf->Cell(15,4,'Fec. Nac',1,0,'C',false);
	$pdf->Cell(40,4,'Numero de Socio/Lote',1,0,'C',false);

	$cantPartidos = 0;
	$i=0;

	$contadorY1 = 44;
	$contadorY2 = 44;
while ($rowE = mysql_fetch_array($lstNuevosJugadores)) {
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
		$pdf->SetFont('Arial','',8);
		$pdf->Ln();
		$pdf->SetX(5);

		$i=0;

		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,4,'',1,0,'C',false);
		$pdf->Cell(60,4,'Apellido y Nombre',0,0,'C',false);
		$pdf->Cell(15,4,'Nro. Doc.',0,0,'C',false);
		$pdf->Cell(15,4,'Fec. Nac',1,0,'C',false);
		$pdf->Cell(40,4,'Nro Serie Lote',1,0,'C',false);

	}


	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(5,4,$cantPartidos,1,0,'C',false);
	$pdf->Cell(60,4,utf8_decode($rowE['apellido']).' '.utf8_decode($rowE['nombres']),1,0,'L',false);
	$pdf->Cell(15,4,$rowE['nrodocumento'],1,0,'C',false);
	$pdf->Cell(15,4,$rowE['fechanacimiento'],1,0,'C',false);
	$pdf->Cell(40,4,$rowE['numeroserielote'],1,0,'C',false);

	$contadorY1 += 4;

	//$pdf->SetY($contadorY1);


}

$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','',7);
$pdf->MultiCell(190, 3, utf8_decode('Certifico que los arriba Inscriptos son Socios-Propietarios de Lotes del Country (titulares, cónyugues, ascendientes, descendientes o yernos únicamente), y/o jugadores que se enmarcan dentro del artículo 2 incisos "a", "b" y "d" de vuestro reglamento de torneos, estando estatutariamente habilitados para representar a la Institución en competencias deportivas. Manifiesto conocer y aceptar en todas sus partes el Reglamento de los Torneos y el Reglamento del Tribunal de Disciplina, comprometiéndose el Country al que represento, a cumplir y hacer cumplir los derechos y obligaciones obrantes en los mismos y a comunicar a la Asociación, en forma inmediata, cualquier modificación en la condición o categoría de los socios-propietarios y/o familiares inscriptos en la presente lista.'),0,'','');
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','b',8);
$pdf->MultiCell(190, 4, utf8_decode('Nota: El Padrón deberá estar firmado por el Presidente y/o Secretario de la Institución, con sellos aclaratorios y certificación Bancaria o de un Escribano público acerca de las identidades de los Firmantes, adjuntando además un elemento probatorio del carácter de su función (fotocopia certificada del libro de Actas, certificación Bancaria u otras).'),0,'','');


Footer($pdf);


$pdf->AddPage();

$pdf->SetFont('Arial','',12);
$pdf->Cell(200,5,utf8_decode('ACTUALIZACIÓN DE DATOS AÑO 2019'),0,0,'C',false);
$pdf->Ln();
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(200,5,'COUNTRY:',0,0,'L',false);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('1º DELEGADO:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('NÚMERO PARTICULAR:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('NÚMERO LABORAL:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('NÚMERO DE CELULAR:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,'E-MAIL:',0,0,'L',false);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('2º DELEGADO:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('NÚMERO PARTICULAR:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('NÚMERO LABORAL:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('NÚMERO DE CELULAR:'),0,0,'L',false);
$pdf->Ln();
$pdf->Cell(200,5,'E-MAIL:',0,0,'L',false);

Footer($pdf);


$pdf->AddPage();

$pdf->SetFont('Arial','b',10);
$pdf->Cell(200,5,'DATOS COMPLEMENTARIOS ALTA DE EQUIPO',0,0,'C',false);$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('Año: _____		'),0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,'Country: ______________________________',0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,'Nombre del equipo : ______________________________',0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('Categoría: ______________________________'),0,0,'L',false);$pdf->Ln();
$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('Dirección de la Administración: _________________________________________________________________________'),0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Arial','',10);
$pdf->Cell(200,5,utf8_decode('Localidad: __________________________  Código Postal: _____  Tel.: __________________  Fax: ___________________'),0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,'E-mail: _________________________________________________',0,0,'L',false);$pdf->Ln();
$pdf->SetFont('Arial','b',10);
$pdf->Cell(200,5,utf8_decode('Dirección del Country: _________________________________________________________________________________'),0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Arial','',10);
$pdf->Cell(200,5,utf8_decode('Localidad: __________________________  Código Postal: _____  Tel.: __________________  Fax: ___________________'),0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,'E-mail: _________________________________________________',0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('Condición de IVA: _____________________	CUIT: ______________________'),0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Arial','b',10);
$pdf->Cell(200,5,'Servicio de Emergencias (nombre y tel.): ________________________________________________________________',0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,' ___________________________________________________________________________________________________',0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->Cell(200,5,utf8_decode('Camisetas (detallar colores y descripción):'),0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Arial','',10);
$pdf->Cell(200,5,'	Juego 1: ___________________________________________________________________________',0,0,'L',false);$pdf->Ln();
$pdf->Cell(200,5,'	Juego 2: ___________________________________________________________________________',0,0,'L',false);$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Arial','b',10);
$pdf->Cell(50,5,'CANCHAS HABILITADAS',0,0,'L',false);
$pdf->Cell(50,5,'Primera',0,0,'L',false);
$pdf->Cell(50,5,'Segunda',0,0,'L',false);
$pdf->Cell(50,5,'Tercera',0,0,'L',false);
$pdf->Ln();$pdf->Ln();

$pdf->SetFont('Arial','',10);
$pdf->Cell(50,5,'Domicilio',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Ln();

$pdf->Cell(50,5,'Localidad',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Ln();

$pdf->Cell(50,5,utf8_decode('Teléfono'),0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Ln();

$pdf->Cell(50,5,'Largo x Ancho (mts.)',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Ln();

$pdf->Cell(50,5,'Largo x Alto Arco(mts.)',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(50,5,utf8_decode('Ubicación dentro del club'),0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Cell(50,5,'____________________',0,0,'L',false);
$pdf->Ln();

$pdf->SetFont('Arial','b',8);
$pdf->Cell(200,5,utf8_decode('Nota: Se deberá adjuntar un croquis con la ubicación de las canchas habilitadas dentro del country'),0,0,'L',false);$pdf->Ln();$pdf->Ln();
$pdf->SetFont('Arial','b',10);
$pdf->Ln();
$pdf->Cell(60,5,'DELEGADOS DEL COUNTRY',0,0,'L',false);
$pdf->Cell(70,5,'Titular',0,0,'L',false);
$pdf->Cell(70,5,'Suplente',0,0,'L',false);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$pdf->Cell(60,5,'Apellido y Nombre',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(60,5,'Doc. Identidad',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(60,5,utf8_decode('Teléfono y e-mail'),0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(60,5,'Firma',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();
$pdf->Ln();

$pdf->SetFont('Arial','b',8);
$pdf->Cell(60,5,'SOCIOS RESPONSABLES DEL EQUIPO',0,0,'L',false);
$pdf->SetFont('Arial','b',10);
$pdf->Cell(70,5,'Uno',0,0,'L',false);
$pdf->Cell(70,5,'Dos',0,0,'L',false);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$pdf->Cell(60,5,'Apellido y Nombre',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(60,5,'Doc. Identidad',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(60,5,utf8_decode('Teléfono y e-mail'),0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();


$pdf->Cell(60,5,'Firma',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Cell(70,5,'______________________________',0,0,'L',false);
$pdf->Ln();
$pdf->Ln();


Footer($pdf);



$pdf->AddPage();

$pdf->SetFont('Arial','',14);
$pdf->MultiCell(200,6,utf8_decode('Recuerde presentar la siguiente documentación junto con el padrón de socios/propietarios.'),0,'L',false);
$pdf->Ln();
$pdf->MultiCell(200,6,utf8_decode('* Nota institucional especificando los datos del servicio médico brindados por el country.'),0,'L',false);
$pdf->Ln();
$pdf->MultiCell(200,6,utf8_decode('* El acta de designación de autoridades y controlar que todas las hojas estén firmadas (con una sola certificación basta).'),0,'L',false);
$pdf->Ln();
$pdf->MultiCell(200,6,utf8_decode('* Los datos complementarios podrán ser completados a mano al igual que la actualización de datos.'),0,'L',false);

Footer($pdf);


///////////////***************************  para las bajas  *****************************************//////

//$pdf->AddPage();
	/***********************************    PRIMER CUADRANTE ******************************************/

//	$pdf->Image('../imagenes/logoparainformes.png',2,2,40);

	/***********************************    FIN ******************************************/



	//////////////////// Aca arrancan a cargarse los datos de los equipos  /////////////////////////

	/*
	$pdf->SetFillColor(183,183,183);
	$pdf->SetFont('Arial','B',10);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetY(25);
	$pdf->SetX(5);
	$pdf->Cell(200,5,'Bajas - Countrie '.utf8_decode($nombre),1,0,'C',true);
	$pdf->SetFont('Arial','',8);
	$pdf->Ln();
	$pdf->Ln();
	$pdf->SetX(5);

	$pdf->SetFont('Arial','',8);
	$pdf->Cell(5,4,'',1,0,'C',false);
	$pdf->Cell(60,4,'Apellido y Nombre',1,0,'C',false);
	$pdf->Cell(15,4,'Nro. Doc.',1,0,'C',false);
	$pdf->Cell(16,4,'Fecha Nac.',1,0,'C',false);
	$pdf->Cell(16,4,'Fecha Baja',1,0,'C',false);
	$cantPartidos = 0;
	$i=0;

	$contadorY1 = 44;
	$contadorY2 = 44;
while ($rowE = mysql_fetch_array($resDatosBaja)) {
	$i+=1;
	$cantPartidos += 1;

	if ($i > 61) {
		$pdf->AddPage();
		$pdf->Image('../imagenes/logoparainformes.png',2,2,40);
		$pdf->SetFont('Arial','B',10);
		$pdf->Ln();
		$pdf->Ln();
		$pdf->SetY(25);
		$pdf->SetX(5);
		$pdf->Cell(200,5,'Countrie '.utf8_decode($nombre),1,0,'C',false);
		$pdf->SetFont('Arial','',8);
		$pdf->Ln();
		$pdf->SetX(5);

		$i=0;

		$pdf->SetFont('Arial','',8);
		$pdf->Cell(5,4,'',1,0,'C',false);
		$pdf->Cell(60,4,'Apellido y Nombre',0,0,'C',false);
		$pdf->Cell(15,4,'Nro. Doc.',0,0,'C',false);
		$pdf->Cell(16,4,'Fecha Nac.',1,0,'C',false);
		$pdf->Cell(16,4,'Fecha Baja',1,0,'C',false);
	}


	$pdf->Ln();
	$pdf->SetX(5);
	$pdf->SetFont('Arial','',7);
	$pdf->Cell(5,4,$cantPartidos,1,0,'C',false);
	$pdf->Cell(60,4,utf8_decode($rowE['apyn']),1,0,'L',false);
	$pdf->Cell(15,4,$rowE['nrodocumento'],1,0,'C',false);
	$pdf->Cell(16,4,$rowE['fechanacimiento'],1,0,'C',false);
	$pdf->Cell(16,4,$rowE['fechabaja'],1,0,'C',false);






	$contadorY1 += 4;

	//$pdf->SetY($contadorY1);


}
*/
//120 x 109


$nombreTurno = "JUGADORES-COUNTRIES-".$fecha.".pdf";

$pdf->Output($nombreTurno,'I');


?>

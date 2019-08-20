<?php

$pathPlanilla  = 'arbitros';

$filesPlanilla = array_diff(scandir($pathPlanilla), array('.', '..'));

include 'includes/ImageResize.php';
include 'includes/ImageResizeException.php';


/*
$image = new \Gumlet\ImageResize($pathPlanilla.'/'.$value);
$image->scale(50);
$image->save($pathPlanilla.'/'.$value);
*/

$cad = '';

foreach ($filesPlanilla as $key => $value) {
   if ($value != 'index.php') {
      $pathPlanilla  = 'arbitros/'.$value.'/1';
      $pathComplemento  = 'arbitros/'.$value.'/2';

      $filesPlanillaAux = array_diff(scandir($pathPlanilla), array('.', '..'));
      $filesComplementoAux = array_diff(scandir($pathComplemento), array('.', '..'));

      foreach ($filesPlanillaAux as $key => $value) {
         $cad .= '$'."image = new \Gumlet\ImageResize('".$pathPlanilla.'/'.$value."');<br>";
         $cad .= '$'.'image->scale(50);<br>';
         $cad .= '$'."image->save('".$pathPlanilla.'/'.$value."');<br><br>";
         //echo $pathPlanilla.'/'.$value.'<br>';
      }

      foreach ($filesComplementoAux as $key => $value) {
         $cad .= '$'."image = new \Gumlet\ImageResize('".$pathComplemento.'/'.$value."');<br>";
         $cad .= '$'.'image->scale(50);<br>';
         $cad .= '$'."image->save('".$pathComplemento.'/'.$value."');<br><br>";
      }
   }

}

echo $cad;


?>

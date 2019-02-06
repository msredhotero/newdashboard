$(function () {

   //recuperamos la query string
   var club = document.getElementById("club").value;
   var altura = document.getElementById("altura").value;

   traerTareas(club, altura);

   setInterval(function() {
      traerTareas(club, altura);
   },5000);﻿
});

function traerTareas(club, altura) {
   $.ajax({
        dataType: "json",
        data:  {
            idcountrie: club,
            altura: altura,
            accion: 'traerTareasGeneralPorCountrieIncompletas'
        },
        url:   altura + 'ajax/ajax.php',
        type:  'post',
        beforeSend: function () {
            $('.tasks').html('');
        },
        success:  function (response) {
            $('.tasks').html(response.respuesta);
            $('.tareas-cantidad').html(response.cantidad);
        }
   });
}

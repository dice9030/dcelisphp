<?php
require_once('logica.php');


$PostData = $_POST;

$respuesta = InsertarListarusuario($PostData);
echo $respuesta;

function InsertarListarusuario($RowArt){

	$DataInsert = array(                 
                   "nombre"     => $RowArt['Nombre'],
                   "edad"       => $RowArt['Edad'],
                   "direccion"  => $RowArt['Direccion']                
               );

   $New = LogicaPDO::insert("tablanueva",$DataInsert);
   $New = $New['lastInsertId'];
   return  "Codigo Generado Nro: {$New}";

}
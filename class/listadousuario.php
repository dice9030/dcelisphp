<?php
require_once('logica.php');


echo var_dump(Listarusuario()) ;

function Listarusuario(){


		$Sql = "SELECT Id,nombre,edad,direccion FROM tablanueva";

    $dt = LogicaPDO::fetchAllObj($Sql);
	return $dt;

	

}
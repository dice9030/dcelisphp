<?php

require_once('logica.php');

function listado(){

	$SqlCurricula = "SELECT CurriculaCod, ProgramaCod, ProductoCod , Tema
                            Profesor, Entidad, FechReg, Ciclo, CodProgAlmacen,
                            TipoAcceso, Orden, UNegocio, Usuario, tipo_curricula ,
                            FechaHoraActualizacion  FROM curricula";

    $dt = LogicaPDO::fetchAllObj($SqlCurricula);
	return $dt;
}

function listadoconsulta($CurriculaCod){

	$SqlCurricula = "SELECT CurriculaCod, ProgramaCod, ProductoCod , Tema
                            Profesor, Entidad, FechReg, Ciclo, CodProgAlmacen,
                            TipoAcceso, Orden, UNegocio, Usuario, tipo_curricula ,
                            FechaHoraActualizacion  FROM curricula WHERE CurriculaCod= :CurriculaCod";

    $dt = LogicaPDO::fetchAllObj($SqlCurricula,compact("CurriculaCod"));
	return $dt;
}

function inserttabla($RowArt){

	$DataInsert = array(
                 
                   "nombre"     => $RowArt['nombre'],
                   "edad"       => $RowArt['edad'],
                   "direccion"  => $RowArt['direccion']                
               );

   $New = LogicaPDO::insert("tablanueva",$DataInsert);
   $New = $New['lastInsertId'];
   return $New;

}

function Updatetabla($RowArt){

	$DataInsert = array(
                 
                   "nombre"     => $RowArt['nombre'],
                   "edad"       => $RowArt['edad'],
                   "direccion"  => $RowArt['direccion']                
               );

   $update = LogicaPDO::update("tablanueva",
    				 ["nombre"=>$RowArt['nombre'],"edad"=>$RowArt['edad'],"direccion"=>$RowArt['direccion'] ],
    				 ["Id"=>$RowArt['Id'] ]
    				 );             
   return $update;

}

function Deletetabla($RowArt){
	
   $delete = LogicaPDO::delete("tablanueva",["Id"=>$RowArt['Id'] ] );             
   return $delete;

}


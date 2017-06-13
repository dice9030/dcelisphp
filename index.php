<?php
require_once('class/entidades.php');
?>

<!DOCTYPE html>
<html>
<head>
	<title>Dcelis</title>
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
	<script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>	
	<script type="text/javascript">
		var data = [{"nombre":"diego","edad":"12","direccion":"jr. pinar del rio 2279"}];
		console.log(data);

		function enviar(){
				var textrespuesta = "";
				txtnombre=document.getElementById('nombre').value
				txtedad=document.getElementById('edad').value
				txtdireccion=document.getElementById('direccion').value
			    textrespuesta= $.post( "class/entidadesusuario.php",{ Nombre: txtnombre, Edad: txtedad,Direccion: txtdireccion }, function(d) {
 				   console.log(d);		
				})

				console.log(textrespuesta);
				 
		}
	</script>
</head>
<body>



<div class="bs-example col-md-4"" >
	
	<form id="formulario">
		<div class="form-group">
			<label for="nombre">Nombre</label>
			<input type="text" class="form-control" id="nombre" placeholder="Nombre">
		</div>
		<div class="form-group">
			<label for="edad">Edad</label>
			<input type="text" class="form-control" id="edad" placeholder="Edad">
		</div>	
		<div class="form-group">
			<label for="direccion">Direccion</label>
			<input type="text" class="form-control" id="direccion" placeholder="Direccion">
		</div>		
	<button onclick="enviar()" type="button" class="btn btn-default">Enviar</button>
	</form>

</div>




<?php
	




	/*
 	var_dump(inserttabla(array("nombre"=>"Diego","edad"=>"12","direccion"=>"jr. pinar del rio 2279")));
 	var_dump(Updatetabla(array("Id"=>"2","nombre"=>"Leo","edad"=>"12","direccion"=>"jr. pinar del rio 2279")));
 	var_dump(Deletetabla(array("Id"=>"2")));
	*/
?>


</body>
</html>
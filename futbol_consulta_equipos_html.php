<?php
	$tabla_equipos='';
	$codigo=$mensaje='';

	//conexión a la base de datos
	require 'conexion_futbol_PDO.php';

	//Seleccionar todos los equipos de la tabla equipos
	try {
		//la sentencia es preparada con los parametros //parametro LIMIT filainicial y filas a mostrar
		$stmt=$dbh->PREPARE("SELECT * FROM equipo ORDER BY nombre");
		// Especificar como se quieren devolver los datos
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
						//$stmt->setFetchMode(PDO::FETCH_NUM);
						//$stmt->setFetchMode(PDO::FETCH_BOTH);
		
		
		//Ejecutar la sentencia
		$stmt->execute();
		
		//numero de filas modificadas
		//echo $stmt->rowCount();

		//bucle para obtener cada una de las filas obtenidas
		$equipos = array();		
		while ($fila = $stmt->fetch()) {
			array_push($equipos, $fila);
			//echo "<br>";
			//print_r($fila);
			//echo "<br>";
		}

		//print_r($equipos);
		//$codigo='00';
		//$mensaje="OK";
					
	}catch (PDOException $e) {
		//echo $e->getCode().' '.$e->getMessage();
		if ($stmt->errorInfo()[1] == 1146) {
			$codigo=$stmt->errorInfo()[1];
			$mensaje='tabla no existe'.$e->getMessage();
		} else {
			$codigo=$e->getCode();
			$mensaje=$e->getMessage();
		}
		
	}catch (Exception $e) {
		$codigo=$e->getCode();
		$mensaje=$e->getMessage();
		
	}
				
	//bucle while para confeccionar una tabla (etiqueta <table> por equipo
	//la celda de la tabla de la imagen del escudo será un enlace con la pantalla de consulta de jugadores. Le pasaremos el id del equipo por la url
		foreach ($equipos as $key => $datos) {
		$equipo=$datos['equipo_cod'];
		$nombre=$datos['nombre'];
		$anyoFundacion=$datos['fundacion'];
		$presidente=$datos['presidente'];
		$fotoEscudo=$datos['foto_escudo'];
		//echo ($equipo.'-'.$nombre.'-'.$anyoFundacion.'-'.$presidente.'-'.$fotoEscudo);
		//echo ('<br>');
		$tabla_equipos.="<table border='1'>";
			$tabla_equipos.="<tr>";
				$tabla_equipos.="<td class='escudo' rowspan='3'><a href='futbol_consulta_jugadores_html.php?equipo_cod=$equipo'><img src='img/$fotoEscudo' alt='escudo'></a></td>";
			  	$tabla_equipos.="<td>$nombre</td>";
			$tabla_equipos.="</tr>"; 
			$tabla_equipos.="<tr>";
				$tabla_equipos.="<td>$anyoFundacion</td>"; 
			$tabla_equipos.="</tr>"; 
			$tabla_equipos.="<tr>";
				$tabla_equipos.="<td>$presidente</td>";
			$tabla_equipos.="</tr>"; 
		$tabla_equipos.="</table>"; 
	}
		
?>		
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style type="text/css">
		div.container {
			text-align: justify; margin: auto; background-color: cyan; padding:40px;
		}
		div#equipos {
			background:cyan; width:300px; height:200px; float:left; border: 2px solid blue; 
		}
		div#colLeft {
			background:salmon; width:58px; height:200px; float:left; border:1px solid black; text-align: center; line-height: 200px; vertical-align: middle;
		}
		div#colRight {
			background:cyan; width:238px; height:200px; float:left; border:1px solid black; text-align: justify; vertical-align: middle;
		}
		p {
			border: 1px solid black; padding:10px 0px 10px 10px;
		}
		table {display: inline-block;}
		td {width: 240px;}
		td.escudo {width: 50px;text-align: center;background-color: white;}
	</style>
	<script type="text/javascript">
			
	</script>
</head>
<body>
	<div class="container">
		<h2 style="text-align:center">EQUIPOS DE PRIMERA DIVISION</h2>
		<?=$tabla_equipos?>
	</div>
	<span><?=$codigo?></span><span> - </span><span><?=$mensaje?><br>
</body>
</html>
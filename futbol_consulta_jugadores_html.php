<?php
	
	$foto_estadio=$nombre_estadio=$nombre=null;
	//conexión a la base de datos
	require 'conexion_futbol_PDO.php';
	//comprobar si nos llega un equipo por la url (GET). En caso contrario volver a la consulta de equipos
	//recuperar el id del equipo que nos llega por GET
	if (isset($_GET['equipo_cod'])) {
		$equipo=$_GET['equipo_cod'];
	} else {
		$url = $_SERVER['HTTP_REFERER'];
		header("Location: $url");
	}	

	//seleccionar los datos del equipo de la tabla equipo
	$consultaEquipo=buscar_equipo($equipo,$dbh);
	//print_r($consultaEquipo);
	$e=$consultaEquipo[0];
	if ($e['codigo']!='00') {
		echo ("error: ".$e['codigo'].' '.$e['mensaje']);
	} else {
		$datosEquipo=$consultaEquipo[1];
	
		//informar las variables: nombre, foto_equipo, foto_escudo con los datos de la consulta
		foreach ($datosEquipo as $key => $datos) {
			$nombre=$datos['nombre'];
			$foto_escudo=$datos['foto_escudo'];
			$foto_equipo=$datos['foto_equipo'];
		}
		//acceder a la tabla estadio para consultar los datos del estadio 
		$datosEstadio=buscar_estadio($equipo,$dbh);
		
		//informar las variables: nombre_estadio, foto_estadio con los datos de la consulta
		$foto_estadio=$datosEstadio[0]['foto_estadio'];
		$nombre_estadio=$datosEstadio[0]['nombre'];
		
		//acceder a la tabla jugador apra recuperar todos los jugadores del equipo ordenados por nombre y apellidos
		$regJugadores=buscar_jugadores($equipo,$dbh);
		
		//bucle while para confeccionar la lista de jugadores del equipo (lista, tabla, div coloreados alternativamente, etc)
		$jugadores=null;
		$jugadores.="<div class='jugadores'>";
		$jugadores.="<table >";
		foreach ($regJugadores as $key => $datos) {
			
			$nombre=$datos['nombre'].' '.$datos['apellidos'];
			$numero_camiseta=$datos['numero_camiseta'];
			$calidad=$datos['calidad'];
			//echo ("$numero_camiseta  $nombre");
			//echo "<br>";
			//utilizar un atributo title de forma que, al pasar el raton por encima, obtengamos la valoración del jugador (campo descripcion de la tabla jugador)
			//informar la variable jugadores con la lista anterior
				$jugadores.="<tr class='jugador' title=$calidad>";
					$jugadores.="<td>$numero_camiseta</td>";
					$jugadores.="<td>$nombre</td>";
				$jugadores.="</tr>"; 
		}
		$jugadores.="</table>";
		$jugadores.="</div>";	 
	}
	//función para obtener los datos del equipo
	function buscar_equipo($equipo,$dbh) {
		try {
		//carga atributos recibidos
		
			if ($equipo=='') {
				throw new Exception("falta el equipo a seleccionar", 10);
			}
			//la sentencia es preparada con los parametros
			$stmt=$dbh->PREPARE("SELECT * FROM equipo WHERE equipo_cod = :equipo_cod");
			//bind de los parametros // asigna los valores a la sentencia preparada
			$stmt->bindParam(':equipo_cod', $equipo);
			// Especificar como se quieren devolver los datos
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			//Ejecutar la sentencia
			$stmt->execute();
			//numero de filas modificadas
			//echo $stmt->rowCount();
			$datos = array();
			//bucle para obtener cada una de las filas obtenidas
			while ($fila = $stmt->fetch()) {
				array_push($datos, $fila);
				//echo "<br>";
				//print_r($datos);
			}
			$codigo='00';
			$mensaje="OK";
			$control=array('codigo'=>$codigo, 'mensaje'=> $mensaje);
			$respuesta=array($control, $datos);
			return $respuesta;
			
		}catch (PDOException $e) {
			//echo $e->getCode().' '.$e->getMessage();
			if ($stmt->errorInfo()[1] == 1146) {
				$codigo=$stmt->errorInfo()[1];
				$mensaje='tabla no existe'.$e->getMessage();
			} else {
				$codigo=$e->getCode();
				$mensaje=$e->getMessage();
			}
			$control=array('codigo'=>$codigo, 'mensaje'=> $mensaje);
			$respuesta=array($control);
			return $respuesta;
		}catch (Exception $e) {
			$codigo=$e->getCode();
			$mensaje=$e->getMessage();
			$control=array('codigo'=>$codigo, 'mensaje'=> $mensaje);
			$respuesta=array($control);
			return $respuesta;
		}
		

	}

	//función para obtener los jugadores de un equipo
	function buscar_jugadores($equipo,$dbh) {
		//echo ("buscar_jugadores");
		//echo ($equipo);
		try {
		//carga atributos recibidos
			if ($equipo=='') {
				throw new Exception("falta el equipo a seleccionar", 10);
			}
			//la sentencia es preparada con los parametros
			$stmt=$dbh->PREPARE("SELECT * FROM jugador WHERE equipo_cod = :equipo_cod");
			//bind de los parametros // asigna los valores a la sentencia preparada
			$stmt->bindParam(':equipo_cod', $equipo);
			// Especificar como se quieren devolver los datos
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			//Ejecutar la sentencia
			$stmt->execute();
			//numero de filas modificadas
			//echo $stmt->rowCount();
			$regJugadores = array();
			//bucle para obtener cada una de las filas obtenidas
			while ($fila = $stmt->fetch()) {
				array_push($regJugadores, $fila);
				//echo "<br>";
				//print_r($fila);
			}
			$codigo='00';
			$mensaje="OK";
			return $regJugadores;
			
		}catch (PDOException $e) {
			//echo $e->getCode().' '.$e->getMessage();
			if ($stmt->errorInfo()[1] == 1146) {
				$codigo=$stmt->errorInfo()[1];
				$mensaje='tabla no existe'.$e->getMessage();
			} else {
				$codigo=$e->getCode();
				$mensaje=$e->getMessage();
				return ($codigo. ' '.$mensaje);
			}
		}catch (Exception $e) {
			$codigo=$e->getCode();
			$mensaje=$e->getMessage();
			return ($codigo. ' '.$mensaje);
		}

	}

	//Función para obtener los datos del estadio
	function buscar_estadio($equipo,$dbh) {
		try {
		//carga atributos recibidos
		
			if ($equipo=='') {
				throw new Exception("falta el equipo a seleccionar", 10);
			}
			//la sentencia es preparada con los parametros
			$stmt=$dbh->PREPARE("SELECT * FROM estadio WHERE equipo_cod = :equipo_cod");
			//bind de los parametros // asigna los valores a la sentencia preparada
			$stmt->bindParam(':equipo_cod', $equipo);
			// Especificar como se quieren devolver los datos
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			//Ejecutar la sentencia
			$stmt->execute();
			//numero de filas modificadas
			//echo $stmt->rowCount();
			$estadio = array();
			//bucle para obtener cada una de las filas obtenidas
			while ($fila = $stmt->fetch()) {
				array_push($estadio, $fila);
				//echo "<br>";
				//print_r($estadio);
			}

			$codigo='00';
			$mensaje="OK";
			return $estadio;
			
		}catch (PDOException $e) {
			//echo $e->getCode().' '.$e->getMessage();
			if ($stmt->errorInfo()[1] == 1146) {
				$codigo=$stmt->errorInfo()[1];
				$mensaje='tabla no existe'.$e->getMessage();
			} else {
				$codigo=$e->getCode();
				$mensaje=$e->getMessage();
				return ($codigo. ' '.$mensaje);
			}
		}catch (Exception $e) {
			$codigo=$e->getCode();
			$mensaje=$e->getMessage();
			return ($codigo. ' '.$mensaje);
		}
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<style type="text/css">
		div.container {
			width:900px; text-align: center; margin: auto; background-color: cyan;
		}
		div#imagenes {
			background:cyan; display: block;  
		}
		img {
			margin: 20px 60px 20px 60px;
		}
		div.jugadores {
			background:cyan; text-align: justify; display: block;
		}
		.jugadores table {width: 100%;}
		p {
			padding:0px; margin:0px;
		}
		tr:nth-child(odd) {background: salmon;}
		tr:nth-child(even) {background: cyan;}
	</style>
</head>
<body>
	<div class="container">
		<h2 style="text-align:center">JUGADORES DE PRIMERA DIVISION</h2>
		<h2 style='text-align:center'><?=$nombre?></h2>

		<div id='imagenes'>
			<img src='img/<?=$foto_equipo?>'>
			<img width='5%' src='img/<?=$foto_escudo?>'>
			<img src='img/<?=$foto_estadio?>' title='<?=$nombre_estadio?>'>
		</div>
		<?=$jugadores?>

		<br><br>
		<a href="futbol_consulta_equipos_html.php?">Volver a selección de equipos</a>
		&nbsp&nbsp
		<a href="futbol_consulta_calidades_html.php?idequipo=<?=$equipo?>" target='calidades'>consultar calidades</a><br><br>
		<iframe width="100%" height="450" frameborder="0" name="calidades"></iframe>
	</div>
	
</body>
</html>
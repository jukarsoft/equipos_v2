<?php
	$calidades1="";
	$calidades2="";
	$equipo=null;

	// conexión a la base de datos
	require 'conexion_futbol_PDO.php';
	//comprobar si nos llega un equipo por la url (GET). En caso contrario volver a la consulta de equipos
	//recuperar el id del equipo que nos llega por GET
	if (isset($_GET['idequipo'])) {
		$equipo=$_GET['idequipo'];
	} else {echo "falta el equipo";

	}	
	
	//comprobar si nos llega un equipo por la url (GET). En caso contrario volver a la consulta de equipos
	
	//recuperar el equipo que nos llega por GET
	
	//seleccionar todos los jugadores de la tabla jugador que pertenecen al equipo que nos llega por GET ordenados por nombre y apellidos
	$regJugadores=buscar_jugadores($equipo,$dbh);
	//print_r($regJugadores);
	$jugadores=null;

	//montaremos dos filas de una tabla: una con las barras de las calidades de 300px de alto y otra con los jugadores (el número de camiseta del jugador). Habrá tantas columnas como jugadores tenga el equipo
	//bucle while para informar cada una de las columnas de las dos filas
	//informar la celda td de la fila de calidades con una etiqueta div de color amarillo, ancho 30px y alto proporcional a la calidad del jugador:
	$calidades1.="<tr>";
	foreach ($regJugadores as $key => $datos) {
		$nombre=$datos['nombre'].$datos['apellidos'];
		$numero_camiseta=$datos['numero_camiseta'];
		$calidad=$datos['calidad'];
		//echo ("$numero_camiseta  $nombre");
		//echo "<br>";
		$barra=30*$calidad.'px';
		$calidades1.="<td title=$nombre style=height:300px>";
			$calidades1.="<div class='barra' style='background-color:yellow; height:$barra;>'></div>";
		$calidades1.="</td>"; 
	}
	$calidades1.="</tr>";
	//bucle while para informar cada una de las columnas de las dos filas
	//informar la celda td de la fila de jugadores con el número de camiseta consultado en la tabla jugador
	//para calcular el alto multiplicar la calidad de la tabla jugador por 30
	$calidades2.="<tr>";
	foreach ($regJugadores as $key => $datos) {
		$nombre=$datos['nombre'].$datos['apellidos'];
		$numero_camiseta=$datos['numero_camiseta'];
		$calidad=$datos['calidad'];
		//echo ("$numero_camiseta  $nombre");
		//echo "<br>";
		$calidades2.="<th title=$calidad>$numero_camiseta";
		$calidades2.="</th>"; 
		
	}
	$calidades2.="</tr>";

	//Obtener jugadores de un equipo		
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
				echo ($codigo. ' '.$mensaje);
			}
		}catch (Exception $e) {
			$codigo=$e->getCode();
			$mensaje=$e->getMessage();
			echo ($codigo. ' '.$mensaje);
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
			margin: auto; text-align: center; height: 340px;
		}
		div.calidades {
			vertical-align: top; 
		}
		table {width: 100%;}
		th, td {
			background:white; width:30px; border: 2px solid green; text-align: center; vertical-align: bottom;
		}
		p {
			padding:0px; margin:0px;
		}
		
	</style>
</head>
<body>
	<div class="container">
		<h2 style="text-align:center">EJERCICIO CALIDADES</h2>
		<div class="calidades">
			<table>
				<?php echo $calidades1; ?>
				<?php echo $calidades2; ?>
			</table>
		</div>
	</div>
</body>
</html>
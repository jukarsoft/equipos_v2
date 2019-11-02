<?php 
		$dsn = "mysql:host=localhost;dbname=futbol;charset=UTF8"; 
		//crear objeto del pdo // parametros de conexión , usuario, contraseña
		$usuario="root";
		$password="";
		$dbh = new PDO($dsn, $usuario, $password); 
		//activar excepciones
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
?>
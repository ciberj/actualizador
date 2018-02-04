<?php
	$mbd = new PDO('mysql:host=localhost','root', '');  
	$crear_db = $mbd->prepare('CREATE DATABASE IF NOT EXISTS productos COLLATE utf8_spanish_ci');   
 	$crear_db->execute();

 	$use_db = $mbd->prepare('USE productos');   
	$use_db->execute();


	$crear_tb_users = $mbd->prepare("
		 CREATE TABLE `productos` (
			  `codigo` varchar(50) NOT NULL,
			  `ean` varchar(11) NOT NULL,
			  `nombre` varchar(256) NOT NULL,
			  `marca` varchar(256) NOT NULL,
			  `stock_00` int(11) NOT NULL,
			  `stock_01` int(11) NOT NULL,
			  `stock_07` int(11) NOT NULL,
			  `pendiente_recepcion` int(11) NOT NULL,
			  `tarifa_base` decimal(10,2) NOT NULL,
			  `tarifa_pvp` decimal(10,2) NOT NULL,
			  `tarifa_tiendas` decimal(10,2) NOT NULL,
			  `oferta_porcent` decimal(10,2) NOT NULL,
			  `oferta_especial` decimal(10,2) NOT NULL,
			  `oferta_pvp` decimal(10,2) NOT NULL,
			  `jl_web` decimal(10,2) NOT NULL,
			  `jl_coste` decimal(10,2) NOT NULL,
			  `pvpFinal` decimal(10,2) NOT NULL
			)"); 
	 $crear_tb_users->execute();

	 $crear_tb_users = $mbd->prepare("ALTER TABLE `productos`ADD PRIMARY KEY (`codigo`)");
	  $crear_tb_users->execute();

 echo "la base de datos se ha creado correctamente.";


?>
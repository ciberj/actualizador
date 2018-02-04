<?php
include_once("auxiliar.php");
include_once("config.php");



class ReadFileCsv {

	private $url;
	private $numCampos;
	private $nombresCampos;
	private $numRegistros;
	private $separador;
	private $contenedor; // contenedor del registro, ej. ""
	private $cabecera;
	public $registros;
	public $modificados;
	public $config;

	function __construct($url,$separador,$contenedor,$archivoSalida){

            $this->url= $url;
            $this->numCampos=0;
            $this->numRegistros=0;
            $this->nombresCampos;
            $this->separador=$separador;
            $this->contenedor=$contenedor;
            $this->registros=[];
            $this->archivoSalida=$archivoSalida;
            $this->modificados=[];




    }

    public function grabarFicheroTodo($fichero){
    	$mbd = new PDO('mysql:host=localhost;dbname=productos','alfonso', '123456');  
		
		$stmt = $mbd->prepare("SELECT * FROM productos");
		$stmt->execute();
		//$result = $stmt->fetchAll();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		
		//mostrarTabla($result[0],$result,$camposMostrar);

		foreach ($result[0] as $campo=>$value) {
					
					$camposMostrar[]=$campo; // almacena todos los campos
					}
		$camposMostrar=['codigo','nombre','tarifa_tiendas','pvpFinal']; // en este caso solo esto campos
		//mostrarTabla($camposMostrar,$result);
		
		grabarTabla($camposMostrar,$result,$fichero);


    }

    public function __get($propiedad){
            return $this->$propiedad;
    }

    public function leerFichero(){

    	$registros = array();
		if (($fichero = fopen($this->url, "r")) !== FALSE) {
		    // Lee los nombres de los campos
		    $this->nombresCampos = fgetcsv($fichero, 0, ";", "\"", "\""); // 1ª linea del fichero
		    $this->numCampos = count($this->nombresCampos); 
    // Lee los registros
    
		    while (($datos = fgetcsv($fichero, 0, ";", "\"", "\"")) !== FALSE) {  // resto de lineas
		        // Crea un array asociativo con los nombres y valores de los campos
		        for ($icampo = 0; $icampo < $this->numCampos; $icampo++) {
		        		if (isset($datos[$icampo])){
		        			$registro[$this->nombresCampos[$icampo]] = $datos[$icampo];
		        		}else{
		        			$registro[$this->nombresCampos[$icampo]] = "";
		        		}         
		        }

		        // Añade el registro leido al array de registros
		        $registros[] = $registro;
		    }
		    fclose($fichero);
		    $this->numRegistros=count($registros);
		    $this->registros=$registros;
		 	
		} // while

    } // funcion

    public function mostrarCabecera(){
    	foreach ($this->nombresCampos as $campo=>$valor){
    		echo $campo." - ".$valor."<br>";
    	}
    }

    public function grabarBd($user,$pass){
		
		$mbd = new PDO('mysql:host=localhost;dbname=productos','alfonso', '123456');  
		$sql = "TRUNCATE TABLE productos";
		$command = $mbd->prepare($sql);
		$command->execute();
		$stmt = $mbd->prepare("INSERT INTO productos (codigo, ean, nombre, marca,stock_00,stock_01,stock_07,pendiente_recepcion,tarifa_base,tarifa_pvp,tarifa_tiendas,oferta_porcent,oferta_especial,oferta_pvp,jl_web,jl_coste,pvpFinal)
             VALUES (:codigo, :ean, :nombre, :marca,:stock_00,:stock_01,:stock_07,:pendiente_recepcion,:tarifa_base,:tarifa_pvp,:tarifa_tiendas,:oferta_porcent,:oferta_especial,:oferta_pvp,:jl_web,:jl_coste,:pvpFinal)");

		//echo $this->registros[5]['codigo'];
		
		foreach ($this->registros as $registro) {
			
			if ($registro['stock_00']>0){ // si hay stock jl_coste es el coste correcto
				$pvpFinal=ajustarPvp($registro['jl_coste']);
				if (abs($pvpFinal-$registro['tarifa_tiendas'])>10){ // cuando la diferencia es +->10
					$producto['codigo']=$registro['codigo'];
					$producto['nombre']=$registro['nombre'];
					$producto['marca']=$registro['marca'];
					$producto['pvpAnterior']=$registro['tarifa_tiendas'];
					$producto['pvpFinal']=$pvpFinal;
					
					$modificados[]=$producto;
					
				}
			}else{
				// de momento cojo el campo 25% rentabilidad de la tabla
				$pvpFinal=$registro['tarifa_tiendas'];
			}
			
			
			$stmt->bindParam(':codigo', $registro['codigo']);  	
			$stmt->bindParam(':ean', $registro['ean']);
			$stmt->bindParam(':nombre', $registro['nombre']);
			$stmt->bindParam(':marca', $registro['marca']);
			$stmt->bindParam(':stock_00', $registro['stock_00']);
			$stmt->bindParam(':stock_01', $registro['stock_01']);
			$stmt->bindParam(':stock_07', $registro['stock_07']);
			$stmt->bindParam(':pendiente_recepcion', $registro['pendiente_recepcion']);
			$stmt->bindParam(':tarifa_base', $registro['tarifa_base']);
			$stmt->bindParam(':tarifa_pvp', $registro['tarifa_pvp']);
			$stmt->bindParam(':tarifa_tiendas', $registro['tarifa_tiendas']);
			$stmt->bindParam(':oferta_porcent', $registro['oferta_porcent']);
			$stmt->bindParam(':oferta_especial', $registro['oferta_especial']);
			$stmt->bindParam(':oferta_pvp', $registro['oferta_pvp']);
			$stmt->bindParam(':jl_web', $registro['jl_web']);
			$stmt->bindParam(':jl_coste', $registro['jl_coste']);
			$stmt->bindParam(':pvpFinal', $pvpFinal);
			$stmt->execute();
			//echo "insertado codigo: ".$registro['codigo']."<br>";
			
		}
		$this->modificados=$modificados;
			//grabarTabla(['codigo','nombre','marca','pvpAnterior','pvpFinal'],$modificados,'modificados.csv');
		
    }
    function grabarFicheroModificados($fichero){

    	grabarTabla(['codigo','nombre','marca','pvpAnterior','pvpFinal'],$this->modificados,$fichero);
    }
  
  function buscarModificados(){
	$mbd = new PDO('mysql:host=localhost;dbname=productos','alfonso', '123456');
	$sql = "SELECT * FROM productos";
	$command = $mbd->prepare($sql);
	$command->execute();
	$total = $command->rowCount();/*
	while ($row = $stmt->fetchObject()) {  // si lo queremos tratar como objeto
	   echo $row->filmName; 
	}*/
	
	$result = $command->fetchAll();					// si lo queremos tratar como array
	
	foreach($result as $registro){
		if ($registro['stock_00']>0){ // si hay stock jl_coste es el coste correcto
				$pvpFinal=ajustarPvp($registro['jl_coste']);
				if (abs($registro['pvpFinal']-$registro['tarifa_tiendas'])>10){ // cuando la diferencia es +->10
					
					$modificados[]=$registro;
					
				}
		}else{
				// de momento cojo el campo 25% rentabilidad de la tabla
				$pvpFinal=$registro['tarifa_tiendas'];
		}
		// grabo el registro en la base datos.
		$sql = 'UPDATE productos SET pvpFinal='.$pvpFinal.' WHERE codigo='.$registro['codigo'];
		//echo $sql;
		$command = $mbd->prepare($sql);
		$command->execute();
		;

		
	}
	ECHO "NUMERO TOTAL DE M";
	$this->modificados=$modificados;

	    
}  
	
}








function ajustarPvp($costo){
    if ($costo==0) return 0;
    $pvpTemp=ceil($costo*1.21*1.20);
	//echo "<br><br>";
	//echo "costo: ".$costo."-----> PVP temp: ".$pvpTemp."<br>";
	//echo "intentamos ajustar a 9€:";
	$string=(string)$pvpTemp;
	$cont=strlen($string);
	//echo "__".(int)$string[$cont-1];
	$pvpTemp9=(9-(int)$string[$cont-1])+$pvpTemp;
	//echo "__".$pvpTemp9." --- Con margen: ";
	$margen=(((($pvpTemp9/1.21)/$costo))-1)*100;
	//echo $margen;
	if ($margen>23){    // intentamos ajustar al 4.9
		$pvpTemp4=(4-(int)$string[$cont-1])+$pvpTemp;
		//echo "<br>"."ajustamos al 5 : ".$pvpTemp4;
		//echo $pvpTemp4." --- Con margen: ";
		$margen=(((($pvpTemp4/1.21)/$costo))-1)*100;
		//echo $margen;
		$margen=(((($pvpTemp4/1.21)/$costo))-1)*100;
		 if ($margen>20){// confirmamos que cuando ajustamos al 5 no estamos por debajo del 20%3
			$pvpFinal=$pvpTemp4;
		 }else{
		 	$pvpFinal=$pvpTemp9;
		 }
	} else {
		//echo "<br>esta bien ajustado";
		
		 $pvpFinal=$pvpTemp9;

		
	}

	//echo ">El Precio final correto sera: ".$pvpFinal;
	return $pvpFinal;

    }

?>
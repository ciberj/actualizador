<head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
<script>
	$(document).ready(function(){
$("button").click(function(){
        var parametros = {
                "modelo" : "3HB506XM"
                
        };
        $.ajax({
                data:  parametros,
                url:   'http://juanlucas.com/scripts/etiquetas/A4x3.php',
                type:  'post',
                beforeSend: function () {
                        $("#resultado").html("Procesando, espere por favor...");
                },
                success:  function (response) {
                        $("#resultado").html(response);
                }
        });
})});
</script>

<body>

<div id="div1"><h2>Let jQuery AJAX Change This Text</h2></div>

<button>Get External Content</button>
<div id="resultado"></div>
</body>
<?php

include_once ("ReadFileCsv.php");
include_once("config.php");


$fichero = new ReadFileCsv($config["urlSource"],";","\"",$config['archivoSalida']);
$fichero->leerFichero();
//echo "Número de campos: ".$fichero->numCampos."<br>";
//echo $fichero->registros[5]["codigo"];
$fichero->grabarBd("alfonso","123456");
$fichero->grabarFicheroModificados("modificados.csv");
$fichero->grabarFicheroTodo('todo.csv');



echo "<h1><a href=\"descargarTodo.php?fichero=todo.csv\">fichero: todo.csv</a>"."<BR>";
echo "<a href=\"descargarTodo.php?fichero=modificados.csv\">fichero: modificados.csv</a></h1>";
$zip = new ZipArchive();
$filename = "archivos.zip";
if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$zip->addFile('todo.csv');
$zip->addFile('modificados.csv');
 $zip->close();
 echo "<h1><a href=\"descargarTodo.php?fichero=archivos.zip\">fichero: archivos.zip</a></h1>";
 
$camposMostrar=['codigo','nombre','pvpAnterior','pvpFinal']; // en este caso solo esto campos

mostrarTabla($camposMostrar,$fichero->modificados);


/*
$ch = curl_init("http://212.145.243.67/scripts/read_tabla_articulos.php");
$fp = fopen("productos.csv", "w");

$productos=file_get_contents("http://212.145.243.67/scripts/read_tabla_articulos.php");
echo $productos;

curl_setopt($ch, CURLOPT_FILE, $fp);

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // evita que muestre resultado.

$productos=curl_exec($ch);

$productos=explode("\n", $productos);
$fichero = new SplFileObject("http://212.145.243.67/scripts/read_tabla_articulos.php");
while (!$fichero->eof()) {
    var_dump($fichero->fgetcsv());
}



curl_close($ch);
fclose($fp);*/
?>
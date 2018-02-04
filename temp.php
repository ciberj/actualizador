<?php
$zip = new ZipArchive();
$filename = "archivos.zip";
if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("cannot open <$filename>\n");
}

$zip->addFile('todo.csv');
$zip->addFile('modificados.csv');
 $zip->close();
echo 'Creado '.$filename;
$file="archivos.zip";
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename='.$file);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file));
ob_clean();
flush();
readfile($file);

?>
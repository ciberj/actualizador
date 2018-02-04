<?php

if(isset($_GET['fichero'])) {
    $file=basename($_GET['fichero']);
}
else {
    header('HTTP/1.0 404 Not Found');
    die('File not found!');
}
//$file=basename($_GET['fichero']);

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
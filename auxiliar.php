<?php

	function mostrarTabla($cabecera,$datos){ //$cabecera,$datos,

		echo '	<style type="text/css" media="screen">
				table tr:hover {
				  background-color: #f00;
				  cursor: pointer;
				}
				
			</style>';

			echo "<table>";
			echo "<thead><tr>";
			if (isset($cabecera)){
				foreach ($cabecera as $value) {
				echo "<th>".$value."</th>";
				}
			
			
				echo "</tr></thead>";
				foreach ($datos as $row) {
					
					echo "<tr>";
					foreach ($cabecera as $value) {
						echo '<td>'.$row[$value]."</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
			} // if
		
	}

	function grabarTabla($cabecera,$datos,$nombreArchivo){

		//ob_end_clean();
		// creo el archivo en el servidor.
			$fp= fopen($nombreArchivo, 'w'); // para que no se grabe en el servidor y vaya al explorador
		
		 fputcsv($fp, $cabecera,";", "\""); // imprimo cabecera
		 
		 
		 foreach ($datos as $row) 
		 {
		    fputcsv($fp, $row,";", "\"");
		 }
		 fclose($fp);
		// lo descargo
		 /*

		$file = basename($nombreArchivo);
			

			if(!$file){ // file does not exist
			    echo 'file not found';
			} else {
			    header("Cache-Control: public");
			    header("Content-Description: File Transfer");
			    header("Content-Disposition: attachment; filename=$file");
			    header("Content-Type: application/force-download");
			    header("Content-Transfer-Encoding: octet-stream");

			    // read the file from disk
			    
			    readfile($file);
			}*/
		}
		/* PARA CUANDO QUIERO IMPRIMIR LO QUE SALE EN BROWSER 
		
		 header( "Content-Type: text/csv;charset=utf-8" );
		 header( "Content-Disposition: attachment;filename=\"".$nombreArchivo."\"" );
		 header("Pragma: no-cache");
		 header("Expires: 0");
		
		 $fp= fopen('php://output', 'w'); // para que no se grabe en el servidor y vaya al explorador
		
		 fputcsv($fp, $cabecera,";", "\""); // imprimo cabecera
		 
		 
		 foreach ($datos as $row) 
		 {
		    fputcsv($fp, $row,";", "\"");
		 }
		 fclose($fp);
		 
	}*/
	

?>

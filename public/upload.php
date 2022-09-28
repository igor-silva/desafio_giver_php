<?php
header('Access-Control-Allow-Origin: *');
$target_dir = "uploads/";
$target_file = $_FILES["file"]["name"];
$uploadOk = 1;
$csvFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$dirFull = $target_dir.$target_file;


if(file_exists($dirFull)){
    unlink($$dirFull);
}



// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Desculpe, o arquivo já existe.";
        $uploadOk = 0;
    }
    
    // Check file size
   /* if ($_FILES["file"]["size"] > 50000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }*/
    
    // Allow certain file formats
    if($csvFileType != "csv") {
        echo "Desculpe, apenas arquivos CSV são permitidos.";
        $uploadOk = 0;
    }
    
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Desculpe, seu arquivo não foi carregado.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/".$target_file)) {
            echo "O arquivo ". htmlspecialchars( basename( $_FILES["file"]["name"])). " foi carregado.";
        } else {
            echo "Desculpe, ocorreu um erro ao enviar seu arquivo. ".$_FILES["file"]["error"];
        }
    }
        // Open the file for reading
    if (($h = fopen($dirFull, "r")) !== FALSE) {
        // Convert each line into the local $data variable
        while (($data = fgetcsv($h, 1000, ",")) !== FALSE){		
            // Read the data from a single line
            $array_data[] = $data;	
        }
  
        // Close the file
        fclose($h);

        // Display the code in a readable format
        echo "<pre>";
        var_dump($array_data);
        echo "</pre>";
    }
  
}



?>
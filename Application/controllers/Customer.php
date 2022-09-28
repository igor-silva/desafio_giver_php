<?php

use Application\core\Controller;

class Customer extends Controller
{
  /**
  * chama a view index.php da seguinte forma /customer/index   ou somente   /customer
  * e retorna para a view todos os usuários no banco de dados.
  */
  public function index()
  {
    $Customers = $this->model('Customers'); // é retornado o model Customers()
    $data = $Customers::findAll();
    $this->view('customer/index', ['customers' => $data]);
  }

  /**
  * chama a view show.php da seguinte forma /customer/show passando um parâmetro 
  * via URL /customer/show/id e é retornado um array contendo (ou não) um determinado
  * cliente. Além disso é verificado se foi passado ou não um id pela url, caso
  * não seja informado, é chamado a view de página não encontrada.
  * @param  int   $id   Identificado do cliente.
  */
  public function show($id = null)
  {
    if (is_numeric($id)) {
      $Customers = $this->model('Customers');
      $data = $Customers::findById($id);
      $this->view('customer/show', ['customer' => $data]);
    } else {
      $this->pageNotFound();
    }
  }

  public function api($type_report, $limit, $offset)
  {
    $Customers = $this->model('Customers'); // é retornado o model Customers()
    $data = $Customers::findWhere($type_report, $limit, $offset);
   // $this->view('customer/api', ['customers' => $data]);
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
    echo $json = json_encode($data, JSON_UNESCAPED_UNICODE);
  
  }


  public function insert()
  {

    header('Access-Control-Allow-Origin: *');
    $target_dir = "uploads/";
    $target_file = $_FILES["file"]["name"];
    $uploadOk = 1;
    $csvFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $dirFull = $target_dir.$target_file;

    if(isset($_POST["submit"])) {

        if (file_exists($target_file)) {
            unlink($dirFull);
        }
        
       /* if ($_FILES["file"]["size"] > 50000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }*/
        
        if($csvFileType != "csv") {
            echo "Desculpe, apenas arquivos CSV são permitidos.";
            $uploadOk = 0;
        }
        
        if ($uploadOk == 0) {
            echo "Desculpe, seu arquivo não foi carregado.";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], "uploads/".$target_file)) {

              echo "O arquivo ". htmlspecialchars( basename( $_FILES["file"]["name"])). " foi carregado. <br> ";

              $rows = array_map('str_getcsv', file($dirFull));
              $header = array_shift($rows);
              $csv = array();
              foreach ($rows as $row) {
                $csv[] = array_combine($header, $row);
              }

          
              if (count($csv) > 0) {
                $Customers = $this->model('Customers');
                $insert = $Customers::insertCsvData($dirFull);
                $this->view('customer/insert');
              } else {
                $this->pageNotFound();
              }

              
            } else {
                echo "Desculpe, ocorreu um erro ao enviar seu arquivo. Error: ".$_FILES["file"]["error"];
            }
        }
           
      
    }

  }


}

<?php

namespace Application\models;

use Application\core\Database;
use PDO;
class Customers
{
  /** Poderiamos ter atributos aqui */

  /**
  * Este método busca todos os clientes armazenados na base de dados
  *
  * @return   array
  */
  public static function findAll()
  {
    $conn = new Database();
    $result = $conn->executeQuery('SELECT * FROM customers');

    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
  * Este método busca um cliente armazenados na base de dados com um
  * determinado ID
  * @param    int     $id   Identificador único do usuário
  *
  * @return   array
  */
  public static function findById(int $id)
  {
    $conn = new Database();
    $result = $conn->executeQuery('SELECT * FROM customers WHERE id = :ID LIMIT 1', array(
      ':ID' => $id
    ));

    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  public static function findWhere($type_report, $limit, $offset)
  {
    $conn = new Database();

    if($type_report="1"){//Listar todos
      $where = "1";
      $fields = "*";
    }elseif($type_report="2"){//Sobronome em branco
      $where = "last_name = ''";
      $fields = "COUNT(*) AS TOTAL_BLANK";
    }elseif($type_report="3"){//Sobronome preenchido
      $where = "last_name <> ''";
      $fields = "COUNT(*) AS TOTAL_NOBLANK";
    }elseif($type_report="4"){//E-mails inválidos
      $where = "email NOT REGEXP '^[a-zA-Z0-9][a-zA-Z0-9.!#$%&'*+-/=?^_`{|}~]*?[a-zA-Z0-9._-]?@[a-zA-Z0-9][a-zA-Z0-9._-]*?[a-zA-Z0-9]?\\.[a-zA-Z]{2,63}$'";
      $fields = "COUNT(*) AS TOTAL_INVALID";
    }elseif($type_report="5"){//E-mails válidos
      $where = "email REGEXP '^[a-zA-Z0-9][a-zA-Z0-9.!#$%&'*+-/=?^_`{|}~]*?[a-zA-Z0-9._-]?@[a-zA-Z0-9][a-zA-Z0-9._-]*?[a-zA-Z0-9]?\\.[a-zA-Z]{2,63}$'";
      $fields = "COUNT(*) AS TOTAL_VALID";
    }elseif($type_report="6"){//Genero em branco
      $where = "gender = ''";
      $fields = "COUNT(*) AS TOTAL_GENDER_BLANK";
    }elseif($type_report="7"){//Genero peenchido
      $where = "gender <> ''";
      $fields = "COUNT(*) AS TOTAL_GENDER_NOBLANK";
    } 
    
    $result = $conn->executeQuery('SELECT '.$fields.' FROM customers WHERE '.$where.'  LIMIT '.$limit.', '.$offset);

    return $result->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
  * Este método prepara a query e dados a serem armazenados na base de dados
  * @param    string     $dirFull   diretório do arquivo
  *
  */
  public static function insertCsvData($dirFull)
  {
    $conn = new Database();

    $file = $dirFull;
    $handle = fopen($file, "r");

    try { 
      fgets($handle); 
      while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
        $dataCsv[] = $data; 
      }   

      $sql = 'INSERT INTO customers (id,first_name,last_name,email,gender,ip_address,company,city,title,website) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

      $insertData =  $conn->executeInsert($sql,$dataCsv); 
      
      fclose($handle);

      echo 'Dados importados!';

    } catch(PDOException $e) {
        die($e->getMessage());
    }       

  }

}

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
    $limit_offset = '';

    switch ($type_report) {
      case '1': //Listar todos
        $whereCond = "1";
        $fields = "*";
        $limit_offset = ' LIMIT '.$limit.', '.$offset;
        break;
      case '2': //Sobronome em branco
        $whereCond = "last_name = ''";
        $fields = "COUNT(*) AS TOTAL_LAST_NAME_BLANK";
        break;
      case '3': //Sobronome preenchido
        $whereCond = "last_name <> ''";
        $fields = "COUNT(*) AS TOTAL_LAST_NAME_NOBLANK";
        break;
      case '4': //E-mails inválidos
        $whereCond = "email NOT REGEXP '^[a-zA-Z0-9][a-zA-Z0-9.!#$%&*+-/=?^_`{|}~]*?[a-zA-Z0-9._-]?@[a-zA-Z0-9][a-zA-Z0-9._-]*?[a-zA-Z0-9]?\\.[a-zA-Z]{2,63}$'";
        $fields = "COUNT(*) AS TOTAL_EMAIL_INVALID";
        break;
      case '5': //E-mails válidos
        $whereCond = "email REGEXP '^[a-zA-Z0-9][a-zA-Z0-9.!#$%&*+-/=?^_`{|}~]*?[a-zA-Z0-9._-]?@[a-zA-Z0-9][a-zA-Z0-9._-]*?[a-zA-Z0-9]?\\.[a-zA-Z]{2,63}$'";
       $fields = "COUNT(*) AS TOTAL_EMAIL_VALID";
        break;
      case '6': //Genero em branco
        $whereCond = "gender = ''";
        $fields = "COUNT(*) AS TOTAL_GENDER_BLANK";
        break;
      case '7': //Genero peenchido
        $whereCond = "gender <> ''";
        $fields = "COUNT(*) AS TOTAL_GENDER_NOBLANK";
        break;
      case '8': //Total clientes
          $whereCond = "1";
          $fields = "COUNT(*) AS TOTAL_CUSTOMERS";
          break;
      case '9': //Chart gender
        $whereCond = "1 GROUP BY gender HAVING count(*) > 0";
        $fields = "gender GENDER, count(*) TOTAL_GENDER";
        break;
    }

    
    $result = $conn->executeQuery('SELECT '.$fields.' FROM customers WHERE '.$whereCond.$limit_offset);

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

    } catch(PDOException $e) {
        die($e->getMessage());
    }       

  }

}

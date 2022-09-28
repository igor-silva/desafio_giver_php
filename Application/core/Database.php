<?php

namespace Application\core;

use PDO;
class Database extends PDO
{
  // configuração do banco de dados
  private $DB_NAME = 'giver';
  private $DB_USER = 'root';
  private $DB_PASSWORD = '';
  private $DB_HOST = 'localhost';
  //private $DB_PORT = 5432;

  // armazena a conexão
  private $conn;

  public function __construct()
  {
    // Quando essa classe é instanciada, é atribuido a variável $conn a conexão com o db
    $this->conn = new PDO("mysql:dbname=$this->DB_NAME;host=$this->DB_HOST;user=$this->DB_USER;password=$this->DB_PASSWORD");
  }

  /**
  * Este método recebe um objeto com a query 'preparada' e atribui as chaves da query
  * seus respectivos valores.
  * @param  PDOStatement  $stmt   Contém a query ja 'preparada'.
  * @param  string        $key    É a mesma chave informada na query.
  * @param  string        $value  Valor de uma determinada chave.
  */
  private function setParameters($stmt, $key, $value)
  {
    $stmt->bindParam($key, $value);
  }

  /**
  * A responsabilidade deste método é apenas percorrer o array de com os parâmetros
  * obtendo as chaves e os valores para fornecer tais dados para setParameters().
  * @param  PDOStatement  $stmt         Contém a query ja 'preparada'.
  * @param  array         $parameters   Array associativo contendo chave e valores para fornece a query
  */
  private function mountQuery($stmt, $parameters)
  {
    foreach( $parameters as $key => $value ) {
      $this->setParameters($stmt, $key, $value);
    }
  }

  /**
  * Este método é responsável por receber a query e os parametros, preparar a query
  * para receber os valores dos parametros informados, chamar o método mountQuery,
  * executar a query e retornar para os métodos tratarem o resultado.
  * @param  string   $query       Instrução SQL que será executada no banco de dados.
  * @param  array    $parameters  Array associativo contendo as chaves informada na query e seus respectivos valores
  *
  * @return PDOStatement
  */
  public function executeQuery(string $query, array $parameters = [])
  {
    $stmt = $this->conn->prepare($query);
    $this->mountQuery($stmt, $parameters);
    $stmt->execute();
    return $stmt;
  }

  /**
  * Este método é responsável por receber a query e os parametros, preparar a query
  * para receber os valores dos parametros informados, controlar a transação,
  * executar a query e inserir dados no banco de dados.
  * @param  string   $query       Instrução SQL que será executada no banco de dados.
  * @param  array    $parameters  Array associativo contendo os dados do arquivo CSV
  *
  * @return PDOStatement
  */
  public function executeInsert(string $query,array $data = [])
  {
    $stmt = $this->conn->prepare($query);
   
    try {
      $this->conn->beginTransaction();
      foreach ($data as $chave =>  $row)
      {
      
        $id = intval($row[0]);
        if($id <> 0){
          $stmt->bindParam(1, $id);
          $stmt->bindParam(2, $row[1]);
          $stmt->bindParam(3, $row[2]);
          $stmt->bindParam(4, $row[3]);
          $stmt->bindParam(5, $row[4]); 
          $stmt->bindParam(6, $row[5]);
          $stmt->bindParam(7, $row[6]);
          $stmt->bindParam(8, $row[7]);
          $stmt->bindParam(9, $row[8]);
          $stmt->bindParam(10, $row[9]);
          $stmt->execute();
        }
       
      }

    $this->conn->commit();
    }catch (Exception $e){
      $this->conn->rollback();
        throw $e;
    }
  }

}

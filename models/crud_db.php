<?php

/**
 * A classe 'crud_db' é responsável para efetiva comandos sql no banco de dados, como, insert, update, select, delete, count;
 * 
 * @author Joab Torres <joabtorres1508@gmail.com>
 * @version 1.0
 * @copyright  (c) 2019, Joab Torres Alencar - Analista de Sistemas 
 * @access public
 * @package models
 * @example classe crud_db
 */
class crud_db extends model {

    /**
     * String $numRows - referente q quantidade de linhas obtidas no select;
     * @access private
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    private $numRows;

    /**
     * Está função tem como objetivo retorna a quantidade de registro encontrados armazenados na variavel $numRows
     * @access public
     * @return int
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function getNumRows() {
	return $this->numRows;
    }

    /**
     * Está função é responsável para cadastrar novos registros;
     * @param String $sql_command  - Comando SQL;
     * @param Array $data - Dados salvo em array para seres setados por um foreach;
     * @access public
     * @return boolean 
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function create($sql_command, $data) {
	try {
	    $sql = $this->db->prepare($sql_command);
	    foreach ($data as $indice => $valor) {
		$sql->bindValue(":" . $indice, $valor);
	    }
	    $sql->execute();
	    return true;
	} catch (PDOException $ex) {
	    echo '<script> alert("Mensagem: ' . $ex->getMessage() . '")</script>';
	}
    }

    /**
     * Está função é responsável para consultas no banco e retorna os resultados obtidos;
     * @param String $sql_command  - Comando SQL;
     * @param Array $data - Dados salvo em array para seres setados por um foreach;
     * @access public
     * @return array $sql->fetchAll() [caso encontre] | bollean FALSE [caso contrário] 
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function read($sql_command, $data = array()) {
	if (!empty($data)) {
	    $sql = $this->db->prepare($sql_command);
	    foreach ($data as $indice => $valor) {
		$sql->bindValue(":" . $indice, $valor);
	    }
	    $sql->execute();
	} else {
	    $sql = $this->db->query($sql_command);
	}

	if ($sql->rowCount() > 0) {
	    $this->numRows = $sql->rowCount();
	    return $sql->fetchAll();
	} else {
	    $this->numRows = 0;
	    return FALSE;
	}
    }

    /**
     * Está função é responsável para consultas no banco e retorna os resultados obtidos;
     * @param String $sql_command  - Comando SQL;
     * @param Array $data - Dados salvo em array para seres setados por um foreach;
     * @access public
     * @return array $sql->fetch() [caso encontre] | bollean FALSE [caso contrário] 
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function read_specific($sql_command, $data = array()) {
	if (!empty($data)) {
	    $sql = $this->db->prepare($sql_command);

	    foreach ($data as $indice => $valor) {
		$sql->bindValue(":" . $indice, $valor);
	    }
	    $sql->execute();
	} else {
	    $sql = $this->db->query($sql_command);
	}
	if ($sql->rowCount() > 0) {
	    $this->numRows = $sql->rowCount();
	    return $sql->fetch();
	} else {
	    $this->numRows = 0;
	    return FALSE;
	}
    }

    /**
     * Está função é responsável para altera um registro específico;
     * @param String $sql_command  - Comando SQL;
     * @param Array $data - Dados salvo em array para seres setados por um foreach;
     * @access public
     * @return bollean TRUE ou FALSE
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function update($sql_command, $data) {
	try {
	    $sql = $this->db->prepare($sql_command);
	    foreach ($data as $indice => $valor) {
		$sql->bindValue(":" . $indice, $valor);
	    }
	    $sql->execute();
	    return true;
	} catch (PDOException $ex) {
	    echo '<script> alert("Mensagem: ' . $ex->getMessage() . '")</script>';
	}
    }

    /**
     * Está é responsável excluir um registro específico
     * @param String $sql_command  - Comando SQL;
     * @param Array $data - Dados salvo em array para seres setados por um foreach;
     * @access public
     * @return boolean TRUE or FALSE
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function remove($sql_command, $data) {
	$sql = $this->db->prepare($sql_command);
	foreach ($data as $indice => $valor) {
	    $sql->bindValue(":" . $indice, $valor);
	}
	$sql->execute();
	return true;
    }

}

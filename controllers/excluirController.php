<?php

/**
 * A classe 'excluirrController' é responsável para fazer o gerenciamento na exclusão  de usuários, unidade, servidores, relatórios semestral, ata de defesa e prorrogação de afastamento
 * @author Joab Torres <joabtorres1508@gmail.com>
 * @version 1.0
 * @copyright  (c) 2017, Joab Torres Alencar - Analista de Sistemas 
 * @access public
 * @package controllers
 * @example classe excluirController
 */
class excluirController extends controller {

    /**
     * Está função pertence a uma action do controle MVC, ela é chama a função reserva($cod);
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function index($cod) {
        $this->reserva($cod);
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável para deleta um registro no banco de dados da tabela
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function reserva($cod) {
        if ($this->checkUser() && !empty($cod)) {
            $crudModel = new crud_db();
            if ($this->checkNivel()) {
                $crudModel->remove("DELETE FROM reserva WHERE md5(id)=:cod", array('cod' => $cod));
            } else {
                $crudModel->remove("DELETE FROM reserva WHERE md5(id)=:cod AND id_usuario=:id AND status=0", array('cod' => $cod, 'id' => $this->getId()));
            }
            $url = "Location: " . BASE_URL . "relatorio/reserva";
            header($url);
        } else {
            $url = "Location: " . BASE_URL . "home";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável para deleta um registro no banco de dados da tabela
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function horario($cod) {
        if ($this->checkUser() && !empty($cod) && $this->checkNivel()) {
            $crudModel = new crud_db();
            if ($crudModel->remove("DELETE FROM horario WHERE md5(id)=:cod", array('cod' => $cod))) {
                $url = "Location: " . BASE_URL . "relatorio/horario/1";
                header($url);
            } else {
                $url = "Location: " . BASE_URL . "relatorio/horario";
                header($url);
            }
        } else {
            $url = "Location: " . BASE_URL . "home";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável para deleta um registro no banco de dados da tabela
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function laboratorio($cod) {
        if ($this->checkUser() && !empty($cod) && $this->checkNivel()) {
            $crudModel = new crud_db();
            $resultado = $crudModel->read_specific("SELECT * FROM laboratorio WHERE md5(id)= :id", array('id' => $cod));
            if ($resultado) {
                $crudModel->remove("DELETE FROM horario WHERE id_laboratorio=:id ", array('id' => $resultado['id']));
                $crudModel->remove("DELETE FROM reserva WHERE id_laboratorio=:id ", array('id' => $resultado['id']));
                $crudModel->remove("DELETE FROM laboratorio WHERE id=:id ", array('id' => $resultado['id']));
            }
            $url = "Location: " . BASE_URL . "relatorio/laboratorio/1";
            header($url);
        } else {
            $url = "Location: " . BASE_URL . "home";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável para deleta um registro no banco de dados da tabela
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function administrador($cod) {
        if ($this->checkUser() && !empty($cod) && $this->checkNivel()) {
            $admModel = new administrador();
            if ($admModel->remove(array('cod' => $cod))) {
                if (md5($this->getId()) == $cod) {
                    $url = "Location: " . BASE_URL . "login";
                    header($url);
                } else {
                    $url = "Location: " . BASE_URL . "relatorio/administrador/1";
                    header($url);
                }
            } else {
                $url = "Location: " . BASE_URL . "404";
                header($url);
            }
        } else {
            $url = "Location: " . BASE_URL . "home";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável para deleta um registro no banco de dados da tabela
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function usuario($cod) {
        if ($this->checkUser() && !empty($cod) && $this->checkNivel()) {
            $userModel = new usuario();
            $crudModel = new crud_db();
            $resultado = $crudModel->read_specific("SELECT * FROM usuario WHERE md5(id)=:id", array('id' => $cod));
            if ($resultado) {
                $crudModel->remove("DELETE FROM reserva WHERE md5(id_usuario)=:id", array('id' => $cod));
                if ($userModel->remove(array('id' => $cod))) {
                    if (md5($this->getId()) == $cod) {
                        $url = "Location: " . BASE_URL . "login";
                        header($url);
                    } else {
                        $url = "Location: " . BASE_URL . "relatorio/administrador/1";
                        header($url);
                    }
                } else {
                    $url = "Location: " . BASE_URL . "404";
                    header($url);
                }
            }
        } else {
            $url = "Location: " . BASE_URL . "home";
            header($url);
        }
    }

}

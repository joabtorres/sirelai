<?php

/**
 *  classe "relatorioController"' é responsável para fazer o gerenciamento na dos relatorios unidade, servidores e geração de arquivos em pdf
 * 
 * @author Joab Torres <joabtorres1508@gmail.com>
 * @version 1.0
 * @copyright  (c) 2017, Joab Torres Alencar - Analista de Sistemas 
 * @access public
 * @package controllers
 * @example classe relatorioController
 */
class relatorioController extends controller {

    /**
     * Está função pertence a uma action do controle MVC, ela chama a  função cooperados();
     * @access public
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function index() {
        
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável para mostra todas as unidades.
     * @param int $page - paginação
     * @access public
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function reserva($page = 1) {
        if ($this->checkUser()) {
            $viewName = 'reserva/relatorio';
            $dados = array();
            $crudModel = new crud_db();
            if (!$this->checkNivel()) {
                $dados['usuarios'] = $crudModel->read("SELECT * FROM usuario WHERE status=1 AND id=:id ORDER BY nome, categoria ASC", array('id' => $this->getId()));
                $sql = 'SELECT u.categoria , u.nome, u.sobrenome, u.curso, r.*, l.nome as lab_nome FROM usuario as u INNER JOIN reserva as r ON u.id=r.id_usuario INNER JOIN laboratorio as l ON r.id_laboratorio=l.id WHERE r.id >0 AND u.id=' . $this->getId();
            } else {
                $dados['usuarios'] = $crudModel->read("SELECT * FROM usuario WHERE status=1 ORDER BY nome, categoria ASC");
                $sql = 'SELECT u.categoria , u.nome, u.sobrenome, u.curso, r.*, l.nome as lab_nome FROM usuario as u INNER JOIN reserva as r ON u.id=r.id_usuario INNER JOIN laboratorio as l ON r.id_laboratorio=l.id WHERE r.id >0 ';
            }
            $array = array();
            $parametro = '';
            if (isset($_GET['nBuscarBT'])) {
                $parametro = '?nInicio=' . $_GET['nInicio'] . '&nTermino=' . $_GET['nTermino'] . '&nStatus=' . $_GET['nStatus'] . '&nCategoria=' . $_GET['nCategoria'] . '&nUsuario=' . $_GET['nUsuario'] . '&nBuscarBT=BuscarBT';

                //data inicial
                if (!empty($_GET['nInicio']) && !empty($_GET['nTermino'])) {
                    $sql .= " AND  (r.data_inicial >=:data_inicial AND data_final <= :data_final) ";
                    $array['data_inicial'] = $this->formatDateBD(addslashes($_GET['nInicio']));
                    $array['data_final'] = $this->formatDateBD(addslashes($_GET['nTermino']));
                }

                if (!empty($_GET['nStatus'])) {
                    $sql = $sql . " AND r.status=:status";
                    switch ($_GET['nStatus']) {
                        case "Liberado":
                            $array['status'] = 1;
                            break;
                        case "Inativo":
                            $array['status'] = 0;
                            break;
                    }
                }
                //categoria
                if (!empty($_GET['nCategoria'])) {
                    $sql .= " AND  u.categoria=:categoria ";
                    $array['categoria'] = addslashes($_GET['nCategoria']);
                }
                if (!empty($_GET['nUsuario'])) {
                    $sql .= " AND  u.id=:id ";
                    $array['id'] = addslashes($_GET['nUsuario']);
                }
            }
            $limite = 30;
            $total_registro = $crudModel->read_specific("SELECT COUNT(id) AS qtd FROM reserva");
            $paginas = $total_registro['qtd'] / $limite;
            $indice = 0;
            $pagina_atual = (isset($page) && !empty($page)) ? addslashes($page) : 1;
            $indice = ($pagina_atual - 1) * $limite;
            $dados["paginas"] = $paginas;
            $dados["pagina_atual"] = $pagina_atual;
            $dados['metodo_buscar'] = $parametro;
            $sql .= " ORDER BY r.id DESC LIMIT $indice,$limite";
            $dados['reservas'] = $crudModel->read($sql, $array);
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    public function dias_uteis($page = 1) {
        if ($this->checkUser() && $this->checkNivel()) {
            $viewName = 'dias_uteis/relatorio';
            $dados = array();
            $crudModel = new crud_db();
            if (isset($_POST['nSalvar'])) {
                if (!empty($_POST['nMin']) && !empty($_POST['nMax'])) {
                    $array = array();
                    $array['id'] = addslashes($_POST['nCod']);
                    $array['minimo'] = addslashes($_POST['nMin']);
                    $array['maximo'] = addslashes($_POST['nMax']);
                    if ($crudModel->update("UPDATE dias_uteis SET minimo=:minimo, maximo=:maximo WHERE id=:id", $array)) {
                        $dados['erro'] = array('class' => 'alert-success', 'msg' => '<i class="fas fa-check-double"></i> Alteração realizada com sucesso!');
                    }
                } else {
                    $dados['erro'] = array('class' => 'alert-danger', 'msg' => '<i class="fa fa-times"></i> Preenchar os campos obrigatórios.');
                }
            }
            $sql = 'SELECT * FROM dias_uteis';
            $dados['dias'] = $crudModel->read($sql);
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    public function horario($page = 1) {
        if ($this->checkUser() && $this->checkNivel()) {
            $viewName = 'horario/relatorio';
            $dados = array();
            $crudModel = new crud_db();
            $dados['labs'] = $crudModel->read("SELECT * FROM laboratorio ORDER BY nome ASC");
            $sql = 'SELECT h.*, l.nome FROM horario AS h INNER JOIN laboratorio AS l ON h.id_laboratorio=l.id WHERE h.id >0 ';
            $array = array();
            $parametro = '';
            if (isset($_GET['nBuscarBT'])) {
                $parametro = '?&nStatus=' . $_GET['nStatus'] . '&nLaboratorio=' . $_GET['nLaboratorio'] . '&nBuscarBT=BuscarBT';


                if (!empty($_GET['nStatus'])) {
                    $sql = $sql . " AND h.status=:status";
                    switch ($_GET['nStatus']) {
                        case "Disponível":
                            $array['status'] = 1;
                            break;
                        case "Indisponível":
                            $array['status'] = 0;
                            break;
                    }
                }
                if (!empty($_GET['nLaboratorio'])) {
                    $sql .= " AND  l.id=:id ";
                    $array['id'] = addslashes($_GET['nLaboratorio']);
                }
            }
            $limite = 30;
            $total_registro = $crudModel->read_specific("SELECT COUNT(id) AS qtd FROM horario");
            $paginas = $total_registro['qtd'] / $limite;
            $indice = 0;
            $pagina_atual = (isset($page) && !empty($page)) ? addslashes($page) : 1;
            $indice = ($pagina_atual - 1) * $limite;
            $dados["paginas"] = $paginas;
            $dados["pagina_atual"] = $pagina_atual;
            $dados['metodo_buscar'] = $parametro;
            $sql .= " ORDER BY h.id DESC LIMIT $indice,$limite";
            $dados['horarios'] = $crudModel->read($sql, $array);
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    public function laboratorio($page = 1) {
        if ($this->checkUser() && $this->checkNivel()) {
            $viewName = 'laboratorio/relatorio';
            $dados = array();
            $crudModel = new crud_db();
            $dados['labs'] = $crudModel->read("SELECT * FROM laboratorio ORDER BY nome ASC");
            $sql = 'SELECT * FROM laboratorio as l WHERE l.id >0 ';
            $array = array();
            $parametro = '';
            if (isset($_GET['nBuscarBT'])) {
                $parametro = '?&nStatus=' . $_GET['nStatus'] . '&nLaboratorio=' . $_GET['nLaboratorio'] . '&nBuscarBT=BuscarBT';


                if (!empty($_GET['nStatus'])) {
                    $sql = $sql . " AND l.status=:status";
                    switch ($_GET['nStatus']) {
                        case "Disponível":
                            $array['status'] = 1;
                            break;
                        case "Indisponível":
                            $array['status'] = 0;
                            break;
                    }
                }
                if (!empty($_GET['nLaboratorio'])) {
                    $sql .= " AND  l.id=:id ";
                    $array['id'] = addslashes($_GET['nLaboratorio']);
                }
            }
            $limite = 30;
            $total_registro = $crudModel->read_specific("SELECT COUNT(id) AS qtd FROM laboratorio");
            $paginas = $total_registro['qtd'] / $limite;
            $indice = 0;
            $pagina_atual = (isset($page) && !empty($page)) ? addslashes($page) : 1;
            $indice = ($pagina_atual - 1) * $limite;
            $dados["paginas"] = $paginas;
            $dados["pagina_atual"] = $pagina_atual;
            $dados['metodo_buscar'] = $parametro;
            $sql .= " ORDER BY l.nome ASC LIMIT $indice,$limite";
            $dados['laboratorios'] = $crudModel->read($sql, $array);
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, responsável para fazer uma buscar rápida, por nz ou nome.
     * @param int $page - paginação
     * @access public
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function usuario($page = 1) {
        if ($this->checkUser() && $this->checkNivel()) {
            $viewName = 'usuario/relatorio';
            $dados = array();
            $crudModel = new crud_db();
            $dados['users'] = $crudModel->read("SELECT * FROM usuario ORDER BY nome, categoria ASC");
            $sql = 'SELECT * FROM usuario WHERE id >0 ';
            $array = array();
            $parametro = '';
            if (isset($_GET['nBuscarBT'])) {
                $parametro = '?&nStatus=' . $_GET['nStatus'] . '&nCategoria=' . $_GET['nCategoria'] . '&nUsuario=' . $_GET['nUsuario'] . '&nBuscarBT=BuscarBT';

                if (!empty($_GET['nStatus'])) {
                    $sql = $sql . " AND status=:status";
                    switch ($_GET['nStatus']) {
                        case "Disponível":
                            $array['status'] = 1;
                            break;
                        case "Indisponível":
                            $array['status'] = 0;
                            break;
                    }
                }
                //categoria
                if (!empty($_GET['nCategoria'])) {
                    $sql .= " AND  categoria=:categoria ";
                    $array['categoria'] = addslashes($_GET['nCategoria']);
                }

                if (!empty($_GET['nUsuario'])) {
                    $sql .= " AND  id=:id ";
                    $array['id'] = addslashes($_GET['nUsuario']);
                }
            }
            $limite = 30;
            $total_registro = $crudModel->read_specific("SELECT COUNT(id) AS qtd FROM usuario");
            $paginas = $total_registro['qtd'] / $limite;
            $indice = 0;
            $pagina_atual = (isset($page) && !empty($page)) ? addslashes($page) : 1;
            $indice = ($pagina_atual - 1) * $limite;
            $dados["paginas"] = $paginas;
            $dados["pagina_atual"] = $pagina_atual;
            $dados['metodo_buscar'] = $parametro;
            $sql .= " ORDER BY id DESC LIMIT $indice,$limite";
            $dados['usuarios'] = $crudModel->read($sql, $array);
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, responsável para fazer uma buscar rápida, por nz ou nome.
     * @param int $page - paginação
     * @access public
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function administrador($page = 1) {
        if ($this->checkUser() && $this->checkNivel()) {
            $viewName = 'administrador/relatorio';
            $dados = array();
            $admModel = new administrador();
            $dados['users'] = $admModel->read("SELECT * FROM administrador ORDER BY nome ASC");
            $sql = 'SELECT * FROM administrador WHERE id >0 ';
            $array = array();
            $parametro = '';
            if (isset($_GET['nBuscarBT'])) {
                $parametro = '?&nStatus=' . $_GET['nStatus'] . '&nUsuario=' . $_GET['nUsuario'] . '&nBuscarBT=BuscarBT';

                if (!empty($_GET['nStatus'])) {
                    $sql = $sql . " AND status=:status";
                    switch ($_GET['nStatus']) {
                        case "Disponível":
                            $array['status'] = 1;
                            break;
                        case "Indisponível":
                            $array['status'] = 0;
                            break;
                    }
                }
                //categoria
                if (!empty($_GET['nCategoria'])) {
                    $sql .= " AND  cargo=:cargo ";
                    $array['cargo'] = addslashes($_GET['nCategoria']);
                }
                if (!empty($_GET['nUsuario'])) {
                    $sql .= " AND  id=:id ";
                    $array['id'] = addslashes($_GET['nUsuario']);
                }
            }
            $limite = 30;
            $total_registro = $admModel->read_specific("SELECT COUNT(id) AS qtd FROM administrador");
            $paginas = $total_registro['qtd'] / $limite;
            $indice = 0;
            $pagina_atual = (isset($page) && !empty($page)) ? addslashes($page) : 1;
            $indice = ($pagina_atual - 1) * $limite;
            $dados["paginas"] = $paginas;
            $dados["pagina_atual"] = $pagina_atual;
            $dados['metodo_buscar'] = $parametro;
            $sql .= " ORDER BY id DESC LIMIT $indice,$limite";
            $dados['usuarios'] = $admModel->read($sql, $array);
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

}

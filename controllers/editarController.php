<?php

/**
 * A classe 'editarController' é responsável para fazer o gerenciamento na edição de usuários, unidade, servidores, relatórios semestral, ata de defesa e prorrogação de afastamento
 * 
 * @author Joab Torres <joabtorres1508@gmail.com>
 * @version 1.0
 * @copyright  (c) 2019, Joab Torres Alencar - Analista de Sistemas 
 * @access public
 * @package controllers
 * @example classe editarController
 */
class editarController extends controller {

    /**
     * Está função pertence a uma action do controle MVC, ela é chama a função unidade($cod);
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function index($cod) {
        $this->reserva($cod);
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável por controlar a edição da reserva
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function reserva($cod) {
        if ($this->checkUser() && !empty($cod)) {
            $viewName = 'reserva/edicao';
            $dados = array();
            $crudModel = new crud_db();

            $minimo = 0;
            $maximo = 0;
            //seleciona os usuários
            if ($this->checkNivel()) {
                $dados['usuarios'] = $crudModel->read("SELECT * FROM usuario WHERE status=1 ORDER BY nome, categoria ASC");
            } else {
                $dados['usuarios'] = $crudModel->read("SELECT * FROM usuario WHERE status=1 AND id=:id ORDER BY nome, categoria ASC", array('id' => $this->getId()));
                $dias_uteis = $crudModel->read_specific("SELECT * FROM dias_uteis WHERE categoria=:categoria", array('categoria' => $this->getCategoria()));
                if ($crudModel->getNumRows()) {
                    $minimo = $this->getdiasUteis($dias_uteis['minimo']);
                    $maximo = $this->getdiasUteis($dias_uteis['maximo'] + $dias_uteis['minimo'] - 1);
                }
            }
            echo ' <script>minDate = ' . $minimo . '; maxDate=' . $maximo . ';</script>';
            //lista os laboratórios disponíveis
            $dados['laboratorios'] = $crudModel->read("SELECT * FROM laboratorio WHERE status=1 ORDER BY nome ASC");

            if (isset($_POST['nSalvar'])) {
                $reserva = array();
                //id
                $reserva['id'] = $_POST['nId'];
                //nome
                if (!empty($_POST['nUsuario'])) {
                    $reserva['id_usuario'] = addslashes($_POST['nUsuario']);
                } else {
                    $dados['reserva_erro']['id_usuario']['msg'] = 'Informe o usuário';
                    $dados['reserva_erro']['id_usuario']['class'] = 'has-error';
                }
                //laboratorio
                if (!empty($_POST['nLaboratorio'])) {
                    $reserva['id_laboratorio'] = addslashes($_POST['nLaboratorio']);
                } else {
                    $dados['reserva_erro']['id_laboratorio']['msg'] = 'Informe o Laboratório';
                    $dados['reserva_erro']['id_laboratorio']['class'] = 'has-error';
                }
                //data_inicial
                if (!empty($_POST['data_inicial'])) {
                    $reserva['data_inicial'] = $this->formatDateBD($_POST['data_inicial']);
                } else {
                    $dados['reserva_erro']['data_inicial']['msg'] = 'Informe a data';
                    $dados['reserva_erro']['data_inicial']['class'] = 'has-error';
                }
                //data_final
                if (!empty($_POST['data_final'])) {
                    $reserva['data_final'] = $this->formatDateBD($_POST['data_final']);
                } else {
                    $dados['reserva_erro']['data_final']['msg'] = 'Informe a data';
                    $dados['reserva_erro']['data_final']['class'] = 'has-error';
                }


                //validar data;
                $data_inicial = 0;
                $data_final = 0;
                if (isset($reserva['data_inicial']) && isset($reserva['data_final'])) {
                    $data_inicial = strtotime($reserva['data_inicial']);
                    $data_final = strtotime($reserva['data_final']);

                    if ($this->checkNivel() == false) {
                        $data_atual = strtotime(date('Y-m-d'));
                        if (($data_inicial < strtotime('+' . $minimo . ' day', $data_atual)) || ($data_inicial > strtotime('+' . $maximo . ' day', strtotime(date('Y-m-d'))))) {
                            $d1 = strtotime('+' . $minimo . ' day', $data_atual);
                            $d2 = strtotime('+' . $maximo . ' day', $data_atual);
                            $dados['reserva_erro']['data_inicial']['msg'] = 'Infome a data entre ' . date("d/m/Y", $d1) . ' a ' . date("d/m/Y", $d2);
                            $dados['reserva_erro']['data_inicial']['class'] = 'has-error';
                        }
                        if (($data_final < strtotime('+' . $minimo . ' day', strtotime(date('Y-m-d')))) || ($data_final > strtotime('+' . $maximo . ' day', strtotime(date('Y-m-d'))))) {
                            $d1 = strtotime('+' . $minimo . ' day', $data_atual);
                            $d2 = strtotime('+' . $maximo . ' day', $data_atual);
                            $dados['reserva_erro']['data_final']['msg'] = 'Infome a data entre ' . date("d/m/Y", $d1) . ' a ' . date("d/m/Y", $d2);
                            $dados['reserva_erro']['data_final']['class'] = 'has-error';
                        }
                        //aluno não pode marcar para final de semana
                        if ($this->getCategoria() == "Aluno(a)") {
                            if (($this->getDiaSemana($data_inicial) == 'sabado') || ($this->getDiaSemana($data_inicial) == 'domingo')) {
                                $dados['reserva_erro']['data_inicial']['msg'] = 'Não é reservado o laboratório aos finais da semana.';
                                $dados['reserva_erro']['data_inicial']['class'] = 'has-error';
                            }
                            if (($this->getDiaSemana($data_final) == 'sabado') || ($this->getDiaSemana($data_final) == 'domingo')) {
                                $dados['reserva_erro']['data_final']['msg'] = 'Não é reservado o laboratório aos finais da semana.';
                                $dados['reserva_erro']['data_final']['class'] = 'has-error';
                            }
                        }
                    }
                    if ($data_inicial > $data_final) {
                        $dados['reserva_erro']['data_final']['msg'] = 'A data final deve ser maior ou igual ao campo da data inicial';
                        $dados['reserva_erro']['data_final']['class'] = 'has-error';
                        $dados['reserva_erro']['data_inicial']['msg'] = 'A data final deve ser maior ou igual ao campo da data inicial';
                        $dados['reserva_erro']['data_inicial']['class'] = 'has-error';
                    }
                }

                //nHorario
                if (!empty($_POST['nHorario'])) {
                    $array = explode(" - ", $_POST['nHorario']);
                    $reserva['horario_inicial'] = $array[0];
                    $reserva['horario_final'] = $array[1];
                } else {
                    $dados['reserva_erro']['horario']['msg'] = 'Selecione o horário disponível';
                    $dados['reserva_erro']['horario']['class'] = 'has-error';
                }

                if ($data_final > $data_inicial) {
                    if (isset($_POST['nSegunda']) || isset($_POST['nTerca']) || isset($_POST['nQuarta']) || isset($_POST['nQuinta']) || isset($_POST['nSexta']) || isset($_POST['nSabado'])) {
                        $reserva['segunda'] = isset($_POST['nSegunda']) && !empty($_POST['nSegunda']) ? 1 : 0;
                        $reserva['terca'] = isset($_POST['nTerca']) && !empty($_POST['nTerca']) ? 1 : 0;
                        $reserva['quarta'] = isset($_POST['nQuarta']) && !empty($_POST['nQuarta']) ? 1 : 0;
                        $reserva['quinta'] = isset($_POST['nQuinta']) && !empty($_POST['nQuinta']) ? 1 : 0;
                        $reserva['sexta'] = isset($_POST['nSexta']) && !empty($_POST['nSexta']) ? 1 : 0;
                        $reserva['sabado'] = isset($_POST['nSabado']) && !empty($_POST['nSabado']) ? 1 : 0;
                    } else {
                        $dados['reserva_erro']['dias']['msg'] = 'Selecione os dias da semana';
                        $dados['reserva_erro']['dias']['class'] = 'has-error';
                    }
                } else if ($data_inicial > 0) {
                    $diasemana = array('segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado');
                    $dia = $this->getDiaSemana($data_inicial);
                    for ($i = 0; $i < count($diasemana); $i++) {
                        if ($dia == $diasemana[$i]) {
                            $reserva[$diasemana[$i]] = 1;
                        } else {
                            $reserva[$diasemana[$i]] = 0;
                        }
                    }
                }
                //turma
                $reserva['turma'] = isset($_POST['nTurma']) ? addslashes($_POST['nTurma']) : '';
                //disciplina
                $reserva['disciplina'] = isset($_POST['nDisciplina']) ? addslashes($_POST['nDisciplina']) : '';
                if ($this->checkNivel()) {
                    //status
                    $reserva['status'] = !empty($_POST['nStatus']) ? 1 : 0;
                    //descriçao
                    $reserva['descricao'] = addslashes($_POST['nDescricao']);
                }
                if (isset($dados['reserva_erro']) && !empty($dados['reserva_erro'])) {
                    $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Preencha todos os campos corretamente.';
                    $dados['erro']['class'] = 'alert-danger';
                    $dados['reserva'] = $reserva;
                } else {
                    $sql = "SELECT COUNT(id) as qtd, id_usuario FROM reserva WHERE id!=" . $reserva['id'] . " AND id_laboratorio = '" . $reserva['id_laboratorio'] . "' AND (NOT ( data_inicial > '" . $reserva["data_final"] . "' OR data_final < '" . $reserva["data_inicial"] . "' )) AND (horario_inicial BETWEEN '" . $reserva['horario_inicial'] . "' AND '" . $reserva['horario_final'] . "' OR horario_final BETWEEN '" . $reserva['horario_inicial'] . "' AND '" . $reserva['horario_final'] . "')";
                    $array = array('segunda' => $reserva['segunda'], 'terca' => $reserva['terca'], 'quarta' => $reserva['quarta'], 'quinta' => $reserva['quinta'], 'sexta' => $reserva['sexta'], 'sabado' => $reserva['sabado']);
                    $d = false;
                    foreach ($array as $indice => $key) {
                        if ($key == 1) {
                            if ($d == true) {
                                $sql .= " OR $indice=" . $key;
                            } else {
                                $d = true;
                                $sql .= " AND ($indice=" . $key;
                            }
                        }
                    }
                    if ($d == true) {
                        $sql .= " )";
                    }
                    $resultado = $crudModel->read_specific($sql);
                    if ($resultado['qtd'] > 0) {
                        //verifica que categoria é 
                        $user2 = $crudModel->read_specific('SELECT * FROM usuario WHERE id=' . $resultado['id_usuario']);
                        if ($user2['categoria'] == "Aluno(a)") {
                            $sql = "SELECT COUNT(*) as qtd, r.id_usuario FROM reserva AS r INNER JOIN usuario AS u ON r.id_usuario=u.id WHERE r.id!=" . $reserva['id'] . " AND r.id_usuario=" . $reserva['id_usuario'] . " AND (NOT ( data_inicial > '" . $reserva["data_final"] . "' OR data_final < '" . $reserva["data_inicial"] . "' )) ";
                            $resultado = $crudModel->read_specific($sql);
                        }
                        if ($resultado['qtd'] > 0) {
                            $user = $crudModel->read_specific('SELECT * FROM usuario WHERE id=' . $reserva['id_usuario']);
                            //se for aluno, verifica quantidade de registro
                            if (($user2['categoria'] == $user['categoria']) && ($user2['categoria'] == "Aluno(a)")) {
                                $sql = "SELECT COUNT(*) as qtd FROM reserva AS r INNER JOIN usuario AS u ON r.id_usuario=u.id WHERE r.id!=" . $reserva['id'] . " AND r.id_usuario=" . $reserva['id'] . " AND (NOT ( data_inicial > '" . $reserva["data_final"] . "' OR data_final < '" . $reserva["data_inicial"] . "' )) AND (horario_inicial BETWEEN '" . $reserva['horario_inicial'] . "' AND '" . $reserva['horario_final'] . "' OR horario_final BETWEEN '" . $reserva['horario_inicial'] . "' AND '" . $reserva['horario_final'] . "')";
                                $result = $crudModel->read_specific($sql);
                                if (intval($result['qtd']) >= 2) {
                                    $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Só pode reserva o laboratório duas vezes no mesmo periodo.';
                                    $dados['erro']['class'] = 'alert-danger';
                                    $_POST = array();
                                }
                            } else {
                                $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Já possui uma reserva agendada para este periodo, dia e horário.';
                                $dados['erro']['class'] = 'alert-danger';
                                $_POST = array();
                            }
                        }
                    }

                    if (empty($dados['erro'])) {
                        //se ele for adm ele altera tudo
                        if ($this->checkNivel()) {
                            $sql = " UPDATE reserva SET id_usuario=:id_usuario, id_laboratorio=:id_laboratorio, data_inicial=:data_inicial, data_final=:data_final, horario_inicial=:horario_inicial, horario_final=:horario_final, turma=:turma, disciplina=:disciplina, segunda=:segunda, terca=:terca, quarta=:quarta, quinta=:quinta, sexta=:sexta, sabado=:sabado, status=:status, descricao=:descricao WHERE id=:id";
                        } else {
                            //se nao só altera alguns campos
                            $sql = " UPDATE reserva SET id_usuario=:id_usuario, id_laboratorio=:id_laboratorio, data_inicial=:data_inicial, data_final=:data_final, horario_inicial=:horario_inicial, horario_final=:horario_final, turma=:turma, disciplina=:disciplina, segunda=:segunda, terca=:terca, quarta=:quarta, quinta=:quinta, sexta=:sexta, sabado=:sabado WHERE id=:id";
                        }
                        $cadastro = $crudModel->update($sql, $reserva);
                        if ($cadastro) {
                            $dados['erro']['msg'] = '<i class="fa fa-check" aria-hidden="true"></i> Alteração realizada com sucesso!';
                            $dados['erro']['class'] = 'alert-success';
                            $_POST = array();
                        }
                        $dados['reserva'] = $reserva;
                    }
                }
            } else {
                if ($this->checkNivel()) {
                    $dados['reserva'] = $crudModel->read_specific("SELECT * FROM reserva WHERE md5(id)=:id", array('id' => $cod));
                } else {
                    $dados['reserva'] = $crudModel->read_specific("SELECT * FROM reserva WHERE md5(id)=:id AND id_usuario=:id_usuario", array('id' => $cod, 'id_usuario' => $this->getId()));
                }
                if (empty($dados['reserva']) || !is_array($dados['reserva'])) {
                    $url = "Location: " . BASE_URL . "relatorio/reserva";
                    header($url);
                }
            }

            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável por controlar a edição do horario
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function horario($cod) {
        if ($this->checkUser() && $this->checkNivel() && !empty($cod)) {
            $viewName = 'horario/edicao';
            $dados = array();
            $crudModel = new crud_db();
            $dados['labs'] = $crudModel->read("SELECT * FROM laboratorio ORDER BY nome ASC");
            $dados['horario'] = $crudModel->read_specific("SELECT * FROM horario WHERE md5(id)=:id", array('id' => $cod));
            if (!is_array($dados['horario'])) {
                $url = "Location: " . BASE_URL . "relatorio/horario";
                header($url);
            }
            if (isset($_POST['nSalvar'])) {
                if (!empty($_POST['hora_inicial']) && !empty($_POST['hora_final'])) {
                    $horario = array();
                    $horario['id'] = $_POST['nCod'];
                    $horario['id_laboratorio'] = addslashes($_POST['nIdLab']);
                    $horario['hora_inicial'] = addslashes($_POST['hora_inicial']);
                    $horario['hora_final'] = addslashes($_POST['hora_final']);
                    $horario['status'] = $_POST['nStatus'] == "Disponível" ? 1 : 0;
                    $resultado = $crudModel->update("UPDATE horario SET id_laboratorio=:id_laboratorio, hora_inicial=:hora_inicial, hora_final=:hora_final, status=:status WHERE id=:id", $horario);
                    if ($resultado) {
                        $dados['erro'] = array('class' => 'alert-success', 'msg' => '<i class="fas fa-check-double"></i> Alteração realizada com sucesso!');
                    }
                    $dados['horario'] = $horario;
                } else {
                    $dados['erro'] = array('class' => 'alert-danger', 'msg' => '<i class="fa fa-times"></i> Preenchar os campos obrigatórios.');
                }
            }
            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável por controlar a edição do laboratorio
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function laboratorio($cod) {
        if ($this->checkUser() && $this->checkNivel() && !empty($cod)) {
            $viewName = 'laboratorio/edicao';
            $dados = array();
            $crudModel = new crud_db();

            if (isset($_POST['nSalvar'])) {
                if (!empty($_POST['nNome']) && !empty($_POST['qtd_computador'])) {
                    $laboratorio = array();
                    $laboratorio['id'] = addslashes($_POST['nCod']);
                    $laboratorio['nome'] = addslashes($_POST['nNome']);
                    $laboratorio['qtd_computador'] = addslashes($_POST['qtd_computador']);
                    $laboratorio['status'] = addslashes($_POST['nStatus']);
                    $resultado = $crudModel->update("UPDATE laboratorio SET nome=:nome, qtd_computador=:qtd_computador, status=:status WHERE id=:id", $laboratorio);
                    if ($resultado) {
                        $dados['erro'] = array('class' => 'alert-success', 'msg' => '<i class="fas fa-check-double"></i> Alteração realizada com sucesso!');
                    }
                    $dados['laboratorio'] = $laboratorio;
                } else {
                    $dados['erro'] = array('class' => 'alert-danger', 'msg' => '<i class="fa fa-times"></i> Preenchar os campos obrigatórios.');
                }
            } else {
                $dados['laboratorio'] = $crudModel->read_specific("SELECT * FROM laboratorio WHERE md5(id)=:id", array('id' => $cod));
                if (!is_array($dados['laboratorio'])) {
                    $url = "Location: " . BASE_URL . "relatorio/laboratorio";
                    header($url);
                }
            }

            $this->loadTemplate($viewName, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável por controlar a edição do usuário ADMINISTRADOR
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function administrador($cod) {
        if ($this->checkUser() && $this->checkNivel() && !empty($cod)) {
            $view = "administrador/edicao";
            $dados = array();
            $admModel = new administrador();
            //Array que vai armazena os dados do usuário;
            $usuario = array();
            if (isset($_POST['nSalvar'])) {
                //id
                $usuario['id'] = addslashes($_POST['nCod']);
                //nome
                if (!empty($_POST['nNome'])) {
                    $usuario['nome'] = addslashes($_POST['nNome']);
                } else {
                    $dados['usuario_erro']['nome']['msg'] = 'Informe o nome';
                    $dados['usuario_erro']['nome']['class'] = 'has-error';
                }
                //sobrenome
                if (!empty($_POST['nSobrenome'])) {
                    $usuario['sobrenome'] = addslashes($_POST['nSobrenome']);
                } else {
                    $dados['usuario_erro']['sobrenome']['msg'] = 'Informe o sobrenome';
                    $dados['usuario_erro']['sobrenome']['class'] = 'has-error';
                }
                //nMatricula
                if (!empty($_POST['nMatricula'])) {
                    $usuario['matricula'] = addslashes($_POST['nMatricula']);
                    if ($admModel->read_specific('SELECT * FROM administrador WHERE matricula=:matricula AND id != :id', array('matricula' => $usuario['matricula'], 'id' => $usuario['id']))) {
                        $dados['usuario_erro']['matricula']['msg'] = 'matricula já cadastrada';
                        $dados['usuario_erro']['matricula']['class'] = 'has-error';
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Não é possível cadastrar uma matricula/Siape já cadastrada, por favor informe outra matricula/Siape';
                        $dados['erro']['class'] = 'alert-danger';
                        $usuario['matricula'] = null;
                    }
                } else {
                    $dados['usuario_erro']['matricula']['msg'] = 'Informe a matricula/siape';
                    $dados['usuario_erro']['matricula']['class'] = 'has-error';
                }

                //email
                if (!empty($_POST['nEmail'])) {
                    $usuario['email'] = addslashes($_POST['nEmail']);
                    if ($admModel->read_specific('SELECT * FROM administrador WHERE email=:email AND id != :id', array('email' => $usuario['email'], 'id' => $usuario['id']))) {
                        $dados['usuario_erro']['email']['msg'] = 'E-mail já cadastrado';
                        $dados['usuario_erro']['email']['class'] = 'has-error';
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Não é possível cadastrar um e-mail já cadastrado, por favor informe outro endereço de e-mail';
                        $dados['erro']['class'] = 'alert-danger';
                        $usuario['email'] = null;
                    }
                } else {
                    $dados['usuario_erro']['email']['msg'] = 'Informe o e-mail';
                    $dados['usuario_erro']['email']['class'] = 'has-error';
                }
                //seinha
                if (!empty($_POST['nSenha']) && !empty($_POST['nRepetirSenha'])) {
                    //senha
                    if ($_POST['nSenha'] == $_POST['nRepetirSenha']) {
                        $usuario['senha'] = $_POST['nSenha'];
                    } else {
                        $dados['usuario_erro']['senha']['msg'] = "Os campos 'Senha' e 'Repetir Senha' não estão iguais! ";
                        $dados['usuario_erro']['senha']['class'] = 'has-error';
                    }
                } else {
                    if (isset($usuario['senha'])) {
                        unset($usuario['senha']);
                    }
                }
                //coordenacao
                $usuario['cargo'] = $_POST['nCoordenacao'];
                //sexo
                $usuario['sexo'] = $_POST['nSexo'];

                if (isset($_POST['nStatus'])) {
                    $usuario['status'] = !empty($_POST['nStatus']) ? 1 : 0;
                } else {
                    $usuario['status'] = 1;
                }

                //nivel de acesso
                $usuario['nivel_acesso'] = 1;

                //imagem
                if (isset($_FILES['tImagem-1']) && $_FILES['tImagem-1']['error'] == 0) {
                    $usuario['imagem'] = $_FILES['tImagem-1'];
                    $usuario['img_atual'] = addslashes($_POST['nImagem-user']);
                    if (!empty($_POST['nimagemStatus'])) {
                        $usuario['delete_img'] = true;
                    }
                } else if (!empty($_POST['nimagemStatus'])) {
                    $usuario['imagem'] = addslashes($_POST['nImagem-user']);
                    $usuario['delete_img'] = true;
                } else {
                    $usuario['imagem'] = $_POST['nImagem-user'];
                }

                if (isset($dados['usuario_erro']) && !empty($dados['usuario_erro'])) {
                    $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Preencha todos os campos obrigatórios (*).';
                    $dados['erro']['class'] = 'alert-danger';
                } else {
                    $usuario = $admModel->update($usuario);
                    if ($this->checkNivel() && $this->getId() == $usuario['id']) {
                        $this->setUserSession($usuario);
                    }
                    $dados['erro']['msg'] = '<i class="fa fa-check" aria-hidden="true"></i> Alteração realizada com sucesso!';
                    $dados['erro']['class'] = 'alert-success';
                    $_POST = array();
                }
            } else {
                $usuario = $admModel->read_specific("SELECT * FROM administrador WHERE md5(id)=:id", array('id' => $cod));
                if (!is_array($usuario)) {
                    $url = "Location: " . BASE_URL . "relatorio/administrador";
                    header($url);
                }
            }
            $dados['usuario'] = $usuario;
            $this->loadTemplate($view, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

    /**
     * Está função pertence a uma action do controle MVC, ela é responsável por controlar a edição do usuario
     * @access public
     * @param string $cod - código  em md5
     * @author Joab Torres <joabtorres1508@gmail.com>
     */
    public function usuario($cod) {
        if ($this->checkUser() && !empty($cod)) {
            $view = "usuario/edicao";
            $dados = array();
            if ($this->checkNivel() == false) {
                if (md5($this->getId()) != $cod) {
                    $url = "Location: " . BASE_URL . "home";
                    header($url);
                }
            }
            $userModel = new usuario();
            //Array que vai armazena os dados do usuário;
            $usuario = array();
            if (isset($_POST['nSalvar'])) {
                //id
                $usuario['id'] = addslashes($_POST['nCod']);
                //nome
                if (!empty($_POST['nNome'])) {
                    $usuario['nome'] = addslashes($_POST['nNome']);
                } else {
                    $dados['usuario_erro']['nome']['msg'] = 'Informe o nome';
                    $dados['usuario_erro']['nome']['class'] = 'has-error';
                }
                //sobrenome
                if (!empty($_POST['nSobrenome'])) {
                    $usuario['sobrenome'] = addslashes($_POST['nSobrenome']);
                } else {
                    $dados['usuario_erro']['sobrenome']['msg'] = 'Informe o sobrenome';
                    $dados['usuario_erro']['sobrenome']['class'] = 'has-error';
                }
                //nMatricula
                if (!empty($_POST['nMatricula'])) {
                    $usuario['matricula'] = addslashes($_POST['nMatricula']);
                    if ($userModel->read_specific('SELECT * FROM usuario WHERE matricula=:matricula AND id!=:id', array('matricula' => $usuario['matricula'], 'id' => $usuario['id']))) {
                        $dados['usuario_erro']['matricula']['msg'] = 'matricula já cadastrada';
                        $dados['usuario_erro']['matricula']['class'] = 'has-error';
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Não é possível salvar uma matricula/Siape já cadastrada, por favor informe outra matricula/Siape';
                        $dados['erro']['class'] = 'alert-danger';
                        $usuario['matricula'] = null;
                    }
                } else {
                    $dados['usuario_erro']['matricula']['msg'] = 'Informe a matricula/siape';
                    $dados['usuario_erro']['matricula']['class'] = 'has-error';
                }
                //email
                if (!empty($_POST['nEmail'])) {
                    $usuario['email'] = addslashes($_POST['nEmail']);
                    if ($userModel->read_specific('SELECT * FROM usuario WHERE email=:email AND id!=:id', array('email' => $usuario['email'], 'id' => $usuario['id']))) {
                        $dados['usuario_erro']['email']['msg'] = 'E-mail já cadastrado';
                        $dados['usuario_erro']['email']['class'] = 'has-error';
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Não é possível cadastrar um e-mail já cadastrado, por favor informe outro endereço de e-mail';
                        $dados['erro']['class'] = 'alert-danger';
                        $usuario['email'] = null;
                    }
                } else {
                    $dados['usuario_erro']['email']['msg'] = 'Informe o e-mail';
                    $dados['usuario_erro']['email']['class'] = 'has-error';
                }

                //CPF
                if (!empty($_POST['nCPF'])) {
                    $usuario['cpf'] = addslashes($_POST['nCPF']);
                    if ($userModel->read_specific('SELECT * FROM usuario WHERE cpf=:cpf AND id!=:id', array('cpf' => $usuario['cpf'], 'id' => $usuario['id']))) {
                        $dados['usuario_erro']['cpf']['msg'] = 'CPF já cadastrado';
                        $dados['usuario_erro']['cpf']['class'] = 'has-error';
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Não é possível cadastrar um CPF já cadastrado';
                        $dados['erro']['class'] = 'alert-danger';
                        $usuario['cpf'] = null;
                    }
                } else {
                    $dados['usuario_erro']['cpf']['msg'] = 'Informe o CPF';
                    $dados['usuario_erro']['cpf']['class'] = 'has-error';
                }

                //data de nascimento
                if (!empty($_POST['nNascimento'])) {
                    $usuario['nascimento'] = $this->formatDateBD($_POST['nNascimento']);
                } else {
                    $dados['usuario_erro']['nascimento']['msg'] = 'Informe o nome';
                    $dados['usuario_erro']['nascimento']['class'] = 'has-error';
                }
                //senha
                if (!empty($_POST['nSenha']) && !empty($_POST['nRepetirSenha'])) {
                    //senha
                    if ($_POST['nSenha'] == $_POST['nRepetirSenha']) {
                        $usuario['senha'] = $_POST['nSenha'];
                    } else {
                        $dados['usuario_erro']['senha']['msg'] = "Os campos 'Senha' e 'Repetir Senha' não estão iguais! ";
                        $dados['usuario_erro']['senha']['class'] = 'has-error';
                    }
                } else {
                    if (isset($usuario['senha'])) {
                        unset($usuario['senha']);
                    }
                }

                //nCategoria
                $usuario['categoria'] = $_POST['nCategoria'];
                //nCurso
                if (!empty($_POST['nCurso'])) {
                    $usuario['curso'] = $_POST['nCurso'];
                }
                //sexo
                $usuario['sexo'] = addslashes($_POST['nSexo']);

                if (isset($_POST['nStatus'])) {
                    $usuario['status'] = !empty($_POST['nStatus']) ? 1 : 0;
                } else {
                    $usuario['status'] = 1;
                }


                //imagem
                if (isset($_FILES['tImagem-1']) && $_FILES['tImagem-1']['error'] == 0) {
                    $usuario['imagem'] = $_FILES['tImagem-1'];
                    $usuario['img_atual'] = addslashes($_POST['nImagem-user']);
                    if (!empty($_POST['nimagemStatus'])) {
                        $usuario['delete_img'] = true;
                    }
                } else if (!empty($_POST['nimagemStatus'])) {
                    $usuario['imagem'] = addslashes($_POST['nImagem-user']);
                    $usuario['delete_img'] = true;
                } else {
                    $usuario['imagem'] = $_POST['nImagem-user'];
                }

                if (isset($dados['usuario_erro']) && !empty($dados['usuario_erro'])) {
                    $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> Preencha todos os campos obrigatórios (*).';
                    $dados['erro']['class'] = 'alert-danger';
                } else {
                    $usuario = $userModel->update($usuario);
                    if (!$this->checkNivel() && $this->getId() == $usuario['id']) {
                        $this->setUserSession($usuario);
                    }
                    $dados['erro']['msg'] = '<i class="fa fa-check" aria-hidden="true"></i> Alteração realizada com sucesso!';
                    $dados['erro']['class'] = 'alert-success';
                    $_POST = array();
                }
            } else {
                $usuario = $usuario = $userModel->read_specific("SELECT * FROM usuario WHERE md5(id)=:id", array('id' => $cod));
                if (!is_array($usuario)) {
                    $url = "Location: " . BASE_URL . "relatorio/usuario";
                    header($url);
                }
            }
            $dados['usuario'] = $usuario;
            $this->loadTemplate($view, $dados);
        } else {
            $url = "Location: " . BASE_URL . "login";
            header($url);
        }
    }

}

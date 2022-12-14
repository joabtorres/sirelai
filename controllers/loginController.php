<?php

/**
 * A classe 'loginController' é responsável por fazer validação de login para que tenha acesso ao sistema, podendo verifica se o e-mail e valido e exibindo a opção de recupera senha, 
 * 
 * @author Joab Torres <joabtorres1508@gmail.com>
 * @version 1.0
 * @copyright  (c) 2017, Joab Torres Alencar - Analista de Sistemas 
 * @access public
 * @package controllers
 * @example classe loginController
 */
class loginController extends controller {

    /**
     * Está função pertence a uma action do controle MVC, ela é responśavel por carrega a view  presente no diretorio views/login.php, além disso, ela faz validações de usuário, tenha digitado corretamento todos os campos do login e o usuário esteja registrado no banco será criado um array $_SESSION['usuario_sig_cootax'] com os seguintes dados: nome, url da foto, nível de acesso e usuário ativo e chama a função recupera,caso usuário deseja recupera a senha.
     * @access public
     * @author Joab Torres <joabtorres1508@gmail.com
     */
    public function index() {
        $view = "login";
        $dados = array();
        $_SESSION = array();
        if (isset($_POST['nEntrar']) && !empty($_POST['nEntrar'])) {
            //recaptcha validando
            if (!empty($_POST['nSerachUsuario']) && !empty($_POST['nSearchSenha'])) {
                $usuario = array('usuario' => addslashes($_POST['nSerachUsuario']), 'senha' => md5(sha1($_POST['nSearchSenha'])));
                $dominio = strstr($usuario['usuario'], '@');
                $modoAdmin = !empty($_POST['nModo']) ? $_POST['nModo'] : null;
                if (!empty($modoAdmin)) {
                    $adminModel = new administrador();
                    $resultado = $adminModel->read_specific('SELECT * FROM administrador WHERE email=:usuario AND senha=:senha', $usuario);
                    if (!$resultado) {
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> O Campo Usuário ou Senha está incorreto!';
                    }
                } else {
                    $usuarioModel = new usuario();
                    $resultado = $usuarioModel->read_specific('SELECT * FROM usuario WHERE email=:usuario AND senha=:senha', $usuario);
                    if (!$resultado) {
                        $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> O Campo Usuário ou Senha está incorreto!';
                    }
                }
                if (isset($resultado) && $resultado['status'] != 1) {
                    $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> O Acesso deste usuário está <b>DESABILITADO</b>!';
                }
                if (!isset($dados['erro']) && empty($dados['erro'])) {
                    $this->setUserSession($resultado);
                    header("location: home");
                }
            } else {
                $dados['erro']['msg'] = '<i class="fa fa-info-circle" aria-hidden="true"></i> O Campo Usuário ou Senha não está preenchido!';
            }
        }


        $this->loadView($view, $dados);

        //criando nova senha
        if (isset($_POST['nEnviar'])) {
            $email = addslashes(trim($_POST['nEmail']));
            $modo = !empty($_POST['nModo2']) ? 'admin' : null;
            $_POST = null;
            if ($this->validar_email($email) && $this->recuperar($email, $modo)) {
                echo '<script>$("#modal_confirmacao_email").modal();</script>';
            } else {
                echo '<script>$("#modal_invalido_email").modal();</script>';
            }
        }
    }

}

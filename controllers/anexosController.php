<?php

/**
 * A classe 'homeController' é responsável para fazer o carregamento da página home do sistema
 * 
 * @author Joab Torres <joabtorres1508@gmail.com>
 * @version 1.0
 * @copyright  (c) 2017, Joab Torres Alencar - Analista de Sistemas 
 * @access public
 * @package controllers
 * @example classe homeController
 */
class anexosController extends controller {
    
    public function index(){
	$viewname = 'anexos';
	$array = array();
	$this->loadTemplate($viewname, $array);
    }
    
}
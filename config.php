<?php

/*
 * config.php  - Este arquivo contem informações referente a: Conexão com banco de dados e URL Pádrão
 */

require 'environment.php';
$config = array();
if (ENVIRONMENT == 'development') {
    //Raiz
    define("BASE_URL", "http://localhost/virtualhost/sirelai/");
    //Nome do banco
    $config['dbname'] = 'bd_sirelai';
    //host
    $config['host'] = 'localhost';
    //usuario
    $config['dbuser'] = 'root';
    //senha
    $config['dbpass'] = '';
} else {
    //Raiz
    define("BASE_URL", "http://sirelai.joabtorres.com.br/");
    //Nome do banco
    $config['dbname'] = 'joabtorr_bd_sirelai';
    //host
    $config['host'] = 'localhost';
    //usuario
    $config['dbuser'] = 'joabtorr_develop';
    //senha
    $config['dbpass'] = '+f#yNqTQq2)L';
}
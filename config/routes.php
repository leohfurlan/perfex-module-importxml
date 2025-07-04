<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Rota principal do módulo (página de upload).
 * Acessível via: /admin/importxml
 * Direciona para: Controlador 'Importxml', método 'index'
 */
$route['admin/importxml'] = 'importxml/index';

/**
 * Rota para a ação de upload do formulário.
 * Acessível via: /admin/importxml/upload (usando método POST)
 * Direciona para: Controlador 'Importxml', método 'upload'
 */
$route['admin/importxml/upload'] = 'importxml/upload';

/**
 * Rota para a página que exibe a tabela de dados e serve os dados para o AJAX.
 * Acessível via: /admin/importxml/tabela
 * Direciona para: Controlador 'Importxml', método 'tabela'
 */
$route['admin/importxml/tabela'] = 'importxml/tabela';
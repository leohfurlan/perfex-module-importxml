<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Importar XML
Description: Módulo para importar dados de arquivos XML.
Version: 1.0.1
Author: Leonardo Furlan
*/

hooks()->add_action('admin_init', 'importxml_init');

/**
 * Função única para registrar o menu e as permissões do módulo.
 */
function importxml_init()
{
    $CI = &get_instance();

    // 1. Registrar o item de menu
    $CI->app_menu->add_sidebar_menu_item('importar-xml', [
        'name'     => 'Importar XML',
        'href'     => admin_url('importxml'),
        'position' => 25,
        'icon'     => 'fa fa-upload',
    ]);

    /**
     * 2. Tentar registrar as permissões de forma segura.
     * Esta verificação impede que o site quebre se a função não for encontrada.
     */
    if (function_exists('register_staff_permission')) {
        $capabilities = [
            'capabilities' => [
                'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
                'create' => _l('permission_create'),
                'edit'   => _l('permission_edit'),
                'delete' => _l('permission_delete'),
            ]
        ];
        register_staff_permission('importxml', 'Importar XML', $capabilities);
    } else {
        /**
         * Se a função não existir, loga uma atividade para diagnóstico.
         * Isso é um comportamento anormal do Perfex e precisa ser investigado.
         */
        log_activity('Módulo ImportXML: A função "register_staff_permission" não foi encontrada durante a ativação.');
    }
}
<?php

// Main menu
// if (is_admin()) {
$CI = &get_instance();




if (staff_can('view',  'corban_proposals')) {
    

    // MENU PROPOSTAS
      $CI->app_menu->add_sidebar_menu_item('icash-propostas', [
        'collapse' => true,
        'name'     => 'Propostas',
        'position' => 3,
        'icon'     => 'fa fa-table',
    ]);  

    $CI->app_menu->add_sidebar_children_item('icash-propostas', [
        'slug'     => 'proposals-register',
        'name'     => 'Simular | Digitar',
        'href'     => admin_url('icash_tools/tables'),
        'icon'     => 'fa fa-calculator',
        'position' => 1,
    ]);

    $CI->app_menu->add_sidebar_children_item('icash-propostas', [
        'slug'     => 'proposals-sign',
        'name'     => 'Acompanhar',
        'href'     => admin_url('icash_tools/listar_propostas'),
        'icon'     => 'fa fa-search',
        'position' => 1,
    ]);

    //---------------------------------------------------->


    // Adicionar item de menu principal para tabelas
    $CI->app_menu->add_sidebar_menu_item('icash-tabelas', [
        'collapse' => true,
        'name'     => _l('Controle de Tabelas'),
        'position' => 30,
        'icon'     => 'fa fa-table',
    ]);
}

if (staff_can('view',  'icash_tables')) {
    // Adicionar itens de submenu sob "icash-tabelas"
    $CI->app_menu->add_sidebar_children_item('icash-tabelas', [
        'slug'     => 'view-table',
        'name'     => _l('Ver Tabelas'),
        'href'     => admin_url('icash_tools/tables'),
        'position' => 5,
    ]);
}

if (staff_can('view',  'icash_tables')) {
    $CI->app_menu->add_sidebar_children_item('icash-tabelas', [
        'slug'     => 'insert-table',
        'name'     => _l('Inserir Tabela'),
        'href'     => admin_url('icash_tools/icash_insert_tabelas'),
        'position' => 6,
    ]);
}

if (staff_can('view',  'corban_links')) {
    // Adicionar itens de submenu sob "icash-tabelas"
    $CI->app_menu->add_sidebar_menu_item('corban-links', [
        'slug'     => 'view-links',
        'name'     => _l('Tabelas e Links'),
        'icon'     => 'fa fa-handshake-o menu-icon fa-duotone fa-circle-nodes',
        'href'     => admin_url('icash_tools/listar_comissoes'),
        'position' => 50,
    ]);
}

if (is_admin()) {

    if (staff_can('view',  'corban_links')) {
        // Adicionar itens de submenu sob "icash-tabelas"
        $CI->app_menu->add_sidebar_menu_item('view-list-proposal', [
            'slug'     => 'view-list-proposal',
            'name'     => _l('Nova Tela de Propostas'),
            'icon'     => 'fa-solid fa-file-invoice',
            'href'     => admin_url('icash_tools/List_proposals'),
            'position' => 50,
        ]);
    }
}

// }


// Verifica se o usuário atual é um administrador
if (is_admin()) {

    // ADICIONA UM ITEM DE MENU PRINCIPAL (NÍVEL SUPERIOR)
    // Esse é o menu principal no painel lateral do administrador
    get_instance()->app_menu->add_sidebar_menu_item('icash_tools_config', [
        'slug'     => 'icash_tools_config', // Identificador único do menu
        'name'     => _l('icash_tools_config'), // Nome visível no menu lateral
        'icon'     => 'fa-solid fa-network-wired',
        'href'     => admin_url('icash_tools/v1/icash_tools/view'), // Link de destino
        'position' => 31, // Posição no menu lateral
    ]);

    // ADICIONA UM SUBMENU (ITEM FILHO) DENTRO DO MENU 'icash_tools'
    // Este submenu ficará abaixo do menu principal 'icash_tools'
    get_instance()->app_menu->add_sidebar_children_item('icash_tools_config', [
        'slug'     => 'icash_tools_config', // Slug único (pode repetir se só tiver um filho)
        'name'     => _l('general_settings'), // Nome visível do submenu
        'href'     => admin_url('settings?group=ict_general_settings'), // Link para a aba de configurações criada acima
        'position' => 31,
    ]);

    // ADICIONA UMA ABA NAS CONFIGURAÇÕES DO PERFEX
    // Essa aba será visível em Configurações > com o nome 'advanced_rest_api'
    get_instance()->app_tabs->add_settings_tab('ict_general_settings', [
        'name'     => _l('general_settings'), // Nome visível da aba
        'view'     => 'icash_tools/settings/icash_tools_settings', // View que será carregada
        'icon'     => 'fa-solid fa-network-wired',
        'position' => 5,
    ]);
}

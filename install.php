<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'importxml_data')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'importxml_data` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nr_nota_fiscal` VARCHAR(50) NULL,
        `fornecedor` VARCHAR(255) NULL,
        `cnpj` VARCHAR(20) NULL,
        `cpf` VARCHAR(20) NULL,
        `data_emissao` DATE NULL,
        `data_pagto` DATE NULL,
        `st_cobranca` VARCHAR(50) NULL,
        `valor_cobranca` DECIMAL(15,2) NULL,
        `tipo_pagamento` VARCHAR(100) NOT NULL,
        `nome_arquivo_original` VARCHAR(255) NOT NULL,
        `data_importacao` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
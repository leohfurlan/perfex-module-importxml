<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'nr_nota_fiscal',
    'fornecedor',
    'cnpj',
    'cpf',
    'data_emissao',
    'data_pagto',
    'st_cobranca',
    'valor_cobranca',
    'tipo_pagamento',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'importxml_data';

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = $aRow['nr_nota_fiscal'];
    $row[] = $aRow['fornecedor'];
    $row[] = $aRow['cnpj'];
    $row[] = $aRow['cpf'];
    $row[] = _d($aRow['data_emissao']);
    $row[] = _d($aRow['data_pagto']);
    $row[] = $aRow['st_cobranca'];
    $row[] = app_format_money($aRow['valor_cobranca'], get_base_currency());
    $row[] = $aRow['tipo_pagamento'];

    $output['aaData'][] = $row;
}
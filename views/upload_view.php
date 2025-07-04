<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
// Lista de tipos de pagamento baseada na sua imagem
$tipos_pagamento = [
    'Boleto',
    'Cartão Credito',
    'Débito Automático',
    'Deposito',
    'Dinheiro Rubens',
    'Dinheiro Wesleimar',
    'Pagto. Adiantado',
    'Pagto. Antecipado',
];
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="tw-mb-4">
                            <h4 class="tw-mt-0 tw-font-semibold tw-text-lg">
                                Importar XML
                            </h4>
                            <p>Selecione o tipo de pagamento e os arquivos .xml para carregar no sistema.</p>
                        </div>

                        <?php echo form_open_multipart(admin_url('importxml/upload'), ['id' => 'xml-upload-form']); ?>

                        <div class="form-group">
                            <label for="tipo_pagamento" class="control-label">Tipo de Pagamento</label>
                            <select name="tipo_pagamento" class="selectpicker" data-width="100%" required="true">
                                <option value="">Selecione...</option>
                                <?php foreach ($tipos_pagamento as $tipo) { ?>
                                    <option value="<?php echo $tipo; ?>"><?php echo $tipo; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="xml_files" class="control-label">Arquivos XML</label>
                            <input type="file" name="xml_files[]" class="form-control" multiple="true" required="true" accept=".xml,text/xml">
                        </div>

                        <hr />
                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-upload" aria-hidden="true"></i> Enviar Arquivos
                        </button>
                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('importxml'); ?>" class="btn btn-primary pull-left display-block">
                                Importar Novo XML
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php
                        $table_data = [
                            'NR. Nota Fiscal',
                            'Fornecedor',
                            'CNPJ',
                            'CPF',
                            'Data Emissão',
                            'Data Pagto.',
                            'St Cobrança',
                            'Valor Cobrança',
                            'Tipo Pagamento',
                        ];
                        render_datatable($table_data, 'importxml-data', [], ['data-switches-url' => admin_url('importxml/change_status')]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function() {
        initDataTable('.table-importxml-data', window.location.href, undefined, undefined, 'undefined');
    });
</script>
<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Importxml extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('importxml_model');
    }

    // Exibe a página de upload
    public function index()
    {
        $this->load->view('importxml/upload_view');
    }

    // Processa o upload
    public function upload()
    {
        // ... (Esta função não precisa de nenhuma alteração, continua igual)
        if ($this->input->post()) {
            $tipo_pagamento = $this->input->post('tipo_pagamento', true);

            if (empty($tipo_pagamento)) {
                set_alert('danger', 'Por favor, selecione um tipo de pagamento.');
                redirect(admin_url('importxml'));
            }

            if (isset($_FILES['xml_files']['name']) && !empty($_FILES['xml_files']['name'][0])) {
                $total_files = count($_FILES['xml_files']['name']);
                $files_processed = 0;
                $files_failed = 0;

                for ($i = 0; $i < $total_files; $i++) {
                    $_FILES['file']['name']     = $_FILES['xml_files']['name'][$i];
                    $_FILES['file']['type']     = $_FILES['xml_files']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['xml_files']['tmp_name'][$i];
                    $_FILES['file']['error']    = $_FILES['xml_files']['error'][$i];
                    $_FILES['file']['size']     = $_FILES['xml_files']['size'][$i];

                    $upload_path = FCPATH . 'uploads/importxml/';
                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0755, true);
                    }
                    
                    $config['upload_path']   = $upload_path;
                    $config['allowed_types'] = 'xml';
                    $config['encrypt_name']  = TRUE;

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('file')) {
                        $upload_data = $this->upload->data();
                        
                        // A "mágica" acontece nesta função agora
                        if ($this->processar_arquivo_xml($upload_data['full_path'], $upload_data['orig_name'], $tipo_pagamento)) {
                            $files_processed++;
                        } else {
                            $files_failed++;
                        }
                        
                        @unlink($upload_data['full_path']);
                    } else {
                        $files_failed++;
                        log_activity('Falha no upload de XML: ' . $this->upload->display_errors());
                    }
                }
                
                if ($files_processed > 0) set_alert('success', $files_processed . ' arquivo(s) importado(s).');
                if ($files_failed > 0) set_alert('warning', $files_failed . ' arquivo(s) falharam. Verifique o log de atividades para mais detalhes.');
                
                redirect(admin_url('importxml/tabela'));

            } else {
                set_alert('danger', 'Nenhum arquivo XML selecionado.');
                redirect(admin_url('importxml'));
            }
        }
    }

    /**
     * Função principal que identifica o tipo de XML e chama o processador correto.
     */
    private function processar_arquivo_xml($caminho_arquivo, $nome_original, $tipo_pagamento)
    {
        // Carrega o arquivo sem se preocupar com namespaces, para facilitar a detecção
        $xml_string = file_get_contents($caminho_arquivo);
        $xml_string = str_replace('ns2:', '', $xml_string); // Remove namespaces comuns que podem atrapalhar
        $xml = simplexml_load_string($xml_string);

        if ($xml === false) {
            log_activity('XML mal formatado ou ilegível: ' . $nome_original);
            return false;
        }

        $dados_para_banco = null;

        // **1. Detecta se é uma NF-e (Produto)**
        if (isset($xml->NFe)) {
            $dados_para_banco = $this->processar_nfe($xml);
        } 
        // **2. Detecta se é uma NFS-e (Serviço)**
        elseif (isset($xml->Nfse)) {
            $dados_para_banco = $this->processar_nfse($xml);
        }
        
        // Se os dados foram extraídos com sucesso, adiciona os campos comuns e salva no banco
        if ($dados_para_banco) {
            $dados_para_banco['tipo_pagamento'] = $tipo_pagamento;
            $dados_para_banco['nome_arquivo_original'] = $nome_original;
            $dados_para_banco['data_importacao'] = date('Y-m-d H:i:s');
            
            return $this->importxml_model->adicionar_dados($dados_para_banco);
        }

        log_activity('Layout do XML não reconhecido: ' . $nome_original);
        return false;
    }

    /**
     * Extrai dados de um XML de NF-e (Produto).
     * @param SimpleXMLElement $xml
     * @return array|null
     */
    private function processar_nfe($xml)
    {
        try {
            $infNFe = $xml->NFe->infNFe;
            return [
                'nr_nota_fiscal' => (string) $infNFe->ide->nNF,
                'fornecedor'     => (string) $infNFe->emit->xNome,
                'cnpj'           => isset($infNFe->emit->CNPJ) ? (string) $infNFe->emit->CNPJ : null,
                'cpf'            => isset($infNFe->emit->CPF) ? (string) $infNFe->emit->CPF : null,
                'data_emissao'   => substr((string) $infNFe->ide->dhEmi, 0, 10),
                'data_pagto'     => null, // NFe padrão não tem data de pagamento explícita
                'st_cobranca'    => isset($infNFe->cobr->dup) ? 'Sim' : 'Não',
                'valor_cobranca' => (float) $infNFe->total->ICMSTot->vNF,
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Extrai dados de um XML de NFS-e (Serviço).
     * @param SimpleXMLElement $xml
     * @return array|null
     */
    private function processar_nfse($xml)
    {
        try {
            $infNfse = $xml->Nfse->InfNfse;
            $prestador = $infNfse->DeclaracaoPrestacaoServico->InfDeclaracaoPrestacaoServico->Prestador;
            
            // Em NFS-e, o "Fornecedor" é o Prestador de Serviço
            return [
                'nr_nota_fiscal' => (string) $infNfse->Numero,
                'fornecedor'     => (string) $infNfse->PrestadorServico->RazaoSocial,
                'cnpj'           => isset($prestador->CpfCnpj->Cnpj) ? (string) $prestador->CpfCnpj->Cnpj : null,
                'cpf'            => isset($prestador->CpfCnpj->Cpf) ? (string) $prestador->CpfCnpj->Cpf : null,
                'data_emissao'   => substr((string) $infNfse->DataEmissao, 0, 10),
                'data_pagto'     => null, // NFS-e também não costuma ter data de pagamento
                'st_cobranca'    => 'Sim', // NFS-e é sempre um documento de cobrança
                'valor_cobranca' => (float) $infNfse->ValoresNfse->ValorLiquidoNfse,
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    // Exibe a tabela de dados
    public function tabela()
    {
        if (!$this->input->is_ajax_request()) {
            $data['titulo'] = 'Dados Importados via XML';
            $this->load->view('importxml/tabela_dados_view', $data);
        } else {
            $this->app->get_table_data(module_dir_path('importxml', 'tables/table_importxml_data.php'));
        }
    }
}
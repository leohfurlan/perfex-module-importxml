<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Importxml_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function adicionar_dados($data)
    {
        $this->db->insert(db_prefix() . 'importxml_data', $data);
        return $this->db->insert_id() ? true : false;
    }

    // Esta função não é mais necessária se usarmos a tabela do Perfex
    // public function get_todos_dados() { ... }
}
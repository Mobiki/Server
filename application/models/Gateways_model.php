<?php

class Gateways_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function getAll(Type $var = null)
    {
        return $this->db->get('gateways')->result_array();
    }
    public function getDetail($mac)
    {
        $this->db->where("mac",$mac);
        return $this->db->get('gateways')->result_array();
    }


}
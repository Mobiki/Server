<?php

class Dashboard_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    public function gateway_list(Type $var = null)
    {
        return $this->db->get("gateways")->result_array(); 
    }


    public function device_list(Type $var = null)
    {
        return $this->db->get("devices")->result_array(); 
    }

    public function gateways_count()
    { 
        if ($this->db->table_exists('gateways')) {
            return $this->db->query('SELECT count(id) as gateways_count FROM gateways')->row_array();
        } else {
            return 0;
        }
    }

    public function devices_count(Type $var = null)
    { 
        if ($this->db->table_exists('devices')) {
            return $this->db->query('SELECT count(id) as devices_count FROM devices')->row_array();
        } else {
            return 0;
        }
    }


    public function addAlertLog($data)
    {
        $this->db->insert('alert_suspend',$data);

    }
}

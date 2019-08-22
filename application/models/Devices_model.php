<?php

class Devices_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    public function getAll(Type $var = null)
    {
        return $this->db->get('devices')->result_array();
    }
    public function getDevicesType(Type $var = null)
    {
        return $this->db->get('devices_type')->result_array();
    }

    public function getAllSensors(Type $var = null)
    {
        //$this->db->where('type_id !=', 1);
        return $this->db->get('devices')->result_array();
    }


    public function addAlertLog($data)
    {
        $this->db->insert("alert_logs",$data);
    }
}

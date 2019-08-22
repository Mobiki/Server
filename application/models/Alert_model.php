<?php

class Alert_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    public function getAllAlerts(Type $var = null)
    {
        return $this->db->get("alert_rules")->result_array(); 
    }


    public function getSuspendAlerts(Type $var = null)
    {
        $this->db->where("status",2);
        return $this->db->get("alert_logs")->result_array(); 
    }

    public function getCloseAlerts(Type $var = null)
    {
        $this->db->where("status",3);
        return $this->db->get("alert_logs")->result_array(); 
    }

    public function alertClose($alertid,$devicemac)
    {
        
        $this->db->where("id",$alertid);
        $data = array(
            'close_date'=> date('Y-m-d H:i:s'),
            'status'=>3,
        );
        $this->db->update("alert_logs",$data);


        $client->del('alertset:' . $devicemac);
    }
}
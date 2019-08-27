<?php

class Alert_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function redis(Type $var = null)
    {
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }
    
    public function addAlertLog($data)
    {
        $this->db->insert("alert_logs",$data);
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
        $client = $this->redis();
        
        $this->db->where("id",$alertid);
        $data = array(
            'close_date'=> date('Y-m-d H:i:s'),
            'status'=>3,
        );
        $this->db->update("alert_logs",$data);


        $client->del('alertset:' . $devicemac);
    }
}
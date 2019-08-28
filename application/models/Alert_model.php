<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Alert_model extends CI_Model
{
    protected $table = 'alert_logs';
    protected $alert_rules = 'alert_rules';

    function __construct()
    {
        parent::__construct();
    }

    public function redis()
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
        $this->db->insert("alert_logs", $data);
    }

    public function get_all_alert_rules()
    {
        return $this->db->get($this->alert_rules)
            ->result_array();
    }

    public function insert_alert_rule($data)
    {
        return $this->db->insert($this->alert_rules, $data);
    }

    public function update_alert_rule($id,$data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->alert_rules, $data);
    }
    public function delete_alert_rule($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->alert_rules);
    }

    public function getSuspendAlerts()
    {
        $this->db->where("status", 2);
        return $this->db->get("alert_logs")
        ->result_array();
    }

    public function getCloseAlerts()
    {
        $this->db->where("status", 3);
        return $this->db->get("alert_logs")
        ->result_array();
    }

    public function alertClose($alertid, $devicemac)
    {
        $client = $this->redis();

        $this->db->where("id", $alertid);
        $data = array(
            'close_date' => date('Y-m-d H:i:s'),
            'status' => 3,
        );
        $this->db->update("alert_logs", $data);


        $client->del('alertset:' . $devicemac);
    }
}

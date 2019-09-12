<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Alert_model extends CI_Model
{
    protected $alert_rules = 'alert_rules';
    protected $alert_logs = 'alert_logs';

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

    public function get_all_alert_rules()
    {
        return $this->db->get($this->alert_rules)
            ->result_array();
    }

    public function insert_alert_rule($data)
    {
        return $this->db->insert($this->alert_rules, $data);
    }

    public function update_alert_rule($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->alert_rules, $data);
    }
    public function delete_alert_rule($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->alert_rules);
    }





    //alert_logs
    public function get_all_alert_logs()
    {
        return $this->db->get($this->alert_logs)
            ->result_array();
    }
    public function get_all_suspended_alerts()
    {
        $this->db->where("status", 2);
        return $this->db->get($this->alert_logs)
            ->result_array();
    }
    public function get_all_closed_alerts()
    {
        $this->db->where("status", 3);
        return $this->db->get($this->alert_logs)
            ->result_array();
    }



    public function insert_alert_log($data)
    {
        return $this->db->insert($this->alert_logs, $data);
    }





    public function alert_close($id, $data)
    {
        $this->db->where("id", $id);
        return $this->db->update($this->alert_logs, $data);
    }

    public function get_alert_logs_where($device_id, $gatewey_id, $user_id, $start, $finish)
    {
        
        if ($device_id > 0) {
            $where["device_id"] = $device_id;
        }
        if ($gatewey_id > 0) {
            $where["gatewey_id"] = $gatewey_id;
        }
        
        if ($user_id > 0) {
            $where["suspended_user_id"] = $user_id;
        }

        $where["suspend_date >"] = $start;
        $where["suspend_date <"] = $finish;


        $this->db->where($where);
        return $this->db->get($this->alert_logs)->result_array();
        //->result();
    }
}

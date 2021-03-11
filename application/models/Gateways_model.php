<?php

class Gateways_model extends CI_Model
{
    protected $table = 'gateways';

    function __construct()
    {
        parent::__construct();
    }

    //get_all
    //get_by_id
    //get_where
    //insert
    //update
    //delete

    public function get_all()
    {
        return $this->db->get($this->table)
            ->result_array();
        //->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))
            ->row();
    }

    public function get_by_mac($mac)
    {
        return $this->db->get_where($this->table, array('mac' => $mac))
            ->row();
    }

    public function get_where($where)
    {
        return $this->db->where($where)
            ->get($this->table)
            ->result_array();
        //->result();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    public function get_all_type()
    {
        return $this->db->get("gateway_type")
            ->result_array();
        //->result();
    }
}

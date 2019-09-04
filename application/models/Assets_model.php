<?php

class Assets_model extends CI_Model
{
    protected $table = 'assets';
    protected $asset_type = 'asset_type';

    function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        return $this->db->get($this->table)
            ->result_array();
        //->result();
    }
    
    public function get_all_asset_type()
    {
        return $this->db->get($this->asset_type)
            ->result_array();
        //->result();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))
            ->result_array();
        //->row();
    }

    public function get_by_mac($mac)
    {
        return $this->db->get_where($this->table, array('mac' => $mac))
            ->result_array();
        //->row();
    }

    public function get_by_type($device_type_id)
    {
        return $this->db->get_where($this->table, array('type_id' => $device_type_id))
            ->result_array();
        //->row();
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

    //asset_type
    
    public function insert_asset_type($data)
    {
        return $this->db->insert($this->asset_type, $data);
    }
    public function delete_asset_type($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->asset_type);
    }
    public function update_asset_type($id,$data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->asset_type, $data);
    }
}

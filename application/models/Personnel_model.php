<?php

class Personnel_model extends CI_Model
{
    protected $table = 'personnel';
    protected $personnel_type = 'personnel_type';

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

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, array('id' => $id))
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


    //personnel_type
    public function get_all_personnel_type()
    {
        return $this->db->get($this->personnel_type)
            ->result_array();
        //->result();
    }

    public function get_by_id_personnel_type($id)
    {
        return $this->db->get_where($this->personnel_type, array('id' => $id))
            ->result_array();
        //->row();
    }

    public function get_where_personnel_type($where)
    {
        return $this->db->where($where)
            ->get($this->personnel_type)
            ->result_array();
        //->result();
    }

    public function insert_personnel_type($data)
    {
        return $this->db->insert($this->personnel_type, $data);
    }

    public function update_personnel_type($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->personnel_type, $data);
    }

    public function delete_personnel_type($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->personnel_type);
    }
}

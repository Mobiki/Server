<?php

class Company_model extends CI_Model
{
    var $table = "work_shift";
    var $select_column = array("id", "name", "start_time", "finish_time");
    var $order_column = array("id", "name", null, null, null);

    function __construct()
    {
        parent::__construct();
    }

    public function get_all_work_shifts()
    {
        return $this->db->get($this->table)->result_array();
    }

    function make_query()
    {
        $this->db->select($this->select_column);
        $this->db->from($this->table);
        if (isset($_POST["search"]["value"])) {
            $this->db->like("name", $_POST["search"]["value"]);
            $this->db->or_like("id", $_POST["search"]["value"]);
        }
        if (isset($_POST["order"])) {
            $this->db->order_by($this->order_column[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('id', 'ASC');//order col
        }
    }
    function make_datatables()
    {
        $this->make_query();  
           if($_POST["length"] != -1)  
           {  
                $this->db->limit($_POST['length'], $_POST['start']);  
           }  
           $query = $this->db->get();  
           return $query->result();
    }
    function get_filtered_data()
    {
        $this->make_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    function get_all_data()
    {
        $this->db->select("*");
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function delete($id)
    {
        $this->db->where("id",$id);
        return $this->db->delete($this->table);
    }

    public function add($data)
    {
        return $this->db->insert($this->table,$data);
    }
    public function edit($id,$data)
    {
        $this->db->where("id",$id);
        return $this->db->update($this->table,$data);
    }
}

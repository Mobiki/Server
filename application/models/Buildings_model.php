<?php

class Buildings_model extends CI_Model
{
    protected $table = 'buildings';

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
}

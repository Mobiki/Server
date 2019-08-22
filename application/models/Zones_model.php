<?php

class Zones_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    public function getAllZones(Type $var = null)
    {
        return $this->db->get('zones')->result_array();
    }

    public function getAllZoneCategories(Type $var = null)
    {
        return $this->db->get('zone_categories')->result_array();
    }

}
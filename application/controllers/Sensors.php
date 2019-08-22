<?php

class Sensors extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array(
            'pageId' => 6,
            'pageName' => "Sensors",
        );
        $this->load->view('sensors', $data);
    }
}

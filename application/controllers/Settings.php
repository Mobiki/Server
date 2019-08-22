<?php

class Settings extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array(
            'pageId' => 12,
            'pageName' => "Settings",
        );
        $this->load->view('settings', $data);
    }
}

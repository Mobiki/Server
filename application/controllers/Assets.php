
<?php

class Assets extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index(Type $var = null)
    {
        $data = array(
            'pageId'=>8,
            'pageName' => "Assets",
        );
        $this->load->view('assets', $data);
    }

}
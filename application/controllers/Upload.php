<?php

class Upload extends CI_Controller {

        public function __construct()
        {
                parent::__construct();
                $this->load->helper(array('form', 'url'));
        }

        public function index()
        {
                $this->load->view('upload_form', array('error' => ' ' ));
        }

        public function do_upload()
        {
                $config['upload_path']          = 'assets/images/personnel/';
                $config['allowed_types']        = 'gif|jpg|png';
                $config['max_size']             = 100;
                $config['max_width']            = 1024;
                $config['max_height']           = 768;

                $this->load->library('upload', $config);

                $name = $this->input->post("name");

                if ( ! $this->upload->do_upload('userfile'))
                {
                        $error = array('name'=>$name,'error' => $this->upload->display_errors());

                        $this->load->view('us', $error);
                }
                else
                {
                        $data = array('name'=>$name,'upload_data' => $this->upload->data());

                        $this->load->view('us', $data);
                }
        
        }
}

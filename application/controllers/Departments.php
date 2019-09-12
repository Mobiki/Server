<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Departments extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Departments_model");
    }

    public function redis()
    {
        $client = new Predis\Client([
            'scheme' => $this->config->item('redis_scheme'),
            'host'   => $this->config->item('redis_host'),
            'port'   => $this->config->item('redis_port'),
            'password' => $this->config->item('redis_auth')
        ]);
        return $client;
    }

    public function index()
    {
        $departments = $this->Departments_model->get_all();

        $data = array(
            'pageId' => 8,
            'pageName' => "Departments",
            'departments' => $departments,
        );
        $this->load->view('departments', $data);
    }

    public function add()
    {
        if ($this->input->post('expiry_date', true)=="") {
            $expiry_date = "0000-00-00";
        } else {
            $expiry_date = $this->input->post('expiry_date', true);
        }
        
        $data = array(
            'name' => $this->input->post('name', true),
            'parent_id' => $this->input->post('parent_id', true),
            'expiry_date' => $expiry_date,
        );
        print_r($data);
        $result = $this->Departments_model->insert($data);
        if ($result) {
            redirect('departments');
        } else {
            echo "Error - Departments - Add";
        }
    }

    public function edit()
    { 
        $id = $this->input->post('id', true);
        $data = array(
            'name' => $this->input->post('name', true),
            'parent_id' => $this->input->post('parent_id', true),
            'expiry_date' => $this->input->post('expiry_date', true),
        );
        $result = $this->Departments_model->update($id,$data);
        if ($result) {
            redirect('departments');
        } else {
            echo "Error - Departments - Edit";
        }
    }

    public function delete()
    {
        $id = $this->input->post('id', true);
        $result = $this->Departments_model->delete($id);
        if ($result) {
            redirect('departments');
        } else {
            echo "Error - Departments - Delete";
        }
    }

    public function get_by_id()
    {
        $id = $this->input->get('id', true);
        $result = $this->Departments_model->get_by_id($id);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function treeview()
    {
        $data = [];
        $parent_key = '0';
        $row = $this->db->query('SELECT * from departments');

        if($row->num_rows() > 0)
        {
            $data = $this->membersTree($parent_key);
        }else{
            $data=["id"=>"0","name"=>"No Members presnt in list","text"=>"No Members is presnt in list","nodes"=>[]];
        }
        echo json_encode(array_values($data));
    }

    public function membersTree($parent_key)
    {
        $row1 = [];
        $row = $this->db->query('SELECT id, name from departments WHERE parent_id="'.$parent_key.'"')->result_array();
        foreach($row as $key => $value)
        {
           $id = $value['id'];
           $row1[$key]['id'] = $value['id'];
           $row1[$key]['name'] = $value['name'];
           $row1[$key]['text'] = $value['name'];
           $row1[$key]['nodes'] = array_values($this->membersTree($value['id']));
        }
        return $row1;
    }
    public function departments_treeview()
    {
        $this->load->view("departments_treeview");
    }
}

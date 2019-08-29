<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Personnel extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Personnel_model");
        $this->load->model("Personnel_type_model");

        $this->load->model("Departments_model");
        $this->load->model("Devices_model");

    }

    public function do_upload()
    {
        $config = array(
            'upload_path' => "assets/images/personnel/",
            'allowed_types' => "gif|jpg|png|jpeg",
            'overwrite' => TRUE,
            'max_size' => "2048000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
            'max_height' => "768",
            'max_width' => "1024"
        );
        $this->load->library('upload', $config);

        echo $this->input->post('userfile', true);
        if ($this->upload->do_upload($this->input->post('userfile', true)))
                {
                    echo "true";
                }
                else
                {
                    echo "false";
                }
    }

    public function index()
    {
        $personnel = $this->Personnel_model->get_all();
        $personnel_type = $this->Personnel_model->get_all_personnel_type();

        $departments = $this->Departments_model->get_all();


        $devices = $this->Devices_model->get_all();
        $device_type = $this->Devices_model->get_all_device_type();

        $data = array(
            'pageId' => '7',
            'pageName' => 'Personnel',
            'personnel' => $personnel,
            'personnel_type' => $personnel_type,
            'departments' => $departments,
            'devices' => $devices,
            'devices_type' => $device_type,
        );
        $this->load->view('personnel', $data);
    }


    public function add_personnel()
    {
        $data = array(
            'name' => $this->input->post('name', true),
            'image' => $this->input->post('image', true),
            'email' => $this->input->post('email', true),
            'type_id' => $this->input->post('type_id', true),
            'department_id' => $this->input->post('department_id', true),
            'device_id' => $this->input->post('device_id', true),
            'status' => "1",
        );

        $result = $this->Personnel_model->insert($data);
        if ($result) {
            redirect('personnel');
        } else {
            echo "Error - Personnel - Update";
        }
    }

    public function add_personnel_type()
    {
        $data = array(
            'name' => $this->input->post('name', true),
        );

        $result = $this->Personnel_type_model->insert($data);
        if ($result) {
            redirect('personnel');
        } else {
            echo "Error - Personnel - Update";
        }
    }

    public function add_department()
    {
        $data = array(
            'name' => $this->input->post('name', true),
        );

        $result = $this->Departments_model->insert($data);
        if ($result) {
            redirect('personnel');
        } else {
            echo "Error - Personnel - Update";
        }
    }


    public function delete_personnel()
    {
        $id =  $this->input->post('id', true);
        $result = $this->Personnel_model->delete($id);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Personnel - Delete";
        }
    }

    public function delete_personnel_type()
    {
        $id =  $this->input->post('id', true);
        $result = $this->Personnel_type_model->delete($id);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Personnel type - Delete";
        }
    }

    public function delete_department()
    {
        $id =  $this->input->post('id', true);
        $result = $this->Departments_model->delete($id);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Personnel type - Delete";
        }
    }

    public function edit_personnel()
    {
        $id =  $this->input->post('id', true);
        $data = array(
            'name' => $this->input->post('name', true),
            'image' => $this->input->post('image', true),
            'email' => $this->input->post('email', true),
            'type_id' => $this->input->post('type_id', true),
            'department_id' => $this->input->post('department_id', true),
            'device_id' => $this->input->post('device_id', true),
            'status' => $this->input->post('status', true),
        );

        $result = $this->Personnel_model->update($id, $data);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Personnel - Update";
        }
    }

    public function edit_personnel_type()
    {
        $id =  $this->input->post('id', true);
        $data = array(
            'name' => $this->input->post('name', true),
        );

        $result = $this->Personnel_type_model->update($id, $data);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Departments - Update";
        }
    }

    public function edit_department()
    {
        $id =  $this->input->post('id', true);
        $data = array(
            'name' => $this->input->post('name', true),
        );

        $result = $this->Departments_model->update($id, $data);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Departments - Update";
        }
    }

    public function assign_device()
    {
        $id =  $this->input->post('id', true);
        $device_id =  $this->input->post('device_id', true);

        $result = $this->Personnel_model->update($id, $device_id);
        if ($result == true) {
            redirect('personnel');
        } else {
            echo "Error - Personnel - assign_device";
        }
    }

    public function toredis()
    {
        $client = $this->redis();
        $personnel = $this->Personnel_model->get_all();
        $client->set("personnel", json_encode($personnel));

        $departments = $this->Departments_model->get_all();
        $client->set("departments", json_encode($departments));
    }
}

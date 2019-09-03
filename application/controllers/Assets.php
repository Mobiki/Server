
<?php

class Assets extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Assets_model");

        $this->load->model("Personnel_model");
        $this->load->model("Departments_model");
        $this->load->model("Devices_model");

        $this->load->helper(array('form', 'url'));
    }

    public function index()
    {
        $personnel = $this->Personnel_model->get_all();
        $assets = $this->Assets_model->get_all();
        $asset_type = $this->Assets_model->get_all_asset_type();
        $departments = $this->Departments_model->get_all();
        $devices = $this->Devices_model->get_all();

        $data = array(
            'pageId' => 8,
            'pageName' => "Assets",
            'assets' => $assets,
            'asset_type' => $asset_type,
            'personnel' => $personnel,
            'departments' => $departments,
            'devices' => $devices,
        );
        $this->load->view('assets', $data);
    }

    public function add()
    {
        $config['upload_path']          = 'assets/images/assets/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        //$config['max_size']             = 900;
        //$config['max_width']            = 1920;
        //$config['max_height']           = 1080;

        $this->load->library('upload', $config);
        $now = date('Y-m-d H:i:s');
        if (!$this->upload->do_upload('userfile')) {

            $data = array(
                'name' => $this->input->post('name', true),
                'image' => 'default-asset-image.jpg',
                'stock_code' => $this->input->post('stock_code', true),
                'serial_number' => $this->input->post('serial_number', true),
                'manufacturer' => $this->input->post('manufacturer', true),
                'department_id' => $this->input->post('department_id', true),
                'personnel_id' => $this->input->post('personnel_id', true),
                'description' => $this->input->post('description', true),
                'device_id' => $this->input->post('device_id', true),
                'type_id' => $this->input->post('type_id', true),
                'model' => '0',
                'status' => 1,
                'date_added' => $now,
                'date_modified' => $now,
                //'error' => $this->upload->display_errors()
            );
            //print_r($this->upload->display_errors());
            //die();

        } else {
            $data = array(
                'name' => $this->input->post('name', true),
                'image' => $this->upload->data()['file_name'],
                'type_id' => $this->input->post('type_id', true),
                'description' => $this->input->post('description', true),
                'department_id' => $this->input->post('department_id', true),
                'personnel_id' => $this->input->post('personnel_id', true),
                'device_id' => $this->input->post('device_id', true),
                'serial_number' => $this->input->post('serial_number', true),
                'manufacturer' => $this->input->post('manufacturer', true),
                'stock_code' => $this->input->post('stock_code', true),
                'model' => '0',
                'status' => 1,
                'date_added' => $now,
                'date_modified' => $now,
                //'upload_data' => $this->upload->data()["file_name"]
            );
        }

        $result = $this->Assets_model->insert($data);
        if ($result) {
            redirect('assets/index');
        } else {
            echo "Error - Assets - Add";
        }
    }


    public function delete()
    {
        $id = $this->input->post('id', true);
        $result = $this->Assets_model->delete($id);
        if ($result) {
            redirect('assets/index');
        } else {
            echo "Error - assets - Delete";
        }
    }

    public function edit()
    {
        $id = $this->input->post('id', true);

        $config['upload_path']          = 'assets/images/assets/';
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        //$config['max_size']             = 900;
        //$config['max_width']            = 1920;
        //$config['max_height']           = 1080;

        $this->load->library('upload', $config);
        $now = date('Y-m-d H:i:s');
        if (!$this->upload->do_upload('userfile')) {

            $data = array(
                'name' => $this->input->post('name', true),
                'image' => 'default-asset-image.jpg',
                'stock_code' => $this->input->post('stock_code', true),
                'serial_number' => $this->input->post('serial_number', true),
                'manufacturer' => $this->input->post('manufacturer', true),
                'department_id' => $this->input->post('department_id', true),
                'personnel_id' => $this->input->post('personnel_id', true),
                'description' => $this->input->post('description', true),
                'device_id' => $this->input->post('device_id', true),
                'type_id' => $this->input->post('type_id', true),
                'model' => '0',
                'status' => 1,
                'date_added' => $now,
                'date_modified' => $now,
                //'error' => $this->upload->display_errors()
            );
        } else {
            $data = array(
                'name' => $this->input->post('name', true),
                'image' => $this->upload->data()['file_name'],
                'type_id' => $this->input->post('type_id', true),
                'description' => $this->input->post('description', true),
                'department_id' => $this->input->post('department_id', true),
                'personnel_id' => $this->input->post('personnel_id', true),
                'device_id' => $this->input->post('device_id', true),
                'serial_number' => $this->input->post('serial_number', true),
                'manufacturer' => $this->input->post('manufacturer', true),
                'stock_code' => $this->input->post('stock_code', true),
                'model' => '0',
                'status' => 1,
                'date_added' => $now,
                'date_modified' => $now,
                //'upload_data' => $this->upload->data()["file_name"]
            );
        }





        $result = $this->Assets_model->update($id, $data);
        if ($result) {
            redirect('assets/index');
        } else {
            echo "Error - Assets - Add";
        }
    }
}

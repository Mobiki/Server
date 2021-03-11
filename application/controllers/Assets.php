
<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Assets extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Assets_model");

        $this->load->model("Personnel_model");
        $this->load->model("Departments_model");
        $this->load->model("Devices_model");
        $this->load->model("Zones_model");

        $this->load->helper(array('form', 'url'));
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
        $personnel = $this->Personnel_model->get_all();
        $assets = $this->Assets_model->get_all();
        $asset_type = $this->Assets_model->get_all_asset_type();
        $departments = $this->Departments_model->get_all();
        $devices = $this->Devices_model->get_all();
        $zones = $this->Zones_model->get_all();

        $data = array(
            'pageId' => 8,
            'pageName' => "Assets",
            'assets' => $assets,
            'asset_type' => $asset_type,
            'personnel' => $personnel,
            'departments' => $departments,
            'zones' => $zones,
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
                'zone_id' => $this->input->post('zone_id', true),
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
                'zone_id' => $this->input->post('zone_id', true),
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
            $this->toredis();
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
            $this->toredis();
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
                'stock_code' => $this->input->post('stock_code', true),
                'serial_number' => $this->input->post('serial_number', true),
                'manufacturer' => $this->input->post('manufacturer', true),
                'department_id' => $this->input->post('department_id', true),
                'zone_id' => $this->input->post('zone_id', true),
                'personnel_id' => $this->input->post('personnel_id', true),
                'description' => $this->input->post('description', true),
                'device_id' => $this->input->post('device_id', true),
                'type_id' => $this->input->post('type_id', true),
                'model' => '0',
                'status' => 1,
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
                'zone_id' => $this->input->post('zone_id', true),
                'personnel_id' => $this->input->post('personnel_id', true),
                'device_id' => $this->input->post('device_id', true),
                'serial_number' => $this->input->post('serial_number', true),
                'manufacturer' => $this->input->post('manufacturer', true),
                'stock_code' => $this->input->post('stock_code', true),
                'model' => '0',
                'status' => 1,
                'date_modified' => $now,
                //'upload_data' => $this->upload->data()["file_name"]
            );
        }

        $result = $this->Assets_model->update($id, $data);
        if ($result) {
            $this->toredis();
            redirect('assets/index');
        } else {
            echo "Error - Assets - Add";
        }
    }

    public function assete_types_index()
    {
        $asset_type = $this->Assets_model->get_all_asset_type();

        $data = array(
            'asset_type' => $asset_type,
        );
        $this->load->view('asset_types', $data);
    }

    public function list_asset_type()
    {
        $id = $this->input->get('id', true);
        $asset_type = $this->Assets_model->get_all_asset_type();

        echo '<option value="0">None</option>';
        foreach ($asset_type as $key => $dtvalue) {
            echo '<option value="' . $dtvalue['id'] . '" ';
            if($id==$dtvalue['id']){echo " selected ";}
            echo '>' . $dtvalue['name'] . '</option>';
        }
    }
    public function add_asset_type()
    {
        $data = array(
            'name' => $this->input->post('name', true),
        );
        $result = $this->Assets_model->insert_asset_type($data);
        $this->toredis();
    }

    public function delete_asset_type()
    {
        $id = $this->input->post('id', true);
        $result = $this->Assets_model->delete_asset_type($id);
        $this->toredis();
    }

    public function edit_asset_type()
    {
        $id = $this->input->post('id', true);
        $name = $this->input->post('name', true);
        $data = array(
            'name' => $name,
        );
        $result = $this->Assets_model->update_asset_type($id, $data);
        $this->toredis();
    }

    public function toredis()
    {
        $client = $this->redis();
        $asset_type = $this->Assets_model->get_all_asset_type();
        $client->set("asset_type", json_encode($asset_type));

        $assets = $this->Assets_model->get_all();
        $client->set("assets", json_encode($assets));
    }
}

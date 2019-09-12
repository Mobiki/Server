<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();


class Zones extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model("Zones_model");
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
        $zones = $this->Zones_model->get_all();
        $data = array(
            'pageId' => '3',
            'pageName' => 'Zones',
            'zones' => $zones,
        );
        $this->load->view("zones", $data);
    }

    public function add()
    {
        $data = array(
            'name' => $this->input->post("name", true),
            'parent_id' => $this->input->post("parent_id", true),
            'description' => $this->input->post("description", true),
        );

        $result = $this->Zones_model->insert($data);

        if ($result) {
            $this->toredis();
            redirect('zones');
        } else {
            echo "Error - Zones - Add";
        }
    }

    public function delete()
    {
        $id = $this->input->post("id", true);
        $result = $this->Zones_model->delete($id);

        if ($result) {
            $this->toredis();
            redirect('zones');
        } else {
            echo "Error - Zones - Add";
        }
    }

    public function get_by_id()
    {
        $id = $this->input->get("id", true);
        $result = $this->Zones_model->get_by_id($id);

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function edit()
    {
        $id = $this->input->post("id", true);
        $data = array(
            'name' => $this->input->post("name", true),
            'parent_id' => $this->input->post("parent_id", true),
            'description' => $this->input->post("description", true),
        );
        $result = $this->Zones_model->update($id, $data);

        if ($result) {
            $this->toredis();
            redirect('zones');
        } else {
            echo "Error - Zones - Edit";
        }
    }

    public function toredis()
    {
        $client = $this->redis();
        $zones = $this->Zones_model->get_all();
        $client->set("zones", json_encode($zones));
    }


    public function treeview()
    {
        $data = [];
        $parent_key = '0';
        $row = $this->db->query('SELECT * from zones');

        if ($row->num_rows() > 0) {
            $data = $this->membersTree($parent_key);
        } else {
            $data = ["id" => "0", "name" => "No Members presnt in list", "text" => "No Members is presnt in list", "nodes" => []];
        }
        echo json_encode(array_values($data));
    }

    public function membersTree($parent_key)
    {
        $row1 = [];
        $row = $this->db->query('SELECT id, name from zones WHERE parent_id="' . $parent_key . '"')->result_array();
        foreach ($row as $key => $value) {
            $id = $value['id'];
            $row1[$key]['id'] = $value['id'];
            $row1[$key]['name'] = $value['name'];
            $row1[$key]['text'] = $value['name'];
            $row1[$key]['nodes'] = array_values($this->membersTree($value['id']));
        }
        return $row1;
    }
    public function zones_treeview()
    {
        $this->load->view("zones_treeview");
    }
}

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
        $this->Zones_model->delete($id);
    }

    public function edit()
    {
        $id = $this->input->post("id", true);
        $data = array(
            'name' => $this->input->post("name", true),
            'parent_id' => $this->input->post("parent_id", true),
            'description' => $this->input->post("description", true),
        );
        $this->Zones_model->update($id, $data);
    }

    public function toredis()
    {
        $client = $this->redis();
        $zones = $this->Zones_model->get_all();
        $client->set("zones", json_encode($zones));
    }
}

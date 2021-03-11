<?php

require_once(APPPATH . '../vendor/autoload.php');
Predis\Autoloader::register();

class Company extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model("Personnel_model");
        $this->load->model("Assets_model");
        $this->load->model("Company_model");
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


    public function details()
    {
        $work_shifts = $this->Company_model->get_all_work_shifts();

        $data = array(
            'pageId' => 88,
            'pageNAme' => "Company Details",
            'work_shifts' => $work_shifts,
        );
        $this->load->view('details', $data);
    }
    public function get_work_shifts()
    {
        /*$work_shifts = $this->Company_model->get_all_work_shifts();
        header('Content-Type: application/json');
        echo json_encode($work_shifts);*/


        $fetch_data = $this->Company_model->make_datatables();
        $data = array();
        foreach ($fetch_data as $row) {
            $sub_array = array();
            $sub_array[] = $row->id;
            $sub_array[] = $row->name;
            $sub_array[] = $row->start_time;
            $sub_array[] = $row->finish_time;
            $sub_array[] = '<button type="button" data-toggle="modal" data-target="#add_work_shift"
            class="btn btn-success btn-sm" onclick="edit(' . $row->id . ',\'' . $row->name . '\',\'' . $row->start_time . '\',\'' . $row->finish_time . '\');">Edit</button>';
            $data[] = $sub_array;
        }
        $output = array(
            "draw" => intval($_POST["draw"]),
            "recordsTotal" => $this->Company_model->get_all_data(),
            "recordsFiltered" => $this->Company_model->get_filtered_data(),
            "data" => $data
        );
        echo json_encode($output);
    }

    public function delete_work_shift()
    {
        $id = $this->input->post("id");
        $result = $this->Company_model->delete($id);
        if ($result) {
            redirect("company/details");
        }
    }

    public function add_work_shift()
    {
        $data = array(
            'name'=>$this->input->post("name"),
            'start_time'=>$this->input->post("start_time"),
            'finish_time'=>$this->input->post("finish_time"),
        );
        $result = $this->Company_model->add($data);
        if ($result) {
            redirect("company/details");
        }
    }

    public function edit_work_shift()
    {
        $id = $this->input->post("id");
        $data = array(
            'name'=>$this->input->post("name"),
            'start_time'=>$this->input->post("start_time"),
            'finish_time'=>$this->input->post("finish_time"),
        );
        $result = $this->Company_model->edit($id, $data);
        if ($result) {
            redirect("company/details");
        }
    }
}

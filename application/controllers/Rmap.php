<?php


class Rmap extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = array(
            'a' => 'a',
        );
        $this->load->view('map/map', $data);
    }

    public function personnel()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/personnel");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "test=test");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        header('Content-Type: application/json');
        print_r($result);
    }

    public function visitor()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1:5000/visitor");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "test=test");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        header('Content-Type: application/json');
        print_r($result);
    }
}

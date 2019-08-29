<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("Users_model");
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
	}

	public function index()
	{
		$this->session->unset_userdata('userdata');
		$this->session->sess_destroy();
		$this->load->view('login');
	}
	public function login()
	{
		$this->session->unset_userdata('userdata');
		$this->session->sess_destroy();
		$this->load->view('login');
	}

	public function auth()
	{
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == false) {
			$this->session->sess_destroy();
			$this->session->set_flashdata('login', 'false');
			redirect(site_url() . 'login');
		} else {
			$email = $this->input->post('email', true);
			$password = $this->input->post('password', true);
			$remember = $this->input->post('remember', true);
			$result = $this->Users_model->login_check($email, $password);
			if ($result['auth'] == 'auth1') {
				$this->session->set_userdata('userdata', $result);
				$this->session->set_flashdata('login', 'true'); //giriş için tek kullanımlık.
				redirect(site_url() . 'dashboard');
			} else {
				$this->session->sess_destroy();
				$this->session->set_flashdata('login', 'false');
				redirect(site_url() . 'login');
			}
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect(site_url() . 'login');
	}
}

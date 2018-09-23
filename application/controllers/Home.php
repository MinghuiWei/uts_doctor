<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        
    }
    
    public function index()
    {
        $this->my_smarty->view('home');
    }
    
    public function login()
    {
        $data = [];
        $data['error_msg'] = '';
        
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Passwrod', 'required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
        } else {
            // post handle
            $user = $this->user_model->login_user($this->input->post('email'), $this->input->post('password'));
            if ($user) {
                // login success then redirect
                $this->session->set_userdata('user', $user);
                redirect('/patient/dashboard');
            } else {
                // login fail then show error msg
                $data['error_msgs'] = 'Invalid email or password.';
            }
        }
        // default return login page
        $this->my_smarty->assign('data', $data)->view('login');
	}
	
	public function signup()
    {
        $data = [];
        $data['error_msg'] = '';
        
        $this->form_validation->set_rules('email', 'Email', 'required');
        $this->form_validation->set_rules('password', 'Passwrod', 'required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
        } else {
            // post handle
            $user = $this->user_model->login_user($this->input->post('email'), $this->input->post('password'));
            if ($user) {
                // login success then redirect
                $this->session->set_userdata('user', $user);
                redirect('/patient/dashboard');
            } else {
                // login fail then show error msg
                $data['error_msgs'] = 'Invalid email or password.';
            }
        }
        // default return login page
        $this->my_smarty->assign('data', $data)->view('signup');
	}
	
	public function logout()
    {
		$this->session->unset_userdata("user");
        redirect("/home/login");
    }
    
}
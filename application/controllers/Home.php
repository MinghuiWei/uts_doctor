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
                redirect($user->type);
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
        
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[user.email]');
        $this->form_validation->set_rules('password', 'Passwrod', 'trim|required|min_length[4]|max_length[20]');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('dob', 'Birthday', 'required');
        $this->form_validation->set_rules('medicareNo', 'Medicare No.', 'required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
        } else {
			$input = array(
				"email" => $this->input->post("email"),
				"password" => $this->input->post("password"),
				"firstname" => $this->input->post("firstname"),
				"lastname" => $this->input->post("lastname"),
				"gender" => $this->input->post("gender"),
				"title" => $this->input->post("title"),
				"dob" => $this->input->post("dob"),
				"phone" => $this->input->post("phone"),
				"address" => $this->input->post("address"),
				"medicareNo" => $this->input->post("medicareNo"),
				"type" => "patient"
			);
            // post handle
			$user = $this->user_model->create_user($input);
            if ($user) {
                // login success then redirect
                $this->session->set_userdata('user', $user);
                redirect('/patient/dashboard');
            } else {
                // login fail then show error msg
                $data['error_msgs'] = 'Oops, please verify all your input and try again.';
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
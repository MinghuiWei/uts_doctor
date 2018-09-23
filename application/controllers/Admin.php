<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        
        if ( !$this->session->userdata('user') || $this->session->userdata('user')->type != 'admin')
        {
            redirect('/home/login');
        }
    }
    
    public function index()
    {
        redirect('/admin/doctors');
    }
    
    public function doctors()
    {
        $data = array('doctors' => $this->user_model->get_all_doctors());
        $this->my_smarty->assign('data', $data)->view('admin/doctors');
    }
    
    public function editDoctor($userId = '')
    {
        $data = array();
        $data['error_msgs'] = '';
        
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('dob', 'Birthday', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[10]|max_length[10]|numeric');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
            if ($userId) {
                $data['doctor'] = $this->user_model->get_user($userId);
            } else {
                $data['doctor'] = $this->user_model->create_empty_user('doctor');
            }
        } else {
            $input = array(
            "userId"=>$userId,
            "email" => $this->input->post("email"),
            "firstname" => $this->input->post("firstname"),
            "lastname" => $this->input->post("lastname"),
            "gender" => $this->input->post("gender"),
            "title" => $this->input->post("title"),
            "dob" => $this->input->post("dob"),
            "phone" => $this->input->post("phone"),
            "address" => $this->input->post("address"),
            "specialty" => $this->input->post("specialty"),
            "verification" => $this->input->post("verification"),
            "type" => "doctor"
            );
            if ($userId) {
                $this->user_model->update_user($input);
                redirect('/admin/doctor/'.$userId);
            } else {
                $user = $this->user_model->create_user($input);
                redirect('/admin/doctor/'.$user->userId);
            }
        }
        $this->my_smarty->assign('data', $data)->view('admin/edit-doctor');
    }
    
    public function doctor($userId) {
        $data = array(
        'doctor' => $this->user_model->get_user($userId)
        );
        $this->my_smarty->assign('data', $data)->view('admin/doctor');
        
    }
    
    public function secretaries()
    {
        $secretaries = $this->user_model->get_all_secretaries();
        foreach($secretaries as $index => $secretary) {
            $secretaries[$index]->doctor =
            $this->user_model->get_user($secretary->doctorId);
        }
        $data = array('secretaries' => $secretaries);
        $this->my_smarty->assign('data', $data)->view('admin/secretaries');
    }
    
    public function secretary($userId) {
        $secretary = $this->user_model->get_user($userId);
        $secretary->doctor = $this->user_model->get_user($secretary->doctorId);
        
        $data = array(
        'secretary' => $secretary
        );
        $this->my_smarty->assign('data', $data)->view('admin/secretary');
    }

    public function editSecretary($userId = '')
    {
        $data = array();
        $data['error_msgs'] = '';
        
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('firstname', 'Firstname', 'required');
        $this->form_validation->set_rules('lastname', 'Lastname', 'required');
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('dob', 'Birthday', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[10]|max_length[10]|numeric');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
            if ($userId) {
                $data['secretary'] = $this->user_model->get_user($userId);
            } else {
                $data['secretary'] = $this->user_model->create_empty_user('secretary');
            }
            $data['doctors'] = $this->user_model->get_all_doctors();
        } else {
            $input = array(
            "userId"=>$userId,
            "email" => $this->input->post("email"),
            "firstname" => $this->input->post("firstname"),
            "lastname" => $this->input->post("lastname"),
            "gender" => $this->input->post("gender"),
            "title" => $this->input->post("title"),
            "dob" => $this->input->post("dob"),
            "phone" => $this->input->post("phone"),
            "address" => $this->input->post("address"),
            "doctorId" => $this->input->post("doctorId"),
            "type" => "secretary"
            );
            if ($userId) {
                $this->user_model->update_user($input);
                redirect('/admin/secretary/'.$userId);
            } else {
                $user = $this->user_model->create_user($input);
                redirect('/admin/secretary/'.$user->userId);
            }
        }
        $this->my_smarty->assign('data', $data)->view('admin/edit-secretary');
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('application_model');
        
        if ( !$this->session->userdata('user') || $this->session->userdata('user')->type != 'patient')
        {
            redirect('/home/login');
        }
    }
    
    public function index()
    {
        $this->my_smarty->view('patient/index');
    }
    
    public function applications()
    {
        $user = $this->session->userdata('user');
        $applications = $this->application_model->get_all_applications($user->userId);
        foreach($applications as $index => $application) {
            $applications[$index]->doctor =
            $this->user_model->get_user($application->doctorId);
        }
        $data = array('applications' => $applications);
        $this->my_smarty->assign('data', $data)->view('patient/applications');
    }
    
    public function deleteApplication($applicationId)
    {
        $this->application_model->delete($applicationId);
        redirect('/patient/applications');
    }
}
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
    
}
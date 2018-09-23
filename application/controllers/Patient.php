<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
    }
    
    public function index()
    {
        $this->my_smarty->view('home');
    }
}

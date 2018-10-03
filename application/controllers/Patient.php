<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Patient extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('application_model');
        $this->load->model('appointment_model');
        
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'gif|jpg|png|docx|pdf|txt|zip';
        $this->load->library('upload', $config);
        
        if ( !$this->session->userdata('user') || $this->session->userdata('user')->type != 'patient')
        {
            redirect('/home/login');
        }
    }
    
    public function index()
    {
        redirect('/patient/applications');
        // $this->my_smarty->view('patient/index');
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
    
    public function editInfo()
    {
        $user = $this->session->userdata('user');
        $data = array();
        $data['error_msgs'] = '';
        
        $this->form_validation->set_rules('gender', 'Gender', 'required');
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[10]|max_length[10]|numeric');
        $this->form_validation->set_rules('medicareNo', 'Medicare Number', 'trim|required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
        } else {
            $input = array(
            "userId" => $user->userId,
            "firstname" => $this->input->post("firstname"),
            "lastname" => $this->input->post("lastname"),
            "gender" => $this->input->post("gender"),
            "title" => $this->input->post("title"),
            "phone" => $this->input->post("phone"),
            "address" => $this->input->post("address"),
            "medicareNo" => $this->input->post("medicareNo"),
            );
            $this->user_model->update_user($input);
            $this->session->set_userdata('user', $this->user_model->get_user($user->userId));
            redirect('/patient');
        }
        $this->my_smarty->assign('data', $data)->view('patient/edit-info');
    }
    
    public function editApplication1($applicationId="")
    {
        $user = $this->session->userdata('user');
        $data = array('applicationId'=>$applicationId);
        $data['error_msgs'] = '';
        $this->form_validation->set_rules('action', 'Action', 'required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
        } else {
            redirect('/patient/editApplication2/'.$applicationId);
        }
        $this->my_smarty->assign('data', $data)->view('patient/edit-application1');
    }
    
    public function editApplication2($applicationId="")
    {
        $user = $this->session->userdata('user');
        $data = array('applicationId'=>$applicationId);
        $data['error_msgs'] = '';
        
        $this->form_validation->set_rules('doctorId', 'Doctor Id', 'trim|required');
        $this->form_validation->set_rules('gp', 'GP', 'trim|required');
        $this->form_validation->set_rules('gpAddress', 'Location of GP', 'trim|required');
        $this->form_validation->set_rules('preferedDays', 'Prefered Days', 'trim|required');
        $this->form_validation->set_rules('preferedTime', 'Prefered Time', 'trim|required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
            if ($applicationId) {
                $data['application'] = $this->application_model->get_application($applicationId);
            } else {
                $data['application'] = $this->application_model->create_empty_application();
            }
            $data['doctors'] = $this->user_model->get_all_doctors();
        } else {
            $action = $this->input->post('action');
            $input = array(
            "applicationId"=>$applicationId,
            "patientId" => $user->userId,
            "gp" => $this->input->post("gp"),
            "gpAddress" => $this->input->post("gpAddress"),
            // "referal" => $this->input->post("referal"),
            // "documents" => $this->input->post("documents"),
            "notes" => $this->input->post("notes"),
            "doctorId" => $this->input->post("doctorId"),
            "appointmentType" => $this->input->post("appointmentType"),
            "appointmentTopics" => $this->input->post("appointmentTopics"),
            "preferedDays" => $this->input->post("preferedDays"),
            "preferedTime" => $this->input->post("preferedTime"),
            "specialRequests" => $this->input->post("specialRequests"),
            "submitted" =>$action == 'submit' ? date('Y-m-d') : NULL,
            "status" => $action == 'submit' ? "Pending" : "Draft"
            );

            $config['upload_path'] = "./uploads";
            $config['allowed_types'] = 'gif|jpg|jpeg|png|docx|pdf|txt|zip';
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('referal'))
            {
                $input['referal'] = $this->upload->data('file_name');
                // var_dump($this->upload->data('file_name'));
            } else {
                // var_dump($this->upload->display_errors());
            }

            if ($this->upload->do_upload('documents'))
            {
                $input['documents'] = $this->upload->data('file_name');
                // var_dump($this->upload->data('file_name'));
            } else {
                // var_dump($this->upload->display_errors());
            }

            if ($applicationId) {
                $this->application_model->update_application($input);
                if ($action == 'continue') {
                    redirect('/patient/editApplication3/'.$applicationId);
                }
                if ($action == 'save') {
                    redirect('/patient/applications/');
                }
            } else {
                $application = $this->application_model->create_application($input);
                if ($action == 'continue') {
                    redirect('/patient/editApplication3/'.$application->applicationId);
                }
                if ($action == 'save') {
                    redirect('/patient/applications/');
                }
            }
        }
        $this->my_smarty->assign('data', $data)->view('patient/edit-application2');
    }
    
    public function editApplication3($applicationId="")
    {
        $user = $this->session->userdata('user');
        $data = array('applicationId' => $applicationId);
        $application = $this->application_model->get_application($applicationId);
        $application->doctor = $this->user_model->get_user($application->doctorId);
        $data['error_msgs'] = '';
        
        $this->form_validation->set_rules('action', 'Action', 'required');
        
        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
            $data['application'] = $application;
        } else {
            $action = $this->input->post('action');
            if ($action == 'save') {
                redirect('/patient/applications/');
            }
            if ($action == 'submit') {
                $this->application_model->update_application(array(
                "applicationId" => $applicationId,
                "status" => "Pending",
                "submitted" => date("Y-m-d")
                ));
                redirect('/patient/applications/');
            }
        }
        $this->my_smarty->assign('data', $data)->view('patient/edit-application3');
    }
    
    public function application($applicationId)
    {
        $user = $this->session->userdata('user');
        $data = array(        );
        
        $application = $this->application_model->get_application($applicationId);
        $application->patient = $this->user_model->get_user($application->patientId);
        $application->doctor = $this->user_model->get_user($application->doctorId);
        $application->appointment = $this->appointment_model->get_appointment_by_application_id($applicationId);
        
        $data['application'] = $application;
        $data['doctor'] = $application->doctor;
        $this->my_smarty->assign('data', $data)->view('patient/application');
    }
    
    public function doctors()
    {
        $data = array('doctors' => $this->user_model->get_all_doctors());
        $this->my_smarty->assign('data', $data)->view('patient/doctors');
    }
    
    public function doctor($doctorId)
    {
        $data = array(
        'doctor' => $this->user_model->get_user($doctorId)
        );
        $this->my_smarty->assign('data', $data)->view('patient/doctor');
    }
    
    public function appointments()
    {
        $user = $this->session->userdata('user');
        $appointments = $this->appointment_model->get_all_appointments($user->userId);
        foreach($appointments as $index => $appointment) {
            $application = $this->application_model->get_application($appointment->applicationId);
            $appointments[$index]->application = $application;
            $appointments[$index]->patient = $this->user_model->get_user($application->patientId);
            $appointments[$index]->doctor = $this->user_model->get_user($application->doctorId);
            $appointments[$index]->application->patient = $appointments[$index]->patient;
            $appointments[$index]->application->doctor = $appointments[$index]->doctor;
        }
        $data['appointments'] = $appointments;
        $this->my_smarty->assign('data', $data)->view('patient/appointments');
    }
    
    public function appointment($appointmentId)
    {
        $data = array();
        $appointment = $this->appointment_model->get_appointment($appointmentId);
        $application = $this->application_model->get_application($appointment->applicationId);
        $application->patient = $this->user_model->get_user($application->patientId);
        $application->doctor = $this->user_model->get_user($application->doctorId);
        $appointment->patient = $application->patient;
        $appointment->doctor = $application->doctor;
        $data['application'] = $application;
        $data['appointment'] = $appointment;
        $this->my_smarty->assign('data', $data)->view('patient/appointment');
    }
}
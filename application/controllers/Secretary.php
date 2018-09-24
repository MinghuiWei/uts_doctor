<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Secretary extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('application_model');
        $this->load->model('appointment_model');
        $this->load->model('schedule_model');
        
        if ( !$this->session->userdata('user') || $this->session->userdata('user')->type != 'secretary')
        {
            redirect('/home/login');
        }
    }
    
    public function index()
    {
        redirect('/secretary/doctors');
    }
    
    public function applications($doctorId="")
    {
        $user = $this->user_model->get_user($doctorId);
        $data = array('doctor' => $user);
        $applications = $this->application_model->get_all_doctor_applications($doctorId);
        foreach($applications as $index => $application) {
            $applications[$index]->patient = $this->user_model->get_user($application->patientId);
            $applications[$index]->doctor = $this->user_model->get_user($application->doctorId);
        }
        $data['applications'] = $applications;
        $this->my_smarty->assign('data', $data)->view('secretary/applications');
    }
    
    public function appointments($doctorId="")
    {
        $user = $this->user_model->get_user($doctorId);
        $data = array('doctor' => $user);
        $appointments = $this->appointment_model->get_all_doctor_appointments($doctorId);
        foreach($appointments as $index => $appointment) {
            $application = $this->application_model->get_application($appointment->applicationId);
            $appointments[$index]->application = $application;
            $appointments[$index]->patient = $this->user_model->get_user($application->patientId);
            $appointments[$index]->doctor = $this->user_model->get_user($application->doctorId);
            $appointments[$index]->application->patient = $appointments[$index]->patient;
            $appointments[$index]->application->doctor = $appointments[$index]->doctor;
        }
        $data['appointments'] = $appointments;
        $this->my_smarty->assign('data', $data)->view('secretary/appointments');
    }
    
    public function schedules($doctorId) {
        $user = $this->user_model->get_user($doctorId);
        $schedules = $this->schedule_model->get_all_schedules($doctorId);
        $data = array('schedules' => $schedules, 'doctor' => $user);
        $this->my_smarty->assign('data', $data)->view('secretary/schedules');
    }
    
    public function application($applicationId)
    {
        $user = $this->session->userdata('user');
        $time_option = array();
        for ($i = 8; $i <= 9; $i++) {
            array_push($time_option, "0$i".":00:00");
            array_push($time_option, "0$i".":30:00");
        }
        for ($i = 10; $i <= 17; $i++) {
            array_push($time_option, "$i".":00:00");
            array_push($time_option, "$i".":30:00");
        }
        $data = array(
        'error_msgs' => '',
        'time_option' => $time_option
        );
        
        $action = $this->input->post("action");
        
        $this->form_validation->set_rules('action', 'Action', 'trim|required');
        if ($action == 'create') {
            $this->form_validation->set_rules('date', 'Date', 'trim|required');
            $this->form_validation->set_rules('startTime', 'Start Time', 'trim|required');
            $this->form_validation->set_rules('endTime', 'End Time', 'trim|required');
        }
        if ($action == 'reject') {
            $this->form_validation->set_rules('reason', 'Reason', 'trim|required');
        }
        
        $application = $this->application_model->get_application($applicationId);
        $application->patient = $this->user_model->get_user($application->patientId);
        $application->doctor = $this->user_model->get_user($application->doctorId);
        $application->appointment = $this->appointment_model->get_appointment_by_application_id($applicationId);

        if ($this->form_validation->run() === false) {
            $data['application'] = $application;
            $data['doctor'] = $application->doctor;
            $this->my_smarty->assign('data', $data)->view('secretary/application');
        } else {
            if ($action == 'create') {
                $input = array(
                "secretaryId" => $user->userId,
                "date" => $this->input->post("date"),
                "startTime" => $this->input->post("startTime"),
                "endTime" => $this->input->post("endTime"),
                "applicationId" => $applicationId,
                "patientId" => $application->patientId,
                "doctorId" => $application->doctorId,
                "status" => "Confirmed"
                );
                $this->appointment_model->create_appointment($input);
                
                $input = array(
                "applicationId" => $applicationId,
                "status" => "Arranged"
                );
                $this->application_model->update_application($input);
                
                redirect('/secretary/appointments/'.$application->doctorId);
            }
            if ($action == 'reject') {
                $input = array(
                "applicationId" => $applicationId,
                "reason" => $this->input->post("reason"),
                "status" => "Rejected"
                );
                $this->application_model->update_application($input);
                redirect('/secretary/appointments/'.$application->doctorId);
            }
        }
    }
    
    public function cancelAppointment($appointmentId)
    {
        $appointment = $this->appointment_model->get_appointment($appointmentId);
        $input = array(
        'appointmentId' => $appointmentId,
        'status' => 'Cancelled'
        );
        $this->appointment_model->update_appointment($input);
        redirect('/secretary/appointments/'.$appointment->doctorId);
    }
    
    public function appointment($appointmentId)
    {
        $user = $this->session->userdata('user');
        $time_option = array();
        for ($i = 8; $i <= 9; $i++) {
            array_push($time_option, "0$i".":00:00");
            array_push($time_option, "0$i".":30:00");
        }
        for ($i = 10; $i <= 17; $i++) {
            array_push($time_option, "$i".":00:00");
            array_push($time_option, "$i".":30:00");
        }
        $data = array(
        'error_msgs' => '',
        'time_option' => $time_option
        );
        
        $action = $this->input->post("action");
        
        $this->form_validation->set_rules('action', 'Action', 'trim|required');
        if ($action == 'create') {
            $this->form_validation->set_rules('date', 'Date', 'trim|required');
            $this->form_validation->set_rules('startTime', 'Start Time', 'trim|required');
            $this->form_validation->set_rules('endTime', 'End Time', 'trim|required');
        }
        
        $appointment = $this->appointment_model->get_appointment($appointmentId);
        $application = $this->application_model->get_application($appointment->applicationId);
        $application->patient = $this->user_model->get_user($application->patientId);
        $application->doctor = $this->user_model->get_user($application->doctorId);
        $appointment->patient = $application->patient;
        $appointment->doctor = $application->doctor;
        
        if ($this->form_validation->run() === false) {
            $data['application'] = $application;
            $data['appointment'] = $appointment;
            $data['doctor'] = $appointment->doctor;
            $this->my_smarty->assign('data', $data)->view('secretary/appointment');
        } else {
            $input = array(
            "appointmentId" => $appointmentId,
            "date" => $this->input->post("date"),
            "startTime" => $this->input->post("startTime"),
            "endTime" => $this->input->post("endTime"),
            "status" => "Confirmed"
            );
            $this->appointment_model->update_appointment($input);
            
            $input = array(
            "applicationId" => $applicationId,
            "status" => "Arranged"
            );
            $this->application_model->update_application($input);
            
            redirect('/secretary/appointments/'.$appointment->doctorId);
        }
    }
    
    public function doctors()
    {
        $data = array('doctors' => $this->user_model->get_all_doctors());
        $this->my_smarty->assign('data', $data)->view('secretary/doctors');
    }
    
    public function doctor($doctorId)
    {
        $data = array(
        'doctor' => $this->user_model->get_user($doctorId)
        );
        $this->my_smarty->assign('data', $data)->view('secretary/doctor');
    }
    
}
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
        redirect('/secretary/applications');
    }

    public function applications()
    {
        $user = $this->session->userdata('user');
        $applications = $this->application_model->get_all_doctor_applications($user->doctorId);
        foreach($applications as $index => $application) {
            $applications[$index]->patient = $this->user_model->get_user($application->patientId);
            $applications[$index]->doctor = $this->user_model->get_user($application->doctorId);
        }
        $data = array('applications' => $applications);
        $this->my_smarty->assign('data', $data)->view('secretary/applications');
    }
    
    public function appointments()
    {
        $user = $this->session->userdata('user');
        $data = array();
        $appointments = $this->appointment_model->get_all_doctor_appointments($user->doctorId);
        foreach($appointments as $index => $appointment) {
            $application = $this->application_model->get_application($appointment->applicationId);
            $appointments[$index]->application = $application;
            $appointments[$index]->patient = $this->user_model->get_user($application->patientId);
            $appointments[$index]->doctor = $this->user_model->get_user($application->doctorId);
            $appointments[$index]->application->patient = $appointments[$index]->patient;
            $appointments[$index]->application->doctor = $appointments[$index]->doctor;
        }
        $data = array('appointments' => $appointments);
        $this->my_smarty->assign('data', $data)->view('secretary/appointments');
    }
    
    public function schedules() {
        $user = $this->session->userdata('user');
        $schedules = $this->schedule_model->get_all_schedules($user->doctorId);

        $data = array('schedules' => $schedules, 'doctor' => $this->user_model->get_user($user->doctorId));
        $this->my_smarty->assign('data', $data)->view('secretary/schedules');
    }

    public function application($applicationId)
    {
        $user = $this->session->userdata('user');
        $time_option = array();
        for ($i = 8; $i <= 17; $i++) {
            array_push($time_option, "$i".":00");
            array_push($time_option, "$i".":30");
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

        if ($this->form_validation->run() === false) {
            $data['application'] = $application;
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
                    "status" => "Approved"
                );
                $this->application_model->update_application($input);

                redirect('/secretary/appointments');
            }
            if ($action == 'reject') {
                $input = array(
                    "applicationId" => $applicationId,
                    "reason" => $this->input->post("reason"),
                    "status" => "Rejected"
                );
                $this->application_model->update_application($input);
                redirect('/secretary/applications');
            }

        }
    }

    // public function editSchedule($scheduleId = "")
    // {
    //     $user = $this->session->userdata('user');
    //     $time_option = array();
    //     for ($i = 8; $i <= 17; $i++) {
    //         array_push($time_option, "$i".":00");
    //         array_push($time_option, "$i".":30");
    //     }
    //     $data = array(
    //         'scheduleId' => $scheduleId,
    //         'day_option' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
    //         'time_option' => $time_option
    //     );
    //     $data['error_msgs'] = '';

    //     $this->form_validation->set_rules('day', 'Day', 'trim|required');
    //     $this->form_validation->set_rules('startTime', 'Start Time', 'trim|required');
    //     $this->form_validation->set_rules('endTime', 'End Time', 'trim|required');

    //     if ($this->form_validation->run() === false) {
    //         // default return
    //         $data['error_msgs'] = validation_errors();
    //         if ($scheduleId) {
    //             $data['schedule'] = $this->schedule_model->get_schedule($scheduleId);
    //         } else {
    //             $data['schedule'] = $this->schedule_model->create_empty_schedule();
    //         }
    //     } else {
    //         $input = array(
    //             "scheduleId" => $scheduleId,
    //             "day" => $this->input->post("day"),
    //             "startTime" => $this->input->post("startTime"),
    //             "endTime" => $this->input->post("endTime"),
    //             "doctorId" => $user->userId,
    //         );
    //         if ($scheduleId) {
    //             $this->schedule_model->update_schedule($input);
    //             redirect('/doctor/schedules/');
    //         } else {
    //             $schedule = $this->schedule_model->create_schedule($input);
    //             redirect('/doctor/schedules/');
    //         }
    //     }
    //     $this->my_smarty->assign('data', $data)->view('doctor/edit-schedule');
    // }
    
}
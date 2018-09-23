<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('application_model');
        $this->load->model('schedule_model');
        
        if ( !$this->session->userdata('user') || $this->session->userdata('user')->type != 'doctor')
        {
            redirect('/home/login');
        }
    }
    
    public function index()
    {
        redirect('/doctor/appointments');
    }
    
    public function appointments()
    {
        $user = $this->session->userdata('user');
        $applications = $this->application_model->get_all_doctor_applications($user->userId);
        foreach($applications as $index => $application) {
            $applications[$index]->patient = $this->user_model->get_user($application->patientId);
        }
        $data = array('applications' => $applications);
        $this->my_smarty->assign('data', $data)->view('doctor/appointments');
    }
    
    public function schedules() {
        $user = $this->session->userdata('user');
        $schedules = $this->schedule_model->get_all_schedules($user->userId);

        $data = array('schedules' => $schedules);
        $this->my_smarty->assign('data', $data)->view('doctor/schedules');
    }

    public function deleteSchedule($applicationId)
    {
        $this->schedule_model->delete($applicationId);
        redirect('/patient/applications');
    }
    
    public function editSchedule($scheduleId = "")
    {
        $user = $this->session->userdata('user');
        $time_option = array();
        for ($i = 8; $i <= 17; $i++) {
            array_push($time_option, "$i".":00");
            array_push($time_option, "$i".":30");
        }
        $data = array(
            'scheduleId' => $scheduleId,
            'day_option' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'),
            'time_option' => $time_option
        );
        $data['error_msgs'] = '';

        $this->form_validation->set_rules('day', 'Day', 'trim|required');
        $this->form_validation->set_rules('startTime', 'Start Time', 'trim|required');
        $this->form_validation->set_rules('endTime', 'End Time', 'trim|required');

        if ($this->form_validation->run() === false) {
            // default return
            $data['error_msgs'] = validation_errors();
            if ($scheduleId) {
                $data['schedule'] = $this->schedule_model->get_schedule($scheduleId);
            } else {
                $data['schedule'] = $this->schedule_model->create_empty_schedule();
            }
        } else {
            $input = array(
                "scheduleId" => $scheduleId,
                "day" => $this->input->post("day"),
                "startTime" => $this->input->post("startTime"),
                "endTime" => $this->input->post("endTime"),
                "doctorId" => $user->userId,
            );
            if ($scheduleId) {
                $this->schedule_model->update_schedule($input);
                redirect('/doctor/schedules/');
            } else {
                $schedule = $this->schedule_model->create_schedule($input);
                redirect('/doctor/schedules/');
            }
        }
        $this->my_smarty->assign('data', $data)->view('doctor/edit-schedule');
    }
    
    // public function editApplication1($applicationId="")
    // {
    //     $user = $this->session->userdata('user');
    //     $data = array('applicationId'=>$applicationId);
    //     $data['error_msgs'] = '';
    //     $this->form_validation->set_rules('action', 'Action', 'required');
        
    //     if ($this->form_validation->run() === false) {
    //         // default return
    //         $data['error_msgs'] = validation_errors();
    //     } else {
    //         redirect('/patient/editApplication2/'.$applicationId);
    //     }
    //     $this->my_smarty->assign('data', $data)->view('patient/edit-application1');
    // }
    
    // public function editApplication2($applicationId="")
    // {
    //     $user = $this->session->userdata('user');
    //     $data = array('applicationId'=>$applicationId);
    //     $data['error_msgs'] = '';
        
    //     $this->form_validation->set_rules('doctorId', 'Doctor Id', 'trim|required');
    //     $this->form_validation->set_rules('gp', 'GP', 'trim|required');
    //     $this->form_validation->set_rules('gpAddress', 'Location of GP', 'trim|required');
    //     $this->form_validation->set_rules('preferedDays', 'Prefered Days', 'trim|required');
    //     $this->form_validation->set_rules('preferedTime', 'Prefered Time', 'trim|required');
        
    //     if ($this->form_validation->run() === false) {
    //         // default return
    //         $data['error_msgs'] = validation_errors();
    //         if ($applicationId) {
    //             $data['application'] = $this->application_model->get_application($applicationId);
    //         } else {
    //             $data['application'] = $this->application_model->create_empty_application();
    //         }
    //         $data['doctors'] = $this->user_model->get_all_doctors();
    //     } else {
    //         $action = $this->input->post('action');
    //         $input = array(
    //         "applicationId"=>$applicationId,
    //         "patientId" => $user->userId,
    //         "gp" => $this->input->post("gp"),
    //         "gpAddress" => $this->input->post("gpAddress"),
    //         "referal" => $this->input->post("referal"),
    //         "documents" => $this->input->post("documents"),
    //         "notes" => $this->input->post("notes"),
    //         "doctorId" => $this->input->post("doctorId"),
    //         "appointmentType" => $this->input->post("appointmentType"),
    //         "appointmentTopics" => $this->input->post("appointmentTopics"),
    //         "preferedDays" => $this->input->post("preferedDays"),
    //         "preferedTime" => $this->input->post("preferedTime"),
    //         "specialRequests" => $this->input->post("specialRequests"),
    //         "status" => $action == 'submit' ? "Pending" : "Draft"
    //         );
    //         if ($applicationId) {
    //             $this->application_model->update_application($input);
    //             if ($action == 'continue') {
    //                 redirect('/patient/editApplication3/'.$applicationId);
    //             }
    //             if ($action == 'save') {
    //                 redirect('/patient/applications/');
    //             }
    //         } else {
    //             $application = $this->application_model->create_application($input);
    //             if ($action == 'continue') {
    //                 redirect('/patient/editApplication3/'.$application->applicationId);
    //             }
    //             if ($action == 'save') {
    //                 redirect('/patient/applications/');
    //             }
    //         }
    //     }
    //     $this->my_smarty->assign('data', $data)->view('patient/edit-application2');
    // }
    
    // public function editApplication3($applicationId="")
    // {
    //     $user = $this->session->userdata('user');
    //     $data = array('applicationId' => $applicationId);
    //     $application = $this->application_model->get_application($applicationId);
    //     $application->doctor = $this->user_model->get_user($application->doctorId);
    //     $data['error_msgs'] = '';
        
    //     $this->form_validation->set_rules('action', 'Action', 'required');
        
    //     if ($this->form_validation->run() === false) {
    //         // default return
    //         $data['error_msgs'] = validation_errors();
    //         $data['application'] = $application;
    //     } else {
    //         $action = $this->input->post('action');
    //         if ($action == 'save') {
    //             redirect('/patient/applications/');
    //         }
    //         if ($action == 'submit') {
    //             $this->application_model->update_application(array(
    //                 "applicationId" => $applicationId,
    //                 "status" => "Pending"
    //             ));
    //             redirect('/patient/applications/');
    //         }
    //     }
    //     $this->my_smarty->assign('data', $data)->view('patient/edit-application3');
    // }
}
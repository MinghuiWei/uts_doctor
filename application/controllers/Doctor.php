<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('application_model');
        $this->load->model('appointment_model');
        $this->load->model('schedule_model');
        
        $config = array(
        'protocol' => 'smtp',
        'smtp_host' => 'ssl://smtp.googlemail.com',
        'smtp_port' => 465,
        'smtp_user' => 'uts.hosipital.system@gmail.com',
        'smtp_pass' => 'Uts12345',
        'newline' => "\r\n",
        );
        $this->load->library('email', $config);
        
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
        $data = array('doctor' => $this->user_model->get_user($user->userId));
        $appointments = $this->appointment_model->get_all_doctor_appointments($user->userId);
        foreach($appointments as $index => $appointment) {
            $application = $this->application_model->get_application($appointment->applicationId);
            $appointments[$index]->application = $application;
            $appointments[$index]->patient = $this->user_model->get_user($application->patientId);
            $appointments[$index]->doctor = $this->user_model->get_user($application->doctorId);
            $appointments[$index]->application->patient = $appointments[$index]->patient;
            $appointments[$index]->application->doctor = $appointments[$index]->doctor;
        }
        $data['appointments'] = $appointments;
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
            if ($i < 10) {
                array_push($time_option, "0"."$i".":00:00");
                array_push($time_option, "0"."$i".":30:00");
            } else {
                array_push($time_option, "$i".":00:00");
                array_push($time_option, "$i".":30:00");
            }
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
    
    public function cancelAppointment($appointmentId)
    {
        $appointment = $this->appointment_model->get_appointment($appointmentId);
        $input = array(
        'appointmentId' => $appointmentId,
        'status' => 'Cancelled'
        );
        $this->appointment_model->update_appointment($input);

        $patient = $this->user_model->get_user($appointment->patientId);
        $this->email->from('uts.hosipital.system@gmail.com');
        $this->email->to($patient->email);
        $this->email->subject('Your appointment has been cancelled');
        $this->email->message('Dear '. $patient->title . ' ' . $patient->firstname . ' ' . $patient->lastname . ', your appointment #' . $appointmentId . ' has been approved. please login to see more deail');
        $this->email->send();

        redirect('/doctor/appointments');
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
        
        $appointment = $this->appointment_model->get_appointment($appointmentId);
        $application = $this->application_model->get_application($appointment->applicationId);
        $application->patient = $this->user_model->get_user($application->patientId);
        $application->doctor = $this->user_model->get_user($application->doctorId);
        $appointment->patient = $application->patient;
        $appointment->doctor = $application->doctor;
        
        if ($this->form_validation->run() === false) {
            $data['application'] = $application;
            $data['appointment'] = $appointment;
            $this->my_smarty->assign('data', $data)->view('doctor/appointment');
        } else {
            $input = array(
            "appointmentId" => $appointmentId,
            "notes" => $this->input->post("notes"),
            );
            $this->appointment_model->update_appointment($input);
            redirect('/doctor/appointments');
        }
    }
}
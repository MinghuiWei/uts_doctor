<?php

/**
*
*/
class Appointment_model extends CI_Model
{
    public $TABLE = "appointment";
    var $appointmentId = '';
    var $applicationId = '';
    var $date =  '';
    var $startTime =  '';
    var $endTime =  '';
    var $secretaryId = '';
    var $status = '';
    var $notes = '';
    
    public function get_all_appointments($patiendId)
    {
        $query = $this->db->get_where($this->TABLE, array('patientId' => $patiendId));
        $result = $query->result();
        return $result;
    }
    
    public function get_all_doctor_appointments($doctorId)
    {
        $query = $this->db->get_where($this->TABLE, array('doctorId' => $doctorId));
        $result = $query->result();
        return $result;
    }

    public function get_appointment($appointmentId)
    {
        $query = $this->db->get_where($this->TABLE, array('appointmentId' => $appointmentId));
        $result = $query->row();
        return $result;
    }

    public function get_appointment_by_application_id($applicationId)
    {
        $query = $this->db->get_where($this->TABLE, array('applicationId' => $applicationId));
        $result = $query->row();
        return $result;
    }
    
    public function create_appointment($appointment)
    {
        $this->db->insert($this->TABLE, $appointment);
        return $this->db->get_where($this->TABLE, array('appointmentId' => $this->db->insert_id()))->row();
    }
    
    public function update_appointment($appointment)
    {
        $this->db->where('appointmentId', $appointment['appointmentId']);
        $this->db->update($this->TABLE, $appointment);
        // return $this->db->replace($this->TABLE, $appointment);
    }
    
    public function create_empty_appointment() {
        $appointment = new Appointment_model();
        return $appointment;
    }
    
    public function delete($appointmentId) {
        $this->db->delete($this->TABLE, array('appointmentId' => $appointmentId));
    }
    
}
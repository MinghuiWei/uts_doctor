<?php

/**
*
*/
class Application_model extends CI_Model
{
    public $TABLE = "Application";
    var $applicationId = '';
    var $gp =  '';
    var $gpAddress =  '';
    var $referal =  '';
    var $notes = '';
    var $documents = '';
    var $appointmentType = '';
    var $appointmentTopics = '';
    var $preferedDays = '';
    var $preferedTime = '';
    var $specialRequests = '';
    var $patiendId = '';
    var $doctorId = '';
    var $submitted = '';
    
    public function get_all_applications($patiendId)
    {
        $query = $this->db->get_where($this->TABLE, array('patientId' => $patiendId));
        $result = $query->result();
        return $result;
    }
    
    public function get_all_doctor_applications($doctorId)
    {
        $query = $this->db->get_where($this->TABLE, array('doctorId' => $doctorId));
        $result = $query->result();
        return $result;
    }
    

    public function get_application($applicationId)
    {
        $query = $this->db->get_where($this->TABLE, array('applicationId' => $applicationId));
        $result = $query->row();
        return $result;
    }
    
    public function create_application($application)
    {
        $this->db->insert($this->TABLE, $application);
        return $this->db->get_where($this->TABLE, array('applicationId' => $this->db->insert_id()))->row();
    }
    
    public function update_application($application)
    {
        $this->db->where('applicationId', $application['applicationId']);
        $this->db->update($this->TABLE, $application);
        // return $this->db->replace($this->TABLE, $application);
    }
    
    public function create_empty_application() {
        $application = new Application_model();
        return $application;
    }
    
    public function delete($applicationId) {
        $this->db->delete($this->TABLE, array('applicationId' => $applicationId));
    }
    
}
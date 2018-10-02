<?php

/**
*
*/
class Schedule_model extends CI_Model
{
    public $TABLE = "schedule";
    var $scheduleId = '';
    var $doctorId =  '';
    var $day =  '';
    var $startTime =  '';
    var $endTime = '';
    
    public function get_all_schedules($doctorId)
    {
        $this->db->from($this->TABLE);
        $this->db->where('doctorId', $doctorId);
        $this->db->order_by("day ASC, startTime ASC");
        $query = $this->db->get();
        $result = $query->result();
        return $result;
    }
    
    public function get_schedule($scheduleId)
    {
        $query = $this->db->get_where($this->TABLE, array('scheduleId' => $scheduleId));
        $result = $query->row();
        return $result;
    }
    
    public function create_schedule($schedule)
    {
        $this->db->insert($this->TABLE, $schedule);
        return $this->db->get_where($this->TABLE, array('scheduleId' => $this->db->insert_id()))->row();
    }
    
    public function update_schedule($schedule)
    {
        $this->db->where('scheduleId', $schedule['scheduleId']);
        $this->db->update($this->TABLE, $schedule);
    }
    
    public function create_empty_schedule() {
        $schedule = new schedule_model();
        return $schedule;
    }
    
    public function delete($scheduleId) {
        $this->db->delete($this->TABLE, array('scheduleId' => $scheduleId));
    }
    
}
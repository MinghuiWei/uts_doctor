<?php

/**
*
*/
class User_model extends CI_Model
{
    public $TABLE = "user";
    var $firstname = '';
    var $lastname =  '';
    var $gender =  'Male';
    var $title =  'Mr.';
    var $dob = '';
    var $email = '';
    var $phone = '';
    var $password = '';
    var $address = '';
    var $type = '';
    var $verification = '';
    var $specialty = '';
    var $medicareNo = '';
    var $doctorId = '';
    
    public function get_all_doctors()
    {
        $query = $this->db->get_where($this->TABLE, array('type' => 'doctor'));
        $result = $query->result();
        return $result;
    }
    
    public function get_all_secretaries()
    {
        $query = $this->db->get_where($this->TABLE, array('type' => 'secretary'));
        $result = $query->result();
        return $result;
    }
    
    public function get_user($user_id)
    {
        $query = $this->db->get_where($this->TABLE, array('userId' => $user_id));
        $result = $query->row();
        return $result;
    }
    
    public function create_user($user)
    {
        $this->db->insert($this->TABLE, $user);
        return $this->db->get_where($this->TABLE, array('userId' => $this->db->insert_id()))->row();
    }

    public function update_user($user)
    {
        $this->db->where('userId', $user['userId']);
        $this->db->update($this->TABLE, $user);
        // return $this->db->replace($this->TABLE, $user);            
    }

    
    public function create_empty_user($type) {
        $user = new User_model();
        $user->type = $type;
        return $user;
    }
    
    public function login_user($email, $password)
    {
        if (!$email && $password) {
            return null;
        }
        $user = $this->db->get_where($this->TABLE,
        array('email' => $email, 'password' => $password))->row(0);
        
        return $user;
    }
}
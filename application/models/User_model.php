<?php

/**
 *
 */
class User_model extends CI_Model
{
    public $TABLE = "user";

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

<?php

/**
 *
 */
class User_model extends CI_Model
{
    public $TABLE = "user";

    public function get_all()
    {
        $query = $this->db->get_where($this->TABLE, array('visible' => 1));
        $result = $query->result_array();
        return $result;
    }

    public function create_user($user)
    {
        var_dump($user);
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

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

    public function create_user($user, $type="patient")
    {
        $this->db->insert($TABLE = "users", [
            'firstname' => $user['firstname'],
            'lastname' => $user['last_name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'title' => $user['title'],
            'dob' => $user['dob'],
            'gender' => $user['gender'],
            'address' => $user['address'],
            'medicareNo' => $user['medicareNo'],
            'patient' => $type,
        ]);
        return $this->db->get_where($this->$TABLE,
            ['id' => $this->db->insert_id()])->row();
    }

    public function login_user($email, $password)
    {
        if (!$email && $password) {
            return null;
        }
        // $this->db->where(['username' => $username]);
        // $user = $this->db->get($this->TABLE)->row(0);

        $user = $this->db->get_where($this->TABLE,
            array('email' => $email, 'password' => $password))->row(0);

        return $user;
    }
}

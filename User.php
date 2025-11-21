<?php

class User {
    private $file = "data/users.json";

    private function loadUsers() {
        if(!file_exists($this->file)) {
            return[];
        }

        $json = file_get_contents($this->file);
        return json_decode($json, true);
    }

    private function saveUsers($users) {
        file_put_contents($this->file, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function register($name, $email, $password) {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return false; // Email already exists
            }
        }

        $users[] = [
            "name" => $name,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "created_at" => date('Y-m-d H:i:s')
        ];

        $this->saveUsers($users);
        return true;
    }

    public function login($email, $password) {
        $users = $this->loadUsers();

        foreach ($users as $user) {
            if ($email == $user["email"] && password_verify($password, $user["password"])) {
                return $user;
            }
        }

        return false;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    const CODE_SUCCESS = 1;
    const CODE_ERROR = 0;
    const RESULT_REGISTRATION_SUCCESS = "Registration success";
    const RESULT_REGISTRATION_FAILED = "Registration success";
    const RESULT_WRONG_PASSWORD = "Password didn't match";
    const RESULT_LOGIN_FAILED = "Login failed";
    const RESULT_LOGIN_SUCCESS = "Login success";
    const RESULT_LOGOUT_FAILED = "Logout failed";
    const RESULT_LOGOUT_SUCCESS = "Logout success";
    const RESULT_USER_NOT_FOUND = "Account not found";
    const RESULT_TOKEN_NOT_FOUND = "Auth token not found";

    protected $table = 'users';

    public function register($userData)
    {
        try {
            $user = DB::table($this->table)->where('email', $userData['email'])->first();
            if ($user != NULL) {
                return array(self::CODE_ERROR, "Alamat Email yang Anda masukkan sudah digunakan untuk registrasi", NULL);
            }

            $salt = str_random(32);

            $id = DB::table($this->table)->insertGetId(
                ['name' => $userData['name'],
                    'email' => $userData['email'],
                    'gender' => $userData['gender'],
                    'encrypted_password' => hash('sha256', $salt . hash('md5', $userData['password'] . $salt)),
                    'salt' => $salt,
                    'is_login' => FALSE,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s")
                ]
            );

            return array(self::CODE_SUCCESS, self::RESULT_REGISTRATION_SUCCESS, $id);
        } catch (QueryException $ex) {
            return array(self::CODE_ERROR, self::RESULT_REGISTRATION_FAILED, $ex->getMessage());
        }
    }

    public function login($userData)
    {
        try {
            $user = NULL;
            if ($userData['email'] != NULL) {
                $user = DB::table($this->table)->where('email', $userData['email'])->first();
                if (empty($user)) {
                    return array(self::CODE_ERROR, self::RESULT_USER_NOT_FOUND, NULL, NULL);
                }
            }

            if ($user != NULL && $user->encrypted_password == hash('sha256', $user->salt . hash('md5', $userData['password'] . $user->salt))) {
                $authToken = str_random(32);
                DB::table($this->table)
                    ->where('email', $userData['email'])
                    ->update(
                        ['is_login' => TRUE,
                            'updated_at' => date("Y-m-d H:i:s"),
                            'auth_token' => $authToken
                        ]);
                $user->auth_token = $authToken;
                return array(self::CODE_SUCCESS, self::RESULT_LOGIN_SUCCESS, NULL, $user);
            } else {
                return array(self::CODE_ERROR, self::RESULT_WRONG_PASSWORD, NULL, NULL);
            }
        } catch (QueryException $ex) {
            return array(self::CODE_ERROR, self::RESULT_LOGIN_FAILED, $ex->getMessage(), NULL);
        }
    }

    public function logout($token)
    {
        try {
            if ($token != NULL) {
                $result = DB::table($this->table)
                    ->where('auth_token', $token)
                    ->update(
                        ['is_login' => FALSE,
                            'updated_at' => date("Y-m-d H:i:s"),
                            'auth_token' => NULL
                        ]);

                if ($result == self::CODE_SUCCESS) {
                    return array(self::CODE_SUCCESS, self::RESULT_LOGOUT_SUCCESS, NULL);
                } else {
                    return array(self::CODE_ERROR, self::RESULT_TOKEN_NOT_FOUND, NULL);
                }
            }
        } catch (QueryException $ex) {
            return array(self::CODE_ERROR, self::RESULT_LOGOUT_FAILED, $ex->getMessage());
        }
    }

}

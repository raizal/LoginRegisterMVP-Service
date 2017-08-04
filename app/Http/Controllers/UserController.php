<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;

/**
 * Class UserController
 * @package TATravel\Http\Controllers
 */
class UserController extends BaseController
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|max:100',
            'gender' => 'required|max:1',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $this->returnJsonErrorDataNotValid($validator->errors());
        }

        $userData['name'] = $request->request->get('name');
        $userData['email'] = $request->request->get('email');
        $userData['gender'] = $request->request->get('gender');
        $userData['password'] = $request->request->get('password');

        if ($userData['gender'] != "M" && $userData['gender'] != "F") {
            $this->returnJsonErrorDataNotValid("Gender must be 'M' of 'F'");
        }

        $user = new User();
        list($status, $message, $technicalMessage) = $user->register($userData);
        $this->returnJson($status, $message, $technicalMessage, null);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $this->returnJsonErrorDataNotValid($validator->errors());
        }

        $userData['email'] = $request->request->get('email');
        $userData['password'] = $request->request->get('password');

        $user = new User();
        list($status, $message, $technicalMessage, $data) = $user->login($userData);
        $this->returnJson($status, $message, $technicalMessage, $data);
    }

    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            $this->returnJsonErrorDataNotValid($validator->errors());
        }

        $token = $request->request->get('token');
        $user = new User();
        list($status, $message, $technicalMessage) = $user->logout($token);
        $this->returnJson($status, $message, $technicalMessage, null);
    }

}

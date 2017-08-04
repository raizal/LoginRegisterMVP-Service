<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Response;

class BaseController extends Controller
{

    const CODE_SUCCESS = 1;
    const CODE_ERROR = 0;

    protected function returnJson($code, $message, $technicalMessage, $data)
    {
        $result = array();
        $result['code'] = $code;
        $result['message'] = $message;
        if (config('app.debug')) {
            $result['debugMessage'] = $technicalMessage;
        }
        $result['data'] = $data;

        header('Content-Type: application/json');
        echo(json_encode($result, JSON_PRETTY_PRINT));
        die;
    }

    protected function returnJsonErrorDataNotValid($errorMessage)
    {
        $result = array();
        $result['code'] = self::CODE_ERROR;
        $result['message'] = "Data yang dikirim tidak valid";
        if (config('app.debug')) {
            $result['debugMessage'] = $errorMessage;
        }

        header('Content-Type: application/json');
        echo(json_encode($result, JSON_PRETTY_PRINT));
        die;
    }

}

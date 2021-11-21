<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    //

    public $code;
    public $message;
    public $data;
    public $status;

    public function getReturn(): JsonResponse
    {
        return new JsonResponse([
            'status' => [
                'code' => $this->code,
                'message' => $this->message
            ],
            'data' => $this->data
        ], $this->status);
    }

    public function setData($data = null)
    {
        $this->data = $data;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setCode($code)
    {
        $this->code = (int)$code;
    }



    public function responseSuccess($message = null, $data = null, $status = 200): JsonResponse
    {
        $this->setCode(env('STATUS_SUCCESS_CODE'));

        if ($data != null) {
            $this->setData($data);
        }

        if ($message != null) {
            $this->setMessage($message);
        }

        $this->setStatus($status);

        return $this->getReturn();
    }
}

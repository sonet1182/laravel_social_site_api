<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Message
{

    protected $status = false;
    protected $message = '';
    protected $url = false;
    protected $api_token = NULL;
    protected $data = [];



    /**
     * Success  Function For API
     * Set api response status as Success
     * This Method is responsible all API Response
     */
    protected function apiSuccess($message = NULL, $data = NULL)
    {
        $this->status = true;
        $this->message = !empty($message) ? $message : 'Successfully';
        if (isset($data) && !is_null($data)) {
            $this->data = $data;
        }
    }

    /**
     * Return Default API Output Message
     * This Method for API Response
     */
    protected function apiOutput($message = NULL, $code = 200)
    {
        $output = ['status'    => $this->status,       'message'   => is_null($message) ? $this->message : $message];
        if (!is_null($this->api_token) || $this->api_token != "") {
            $output['api_token'] = $this->api_token;
        }
        $output['data'] = $this->data;
        return response()->json($output, $code);
    }

    /**
     * Get Error Message
     * If Application Environtment is local then
     * Return Error Message With filename and Line Number
     * else return a Simple Error Message
     */
    protected function getError($e = null, $code = 500)
    {
        if (strtolower(env('APP_ENV')) == 'local' && !empty($e)) {
            $error =  $e->getMessage() . ' On File ' . $e->getFile() . ' on line ' . $e->getLine();
            $code = Str::contains($e->getMessage(), ["No query results for model"]) ? 404 : $code;
            return $this->apiOutput($error, $code);
        }
        return $this->apiOutput('Something went wrong!', $code);
    }



    /**
     * Get Validation Error
     */
    public function getValidationError($validator)
    {
        if (strtolower(env('APP_ENV')) == 'local') {
            return response()->json($validator->errors()->first(), 422);
        }
        return response()->json('Data Validation Error!', 422);
    }
}

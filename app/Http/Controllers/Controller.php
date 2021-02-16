<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include_once('userinterface.php');

class Controller extends BaseController
{
    public $params = [];

    public function index(Request $request, $func)
    {
        session_start();
        $_SESSION['def_lang'] = $request->defLang;
        array_map(function ($one) {
            array_push($this->params, $one);
        }, $request->body);
        return response(['res' => call_user_func_array($func, $this->params)]);
    }
}

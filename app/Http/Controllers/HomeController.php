<?php

namespace App\Http\Controllers;

use App\Service\OAuth\OAuth;
use Illuminate\Http\Request;
class HomeController extends Controller
{
    public function index()
    {
        return '首页';
    }
}

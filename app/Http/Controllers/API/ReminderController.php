<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Reminder;

class ReminderController extends Controller
{
    public function index(){
        $scritpure = new Reminder();
        return $scritpure->todaysScripture();
    }
}

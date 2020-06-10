<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the main screen.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('layout/mainlayout')->with([
            'message' => '<img class="img-fluid" src="'.url('/') .'/img/IRMA-meet.png" alt="IRMA-meet logo">',
            'title' => __('welcome'),
            'buttons' => '<button type="button" class="btn btn-primary btn-lg btn-blue" onclick="startInvitation(\'./irma_auth/start/free\', \'./irma_session/create/free\');">' . __('Start a meeting now') . '</button>'
        ]);
    }
}

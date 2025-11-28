<?php

namespace App\Controllers;

class Error extends BaseController
{
    public function index()
    {
            $session = session();
    // ensure current_project structure exists (testing purposes)
    if (! $session->has('current_project')) {
        $session->set('current_project', [
            [ 'project_index' => 1 ] // default or placeholder
        ]);
    }


        echo view('templates/header');
        echo view('templates/header-assets3', ['session' => $session]);
        echo view('error');
    echo view('templates/footer', ['session' => $session]);    }

}

<?php

namespace FrameJam\Controllers;

use FrameJam\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return $this->view('home/index.twig', [
            'title' => 'Bem-vindo ao FrameJam',
            'description' => 'Um framework PHP minimalista e extensível'
        ]);
    }
} 
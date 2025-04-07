<?php

namespace FrameJam\Controllers;

use FrameJam\Core\Controller;
use FrameJam\Core\View;

class AdminController extends Controller
{
    /**
     * Exibe o painel de administração
     *
     * @return string
     */
    public function index()
    {
        // Verifica se o usuário tem permissão de administrador
        $userPermissions = \FrameJam\Core\Session::get('user_permissions', []);
        
        if (!in_array('admin', $userPermissions)) {
            return View::render('errors/unauthorized', [
                'message' => 'Você não tem permissão para acessar esta área.'
            ]);
        }
        
        // Renderiza a view do painel administrativo
        return View::render('admin/dashboard', [
            'title' => 'Painel Administrativo',
            'user' => \FrameJam\Core\Session::get('user')
        ]);
    }
} 
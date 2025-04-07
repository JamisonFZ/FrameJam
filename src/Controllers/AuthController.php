<?php

namespace FrameJam\Controllers;

use FrameJam\Core\Controller;
use FrameJam\Core\View;
use FrameJam\Core\Session;
use FrameJam\Core\Helpers\Redirect;

class AuthController extends Controller
{
    /**
     * Exibe o formulário de login
     */
    public function showLoginForm()
    {
        // Se o usuário já estiver autenticado, redireciona para a página inicial
        if (Session::get('user_id')) {
            Redirect::to('/');
        }
        
        return View::render('auth/login', [
            'title' => 'Login'
        ]);
    }
    
    /**
     * Processa o login
     */
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Aqui você implementaria a lógica de autenticação
        // Este é apenas um exemplo simplificado
        if ($email === 'admin@example.com' && $password === 'password') {
            // Armazena os dados do usuário na sessão
            Session::set('user_id', 1);
            Session::set('user', [
                'id' => 1,
                'name' => 'Administrador',
                'email' => 'admin@example.com'
            ]);
            
            // Define as permissões do usuário
            Session::set('user_permissions', ['admin', '/admin', '/admin/users', '/admin/settings']);
            
            // Redireciona para a URL pretendida ou para o dashboard
            $intendedUrl = Session::get('intended_url', '/admin');
            Redirect::to($intendedUrl);
        }
        
        // Se o login falhar, redireciona de volta para o formulário com mensagem de erro
        Session::set('error', 'Credenciais inválidas');
        Redirect::to('/login');
    }
    
    /**
     * Processa o logout
     */
    public function logout()
    {
        // Limpa os dados da sessão
        Session::clear();
        
        // Redireciona para a página de login
        Redirect::to('/login');
    }
} 
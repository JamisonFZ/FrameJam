<?php

namespace FrameJam\Core\Middleware;

use FrameJam\Core\Session;
use FrameJam\Core\Config\Config;
use FrameJam\Core\Helpers\Redirect;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle($request, \Closure $next)
    {
        // Verifica se o usuário está autenticado
        if (!Session::get('user_id')) {
            // Armazena a URL atual para redirecionamento após o login
            Session::set('intended_url', $request->getUri());
            
            // Redireciona para a página de login
            Redirect::to(Config::get('auth.login_route', '/login'));
        }

        // Verifica se o usuário tem permissão para acessar a rota
        if ($this->hasRoutePermission($request)) {
            return $next($request);
        }

        // Se não tiver permissão, redireciona para a página de acesso negado
        Redirect::to(Config::get('auth.unauthorized_route', '/unauthorized'));
    }

    /**
     * Verifica se o usuário tem permissão para acessar a rota atual
     */
    private function hasRoutePermission($request): bool
    {
        // Obtém as permissões do usuário da sessão
        $userPermissions = Session::get('user_permissions', []);
        
        // Obtém a rota atual
        $currentRoute = $request->getUri();
        
        // Se não houver permissões definidas, permite o acesso
        if (empty($userPermissions)) {
            return true;
        }

        // Verifica se a rota atual está nas permissões do usuário
        return in_array($currentRoute, $userPermissions);
    }
} 
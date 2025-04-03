<?php

namespace FrameJam\Controllers;

use FrameJam\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $features = [
            [
                'title' => 'Performance',
                'description' => 'Rápido e Eficiente',
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'
            ],
            [
                'title' => 'Flexibilidade',
                'description' => 'Adaptável às Suas Necessidades',
                'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4'
            ],
            [
                'title' => 'Segurança',
                'description' => 'Proteção Integrada',
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
            ]
        ];

        return $this->view('welcome', [
            'features' => $features
        ]);
    }

    public function sobre(Request $request): Response
    {
        return new Response('Sobre o FrameJam');
    }

    public function contato(Request $request): Response
    {
        return new Response('Página de Contato');
    }

    public function enviarContato(Request $request): Response
    {
        // TODO: Implementar lógica de envio de contato
        return new Response('Mensagem enviada com sucesso!');
    }
} 
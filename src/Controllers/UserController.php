<?php

namespace FrameJam\Controllers;

use FrameJam\Core\Request;
use FrameJam\Core\Response;

class UserController
{
    public function index(Request $request): Response
    {
        return new Response('Lista de Usuários');
    }

    public function show(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Detalhes do Usuário {$id}");
    }

    public function create(Request $request): Response
    {
        return new Response('Formulário de Criação de Usuário');
    }

    public function store(Request $request): Response
    {
        return new Response('Usuário criado com sucesso!');
    }

    public function edit(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Formulário de Edição do Usuário {$id}");
    }

    public function update(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Usuário {$id} atualizado com sucesso!");
    }

    public function destroy(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Usuário {$id} excluído com sucesso!");
    }
} 
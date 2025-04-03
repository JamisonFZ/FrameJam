<?php

namespace FrameJam\Controllers;

use FrameJam\Core\Request;
use FrameJam\Core\Response;

class ProductController
{
    public function index(Request $request): Response
    {
        return new Response('Lista de Produtos');
    }

    public function show(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Detalhes do Produto {$id}");
    }

    public function create(Request $request): Response
    {
        return new Response('Formulário de Criação de Produto');
    }

    public function store(Request $request): Response
    {
        return new Response('Produto criado com sucesso!');
    }

    public function edit(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Formulário de Edição do Produto {$id}");
    }

    public function update(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Produto {$id} atualizado com sucesso!");
    }

    public function destroy(Request $request): Response
    {
        $id = $request->getRouteParams()['id'] ?? null;
        return new Response("Produto {$id} excluído com sucesso!");
    }
} 
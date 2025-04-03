@extends('layouts/app')

@section('title', 'Bem-vindo ao FrameJam Framework')

@section('content')
    <div class="px-4 py-6 sm:px-0">
        <div class="border-4 border-dashed border-gray-200 rounded-lg h-96 flex items-center justify-center">
            <div class="text-center">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">
                    Bem-vindo ao FrameJam Framework
                </h2>
                <p class="text-gray-600 mb-8">
                    Um framework PHP simples e essencial para suas aplicações web
                </p>
                <div class="space-x-4">
                    <a href="https://github.com/seu-usuario/framejam" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        GitHub
                    </a>
                    <a href="/docs" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Documentação
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($features ?? [] as $feature)
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ $feature['title'] }}
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $feature['description'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection 
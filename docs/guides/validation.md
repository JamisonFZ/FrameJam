# Sistema de Validação

O sistema de validação do FrameJam fornece uma maneira simples e poderosa de validar dados de entrada em sua aplicação.

## Estrutura Básica

### Regras Básicas

```php
use FrameJam\Core\Validation\Validator;

// Validação básica
$validator = Validator::make($request->all(), [
    'nome' => 'required|min:3|max:255',
    'email' => 'required|email|unique:users',
    'senha' => 'required|min:8|confirmed',
    'idade' => 'required|integer|min:18',
    'termos' => 'required|accepted'
]);

if ($validator->fails()) {
    return back()
        ->withErrors($validator)
        ->withInput();
}
```

### Mensagens Personalizadas

```php
$messages = [
    'nome.required' => 'O nome é obrigatório',
    'nome.min' => 'O nome deve ter no mínimo 3 caracteres',
    'email.required' => 'O e-mail é obrigatório',
    'email.email' => 'Digite um e-mail válido',
    'email.unique' => 'Este e-mail já está em uso',
    'senha.required' => 'A senha é obrigatória',
    'senha.min' => 'A senha deve ter no mínimo 8 caracteres',
    'senha.confirmed' => 'As senhas não conferem',
    'idade.required' => 'A idade é obrigatória',
    'idade.integer' => 'A idade deve ser um número inteiro',
    'idade.min' => 'Você deve ter no mínimo 18 anos',
    'termos.required' => 'Você deve aceitar os termos',
    'termos.accepted' => 'Você deve aceitar os termos'
];

$validator = Validator::make($request->all(), $rules, $messages);
```

## Exemplos Práticos

### Validação de Formulário de Registro

```php
class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'senha' => 'required|string|min:8|confirmed',
            'telefone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'data_nascimento' => 'required|date|before:today',
            'cpf' => 'required|string|regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/|unique:users',
            'termos' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Criar usuário
        $user = User::create($request->validated());

        return redirect()->route('home')
            ->with('success', 'Conta criada com sucesso!');
    }
}
```

### Validação de API

```php
class ApiController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'descricao' => 'required|string|max:1000',
            'preco' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categories,id',
            'imagens.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'tags' => 'array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors()
            ], 422);
        }

        // Criar recurso
        $resource = Resource::create($request->validated());

        return response()->json([
            'message' => 'Recurso criado com sucesso',
            'data' => $resource
        ], 201);
    }
}
```

### Validação de Upload de Arquivos

```php
class FileController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'arquivo' => 'required|file|mimes:pdf,doc,docx|max:5120',
            'tipo' => 'required|in:contrato,relatorio,proposta',
            'descricao' => 'required|string|max:255',
            'data_vencimento' => 'required|date|after:today',
            'tags' => 'array',
            'tags.*' => 'string|max:50'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Processar upload
        $path = $request->file('arquivo')->store('documentos/' . $request->tipo);

        Document::create([
            'user_id' => auth()->id(),
            'tipo' => $request->tipo,
            'caminho' => $path,
            'descricao' => $request->descricao,
            'data_vencimento' => $request->data_vencimento,
            'tags' => $request->tags
        ]);

        return back()->with('success', 'Arquivo enviado com sucesso!');
    }
}
```

### Validação de Formulário de Contato

```php
class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'assunto' => 'required|string|max:100',
            'mensagem' => 'required|string|min:10|max:1000',
            'telefone' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'anexos.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Processar mensagem
        $message = Message::create($request->validated());

        // Enviar e-mail
        Mail::to(config('mail.admin'))->send(new ContactMessage($message));

        return back()->with('success', 'Mensagem enviada com sucesso!');
    }
}
```

## Boas Práticas

1. **Organização**
   - Use regras específicas e claras
   - Agrupe regras relacionadas
   - Mantenha mensagens de erro consistentes

2. **Performance**
   - Evite validações desnecessárias
   - Use regras otimizadas
   - Implemente cache de validação quando apropriado

3. **Segurança**
   - Valide todos os dados de entrada
   - Sanitize dados antes da validação
   - Implemente regras de segurança específicas

4. **Manutenção**
   - Documente regras complexas
   - Mantenha mensagens atualizadas
   - Revise regras periodicamente 
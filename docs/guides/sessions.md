# Sistema de Sessões

O sistema de sessões do FrameJam fornece uma maneira simples e segura de gerenciar dados de sessão entre requisições.

## Estrutura Básica

### Uso Básico

```php
use FrameJam\Core\Session\Session;

// Armazenar valor
Session::put('chave', 'valor');

// Recuperar valor
$valor = Session::get('chave');

// Recuperar valor com valor padrão
$valor = Session::get('chave', 'valor_padrao');

// Verificar se existe
if (Session::has('chave')) {
    // ...
}

// Remover valor
Session::forget('chave');

// Flash message (disponível apenas na próxima requisição)
Session::flash('mensagem', 'Operação realizada com sucesso!');

// Limpar toda a sessão
Session::flush();
```

## Configuração

### Configuração das Sessões

```php
// config/session.php
return [
    'driver' => env('SESSION_DRIVER', 'file'),
    
    'lifetime' => env('SESSION_LIFETIME', 120),
    
    'expire_on_close' => false,
    
    'encrypt' => false,
    
    'files' => storage_path('framework/sessions'),
    
    'connection' => env('SESSION_CONNECTION', null),
    
    'table' => 'sessions',
    
    'store' => env('SESSION_STORE', null),
    
    'lottery' => [2, 100],
    
    'cookie' => env(
        'SESSION_COOKIE',
        'framejam_session'
    ),
    
    'path' => '/',
    
    'domain' => env('SESSION_DOMAIN', null),
    
    'secure' => env('SESSION_SECURE_COOKIE', false),
    
    'http_only' => true,
    
    'same_site' => 'lax',
];
```

## Exemplos Práticos

### Carrinho de Compras

```php
class CartController extends Controller
{
    public function add(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        
        $cart = Session::get('cart', []);
        
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity']++;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'quantity' => 1,
                'price' => $product->price,
                'image' => $product->image
            ];
        }
        
        Session::put('cart', $cart);
        
        return back()->with('success', 'Produto adicionado ao carrinho!');
    }
    
    public function update(Request $request)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] = $request->quantity;
            Session::put('cart', $cart);
        }
        
        return back()->with('success', 'Carrinho atualizado!');
    }
    
    public function remove($productId)
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
        }
        
        return back()->with('success', 'Produto removido do carrinho!');
    }
    
    public function clear()
    {
        Session::forget('cart');
        
        return back()->with('success', 'Carrinho limpo!');
    }
}
```

### Filtros de Busca

```php
class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Salvar filtros na sessão
        if ($request->has('filters')) {
            Session::put('search_filters', $request->filters);
        }
        
        // Recuperar filtros da sessão
        $filters = Session::get('search_filters', []);
        
        $query = Product::query();
        
        if (isset($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }
        
        if (isset($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }
        
        if (isset($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }
        
        $products = $query->paginate(12);
        
        return view('search.index', compact('products', 'filters'));
    }
    
    public function clearFilters()
    {
        Session::forget('search_filters');
        
        return redirect()->route('search.index');
    }
}
```

### Wizard de Formulário

```php
class RegistrationWizardController extends Controller
{
    public function step1(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);
            
            Session::put('registration.step1', $request->all());
            
            return redirect()->route('registration.step2');
        }
        
        $data = Session::get('registration.step1', []);
        
        return view('registration.step1', compact('data'));
    }
    
    public function step2(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'address' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
            ]);
            
            Session::put('registration.step2', $request->all());
            
            return redirect()->route('registration.step3');
        }
        
        $data = Session::get('registration.step2', []);
        
        return view('registration.step2', compact('data'));
    }
    
    public function step3(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            $step1 = Session::get('registration.step1');
            $step2 = Session::get('registration.step2');
            
            $user = User::create([
                'name' => $step1['name'],
                'email' => $step1['email'],
                'address' => $step2['address'],
                'phone' => $step2['phone'],
                'password' => Hash::make($request->password),
            ]);
            
            // Limpar dados da sessão
            Session::forget('registration');
            
            Auth::login($user);
            
            return redirect()->route('dashboard')
                ->with('success', 'Registro concluído com sucesso!');
        }
        
        return view('registration.step3');
    }
}
```

## Boas Práticas

1. **Organização**
   - Use chaves descritivas e consistentes
   - Agrupe dados relacionados
   - Documente estrutura da sessão

2. **Performance**
   - Evite armazenar dados grandes
   - Use cache quando apropriado
   - Limpe dados não utilizados

3. **Segurança**
   - Não armazene dados sensíveis
   - Use HTTPS
   - Configure cookies seguros

4. **Manutenção**
   - Monitore uso de memória
   - Implemente limpeza automática
   - Mantenha logs de operações críticas 
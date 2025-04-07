# Sistema de Eventos

O sistema de eventos do FrameJam fornece uma maneira simples e poderosa de implementar o padrão Observer em sua aplicação, permitindo comunicação desacoplada entre componentes.

## Estrutura Básica

### Definindo Eventos

```php
use FrameJam\Core\Events\Event;

class UserRegistered extends Event
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }
}

class OrderCreated extends Event
{
    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }
}
```

### Definindo Listeners

```php
use FrameJam\Core\Events\Listener;

class SendWelcomeEmail extends Listener
{
    public function handle(UserRegistered $event)
    {
        Mail::to($event->user)->send(new WelcomeEmail($event->user));
    }
}

class NotifyAdmin extends Listener
{
    public function handle(OrderCreated $event)
    {
        // Notificar administrador sobre novo pedido
        Notification::send(User::admins(), new NewOrderNotification($event->order));
    }
}
```

## Configuração

### Registrando Eventos e Listeners

```php
// config/events.php
return [
    'listeners' => [
        UserRegistered::class => [
            SendWelcomeEmail::class,
            CreateUserProfile::class,
        ],
        OrderCreated::class => [
            NotifyAdmin::class,
            UpdateInventory::class,
            SendOrderConfirmation::class,
        ],
    ],
];
```

## Exemplos Práticos

### Evento de Registro de Usuário

```php
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Disparar evento
        event(new UserRegistered($user));

        Auth::login($user);

        return redirect('dashboard');
    }
}
```

### Evento de Criação de Pedido

```php
class OrderController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'total' => $request->total,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $order->items()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // Disparar evento
            event(new OrderCreated($order));

            DB::commit();

            return response()->json([
                'message' => 'Pedido criado com sucesso',
                'order' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Disparar evento de erro
            event(new OrderFailed($request->all(), $e));

            return response()->json([
                'message' => 'Erro ao criar pedido'
            ], 500);
        }
    }
}
```

### Evento de Atualização de Perfil

```php
class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:1024',
        ]);

        $oldEmail = $user->email;

        $user->update($data);

        // Disparar evento se o email foi alterado
        if ($oldEmail !== $user->email) {
            event(new EmailChanged($user, $oldEmail));
        }

        // Disparar evento se o avatar foi atualizado
        if ($request->hasFile('avatar')) {
            event(new AvatarUpdated($user));
        }

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
```

### Evento de Sistema

```php
class SystemMonitor
{
    public function checkHealth()
    {
        $metrics = [
            'cpu' => $this->getCpuUsage(),
            'memory' => $this->getMemoryUsage(),
            'disk' => $this->getDiskUsage(),
        ];

        foreach ($metrics as $metric => $value) {
            if ($value > config("monitoring.{$metric}.threshold")) {
                event(new SystemMetricExceeded($metric, $value));
            }
        }
    }
}
```

## Boas Práticas

1. **Organização**
   - Mantenha eventos e listeners em namespaces separados
   - Use nomes descritivos para eventos
   - Documente o propósito de cada evento

2. **Performance**
   - Use eventos assíncronos quando apropriado
   - Evite listeners pesados em eventos frequentes
   - Monitore o tempo de execução dos listeners

3. **Segurança**
   - Não exponha dados sensíveis nos eventos
   - Valide dados nos listeners
   - Use filas para processamento assíncrono

4. **Manutenção**
   - Mantenha logs de eventos importantes
   - Documente dependências entre eventos
   - Monitore falhas nos listeners 
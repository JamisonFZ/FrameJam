# Sistema de Upload de Arquivos

O sistema de upload de arquivos do FrameJam fornece uma maneira simples e segura de gerenciar uploads de arquivos em sua aplicação.

## Estrutura Básica

### Configuração Básica

```php
// config/filesystems.php
return [
    'default' => env('FILESYSTEM_DRIVER', 'local'),
    
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
        
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],
    ],
];
```

### Uso Básico

```php
use FrameJam\Core\Storage\Storage;

// Upload simples
$path = $request->file('arquivo')->store('uploads');

// Upload com nome personalizado
$path = $request->file('arquivo')->storeAs('uploads', 'nome-arquivo.jpg');

// Upload para disco específico
$path = $request->file('arquivo')->store('uploads', 's3');

// Verificar se é um arquivo válido
if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {
    // Processar upload
}
```

## Exemplos Práticos

### Upload de Imagem de Perfil

```php
class ProfileController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Remover avatar antigo
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload do novo avatar
            $path = $request->file('avatar')->store('avatars', 'public');

            // Atualizar usuário
            $user->update([
                'avatar' => $path
            ]);

            return back()->with('success', 'Avatar atualizado com sucesso!');
        }

        return back()->withErrors(['avatar' => 'Erro ao fazer upload do avatar.']);
    }
}
```

### Upload de Documentos

```php
class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'document' => 'required|mimes:pdf,doc,docx|max:5120',
            'type' => 'required|in:contract,report,proposal'
        ]);

        $path = $request->file('document')->store('documents/' . $request->type);

        Document::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'path' => $path,
            'original_name' => $request->file('document')->getClientOriginalName(),
            'mime_type' => $request->file('document')->getMimeType(),
            'size' => $request->file('document')->getSize()
        ]);

        return back()->with('success', 'Documento enviado com sucesso!');
    }

    public function download(Document $document)
    {
        // Verificar permissão
        $this->authorize('download', $document);

        return Storage::download(
            $document->path,
            $document->original_name
        );
    }
}
```

### Upload de Múltiplos Arquivos

```php
class GalleryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('gallery', 'public');
            
            $uploadedImages[] = Gallery::create([
                'user_id' => Auth::id(),
                'path' => $path,
                'original_name' => $image->getClientOriginalName(),
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize()
            ]);
        }

        return response()->json([
            'message' => 'Imagens enviadas com sucesso!',
            'images' => $uploadedImages
        ]);
    }
}
```

### Upload com Processamento de Imagem

```php
class ImageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120'
        ]);

        $image = $request->file('image');
        
        // Gerar nome único
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        
        // Criar instância do Image
        $img = Image::make($image);
        
        // Redimensionar mantendo proporção
        $img->resize(800, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Salvar versão original
        $originalPath = 'images/original/' . $filename;
        Storage::put($originalPath, $img->encode());
        
        // Criar thumbnail
        $img->fit(200, 200);
        $thumbnailPath = 'images/thumbnails/' . $filename;
        Storage::put($thumbnailPath, $img->encode());
        
        // Salvar no banco
        $imageModel = Image::create([
            'user_id' => Auth::id(),
            'original_path' => $originalPath,
            'thumbnail_path' => $thumbnailPath,
            'original_name' => $image->getClientOriginalName(),
            'mime_type' => $image->getMimeType(),
            'size' => $image->getSize()
        ]);

        return response()->json([
            'message' => 'Imagem processada com sucesso!',
            'image' => $imageModel
        ]);
    }
}
```

## Boas Práticas

1. **Organização**
   - Use diretórios específicos para cada tipo de arquivo
   - Implemente estrutura de pastas organizada
   - Mantenha metadados dos arquivos

2. **Performance**
   - Otimize imagens antes do upload
   - Use processamento assíncrono para arquivos grandes
   - Implemente cache quando apropriado

3. **Segurança**
   - Valide tipos de arquivo
   - Limite tamanho dos arquivos
   - Use nomes de arquivo seguros
   - Implemente verificação de vírus

4. **Manutenção**
   - Implemente limpeza automática
   - Mantenha logs de uploads
   - Monitore uso de espaço 
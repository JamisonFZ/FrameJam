# Variáveis de Ambiente

O FrameJam utiliza variáveis de ambiente para configurar diferentes aspectos do framework. Este guia explica como configurar e usar as variáveis de ambiente em sua aplicação.

## Configuração Inicial

1. Copie o arquivo `.env.example` para `.env`:
```bash
cp .env.example .env
```

2. Configure a chave de aplicação no arquivo `.env`:
```bash
# Gere uma string aleatória de 32 caracteres
APP_KEY=base64:$(openssl rand -base64 32)
```

## Estrutura do Arquivo .env

O arquivo `.env` está organizado em seções lógicas:

### Configurações da Aplicação
```env
APP_NAME=FrameJam
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=America/Sao_Paulo
APP_LOCALE=pt_BR
```

### Configurações do Banco de Dados
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=framejam
DB_USERNAME=root
DB_PASSWORD=
```

### Configurações de Cache
```env
CACHE_DRIVER=file
CACHE_PREFIX=framejam_cache
CACHE_TTL=3600
```

### Configurações de Sessão
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null
SESSION_PATH=/
```

### Configurações de Log
```env
LOG_CHANNEL=stack
LOG_LEVEL=debug
LOG_SLACK_WEBHOOK_URL=
```

### Configurações de Email
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"
```

### Configurações de Fila
```env
QUEUE_CONNECTION=database
QUEUE_TIMEOUT=60
QUEUE_RETRY_AFTER=90
```

### Configurações de Upload
```env
FILESYSTEM_DRIVER=local
FILESYSTEM_DISK=public
UPLOAD_MAX_SIZE=5120
UPLOAD_ALLOWED_TYPES=jpg,jpeg,png,pdf,doc,docx
```

### Configurações de API
```env
API_DEBUG=false
API_THROTTLE=60,1
API_TOKEN_LIFETIME=60
```

### Configurações de Segurança
```env
ENCRYPTION_KEY=
CORS_ALLOWED_ORIGINS=*
CORS_ALLOWED_METHODS=GET,POST,PUT,DELETE,OPTIONS
CORS_ALLOWED_HEADERS=Content-Type,Authorization,X-Requested-With
```

## Acessando Variáveis de Ambiente

### No PHP
```php
use FrameJam\Core\Config\Config;

// Acessando uma configuração
$appName = Config::get('app.name');
$dbHost = Config::get('database.host');

// Com valor padrão
$timezone = Config::get('app.timezone', 'UTC');
```

### No Template Twig
```twig
{{ config('app.name') }}
{{ config('app.url') }}
```

## Boas Práticas

1. **Segurança**
   - Nunca comite o arquivo `.env` no controle de versão
   - Mantenha o `.env.example` atualizado
   - Use valores seguros em produção

2. **Organização**
   - Agrupe configurações relacionadas
   - Use nomes descritivos
   - Documente configurações personalizadas

3. **Performance**
   - Use cache em produção
   - Configure timeouts apropriados
   - Otimize configurações de banco de dados

4. **Manutenção**
   - Mantenha backups das configurações
   - Documente alterações
   - Use versionamento para configurações 
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Painel Administrativo' ?></title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1><?= $title ?? 'Painel Administrativo' ?></h1>
            <div class="user-info">
                Bem-vindo, <?= htmlspecialchars($user['name'] ?? 'Usuário') ?>
                <a href="/logout" class="logout-btn">Sair</a>
            </div>
        </header>
        
        <nav class="admin-nav">
            <ul>
                <li><a href="/admin">Dashboard</a></li>
                <li><a href="/admin/users">Usuários</a></li>
                <li><a href="/admin/settings">Configurações</a></li>
            </ul>
        </nav>
        
        <main class="admin-content">
            <div class="dashboard-widgets">
                <div class="widget">
                    <h3>Estatísticas</h3>
                    <p>Conteúdo do widget de estatísticas</p>
                </div>
                
                <div class="widget">
                    <h3>Atividades Recentes</h3>
                    <p>Conteúdo do widget de atividades</p>
                </div>
                
                <div class="widget">
                    <h3>Notificações</h3>
                    <p>Conteúdo do widget de notificações</p>
                </div>
            </div>
        </main>
    </div>
    
    <script src="/assets/js/admin.js"></script>
</body>
</html> 
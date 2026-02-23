<?php
session_start();

// 1. SEGURANÇA: Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

// FUNÇÃO AUXILIAR (Definida no topo para estar disponível no HTML)
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'confirmado': return 'success';
        case 'cancelado': return 'danger';
        case 'realizado': return 'secondary';
        case 'pendente': default: return 'warning';
    }
}

// --- LÓGICA: Manipular atualização de status ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar_status') {
    $agendamento_id = $_POST['agendamento_id'];
    $novo_status = $_POST['novo_status'];

    if (!empty($agendamento_id) && !empty($novo_status)) {
        try {
            $stmt = $pdo->prepare("UPDATE agendamentos SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $novo_status, ':id' => $agendamento_id]);
        } catch (Exception $e) {
            // Opcional: Definir mensagem de erro na sessão
        }
    }
    // Redireciona para evitar reenvio do formulário
    header('Location: dashboard.php');
    exit;
}

// CONSULTAS PARA O DASHBOARD
try {
    // 1. Cards de Resumo
    $total_pacientes = $pdo->query("SELECT COUNT(*) FROM pacientes")->fetchColumn();
    $agendamentos_hoje_total = $pdo->query("SELECT COUNT(*) FROM agendamentos WHERE DATE(data_agendamento) = CURDATE()")->fetchColumn();

    // 2. Agenda do Dia (detalhada)
    $stmt_agenda_dia = $pdo->prepare(
        "SELECT a.id, a.data_agendamento, a.status, p.nome_completo
         FROM agendamentos a
         JOIN pacientes p ON a.paciente_id = p.id
         WHERE DATE(a.data_agendamento) = CURDATE()
         ORDER BY a.data_agendamento ASC"
    );
    $stmt_agenda_dia->execute();
    $agenda_do_dia = $stmt_agenda_dia->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Pacientes Recentes
    $stmt_recentes = $pdo->query("SELECT id, nome_completo, data_cadastro FROM pacientes ORDER BY data_cadastro DESC LIMIT 5");
    $pacientes_recentes = $stmt_recentes->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erro ao carregar dados do dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Sticky Footer */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .main-content {
            flex: 1;
        }
        
        /* Estilos do Rodapé Padrão */
        footer a { text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: #ffffff !important; text-decoration: underline; }
        
        /* Botão Admin no Footer */
        .admin-btn-hover:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistema Médico</a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']) ?></span>
                <a class="btn btn-outline-light" href="logout.php">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4 main-content">
        <div class="row">
            
            <div class="col-md-3">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action active">Dashboard</a>
                    <a href="pacientes.php" class="list-group-item list-group-item-action">Pacientes</a>
                    <a href="agendamentos.php" class="list-group-item list-group-item-action">Agendamentos</a>
                    <a href="relatorios.php" class="list-group-item list-group-item-action">Relatórios</a>
                </div>
            </div>

            <div class="col-md-9">
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-lightning-charge-fill"></i> Ações Rápidas</h5>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <a href="novo_agendamento.php" class="btn btn-primary"><i class="bi bi-calendar-plus"></i> Novo Agendamento</a>
                            <a href="cadastro_paciente.php" class="btn btn-success"><i class="bi bi-person-plus"></i> Novo Paciente</a>
                        </div>
                        <form action="pacientes.php" method="GET">
                            <div class="input-group">
                                <input type="text" name="busca_nome" class="form-control" placeholder="Buscar paciente por nome ou CPF..." required>
                                <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i> Buscar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body">
                                <h3 class="card-title"><?= $total_pacientes ?></h3>
                                <p class="card-text">Pacientes Cadastrados</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body">
                                <h3 class="card-title"><?= $agendamentos_hoje_total ?></h3>
                                <p class="card-text">Agendamentos para Hoje</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-7 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5><i class="bi bi-calendar-day"></i> Agenda do Dia</h5>
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <?php if (!empty($agenda_do_dia)): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($agenda_do_dia as $ag): ?>
                                            <li class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">
                                                        <i class="bi bi-clock"></i> <?= date('H:i', strtotime($ag['data_agendamento'])) ?> - <?= htmlspecialchars($ag['nome_completo']) ?>
                                                    </h6>
                                                    <span class="badge bg-<?= getStatusClass($ag['status']) ?>"><?= htmlspecialchars($ag['status']) ?></span>
                                                </div>
                                                <form action="dashboard.php" method="POST" class="mt-2">
                                                    <input type="hidden" name="acao" value="atualizar_status">
                                                    <input type="hidden" name="agendamento_id" value="<?= $ag['id'] ?>">
                                                    <div class="input-group input-group-sm">
                                                        <select name="novo_status" class="form-select">
                                                            <option value="Confirmado" <?= $ag['status'] == 'Confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                                            <option value="Realizado" <?= $ag['status'] == 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                                                            <option value="Cancelado" <?= $ag['status'] == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                                            <option value="Pendente" <?= $ag['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                                        </select>
                                                        <button class="btn btn-outline-primary" type="submit">Salvar</button>
                                                    </div>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">Nenhum agendamento para hoje.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5><i class="bi bi-person-check-fill"></i> Atividade Recente</h5>
                            </div>
                            <div class="card-body">
                                <p class="card-subtitle mb-2 text-muted">Últimos pacientes cadastrados</p>
                                <?php if (!empty($pacientes_recentes)): ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($pacientes_recentes as $paciente): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="visualizar_paciente.php?id=<?= $paciente['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($paciente['nome_completo']) ?></a>
                                                <small class="text-muted"><?= date('d/m/Y', strtotime($paciente['data_cadastro'])) ?></small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-center text-muted mt-3">Nenhum paciente cadastrado recentemente.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div> </div> </div> <footer class="bg-dark text-light pt-5 pb-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3 text-primary">Anamnese QR</h5>
                    <p class="small text-secondary">
                        Facilitando o acesso a informações vitais de saúde através da tecnologia. 
                        Sua segurança e bem-estar são nossa prioridade número um.
                    </p>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">Contato</h5>
                    <ul class="list-unstyled small text-secondary">
                        <li class="mb-2">
                            <i class="bi bi-geo-alt-fill me-2 text-primary"></i> 
                            R. José Cosme Pamplona - Bela Vista, Palhoça - SC
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-envelope-fill me-2 text-primary"></i> 
                            suporte@anamneseqr.com.br
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-telephone-fill me-2 text-primary"></i> 
                            (48) 99135-9339
                        </li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">Suporte</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="ajuda.php" class="text-secondary">Central de Ajuda</a></li>
                        <li class="mb-2"><a href="faq.php" class="text-secondary">Perguntas Frequentes (FAQ)</a></li>
                        <li class="mb-2"><a href="reportar_erro.php" class="text-secondary">Reportar um Erro</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-uppercase fw-bold mb-3">Privacidade (LGPD)</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="politica_privacidade.php" class="text-secondary">Política de Privacidade</a></li>
                        <li class="mb-2"><a href="termos_uso.php" class="text-secondary">Termos de Uso</a></li>
                        <li class="mb-2"><a href="portal_privacidade.php" class="text-secondary">Portal da Privacidade</a></li>
                    </ul>

                    <div class="mt-4">
                        <span class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-3 py-2 w-100">
                            <i class="bi bi-shield-lock-fill me-1"></i> Dados Protegidos
                        </span>
                    </div>

                    <div class="mt-2">
                        <a href="login.php" class="badge bg-secondary bg-opacity-25 text-light border border-secondary border-opacity-25 px-3 py-2 w-100 text-decoration-none admin-btn-hover">
                            <i class="bi bi-person-badge-fill me-1"></i> Acesso Administrativo
                        </a>
                    </div>
                </div>
            </div>

            <hr class="border-secondary my-4">

            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="small text-secondary mb-0">© <?= date('Y') ?> Sistema Anamnese QR. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="redes_sociais.php" class="text-secondary me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="redes_sociais.php" class="text-secondary me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="redes_sociais.php" class="text-secondary"><i class="bi bi-linkedin fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
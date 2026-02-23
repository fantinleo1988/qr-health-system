<?php
session_start();

// 1. SEGURANÇA: Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

// FUNÇÃO AUXILIAR (Definida no topo para estar disponível quando necessária)
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'confirmado':
            return 'success';
        case 'cancelado':
            return 'danger';
        case 'realizado':
            return 'secondary';
        case 'pendente':
        default:
            return 'warning';
    }
}

// 2. FILTROS: Capturar valores da URL
$busca_paciente = $_GET['busca_paciente'] ?? '';
$filtro_data = $_GET['filtro_data'] ?? '';
$filtro_status = $_GET['filtro_status'] ?? '';

// 3. CONSULTA AO BANCO
$sql = "SELECT
            a.id,
            a.data_agendamento,
            a.status,
            p.nome_completo AS nome_paciente,
            p.telefone AS telefone_paciente
        FROM
            agendamentos a
        JOIN
            pacientes p ON a.paciente_id = p.id";

$where_conditions = [];
$params = [];

if (!empty($busca_paciente)) {
    $where_conditions[] = "p.nome_completo LIKE :paciente";
    $params[':paciente'] = '%' . $busca_paciente . '%';
}
if (!empty($filtro_data)) {
    $where_conditions[] = "DATE(a.data_agendamento) = :data_ag";
    $params[':data_ag'] = $filtro_data;
}
if (!empty($filtro_status)) {
    $where_conditions[] = "a.status = :status";
    $params[':status'] = $filtro_status;
}

if (count($where_conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY a.data_agendamento DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro ao carregar agendamentos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .main-content {
            flex: 1;
        }
        footer a {
            text-decoration: none;
            transition: color 0.3s ease;
        }
        footer a:hover {
            color: #ffffff !important;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Sistema Médico</a>
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
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                    <a href="pacientes.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-people-fill"></i> Pacientes
                    </a>
                    <a href="agendamentos.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-calendar-check-fill"></i> Agendamentos
                    </a>
                    <a href="relatorios.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-file-earmark-bar-graph-fill"></i> Relatórios
                    </a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-calendar-check-fill"></i> Gerenciar Agendamentos</h5>
                        <a href="novo_agendamento.php" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Novo Agendamento
                        </a>
                    </div>
                    <div class="card-body">
                        
                        <?php if (isset($_SESSION['flash_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= $_SESSION['flash_message'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash_message']); ?>
                        <?php endif; ?>

                        <form method="GET" action="" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="busca_paciente" class="form-control" placeholder="Buscar por paciente..." value="<?= htmlspecialchars($busca_paciente) ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="filtro_data" class="form-control" value="<?= htmlspecialchars($filtro_data) ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="filtro_status" class="form-select">
                                        <option value="">-- Status --</option>
                                        <option value="Pendente" <?= $filtro_status == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                        <option value="Confirmado" <?= $filtro_status == 'Confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                        <option value="Cancelado" <?= $filtro_status == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        <option value="Realizado" <?= $filtro_status == 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>Data e Hora</th>
                                        <th>Paciente</th>
                                        <th>Telefone</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($agendamentos) > 0): ?>
                                        <?php foreach ($agendamentos as $ag): ?>
                                            <tr>
                                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($ag['data_agendamento']))) ?></td>
                                                <td><?= htmlspecialchars($ag['nome_paciente']) ?></td>
                                                <td><?= htmlspecialchars($ag['telefone_paciente']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= getStatusClass($ag['status']) ?>">
                                                        <?= htmlspecialchars($ag['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="detalhes_agendamento.php?id=<?= $ag['id'] ?>" class="btn btn-info btn-sm text-white" title="Ver Detalhes">
                                                        <i class="bi bi-eye"></i> Detalhes
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Nenhum agendamento encontrado.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div> </div> </div> </div> </div> <footer class="bg-dark text-light pt-5 pb-3 mt-5">
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
                    <div class="mt-3">
                        <span class="badge bg-secondary"><i class="bi bi-shield-lock"></i> Dados Protegidos</span>
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
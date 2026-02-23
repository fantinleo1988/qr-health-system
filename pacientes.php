<?php
session_start();

// 1. SEGURANÇA: Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

// 2. FILTROS: Capturar valores da URL (se existirem)
$filtro_condicao = $_GET['filtro_condicao'] ?? '';
$busca_nome = $_GET['busca_nome'] ?? '';
$busca_cpf = $_GET['busca_cpf'] ?? '';

// 3. CONSULTA AO BANCO: Construção dinâmica da query
$sql = "SELECT id, nome_completo, cpf, telefone, condicao_saude FROM pacientes";
$where_conditions = [];
$params = [];

if (!empty($filtro_condicao)) {
    $where_conditions[] = "condicao_saude = :condicao";
    $params[':condicao'] = $filtro_condicao;
}
if (!empty($busca_nome)) {
    $where_conditions[] = "nome_completo LIKE :nome";
    $params[':nome'] = '%' . $busca_nome . '%';
}
if (!empty($busca_cpf)) {
    $cpf_limpo = preg_replace('/\D/', '', $busca_cpf);
    $where_conditions[] = "REPLACE(REPLACE(cpf, '.', ''), '-', '') LIKE :cpf";
    $params[':cpf'] = '%' . $cpf_limpo . '%';
}

if (count($where_conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro ao carregar pacientes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Garante que o rodapé fique no fundo mesmo com pouco conteúdo */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .container.mt-4 {
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

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="dashboard.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                    <a href="pacientes.php" class="list-group-item list-group-item-action active">
                        <i class="bi bi-people-fill"></i> Pacientes
                    </a>
                    <a href="agendamentos.php" class="list-group-item list-group-item-action">
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
                        <h5><i class="bi bi-people-fill"></i> Gerenciar Pacientes</h5>
                        <a href="cadastro_paciente.php" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Cadastrar Paciente
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
                                    <select name="filtro_condicao" class="form-select">
                                        <option value="">-- Todas as Condições --</option>
                                        <option value="Pessoa com 60 anos ou mais" <?= $filtro_condicao == 'Pessoa com 60 anos ou mais' ? 'selected' : '' ?>>Idoso (60+)</option>
                                        <option value="Pessoa com necessidades especiais (PnE)" <?= $filtro_condicao == 'Pessoa com necessidades especiais (PnE)' ? 'selected' : '' ?>>Pessoa com Necessidades Especiais</option>
                                        <option value="Condição crônica de saúde" <?= $filtro_condicao == 'Condição crônica de saúde' ? 'selected' : '' ?>>Condição Crônica</option>
                                        <option value="Condição rara de saúde" <?= $filtro_condicao == 'Condição rara de saúde' ? 'selected' : '' ?>>Condição Rara</option>
                                        <option value="Outras condições (informe em observações)" <?= $filtro_condicao == 'Outras condições (informe em observações)' ? 'selected' : '' ?>>Outras</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="busca_nome" class="form-control" placeholder="Buscar por nome..." value="<?= htmlspecialchars($busca_nome) ?>">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="busca_cpf" class="form-control" placeholder="Buscar por CPF..." value="<?= htmlspecialchars($busca_cpf) ?>">
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
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>CPF</th>
                                        <th>Telefone</th>
                                        <th>Condição de Saúde</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($pacientes) > 0): ?>
                                        <?php foreach ($pacientes as $p): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($p['id']) ?></td>
                                                <td><?= htmlspecialchars($p['nome_completo']) ?></td>
                                                <td><?= htmlspecialchars($p['cpf']) ?></td>
                                                <td><?= htmlspecialchars($p['telefone']) ?></td>
                                                <td><?= htmlspecialchars($p['condicao_saude']) ?></td>
                                                <td class="text-nowrap">
                                                    <a href="ficha.php?id=<?= $p['id'] ?>" class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="editar_paciente.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <button class="btn btn-secondary btn-sm" title="Re-gerar QR Code" onclick="regenerarQrCode(<?= $p['id'] ?>)">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                    <form action="excluir_paciente.php" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este paciente?');">
                                                        <input type="hidden" name="paciente_id" value="<?= $p['id'] ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Nenhum paciente encontrado.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-light pt-5 pb-3 mt-5">
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

    <script>
    async function regenerarQrCode(pacienteId) {
        // Pede confirmação ao usuário
        if (!confirm('Deseja realmente gerar um novo QR Code para este paciente? O link antigo será substituído.')) {
            return;
        }

        try {
            // Envia a requisição para o script PHP
            const response = await fetch('regenerar_qrcode.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ paciente_id: pacienteId })
            });

            const resultado = await response.json();

            // Exibe a mensagem de sucesso ou erro retornada pelo PHP
            if (resultado.success) {
                alert(resultado.message);
                // Opcional: Você poderia atualizar a interface aqui se necessário
            } else {
                throw new Error(resultado.message);
            }

        } catch (error) {
            console.error('Erro:', error);
            alert('Ocorreu um erro: ' + error.message);
        }
    }
    </script>
</body>
</html>
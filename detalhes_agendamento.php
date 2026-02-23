<?php
session_start();

// 1. SEGURANÇA: Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

// LÓGICA PARA EXCLUIR O AGENDAMENTO (se o formulário for enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'excluir') {
    $agendamento_id_excluir = $_POST['agendamento_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
        $stmt->execute([$agendamento_id_excluir]);
        $_SESSION['flash_message'] = "Agendamento excluído com sucesso!";
        header('Location: agendamentos.php');
        exit;
    } catch (Exception $e) {
        die("Erro ao excluir agendamento: " . $e->getMessage());
    }
}

// 2. OBTER E VALIDAR O ID DO AGENDAMENTO PELA URL
$agendamento_id = $_GET['id'] ?? 0;
if (empty($agendamento_id)) {
    header('Location: agendamentos.php');
    exit;
}

// 3. BUSCAR DADOS DO AGENDAMENTO E DO PACIENTE
try {
    $sql = "SELECT
                a.id, a.data_agendamento, a.status, a.observacoes,
                p.id AS paciente_id, p.nome_completo, p.cpf, p.telefone
            FROM
                agendamentos a
            JOIN
                pacientes p ON a.paciente_id = p.id
            WHERE
                a.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$agendamento_id]);
    $agendamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$agendamento) {
        header('Location: agendamentos.php');
        exit;
    }
} catch (Exception $e) {
    die("Erro ao carregar dados do agendamento: " . $e->getMessage());
}

// Função auxiliar para definir a cor do "badge" de status
function getStatusClass($status) {
    switch (strtolower($status)) {
        case 'confirmado': return 'success';
        case 'cancelado': return 'danger';
        case 'realizado': return 'secondary';
        case 'pendente': default: return 'warning';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Agendamento - Sistema Médico</title>
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
                    <a href="dashboard.php" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="pacientes.php" class="list-group-item list-group-item-action">Pacientes</a>
                    <a href="agendamentos.php" class="list-group-item list-group-item-action active">Agendamentos</a>
                    <a href="relatorios.php" class="list-group-item list-group-item-action">Relatórios</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 text-primary"><i class="bi bi-calendar-event-fill me-2"></i> Detalhes do Agendamento</h5>
                        <div>
                            <a href="agendamentos.php" class="btn btn-outline-secondary btn-sm me-2">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <a href="editar_agendamento.php?id=<?= $agendamento['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary text-uppercase small">Dados do Agendamento</h6>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-2">
                                <strong class="d-block text-dark">Data e Hora</strong>
                                <span class="text-secondary"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($agendamento['data_agendamento']))) ?></span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="d-block text-dark">Status</strong>
                                <span class="badge bg-<?= getStatusClass($agendamento['status']) ?>">
                                    <?= htmlspecialchars($agendamento['status']) ?>
                                </span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="d-block text-dark">ID</strong>
                                <span class="text-secondary">#<?= $agendamento['id'] ?></span>
                            </div>
                            <div class="col-12 mt-2">
                                <strong class="d-block text-dark">Observações</strong>
                                <p class="text-secondary mb-0 bg-light p-2 rounded small">
                                    <?= nl2br(htmlspecialchars($agendamento['observacoes'])) ?: 'Nenhuma observação registrada.' ?>
                                </p>
                            </div>
                        </div>

                        <h6 class="fw-bold border-bottom pb-2 mb-3 text-secondary text-uppercase small">Dados do Paciente</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <strong class="d-block text-dark">Nome</strong>
                                <a href="visualizar_paciente.php?id=<?= $agendamento['paciente_id'] ?>" class="text-decoration-none fw-bold">
                                    <?= htmlspecialchars($agendamento['nome_completo']) ?>
                                </a>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="d-block text-dark">CPF</strong>
                                <span class="text-secondary"><?= htmlspecialchars($agendamento['cpf']) ?></span>
                            </div>
                            <div class="col-md-4 mb-2">
                                <strong class="d-block text-dark">Telefone</strong>
                                <span class="text-secondary"><?= htmlspecialchars($agendamento['telefone']) ?></span>
                            </div>
                        </div>
                        
                        <div class="border-top mt-4 pt-3 text-end">
                            <form action="detalhes_agendamento.php?id=<?= $agendamento['id'] ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este agendamento? Esta ação não pode ser desfeita.');">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="agendamento_id" value="<?= $agendamento['id'] ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash-fill me-1"></i> Excluir Agendamento
                                </button>
                            </form>
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
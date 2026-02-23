<?php
session_start();

// 1. SEGURANÇA: Verificar se o usuário está logado
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

$errors = [];
$paciente_id = '';
$data_agendamento = '';
$status = 'Confirmado'; // Valor padrão
$observacoes = '';

// 2. LÓGICA DO FORMULÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paciente_id = trim($_POST['paciente_id']);
    $data_agendamento = trim($_POST['data_agendamento']);
    $status = trim($_POST['status']);
    $observacoes = trim($_POST['observacoes']);

    if (empty($paciente_id)) {
        $errors[] = "O campo 'Paciente' é obrigatório.";
    }
    if (empty($data_agendamento)) {
        $errors[] = "O campo 'Data e Hora' é obrigatório.";
    }

    if (empty($errors)) {
        try {
            $sql = "INSERT INTO agendamentos (paciente_id, data_agendamento, status, observacoes) VALUES (:paciente_id, :data_agendamento, :status, :observacoes)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':paciente_id' => $paciente_id,
                ':data_agendamento' => $data_agendamento,
                ':status' => $status,
                ':observacoes' => $observacoes
            ]);

            $_SESSION['flash_message'] = "Agendamento criado com sucesso!";
            header('Location: agendamentos.php');
            exit;

        } catch (Exception $e) {
            $errors[] = "Erro ao criar agendamento: " . $e->getMessage();
        }
    }
}

// 3. CARREGAR PACIENTES PARA O DROPDOWN
try {
    $stmt_pacientes = $pdo->query("SELECT id, nome_completo FROM pacientes ORDER BY nome_completo ASC");
    $pacientes = $stmt_pacientes->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erro ao carregar a lista de pacientes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Agendamento - Sistema Médico</title>
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
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-primary"><i class="bi bi-calendar-plus me-2"></i> Novo Agendamento</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error): ?>
                                    <p class="mb-0"><?= htmlspecialchars($error) ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="novo_agendamento.php" method="POST">
                            
                            <div class="mb-3">
                                <label for="paciente_id" class="form-label">Paciente *</label>
                                <select class="form-select" id="paciente_id" name="paciente_id" required>
                                    <option value="">-- Selecione um paciente --</option>
                                    <?php foreach ($pacientes as $paciente): ?>
                                        <option value="<?= $paciente['id'] ?>" <?= ($paciente_id == $paciente['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($paciente['nome_completo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="data_agendamento" class="form-label">Data e Hora *</label>
                                <input type="datetime-local" class="form-control" id="data_agendamento" name="data_agendamento" value="<?= htmlspecialchars($data_agendamento) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="Confirmado" <?= ($status == 'Confirmado') ? 'selected' : '' ?>>Confirmado</option>
                                    <option value="Pendente" <?= ($status == 'Pendente') ? 'selected' : '' ?>>Pendente</option>
                                    <option value="Cancelado" <?= ($status == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Detalhes adicionais sobre a consulta..."><?= htmlspecialchars($observacoes) ?></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="agendamentos.php" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">Salvar Agendamento</button>
                            </div>
                        </form>
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
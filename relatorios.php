<?php
session_start();

if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/includes/db.php";

try {
    // Consulta para o gráfico de pacientes por condição
    $stmt_condicoes = $pdo->query("SELECT condicao_saude, COUNT(*) as total FROM pacientes GROUP BY condicao_saude");
    $dados_condicoes = $stmt_condicoes->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para o gráfico de agendamentos por status
    $stmt_status = $pdo->query("SELECT status, COUNT(*) as total FROM agendamentos GROUP BY status");
    $dados_status = $stmt_status->fetchAll(PDO::FETCH_ASSOC);
    
    // --- NOVA CONSULTA: Novos pacientes por mês ---
    $stmt_novos_pacientes = $pdo->query(
        "SELECT DATE_FORMAT(data_cadastro, '%Y-%m') as mes, COUNT(*) as total
         FROM pacientes
         GROUP BY mes
         ORDER BY mes ASC"
    );
    $dados_novos_pacientes = $stmt_novos_pacientes->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erro ao carregar dados para os relatórios: " . $e->getMessage());
}

// Prepara dados para os gráficos (JSON)
$labels_condicoes = json_encode(array_column($dados_condicoes, 'condicao_saude'));
$valores_condicoes = json_encode(array_column($dados_condicoes, 'total'));

$labels_status = json_encode(array_column($dados_status, 'status'));
$valores_status = json_encode(array_column($dados_status, 'total'));

// --- Prepara dados para o novo gráfico ---
$labels_novos_pacientes = json_encode(array_column($dados_novos_pacientes, 'mes'));
$valores_novos_pacientes = json_encode(array_column($dados_novos_pacientes, 'total'));

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Sistema Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <a href="agendamentos.php" class="list-group-item list-group-item-action">Agendamentos</a>
                    <a href="relatorios.php" class="list-group-item list-group-item-action active">Relatórios</a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-bar-graph-fill"></i> Relatórios do Sistema</h5>
                    </div>
                    <div class="card-body">
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header fw-bold text-secondary">Pacientes por Condição</div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <div style="max-width: 300px; width: 100%;">
                                            <canvas id="graficoCondicoes"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header fw-bold text-secondary">Agendamentos por Status</div>
                                    <div class="card-body">
                                        <canvas id="graficoStatus"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-4">
                                <div class="card">
                                    <div class="card-header fw-bold text-secondary">Novos Pacientes por Mês</div>
                                    <div class="card-body">
                                        <canvas id="graficoNovosPacientes" style="max-height: 300px;"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="card mt-4 bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title text-primary mb-3"><i class="bi bi-printer-fill"></i> Geração de Documentos</h5>
                                <form action="gerar_pdf.php" method="POST" target="_blank">
                                    <div class="mb-3">
                                        <label for="tipo_relatorio" class="form-label fw-bold">Selecione o tipo de relatório:</label>
                                        <select name="tipo_relatorio" id="tipo_relatorio" class="form-select" required onchange="toggleDateFields()">
                                            <option value="">-- Selecione --</option>
                                            <option value="lista_pacientes">Lista Completa de Pacientes</option>
                                            <option value="agendamentos_periodo">Agendamentos por Período</option>
                                        </select>
                                    </div>

                                    <div id="dateFields" class="row g-3 mb-3" style="display: none;">
                                        <div class="col-md-6">
                                            <label for="data_inicio" class="form-label">Data de Início:</label>
                                            <input type="date" name="data_inicio" id="data_inicio" class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="data_fim" class="form-label">Data Final:</label>
                                            <input type="date" name="data_fim" id="data_fim" class="form-control">
                                        </div>
                                    </div>

                                    <button class="btn btn-success" type="submit">
                                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> Gerar PDF
                                    </button>
                                </form>
                            </div>
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

    <script>
    // Função para mostrar/ocultar campos de data
    function toggleDateFields() {
        const tipoRelatorio = document.getElementById('tipo_relatorio').value;
        const dateFields = document.getElementById('dateFields');
        if (tipoRelatorio === 'agendamentos_periodo') {
            dateFields.style.display = 'flex';
        } else {
            dateFields.style.display = 'none';
        }
    }

    // --- GRÁFICOS ---

    // Gráfico de Condições (Pizza)
    const ctxCondicoes = document.getElementById('graficoCondicoes').getContext('2d');
    new Chart(ctxCondicoes, {
        type: 'doughnut', // Mudei para doughnut, fica mais moderno
        data: {
            labels: <?= $labels_condicoes ?>,
            datasets: [{
                label: 'Total de Pacientes',
                data: <?= $valores_condicoes ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: { 
            responsive: true, 
            plugins: { legend: { position: 'bottom' } } // Legenda em baixo para não ocupar espaço
        }
    });

    // Gráfico de Status (Barras)
    const ctxStatus = document.getElementById('graficoStatus').getContext('2d');
    new Chart(ctxStatus, {
        type: 'bar',
        data: {
            labels: <?= $labels_status ?>,
            datasets: [{
                label: 'Total de Agendamentos',
                data: <?= $valores_status ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)', 'rgba(255, 159, 64, 0.7)',
                    'rgba(75, 192, 192, 0.7)', 'rgba(150, 150, 150, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
            plugins: { legend: { display: false } }
        }
    });

    // Gráfico de Novos Pacientes (Linhas)
    const ctxNovosPacientes = document.getElementById('graficoNovosPacientes').getContext('2d');
    new Chart(ctxNovosPacientes, {
        type: 'line',
        data: {
            labels: <?= $labels_novos_pacientes ?>,
            datasets: [{
                label: 'Novos Pacientes',
                data: <?= $valores_novos_pacientes ?>,
                fill: true, // Preenchimento abaixo da linha
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.4 // Curva suave
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });
    </script>
</body>
</html>
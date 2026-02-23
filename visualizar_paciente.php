<?php
require_once __DIR__ . "/includes/db.php";

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die("<div class='container mt-5'><div class='alert alert-danger'>ID de paciente inválido.</div></div>");
}

try {
    // Query atualizada para buscar os nomes de estado e cidade
    $sql_paciente = "SELECT p.*, e.nome as nome_estado, c.nome as nome_cidade
                      FROM pacientes p
                      LEFT JOIN estados e ON p.naturalidade_estado = e.id
                      LEFT JOIN cidades c ON p.naturalidade_cidade = c.id
                      WHERE p.id = :id LIMIT 1";
    
    $stmt_paciente = $pdo->prepare($sql_paciente);
    $stmt_paciente->execute([":id" => $id]);
    $paciente = $stmt_paciente->fetch(PDO::FETCH_ASSOC);

    if (!$paciente) {
        die("<div class='container mt-5'><div class='alert alert-danger'>Paciente não encontrado.</div></div>");
    }

    // Busca os medicamentos
    $sql_medicamentos = "SELECT * FROM medicamentos WHERE paciente_id = :paciente_id";
    $stmt_medicamentos = $pdo->prepare($sql_medicamentos);
    $stmt_medicamentos->execute([':paciente_id' => $id]);
    $medicamentos_paciente = $stmt_medicamentos->fetchAll(PDO::FETCH_ASSOC);

    // Calcula a idade
    $idade = 'N/I';
    if (!empty($paciente['data_nascimento'])) {
        $dataNasc = new DateTime($paciente['data_nascimento']);
        $hoje = new DateTime();
        $idade = $hoje->diff($dataNasc)->y;
    }

    // URL do QR Code
    $qrCodeUrl = $paciente['qr_code'];
    if (empty($qrCodeUrl)) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        // Remove o nome do script atual para pegar o diretório base, se necessário, ou usa a raiz
        $qrCodeUrl = $protocol . $host . "/ficha.php?id=" . $paciente['id'];
    }

} catch (PDOException $e) {
    die("<div class='container mt-5'><div class='alert alert-danger'>Erro no banco de dados.</div></div>");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ficha do Paciente: <?= htmlspecialchars($paciente['nome_completo']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
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
        
        /* Estilos da Ficha */
        .info-label { font-weight: 600; color: #555; }
        .info-value { color: #000; }
        .section-divider { border-top: 1px solid #eee; margin-top: 1.5rem; padding-top: 1.5rem; }
        
        /* Estilos do Rodapé Padrão */
        footer a { text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: #ffffff !important; text-decoration: underline; }
        
        /* Botão Admin no Footer */
        .admin-btn-hover:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }

        /* IMPRESSÃO */
        @media print {
            body { background: #fff !important; }
            body * { visibility: hidden; }
            .printable-area, .printable-area * { visibility: visible; }
            .printable-area { position: absolute; left: 0; top: 0; width: 100%; }
            .no-print, footer, nav { display: none !important; }
            .alert { border: 1px solid #ccc !important; }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4 main-content">
    <div class="card shadow-sm printable-area">
        <div class="card-body p-4 p-md-5">

            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2 class="card-title text-primary mb-1"><?= htmlspecialchars($paciente['nome_completo']) ?></h2>
                    <p class="text-muted">
                        Nasc.: <?= !empty($paciente['data_nascimento']) ? date("d/m/Y", strtotime($paciente['data_nascimento'])) : 'N/I' ?> (<?= $idade ?> anos)
                    </p>
                </div>
                <div id="qrCodeContainer" class="qr-container bg-white p-2 border rounded"></div>
            </div>
            
            <div class="d-flex flex-wrap gap-2 my-4 no-print">
                <button class="btn btn-primary" onclick="imprimirFichaCompleta()"><i class="bi bi-printer-fill"></i> Imprimir Ficha Completa</button>
                <button class="btn btn-secondary" onclick="imprimirApenasQR()"><i class="bi bi-qr-code"></i> Imprimir Apenas QR Code</button>
                <button class="btn btn-success" onclick="downloadQR()"><i class="bi bi-download"></i> Baixar QR Code</button>
            </div>

            <div class="alert alert-danger">
                <h5 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> Alergias</h5>
                <p class="mb-0"><?= nl2br(htmlspecialchars($paciente['alergias'])) ?: 'Nenhuma alergia informada.' ?></p>
            </div>
            <div class="alert alert-warning">
                <h5 class="alert-heading fw-bold"><i class="bi bi-heart-pulse-fill"></i> Condição Principal</h5>
                <p class="mb-0"><?= htmlspecialchars($paciente['condicao_saude']) ?: 'Nenhuma condição principal informada.' ?></p>
            </div>

            <div class="row section-divider">
                <div class="col-md-6">
                    <h4><i class="bi bi-person-vcard"></i> Dados Pessoais</h4>
                    <p><span class="info-label">CPF:</span> <span class="info-value"><?= htmlspecialchars($paciente['cpf']) ?></span></p>
                    <p><span class="info-label">Gênero:</span> <span class="info-value"><?= htmlspecialchars($paciente['genero'] ?? 'N/I') ?></span></p>
                    <p><span class="info-label">Pronomes:</span> <span class="info-value"><?= htmlspecialchars($paciente['pronomes'] ?? 'N/I') ?></span></p>
                    <p><span class="info-label">Estado Civil:</span> <span class="info-value"><?= htmlspecialchars($paciente['estado_civil'] ?? 'N/I') ?></span></p>
                    <p><span class="info-label">Naturalidade:</span> <span class="info-value"><?= htmlspecialchars(($paciente['nome_cidade'] ?? 'N/A') . ' - ' . ($paciente['nome_estado'] ?? 'N/A')) ?></span></p>
                </div>
                <div class="col-md-6">
                    <h4><i class="bi bi-telephone-fill"></i> Contatos</h4>
                    <p><span class="info-label">Telefone:</span> <span class="info-value"><?= htmlspecialchars($paciente['telefone']) ?></span></p>
                    <hr>
                    <p class="fw-bold">Contato de Emergência:</p>
                    <p><span class="info-label">Nome:</span> <span class="info-value"><?= htmlspecialchars($paciente['contato_emergencia_nome']) ?></span></p>
                    <p><span class="info-label">Telefone:</span> <span class="info-value"><?= htmlspecialchars($paciente['telefone_emergencia']) ?></span></p>
                </div>
            </div>

            <div class="section-divider">
                <h4><i class="bi bi-capsule"></i> Medicamentos em Uso</h4>
                <div>
                    <?php if (!empty($medicamentos_paciente)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light"><tr><th>Nome</th><th>Dosagem</th><th>Frequência</th></tr></thead>
                                <tbody>
                                    <?php foreach ($medicamentos_paciente as $med): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($med['nome']) ?></td>
                                            <td><?= htmlspecialchars($med['dosagem']) ?></td>
                                            <td><?= htmlspecialchars($med['frequencia']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Nenhum medicamento cadastrado.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="section-divider">
                <h4><i class="bi bi-journal-medical"></i> Outras Informações Médicas</h4>
                <div class="row">
                    <div class="col-md-6">
                         <p><span class="info-label">Tipo Sanguíneo:</span> <span class="info-value fw-bold text-danger"><?= htmlspecialchars($paciente['tipo_sanguineo'] ?? $paciente['tipoSanguineo'] ?? 'N/I') ?></span></p>
                    </div>
                     <div class="col-md-6">
                        <p><span class="info-label">Médico Responsável:</span> <span class="info-value"><?= htmlspecialchars($paciente['medico_responsavel'] ?: 'Não informado.') . ($paciente['contato_medico'] ? ' (' . htmlspecialchars($paciente['contato_medico']) . ')' : '') ?></span></p>
                    </div>
                    <div class="col-12">
                        <p><span class="info-label">Histórico Familiar:</span><br> <span class="info-value"><?= nl2br(htmlspecialchars($paciente['historico_familiar'])) ?: 'Não informado.' ?></span></p>
                    </div>
                    <div class="col-12">
                        <p><span class="info-label">Observações Gerais:</span><br> <span class="info-value"><?= nl2br(htmlspecialchars($paciente['observacoes'])) ?: 'Não informado.' ?></span></p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-5 pt-3 border-top no-print">
                <small class="text-muted">Documento gerado pelo sistema Anamnese QR em <?= date('d/m/Y H:i') ?></small>
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
    // Variável global para armazenar o elemento canvas do QR Code
    let qrCodeCanvas = null;

    document.addEventListener("DOMContentLoaded", () => {
        const container = document.getElementById("qrCodeContainer");
        const qrUrl = <?= json_encode($qrCodeUrl) ?>;

        // Gera o QR Code usando QRCode.js
        if (container && qrUrl) {
            // Limpa qualquer conteúdo anterior
            container.innerHTML = ''; 
            
            new QRCode(container, {
                text: qrUrl,
                width: 128,
                height: 128,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
            
            // Tenta obter o canvas logo após a geração
            // O QRCode.js pode demorar alguns milissegundos para renderizar o canvas ou img
            setTimeout(() => {
                qrCodeCanvas = container.querySelector("canvas") || container.querySelector("img");
            }, 100);
        }
    });

    function downloadQR() {
        // Como QRCode.js pode gerar <canvas> ou <img> dependendo do browser, verificamos ambos
        const element = document.querySelector("#qrCodeContainer canvas") || document.querySelector("#qrCodeContainer img");
        
        if (!element) {
            alert("QR Code ainda não foi gerado. Aguarde um momento.");
            return;
        }
        
        const link = document.createElement("a");
        link.download = "qrcode-paciente-<?= $paciente['id'] ?>.png";
        
        if (element.tagName === 'CANVAS') {
             link.href = element.toDataURL("image/png");
        } else {
             link.href = element.src;
        }
        
        link.click();
    }

    function imprimirFichaCompleta() {
        window.print();
    }

    function imprimirApenasQR() {
        // Re-seleciona para garantir
        const element = document.querySelector("#qrCodeContainer canvas") || document.querySelector("#qrCodeContainer img");

        if (!element) {
            alert("QR Code não encontrado.");
            return;
        }

        let src = '';
        if (element.tagName === 'CANVAS') {
            src = element.toDataURL("image/png");
        } else {
            src = element.src;
        }

        const printWindow = window.open('', '', 'height=500,width=500');
        printWindow.document.write('<html><head><title>Imprimir QR Code</title>');
        printWindow.document.write('<style>body { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; font-family: sans-serif; text-align: center; }</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h3><?= htmlspecialchars($paciente['nome_completo']) ?></h3>');
        printWindow.document.write('<img src="' + src + '" width="200">');
        printWindow.document.write('<p>Scan para acessar a ficha médica de emergência.</p>');
        printWindow.document.write('</body></html>');
        
        printWindow.document.close();
        
        // Aguarda a imagem carregar na nova janela antes de imprimir
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            // printWindow.close(); // Opcional: fechar automaticamente
        };
    }
</script>

</body>
</html>
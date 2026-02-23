<?php
// --- LINHAS DE DEBUG (PODE MANTER POR ENQUANTO) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------

session_start();

// 1. SEGURANÇA E DEPENDÊNCIAS
if (!isset($_SESSION['usuario_logado'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . '/vendor/autoload.php';

// 2. CAPTURAR TIPO DE RELATÓRIO
$tipo_relatorio = $_POST['tipo_relatorio'] ?? '';

// 3. CLASSE PERSONALIZADA PARA CABEÇALHO E RODAPÉ
class MYPDF extends TCPDF {
    public function Header() {
        // Use o caminho absoluto para o logo
        $caminhoLogo = '/var/www/anamneseqrcode/logo.png';
        // O @ suprime erros caso a imagem não possa ser lida, evitando que o PDF quebre.
        @$this->Image($caminhoLogo, 10, 8, 25, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 15, 'Relatório do Sistema Médico', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 15, 'Gerado em: ' . date('d/m/Y H:i'), 0, false, 'R', 0, '', 0, false, 'M', 'M');
        $this->Line(10, 22, $this->getPageWidth() - 10, 22);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// 4. DIRECIONAR PARA A FUNÇÃO CORRETA
switch ($tipo_relatorio) {
    case 'lista_pacientes':
        gerarRelatorioListaPacientes($pdo);
        break;
    
    case 'agendamentos_periodo':
        $data_inicio = $_POST['data_inicio'] ?? null;
        $data_fim = $_POST['data_fim'] ?? null;
        gerarRelatorioAgendamentosPeriodo($pdo, $data_inicio, $data_fim);
        break;

    default:
        // Evita página em branco se nenhum relatório for selecionado
        if (!empty($_POST)) {
            die("Tipo de relatório inválido ou não especificado.");
        }
        // Se a página for acessada diretamente, redireciona
        header('Location: relatorios.php');
        exit;
}


function gerarRelatorioListaPacientes($pdo) {
    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    $pdf->SetCreator('Sistema Médico QR');
    $pdf->SetAuthor($_SESSION['usuario_nome']);
    $pdf->SetTitle('Relatório de Pacientes');
    $pdf->SetMargins(10, 28, 10);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Lista Completa de Pacientes', 0, 1, 'L');
    $pdf->Ln(4); // Adiciona um pequeno espaço

    try {
        $stmt = $pdo->query("SELECT id, nome_completo, cpf, telefone, condicao_saude FROM pacientes ORDER BY nome_completo ASC");
        $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die("Erro ao buscar pacientes: " . $e->getMessage());
    }

    // --- LÓGICA DA TABELA COM MultiCell ---
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(230, 230, 230); // Fundo cinza claro para o cabeçalho
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(180, 180, 180); // Cor da borda
    $pdf->SetLineWidth(0.2);

    // Definição das larguras das colunas (soma = 190mm)
    $w = [15, 65, 35, 30, 45];
    $header = ['ID', 'Nome Completo', 'CPF', 'Telefone', 'Condição de Saúde'];

    // Cabeçalho
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();

    // Dados
    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetFillColor(255);
    if (count($pacientes) > 0) {
        foreach ($pacientes as $p) {
            $pdf->MultiCell($w[0], 6, $p['id'], 1, 'C', 0, 0);
            $pdf->MultiCell($w[1], 6, $p['nome_completo'], 1, 'L', 0, 0);
            $pdf->MultiCell($w[2], 6, $p['cpf'], 1, 'C', 0, 0);
            $pdf->MultiCell($w[3], 6, $p['telefone'], 1, 'C', 0, 0);
            $pdf->MultiCell($w[4], 6, $p['condicao_saude'], 1, 'L', 0, 1); // O último '1' quebra a linha
        }
    } else {
        $pdf->Cell(array_sum($w), 10, 'Nenhum paciente encontrado.', 1, 1, 'C', 1);
    }

    $pdf->Output('relatorio_pacientes_' . date('Y-m-d_H-i-s') . '.pdf', 'I');
    exit;
}


/**
 * FUNÇÃO ATUALIZADA para gerar o relatório de agendamentos.
 */
function gerarRelatorioAgendamentosPeriodo($pdo, $data_inicio, $data_fim) {
    if (empty($data_inicio) || empty($data_fim)) {
        die("Por favor, selecione uma data de início e uma data final.");
    }

    $pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('Sistema Médico QR');
    $pdf->SetAuthor($_SESSION['usuario_nome']);
    $pdf->SetTitle('Relatório de Agendamentos');
    $pdf->SetMargins(10, 28, 10);
    $pdf->SetAutoPageBreak(TRUE, 25);
    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Relatório de Agendamentos por Período', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Cell(0, 8, 'Período: ' . date('d/m/Y', strtotime($data_inicio)) . ' a ' . date('d/m/Y', strtotime($data_fim)), 0, 1, 'L');
    $pdf->Ln(4);

    try {
        $sql = "SELECT a.data_agendamento, a.status, p.nome_completo FROM agendamentos a JOIN pacientes p ON a.paciente_id = p.id WHERE DATE(a.data_agendamento) BETWEEN :inicio AND :fim ORDER BY a.data_agendamento ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':inicio' => $data_inicio, ':fim' => $data_fim]);
        $agendamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        die("Erro ao buscar agendamentos: " . $e->getMessage());
    }

    // --- LÓGICA DA TABELA COM MultiCell ---
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(180, 180, 180);
    $pdf->SetLineWidth(0.2);

    $w = [45, 105, 40];
    $header = ['Data e Hora', 'Paciente', 'Status'];

    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();

    $pdf->SetFont('helvetica', '', 8);
    $pdf->SetFillColor(255);
    if (count($agendamentos) > 0) {
        foreach ($agendamentos as $ag) {
            $pdf->MultiCell($w[0], 6, date('d/m/Y H:i', strtotime($ag['data_agendamento'])), 1, 'C', 0, 0);
            $pdf->MultiCell($w[1], 6, $ag['nome_completo'], 1, 'L', 0, 0);
            $pdf->MultiCell($w[2], 6, $ag['status'], 1, 'C', 0, 1);
        }
    } else {
        $pdf->Cell(array_sum($w), 10, 'Nenhum agendamento encontrado neste período.', 1, 1, 'C', 1);
    }

    $pdf->Output('relatorio_agendamentos_' . date('Y-m-d_H-i-s') . '.pdf', 'I');
    exit;
}
?>
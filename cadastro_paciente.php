<?php
// A conexão com o banco não é necessária para exibir o formulário inicial
// require_once __DIR__ . "/includes/db.php"; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro de Paciente - Anamnese QR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
  <style>
    /* Layout e Cores */
    body { 
        background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%); 
        min-height: 100vh; 
        display: flex;
        flex-direction: column;
    }
    .main-content {
        flex: 1;
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    .section-title { border-left: 4px solid #0d6efd; padding-left: 10px; }
    .card { border-radius: 12px; border: none; }
    
    /* Input de Senha */
    .password-wrapper { position: relative; }
    .toggle-password-icon {
      position: absolute; top: 50%; right: 15px; transform: translateY(-50%);
      cursor: pointer; color: #6c757d;
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

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="bi bi-hospital"></i> Anamnese QR
        </a>
        <div class="navbar-nav ms-auto">
            <a class="btn btn-outline-secondary me-2" href="index.php">
                Voltar ao Início
            </a>
            <a class="btn btn-primary" href="login-paciente.php">
                <i class="bi bi-person-check-fill"></i> Já tenho conta
            </a>
        </div>
    </div>
</nav>

<div class="container main-content">
  <div class="row justify-content-center">
    <div class="col-lg-10">
      <div class="card shadow-lg">
        <div class="card-body p-5">
          <h2 class="card-title mb-4 text-primary fw-bold"><i class="bi bi-person-plus-fill"></i> Cadastro de Paciente</h2>
          
          <div id="formError" class="alert alert-danger d-none mb-3"></div>

          <form id="patientForm" class="row g-3">
            
            <div class="col-12 mt-4"><h4 class="section-title text-muted mb-3">Dados Pessoais</h4></div>
            
            <div class="col-md-6">
              <label class="form-label" for="nome">Nome Completo *</label>
              <input id="nome" class="form-control" required>
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="cpf">CPF *</label>
              <input id="cpf" class="form-control" placeholder="000.000.000-00" required>
            </div>
          
            <div class="col-md-6">
              <label class="form-label" for="email">E-mail *</label>
              <input id="email" class="form-control" required type="email">
            </div>

            <div class="col-md-6"></div> <div class="col-md-6">
              <label class="form-label" for="senha">Senha *</label>
              <div class="password-wrapper">
                <input type="password" id="senha" class="form-control" required>
                <i class="bi bi-eye-slash toggle-password-icon" id="toggleSenha" onclick="togglePassword('senha', this)"></i>
              </div>
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="confirmarSenha">Confirmar Senha *</label>
              <div class="password-wrapper">
                <input type="password" id="confirmarSenha" class="form-control" required>
                <i class="bi bi-eye-slash toggle-password-icon" id="toggleConfirmarSenha" onclick="togglePassword('confirmarSenha', this)"></i>
              </div>
            </div>
          
            <div class="col-md-4">
              <label class="form-label" for="nascimento">Nascimento *</label>
              <input type="date" id="nascimento" class="form-control" required>
            </div>
            
            <div class="col-md-4">
              <label class="form-label" for="genero">Gênero *</label>
              <select id="genero" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Mulher</option>
                <option>Homem</option>
                <option>Mulher Trans</option>
                <option>Homem Trans</option>
                <option>Não binário</option>
                <option>Outro</option>
                <option>Prefiro não informar</option>
              </select>
            </div>
            
            <div class="col-md-4">
              <label class="form-label" for="pronomes">Pronomes *</label>
              <select id="pronomes" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Ela/dela</option>
                <option>Ele/dele</option>
                <option>Elu/delu</option>
                <option>Prefiro não informar</option>
              </select>
            </div>
            
            <div class="col-md-4">
              <label class="form-label" for="estadoCivil">Estado Civil *</label>
              <select id="estadoCivil" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Solteiro</option>
                <option>Casado</option>
                <option>União Estável</option>
                <option>Separado</option>
                <option>Divorciado</option>
                <option>Viúvo</option>
                <option>Prefiro não informar</option>
              </select>
            </div>
            
            <div class="col-md-8">
              <label class="form-label">Naturalidade *</label>
              <div class="row g-2">
                <div class="col-md-6">
                  <select id="estado" class="form-select" required onchange="carregarCidades()">
                    <option value="">-- Selecione um Estado --</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <select id="cidade" class="form-select" required disabled>
                    <option value="">-- Selecione uma Cidade --</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="col-12 mt-4"><h4 class="section-title text-muted mb-3">Endereço</h4></div>
            
            <div class="col-md-3">
              <label class="form-label" for="cep">CEP</label>
              <div class="input-group">
                <input id="cep" class="form-control" maxlength="9" placeholder="00000-000">
                <button class="btn btn-outline-secondary" type="button" onclick="buscarEnderecoPorCEP()">
                  <i class="bi bi-search"></i>
                </button>
              </div>
            </div>
            
            <div class="col-md-7">
              <label class="form-label" for="logradouro">Rua</label>
              <input id="logradouro" class="form-control">
            </div>
            
            <div class="col-md-2">
              <label class="form-label" for="numero">Número</label>
              <input id="numero" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="complemento">Complemento</label>
              <input id="complemento" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="bairro">Bairro</label>
              <input id="bairro" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="localidade">Cidade</label>
              <input id="localidade" class="form-control" readonly>
            </div>
            
            <div class="col-md-4">
              <label class="form-label" for="uf">Estado</label>
              <input id="uf" class="form-control" readonly>
            </div>
            
            <div class="col-md-12">
              <label class="form-label" for="referencia">Ponto de Referência</label>
              <input id="referencia" class="form-control">
            </div>

            <div class="col-12 mt-4"><h4 class="section-title text-muted mb-3">Contatos</h4></div>
            
            <div class="col-md-6">
              <label class="form-label" for="telefone">Seu Telefone *</label>
              <input id="telefone" class="form-control" required placeholder="(11) 99999-9999">
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="contatoEmergenciaNome">Nome do Contato de Emergência *</label>
              <input id="contatoEmergenciaNome" class="form-control" required placeholder="Ex: Maria (Mãe)">
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="telefoneEmergencia">Telefone de Emergência *</label>
              <input id="telefoneEmergencia" class="form-control" required placeholder="(11) 99999-9999">
            </div>

            <div class="col-12 mt-4"><h4 class="section-title text-muted mb-3">Informações Médicas</h4></div>
            
            <div class="col-md-6">
              <label class="form-label" for="condicao_saude">Condição de saúde Principal *</label>
              <select id="condicao_saude" class="form-select" required>
                <option value="">Selecione...</option>
                <option>Pessoa com 60 anos ou mais</option>
                <option>Pessoa com necessidades especiais (PnE)</option>
                <option>Condição crônica de saúde</option>
                <option>Condição rara de saúde</option>
                <option>Nenhuma condição preexistente</option>
                <option>Outras condições (informe em observações)</option>
              </select>
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="tipoSanguineo">Tipo Sanguíneo *</label>
              <select id="tipoSanguineo" class="form-select" required>
                <option value="">Selecione...</option>
                <option>A+</option> <option>A-</option>
                <option>B+</option> <option>B-</option>
                <option>AB+</option> <option>AB-</option>
                <option>O+</option> <option>O-</option>
                <option>Não sei informar</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Medicamentos em uso</label>
              <div class="card bg-light border-0 p-3">
                  <ul id="listaMedicamentos" class="list-group mb-2"></ul>
                  <div class="row g-2">
                    <div class="col-md-4"><input id="medicamentoNome" class="form-control" placeholder="Nome do remédio"></div>
                    <div class="col-md-4"><input id="medicamentoDosagem" class="form-control" placeholder="Dosagem (ex: 500mg)"></div>
                    <div class="col-md-4"><input id="medicamentoFrequencia" class="form-control" placeholder="Frequência (ex: 8/8h)"></div>
                  </div>
                  <button type="button" class="btn btn-outline-secondary btn-sm mt-2 w-auto" style="width: fit-content;" onclick="adicionarMedicamento()">
                      <i class="bi bi-plus-circle"></i> Adicionar à lista
                  </button>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label" for="condicoes">Histórico familiar</label>
              <textarea id="condicoes" class="form-control" rows="2" placeholder="Ex: Pai diabético, Mãe hipertensa..."></textarea>
            </div>
            
            <div class="col-12">
              <label class="form-label" for="alergias">Alergias</label>
              <textarea id="alergias" class="form-control" rows="2" placeholder="Ex: Dipirona, Penicilina, Camarão..."></textarea>
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="medico">Médico Responsável (Opcional)</label>
              <input id="medico" class="form-control">
            </div>
            
            <div class="col-md-6">
              <label class="form-label" for="contatoMedico">Contato do Médico (Opcional)</label>
              <input id="contatoMedico" class="form-control">
            </div>
            
            <div class="col-12">
              <label class="form-label" for="observacoes">Outras Observações</label>
              <textarea id="observacoes" class="form-control" rows="2"></textarea>
            </div>

            <div class="col-12 mt-4 mb-3">
              <button class="btn btn-primary w-100 py-3 fw-bold fs-5 shadow-sm" type="submit">
                <i class="bi bi-check-circle-fill me-2"></i> Finalizar Cadastro
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="qrModal" class="modal fade" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center p-4">
        <h3 class="modal-title mb-3 text-success"><i class="bi bi-check-circle"></i> Sucesso!</h3>
        <p>Seu cadastro foi realizado.</p>
        <div id="qrCodeContainer" class="mb-3 d-flex justify-content-center p-3 bg-light rounded border">
            </div>
        <p class="text-muted small mb-3">Salve este QR Code. Ele é o seu acesso rápido em emergências.</p>
        <div class="d-flex justify-content-center gap-2">
          <button class="btn btn-success" onclick="downloadQR()">
            <i class="bi bi-download"></i> Baixar Imagem
          </button>
          <a href="login-paciente.php" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right"></i> Ir para Login
          </a>
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

<script src="app.js"></script>
<script>
  function togglePassword(inputId, iconElement) {
    const input = document.getElementById(inputId);
    // Altera o tipo do input (de password para text e vice-versa)
    input.type = input.type === "password" ? "text" : "password";
    // Altera o ícone (de olho fechado para aberto e vice-versa)
    iconElement.classList.toggle('bi-eye-slash');
    iconElement.classList.toggle('bi-eye');
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
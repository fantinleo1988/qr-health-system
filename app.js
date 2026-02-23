/**
 * Sistema de Gerenciamento de Pacientes com QR Code
 * Versão Final, Corrigida e Refatorada
 */

// Variável global para armazenar a lista de pacientes
let pacientes = [];

// ==============================================
// INICIALIZAÇÃO E EVENT LISTENERS GLOBAIS
// ==============================================
document.addEventListener('DOMContentLoaded', async () => {

    // Funções de Inicialização
    await carregarEstados();
    toggleSection('cadastro'); // Inicia na tela de cadastro

    // Listeners de Máscaras de Input
    document.getElementById('cpf').addEventListener('input', e => {
        e.target.value = formatarCPF(e.target.value);
    });
    document.getElementById('telefone').addEventListener('input', e => {
        e.target.value = formatarTelefone(e.target.value);
    });
    document.getElementById('telefoneEmergencia').addEventListener('input', e => {
        e.target.value = formatarTelefone(e.target.value);
    });
    document.getElementById('cep').addEventListener('input', e => {
        let cep = e.target.value.replace(/\D/g, '');
        if (cep.length > 5) cep = cep.replace(/(\d{5})(\d)/, '$1-$2');
        e.target.value = cep.substring(0, 9);
    });

    // Delegação de Eventos para a lista de medicamentos
    const listaMedicamentos = document.getElementById('listaMedicamentos');
    if (listaMedicamentos) {
        listaMedicamentos.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.btn-remover-medicamento');
            if (removeButton) {
                removeButton.parentElement.remove();
            }
        });
    }

    // Listener do Formulário de Cadastro
    const patientForm = document.getElementById('patientForm');
    if (patientForm) {
        patientForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const form = this;
            const formError = document.getElementById('formError');
            const submitButton = form.querySelector('button[type="submit"]');

            formError.classList.add('d-none');
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

            const requiredFields = ['nome_completo', 'data_nascimento', 'cpf', 'telefone', 'senha', 'confirmarSenha'];
            let hasError = false;
            for (const fieldId of requiredFields) {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    hasError = true;
                }
            }

            if (hasError) {
                formError.textContent = 'Por favor, preencha todos os campos obrigatórios destacados.';
                formError.classList.remove('d-none');
                return;
            }

            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmarSenha').value;
            if (senha !== confirmarSenha) {
                formError.textContent = 'As senhas não coincidem. Por favor, verifique.';
                formError.classList.remove('d-none');
                document.getElementById('senha').classList.add('is-invalid');
                document.getElementById('confirmarSenha').classList.add('is-invalid');
                return;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());

                data.medicamentos = Array.from(document.getElementById('listaMedicamentos').children)
                    .map(li => {
                        const text = li.querySelector('span').textContent || '';
                        const parts = text.split(' - ');
                        return {
                            nome: (parts[0] || '').trim(),
                            dosagem: (parts[1] || '').trim(),
                            frequencia: (parts[2] || '').trim()
                        };
                    });

                const response = await fetch('salvar_paciente.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success && result.pacienteId) {
                    const qrModalElement = document.getElementById('qrModal');
                    const qrModal = new bootstrap.Modal(qrModalElement);

                    gerarQRCode(result.pacienteId);

                    qrModalElement.addEventListener('hidden.bs.modal', function() {
                        window.location.href = 'login_paciente.php';
                    }, { once: true });

                    qrModal.show();
                    form.reset();
                    document.getElementById('listaMedicamentos').innerHTML = '';
                } else {
                    formError.textContent = result.error || 'Ocorreu um erro desconhecido ao cadastrar.';
                    formError.classList.remove('d-none');
                }
            } catch (error) {
                formError.textContent = 'Erro de conexão com o servidor. Tente novamente.';
                formError.classList.remove('d-none');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Salvar Cadastro';
            }
        });
    }

    // Listeners dos Filtros da Lista
    ['filtroTipo', 'buscaNome', 'buscaCPF'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', atualizarLista);
        }
    });
});

// ==============================================
// FUNÇÕES DE COMUNICAÇÃO COM O BACKEND
// ==============================================

async function carregarPacientes() {
    try {
        const response = await fetch('includes/pacientes.php');
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const responseData = await response.json();
        pacientes = (responseData && responseData.success && Array.isArray(responseData.data)) ? responseData.data : [];
    } catch (error) {
        console.error('Erro ao carregar pacientes:', error);
        pacientes = [];
    }
    return pacientes;
}

async function confirmarExclusao(id) {
    if (confirm("Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.")) {
        try {
            const response = await fetch(`includes/pacientes.php?id=${id}`, {
                method: 'DELETE'
            });
            const resultado = await response.json();
            if (resultado.success) {
                alert("Paciente excluído com sucesso!");
                await atualizarLista();
            } else {
                throw new Error(resultado.error || "Não foi possível excluir o paciente.");
            }
        } catch (error) {
            console.error("Erro ao excluir paciente:", error);
            alert(`Erro: ${error.message}`);
        }
    }
}

// ==============================================
// FUNÇÕES DE MANIPULAÇÃO DA INTERFACE
// ==============================================

function toggleSection(idToShow) {
    document.querySelectorAll('.section').forEach(section => section.classList.add('d-none'));
    const section = document.getElementById(`section-${idToShow}`);
    if (section) section.classList.remove('d-none');
    
    document.querySelectorAll('button[id^="btn-"]').forEach(btn => btn.classList.remove('active'));
    const activeBtn = document.getElementById(`btn-${idToShow}`);
    if (activeBtn) activeBtn.classList.add('active');

    if (idToShow === 'lista') {
        atualizarLista();
    }
}

function visualizarPaciente(id) {
    const paciente = pacientes.find(p => p.id == id);
    if (!paciente) return alert('Paciente não encontrado');
    mostrarModalPaciente(paciente);
}

function mostrarModalPaciente(paciente) {
    const idade = paciente.data_nascimento ? new Date().getFullYear() - new Date(paciente.data_nascimento).getFullYear() : 'N/I';
    
    let medicamentosHtml = 'Nenhum medicamento registrado';
    if (paciente.medicamentos && paciente.medicamentos.length > 2) {
        try {
            const meds = JSON.parse(paciente.medicamentos);
            if (Array.isArray(meds) && meds.length > 0) {
                medicamentosHtml = '<ul class="list-unstyled mb-0">';
                meds.forEach(m => {
                    medicamentosHtml += `<li><strong>${m.nome || ''}</strong>: ${m.dosagem || ''} - ${m.frequencia || ''}</li>`;
                });
                medicamentosHtml += '</ul>';
            }
        } catch (e) {
            medicamentosHtml = paciente.medicamentos;
        }
    }

    const modalContent = `
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes do Paciente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="bi bi-person"></i> Dados Pessoais</h6>
                            <p><strong>Nome:</strong> ${paciente.nome_completo || 'Não informado'}</p>
                            <p><strong>Nascimento:</strong> ${paciente.data_nascimento ? new Date(paciente.data_nascimento).toLocaleDateString('pt-BR') : 'N/I'} (${idade} anos)</p>
                            <p><strong>CPF:</strong> ${paciente.cpf || 'Não informado'}</p>
                            <p><strong>Gênero:</strong> ${paciente.genero || 'Não informado'}</p>
                            <p><strong>Pronomes:</strong> ${paciente.pronomes || 'Não informado'}</p>
                            <p><strong>Estado Civil:</strong> ${paciente.estado_civil || 'Não informado'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="bi bi-house"></i> Endereço</h6>
                            <p><strong>CEP:</strong> ${paciente.cep || 'Não informado'}</p>
                            <p><strong>Logradouro:</strong> ${paciente.logradouro || 'Não informado'}, ${paciente.numero || 'S/N'}</p>
                            <p><strong>Bairro:</strong> ${paciente.bairro || 'Não informado'}</p>
                            <p><strong>Cidade/UF:</strong> ${paciente.localidade || 'Não informado'}/${paciente.uf || 'N/I'}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="bi bi-telephone"></i> Contatos</h6>
                            <p><strong>Telefone:</strong> ${paciente.telefone || 'Não informado'}</p>
                            <p><strong>Contato de Emergência:</strong> ${paciente.contato_emergencia_nome || 'Não informado'} (${paciente.telefone_emergencia || 'N/I'})</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3"><i class="bi bi-heart-pulse"></i> Informações Médicas</h6>
                            <p><strong>Condição de Saúde:</strong> ${paciente.condicao_saude || 'Não informado'}</p>
                            <p><strong>Tipo Sanguíneo:</strong> ${paciente.tipo_sanguineo || 'Não informado'}</p>
                            <p><strong>Médico Responsável:</strong> ${paciente.medico_responsavel || 'Não informado'}</p>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted mb-2"><i class="bi bi-exclamation-triangle"></i> Alergias</h6>
                            <div class="bg-light p-2 rounded">${paciente.alergias || 'Nenhuma alergia registrada'}</div>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted mb-2"><i class="bi bi-capsule"></i> Medicamentos em Uso</h6>
                            <div class="bg-light p-2 rounded">${medicamentosHtml}</div>
                        </div>
                        <div class="col-12">
                            <h6 class="text-muted mb-2"><i class="bi bi-people"></i> Histórico Familiar</h6>
                            <div class="bg-light p-2 rounded">${paciente.historico_familiar || 'Nenhum histórico registrado'}</div>
                        </div>
                         <div class="col-12">
                            <h6 class="text-muted mb-2"><i class="bi bi-chat-left-text"></i> Observações</h6>
                            <div class="bg-light p-2 rounded">${paciente.observacoes || 'Nenhuma observação'}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    `;

    const existingModal = document.getElementById('modalPaciente');
    if (existingModal) existingModal.remove();

    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'modalPaciente';
    modal.innerHTML = modalContent;
    document.body.appendChild(modal);
    
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();

    modal.addEventListener('hidden.bs.modal', () => modal.remove());
}

async function atualizarLista() {
    await carregarPacientes();
    
    const filtroCondicao = document.getElementById('filtroCondicao')?.value || '';
    const buscaNome = document.getElementById('buscaNome')?.value.toLowerCase() || '';
    const buscaCPF = document.getElementById('buscaCPF')?.value.replace(/\D/g, '') || '';
    
    const filtrados = pacientes.filter(p => {
        const matchCondicao = !filtroCondicao || (p.condicao_saude === filtroCondicao);
        const matchNome = !buscaNome || (p.nome_completo || '').toLowerCase().includes(buscaNome);
        const rawCPF = (p.cpf || '').replace(/\D/g, '');
        const matchCPF = !buscaCPF || rawCPF.includes(buscaCPF);
        return matchCondicao && matchNome && matchCPF;
    });
    
    const totalPacientesElement = document.getElementById('totalPacientes');
    if (totalPacientesElement) {
        totalPacientesElement.textContent = filtrados.length;
    }
    renderizarListaPacientes(filtrados);
}

function renderizarListaPacientes(pacientesFiltrados) {
    const lista = document.getElementById('listaPacientes');
    if (!lista) return;
    lista.innerHTML = pacientesFiltrados.length > 0 ?
        pacientesFiltrados.map(p => criarCardPaciente(p)).join('') :
        '<div class="col-12"><p class="text-center text-muted py-4">Nenhum paciente encontrado</p></div>';
}

function criarCardPaciente(paciente) {
    const idade = paciente.data_nascimento ? (new Date().getFullYear() - new Date(paciente.data_nascimento).getFullYear()) : 'N/A';
    
    return `
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title mb-1">${paciente.nome_completo || 'Paciente'}</h5>
                            <div class="card-subtitle text-muted small mb-2">
                                ${paciente.condicao_saude || 'Não informado'} • ${idade} anos • Sangue: ${paciente.tipo_sanguineo || 'N/I'}
                            </div>
                            <div class="text-muted small">CPF: ${paciente.cpf || 'Não informado'}</div>
                        </div>
                        <div class="d-flex gap-1 flex-wrap justify-content-end" style="min-width: 150px;">
                            <button class="btn btn-sm btn-outline-primary" onclick="visualizarPaciente(${paciente.id})"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-outline-success" onclick="gerarQRCodePorId(${paciente.id})"><i class="bi bi-qr-code"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmarExclusao(${paciente.id})"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
}

function gerarQRCode(pacienteId) {
    const qrContainer = document.getElementById('qrCodeContainer');
    
    // Limpa QR Codes gerados anteriormente
    qrContainer.innerHTML = ''; 

    // 1. Cria a URL ABSOLUTA (Essencial para leitura em outros dispositivos)
    // window.location.origin pega "http://localhost" ou "https://seusite.com"
    const baseUrl = window.location.origin; 
    const urlParaQRCode = `${baseUrl}/visualizar_paciente.php?id=${pacienteId}`;

    // Debug: Mostra no console qual URL está sendo gerada
    console.log('Gerando QR Code para:', urlParaQRCode);

    // 2. Gera o QR Code
    new QRCode(qrContainer, {
        text: urlParaQRCode,
        width: 200,
        height: 200,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H // Nível H permite leitura mesmo se danificado
    });
}

// A função de chamada permanece a mesma, mas certifique-se que o ID está correto
async function gerarQRCodePorId(id) {
    // Encontra o paciente na lista global 'pacientes'
    const paciente = pacientes.find(p => p.id == id);
    
    if (paciente) {
        // Atualiza o título do modal (opcional, para melhor UX)
        const modalTitle = document.querySelector('#qrModal .modal-title');
        if(modalTitle) modalTitle.textContent = `QR Code: ${paciente.nome_completo}`;

        // Gera o código
        gerarQRCode(paciente.id);
        
        // Abre o modal
        new bootstrap.Modal(document.getElementById('qrModal')).show();
    } else {
        alert("Erro: Paciente não encontrado na memória local.");
    }
}

function downloadQR() {
    const canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) return alert('QR Code não foi gerado');
    const link = document.createElement('a');
    link.download = `qr-code-paciente.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
}

function closeModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('qrModal'));
    if (modal) modal.hide();
}

function adicionarMedicamento() {
    const nome = document.getElementById('medicamentoNome').value.trim();
    const dosagem = document.getElementById('medicamentoDosagem').value.trim();
    const frequencia = document.getElementById('medicamentoFrequencia').value.trim();
    if (!nome || !dosagem || !frequencia) return alert('Preencha todos os campos do medicamento');
    
    const lista = document.getElementById('listaMedicamentos');
    const item = document.createElement('li');
    item.className = 'list-group-item d-flex justify-content-between align-items-center';
    item.innerHTML = `<span>${nome} - ${dosagem} - ${frequencia}</span><button type="button" class="btn btn-sm btn-outline-danger btn-remover-medicamento"><i class="bi bi-trash"></i></button>`;
    lista.appendChild(item);
    
    document.getElementById('medicamentoNome').value = '';
    document.getElementById('medicamentoDosagem').value = '';
    document.getElementById('medicamentoFrequencia').value = '';
}


// ==============================================
// FUNÇÕES AUXILIARES (COMPLETAS)
// ==============================================

async function carregarEstados() {
    try {
        const response = await fetch('includes/api/get_estados.php');
        const estados = await response.json();
        const select = document.getElementById('estado');
        select.innerHTML = '<option value="">-- Selecione um Estado --</option>';
        estados.forEach(estado => {
            const option = document.createElement('option');
            option.value = estado.id;
            option.textContent = `${estado.nome} (${estado.sigla})`;
            select.appendChild(option);
        });
    } catch (error) {
        console.error('Erro ao carregar estados:', error);
    }
}

async function carregarCidades() {
    const estadoId = document.getElementById('estado').value;
    const select = document.getElementById('cidade');
    select.innerHTML = '<option value="">-- Selecione uma Cidade --</option>';
    select.disabled = true;
    if (!estadoId) return;
    
    try {
        const response = await fetch(`includes/api/get_cidades.php?estado_id=${estadoId}`);
        const cidades = await response.json();
        cidades.forEach(cidade => {
            const option = document.createElement('option');
            option.value = cidade.id;
            option.textContent = cidade.nome;
            select.appendChild(option);
        });
        select.disabled = false;
    } catch (error) {
        console.error('Erro ao carregar cidades:', error);
    }
}

async function buscarEnderecoPorCEP() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    if (cep.length !== 8) return;
    
    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const endereco = await response.json();
        if (endereco.erro) {
            alert('CEP não encontrado');
            return;
        }
        
        document.getElementById('logradouro').value = endereco.logradouro || '';
        document.getElementById('bairro').value = endereco.bairro || '';
        document.getElementById('localidade').value = endereco.localidade || '';
        document.getElementById('uf').value = endereco.uf || '';
        document.getElementById('numero').focus();
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        alert('Erro ao consultar CEP. Verifique sua conexão.');
    }
}

function formatarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf.substring(0, 14);
}

function formatarTelefone(tel) {
    tel = tel.replace(/\D/g, '');
    if (tel.length > 10) {
        tel = tel.replace(/^(\d\d)(\d{5})(\d{4}).*/, "($1) $2-$3");
    } else if (tel.length > 5) {
        tel = tel.replace(/^(\d\d)(\d{4})(\d{0,4}).*/, "($1) $2-$3");
    } else if (tel.length > 2) {
        tel = tel.replace(/^(\d\d)(\d{0,5}).*/, "($1) $2");
    } else {
        tel = tel.replace(/^(\d*)/, "($1");
    }
    return tel.substring(0, 15);
}
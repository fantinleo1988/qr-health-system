// Dados dos pacientes (simulando banco de dados local)
let pacientes = JSON.parse(localStorage.getItem('pacientesMedicos')) || [];
let currentQRData = '';

// Dados de exemplo para demonstração
if (pacientes.length === 0) {
    const exemplosPacientes = [
        {
            id: 1,
            nome: "Maria Silva Santos",
            nascimento: "1945-03-15",
            cpf: "123.456.789-00",
            telefone: "(11) 99999-1111",
            tipoPaciente: "idoso",
            tipoSanguineo: "O+",
            condicoes: "Diabetes tipo 2, Hipertensão arterial, Artrose",
            medicamentos: "Metformina 850mg 2x/dia, Losartana 50mg 1x/dia, Paracetamol conforme necessário",
            alergias: "Penicilina, Dipirona",
            medico: "Dr. João Cardiologista - CRM 12345-SP",
            contatoEmergenciaNome: "Pedro Santos",
            telefoneEmergencia: "(11) 98888-2222",
            observacoes: "Paciente com mobilidade reduzida, usa bengala"
        },
        {
            id: 2,
            nome: "Carlos Eduardo Lima",
            nascimento: "1980-07-22",
            cpf: "987.654.321-00",
            telefone: "(11) 97777-3333",
            tipoPaciente: "deficiencia",
            tipoSanguineo: "A+",
            condicoes: "Paraplegia, Infecção urinária recorrente",
            medicamentos: "Antibiótico profilático, Vitamina D",
            alergias: "Nenhuma conhecida",
            medico: "Dra. Ana Neurologista - CRM 54321-SP",
            contatoEmergenciaNome: "Lucia Lima",
            telefoneEmergencia: "(11) 96666-4444",
            observacoes: "Cadeirante, necessita acessibilidade"
        },
        {
            id: 3,
            nome: "Ana Paula Oliveira",
            nascimento: "1975-11-08",
            cpf: "456.789.123-00",
            telefone: "(11) 95555-5555",
            tipoPaciente: "rara",
            tipoSanguineo: "B-",
            condicoes: "Síndrome de Ehlers-Danlos",
            medicamentos: "Analgésicos, Fisioterapia regular",
            alergias: "Látex, Aspirina",
            medico: "Dr. Roberto Geneticista - CRM 98765-SP",
            contatoEmergenciaNome: "Rosa Oliveira",
            telefoneEmergencia: "(11) 94444-6666",
            observacoes: "Pele muito frágil, evitar procedimentos invasivos"
        }
    ];
    
    pacientes = exemplosPacientes;
    localStorage.setItem('pacientesMedicos', JSON.stringify(pacientes));
}

// Carrega todos os estados ao abrir a página
function carregarEstados() {
    fetch("https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome")
        .then(res => res.json())
        .then(estados => {
            const estadoSelect = document.getElementById("estado");
            estados.forEach(estado => {
                const option = document.createElement("option");
                option.value = estado.sigla;
                option.text = `${estado.nome} (${estado.sigla})`;
                estadoSelect.add(option);
            });
        });
}

// Carrega cidades do estado selecionado
function carregarCidades() {
    const uf = document.getElementById("estado").value;
    const cidadeSelect = document.getElementById("cidade");
    cidadeSelect.innerHTML = "<option value=''>-- Selecione uma Cidade --</option>";
    cidadeSelect.disabled = true;

    if (!uf) return;

    fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados/${uf}/municipios`)
        .then(res => res.json())
        .then(cidades => {
            cidades.forEach(cidade => {
                const option = document.createElement("option");
                option.value = cidade.nome;
                option.text = cidade.nome;
                cidadeSelect.add(option);
            });
            cidadeSelect.disabled = false;
        });
}

// Busca endereço pelo CEP
function buscarEndereco() {
    const cep = document.getElementById("cep").value.replace(/\D/g, '');

    if (cep.length !== 8) {
        alert("CEP inválido");
        return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (data.erro) {
                alert("CEP não encontrado.");
                return;
            }

            document.getElementById("logradouro").value = data.logradouro || "";
            document.getElementById("bairro").value = data.bairro || "";
            document.getElementById("localidade").value = data.localidade || "";
            document.getElementById("uf").value = data.uf || "";
            document.getElementById("complemento").focus();
        })
        .catch(() => alert("Erro ao buscar o endereço."));
}

// Adiciona medicamento à lista
function adicionarMedicamento() {
    const nome = document.getElementById('medicamentoNome').value.trim();
    const dosagem = document.getElementById('medicamentoDosagem').value.trim();
    const frequencia = document.getElementById('medicamentoFrequencia').value.trim();

    if (nome && dosagem && frequencia) {
        const lista = document.getElementById('listaMedicamentos');
        const novoItem = document.createElement('li');
        novoItem.className = 'flex justify-between items-center bg-gray-50 p-2 rounded';
        
        novoItem.innerHTML = `
            <span>${nome} - ${dosagem}mg - ${frequencia}</span>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                ✖
            </button>
        `;
        
        lista.appendChild(novoItem);

        // Limpar os campos após adicionar
        document.getElementById('medicamentoNome').value = '';
        document.getElementById('medicamentoDosagem').value = '';
        document.getElementById('medicamentoFrequencia').value = '';
    } else {
        alert('Por favor, preencha todos os campos do medicamento.');
    }
}

// Formata CPF
function formatarCPF(cpf) {
    cpf = cpf.replace(/\D/g, '');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
    cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return cpf;
}

// Formata telefone
function formatarTelefone(telefone) {
    telefone = telefone.replace(/\D/g, '');
    telefone = telefone.replace(/(\d{2})(\d)/, '($1) $2');
    telefone = telefone.replace(/(\d{5})(\d)/, '$1-$2');
    return telefone;
}

// Navegação entre seções
function showSection(section) {
    // Esconder todas as seções
    document.querySelectorAll('.section').forEach(s => s.classList.add('hidden'));
    
    // Mostrar seção selecionada
    document.getElementById(`section-${section}`).classList.remove('hidden');
    document.getElementById(`section-${section}`).classList.add('fade-in');
    
    // Atualizar botões
    document.querySelectorAll('button[id^="btn-"]').forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white');
        btn.classList.add('text-indigo-600', 'hover:bg-indigo-50');
    });
    
    document.getElementById(`btn-${section}`).classList.add('bg-indigo-600', 'text-white');
    document.getElementById(`btn-${section}`).classList.remove('text-indigo-600', 'hover:bg-indigo-50');
    
    // Atualizar lista se necessário
    if (section === 'lista') {
        atualizarLista();
    }
}

// Formatação de CPF
document.getElementById('cpf').addEventListener('input', function(e) {
    e.target.value = formatarCPF(e.target.value);
});

// Formatação de telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    e.target.value = formatarTelefone(e.target.value);
});

// Formatação de telefone de emergência
document.getElementById('telefoneEmergencia').addEventListener('input', function(e) {
    e.target.value = formatarTelefone(e.target.value);
});

// Formatação de CEP
document.getElementById('cep').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 5) {
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
    }
    e.target.value = value;
});

// Função corrigida para gerar QR Code
function gerarQRCode(paciente) {
    // Verificar se o paciente é um objeto válido
    if (typeof paciente !== 'object') {
        try {
            paciente = JSON.parse(paciente);
        } catch (e) {
            console.error('Dados do paciente inválidos:', paciente);
            alert('Erro: Dados do paciente inválidos');
            return;
        }
    }
    
    // Criar objeto simplificado para o QR Code
    const qrData = {
        nome: paciente.nome || 'Não informado',
        tipoSanguineo: paciente.tipoSanguineo || 'Não informado',
        alergias: paciente.alergias || 'Nenhuma alergia conhecida',
        medicamentos: paciente.medicamentos || 'Nenhum medicamento registrado',
        contatoEmergencia: paciente.contatoEmergenciaNome && paciente.telefoneEmergencia 
            ? `${paciente.contatoEmergenciaNome} - ${paciente.telefoneEmergencia}`
            : 'Não informado',
        medico: paciente.medico || 'Não informado'
    };
    
    currentQRData = JSON.stringify(qrData);
    
    const container = document.getElementById('qrCodeContainer');
    container.innerHTML = '';
    
    // Gerar QR Code com tratamento de erros
    QRCode.toCanvas(container, currentQRData, {
        width: 256,
        margin: 2,
        color: {
            dark: '#1f2937',
            light: '#ffffff'
        }
    }, function (error) {
        if (error) {
            console.error('Erro ao gerar QR Code:', error);
            alert('Erro ao gerar QR Code. Verifique o console para detalhes.');
            return;
        }
        document.getElementById('qrModal').classList.remove('hidden');
        document.getElementById('qrModal').classList.add('flex');
    });
}

// Função para download do QR Code
function downloadQR() {
    const canvas = document.querySelector('#qrCodeContainer canvas');
    if (!canvas) {
        alert('QR Code não foi gerado corretamente. Tente novamente.');
        return;
    }
    
    const link = document.createElement('a');
    link.download = `qr-code-paciente-${new Date().toISOString().slice(0,10)}.png`;
    link.href = canvas.toDataURL();
    link.click();
}

// Fechar modal
function closeModal() {
    document.getElementById('qrModal').classList.add('hidden');
    document.getElementById('qrModal').classList.remove('flex');
}

// Cadastro de paciente
document.getElementById('patientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Coletar medicamentos
    const medicamentosItens = Array.from(document.getElementById('listaMedicamentos').children).map(item => {
        return item.firstChild.textContent.trim();
    }).join(', ');
    
    const paciente = {
        id: Date.now(),
        nome: document.getElementById('nome').value,
        nascimento: document.getElementById('nascimento').value,
        cpf: document.getElementById('cpf').value,
        sexo: document.getElementById('sexo').value,
        opcaoSexual: document.getElementById('opcaoSexual').value,
        estadoCivil: document.getElementById('estadoCivil').value,
        naturalidade: `${document.getElementById('cidade').value}/${document.getElementById('estado').value}`,
        endereco: {
            cep: document.getElementById('cep').value,
            logradouro: document.getElementById('logradouro').value,
            numero: document.getElementById('numero').value,
            complemento: document.getElementById('complemento').value,
            bairro: document.getElementById('bairro').value,
            cidade: document.getElementById('localidade').value,
            estado: document.getElementById('uf').value,
            referencia: document.getElementById('referencia').value
        },
        telefone: document.getElementById('telefone').value,
        contatoEmergenciaNome: document.getElementById('contatoEmergenciaNome').value,
        telefoneEmergencia: document.getElementById('telefoneEmergencia').value,
        tipoPaciente: document.getElementById('tipoPaciente').value,
        tipoSanguineo: document.getElementById('tipoSanguineo').value,
        condicoes: document.getElementById('condicoes').value,
        medicamentos: medicamentosItens,
        alergias: document.getElementById('alergias').value,
        medico: document.getElementById('medico').value,
        contatoMedico: document.getElementById('contatoMedico').value,
        observacoes: document.getElementById('observacoes').value,
        dataCadastro: new Date().toISOString()
    };
    
    pacientes.push(paciente);
    localStorage.setItem('pacientesMedicos', JSON.stringify(pacientes));
    
    // Gerar QR Code
    gerarQRCode(paciente);
    
    // Limpar formulário
    document.getElementById('patientForm').reset();
    document.getElementById('listaMedicamentos').innerHTML = '';
    document.getElementById('cidade').innerHTML = '<option value="">-- Selecione uma Cidade --</option>';
    document.getElementById('cidade').disabled = true;
});

// Atualizar lista de pacientes
function atualizarLista() {
    const lista = document.getElementById('listaPacientes');
    const filtroTipo = document.getElementById('filtroTipo').value;
    const buscaNome = document.getElementById('buscaNome').value.toLowerCase();
    const buscaCPF = document.getElementById('buscaCPF').value.replace(/\D/g, '');
    
    let pacientesFiltrados = pacientes.filter(p => {
        const matchTipo = !filtroTipo || p.tipoPaciente === filtroTipo;
        const matchNome = !buscaNome || p.nome.toLowerCase().includes(buscaNome);
        const matchCPF = !buscaCPF || p.cpf.replace(/\D/g, '').includes(buscaCPF);
        return matchTipo && matchNome && matchCPF;
    });
    
    document.getElementById('totalPacientes').textContent = pacientesFiltrados.length;
    
    lista.innerHTML = pacientesFiltrados.map(paciente => {
        const tipoIcons = {
            idoso: '👴',
            deficiencia: '♿',
            cronica: '🏥',
            rara: '🔬',
            outros: '📋'
        };
        
        const idade = new Date().getFullYear() - new Date(paciente.nascimento).getFullYear();
        
        return `
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">${paciente.nome}</h3>
                        <p class="text-gray-600">${tipoIcons[paciente.tipoPaciente]} ${paciente.tipoPaciente.charAt(0).toUpperCase() + paciente.tipoPaciente.slice(1)} • ${idade} anos • ${paciente.tipoSanguineo}</p>
                        <p class="text-sm text-gray-500 mt-1">CPF: ${paciente.cpf}</p>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="visualizarPaciente(${paciente.id})" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                            👁️ Visualizar
                        </button>
                        <button onclick="gerarQRCode(${JSON.stringify(paciente)})" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors">
                            📱 Gerar QR Code
                        </button>
                        <button onclick="excluirPaciente(${paciente.id})" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors">
                            🗑️ Excluir paciente
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>Condições:</strong> ${paciente.condicoes ? paciente.condicoes.substring(0, 50) + (paciente.condicoes.length > 50 ? '...' : '') : 'Nenhuma registrada'}
                    </div>
                    <div>
                        <strong>Contato:</strong> ${paciente.telefone}
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Visualizar paciente
function visualizarPaciente(id) {
    const paciente = pacientes.find(p => p.id === id);
    if (!paciente) return;
    
    const tipoIcons = {
        idoso: '👴 Idoso',
        deficiencia: '♿ Pessoa com Deficiência',
        cronica: '🏥 Doença Crônica',
        rara: '🔬 Doença Rara',
        outros: '📋 Outros'
    };
    
    const idade = new Date().getFullYear() - new Date(paciente.nascimento).getFullYear();
    
    // Criar um modal para exibir os detalhes
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="bg-white rounded-xl p-8 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Detalhes do Paciente</h3>
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700">
                    ✖
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">👤 Dados Pessoais</h4>
                    <p><strong>Nome:</strong> ${paciente.nome}</p>
                    <p><strong>Data Nascimento:</strong> ${new Date(paciente.nascimento).toLocaleDateString('pt-BR')} (${idade} anos)</p>
                    <p><strong>CPF:</strong> ${paciente.cpf}</p>
                    <p><strong>Sexo:</strong> ${paciente.sexo || 'Não informado'}</p>
                    <p><strong>Opção Sexual:</strong> ${paciente.opcaoSexual || 'Não informado'}</p>
                    <p><strong>Estado Civil:</strong> ${paciente.estadoCivil || 'Não informado'}</p>
                    <p><strong>Naturalidade:</strong> ${paciente.naturalidade || 'Não informado'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">🏠 Endereço</h4>
                    <p><strong>CEP:</strong> ${paciente.endereco?.cep || 'Não informado'}</p>
                    <p><strong>Logradouro:</strong> ${paciente.endereco?.logradouro || 'Não informado'} ${paciente.endereco?.numero || ''}</p>
                    <p><strong>Complemento:</strong> ${paciente.endereco?.complemento || 'Não informado'}</p>
                    <p><strong>Bairro:</strong> ${paciente.endereco?.bairro || 'Não informado'}</p>
                    <p><strong>Cidade/UF:</strong> ${paciente.endereco?.cidade || 'Não informado'}/${paciente.endereco?.estado || 'Não informado'}</p>
                    <p><strong>Referência:</strong> ${paciente.endereco?.referencia || 'Não informado'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">📞 Contatos</h4>
                    <p><strong>Telefone:</strong> ${paciente.telefone}</p>
                    <p><strong>Contato Emergência:</strong> ${paciente.contatoEmergenciaNome || 'Não informado'} - ${paciente.telefoneEmergencia || 'Não informado'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">🏥 Informações Médicas</h4>
                    <p><strong>Tipo:</strong> ${tipoIcons[paciente.tipoPaciente] || 'Não informado'}</p>
                    <p><strong>Tipo Sanguíneo:</strong> ${paciente.tipoSanguineo || 'Não informado'}</p>
                    <p><strong>Médico Responsável:</strong> ${paciente.medico || 'Não informado'}</p>
                    <p><strong>Contato Médico:</strong> ${paciente.contatoMedico || 'Não informado'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">⚠️ Alergias</h4>
                    <p>${paciente.alergias || 'Nenhuma alergia registrada'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">💊 Medicamentos</h4>
                    <p>${paciente.medicamentos || 'Nenhum medicamento registrado'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">🧬 Histórico Familiar</h4>
                    <p>${paciente.condicoes || 'Nenhuma condição registrada'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-gray-800 mb-2 border-b pb-1">📝 Observações</h4>
                    <p>${paciente.observacoes || 'Nenhuma observação'}</p>
                </div>
            </div>
            
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Excluir paciente
function excluirPaciente(id) {
    if (confirm('Tem certeza que deseja excluir este paciente?')) {
        pacientes = pacientes.filter(p => p.id !== id);
        localStorage.setItem('pacientesMedicos', JSON.stringify(pacientes));
        atualizarLista();
    }
}

// Ler QR Code
function lerQRCode() {
    const input = document.getElementById('qrInput').value.trim();
    if (!input) {
        alert('Por favor, cole os dados do QR Code.');
        return;
    }
    
    try {
        const dados = JSON.parse(input);
        exibirDadosLidos(dados);
    } catch (error) {
        alert('Dados do QR Code inválidos. Verifique se copiou corretamente.');
    }
}

// Exibir dados lidos do QR
function exibirDadosLidos(dados) {
    const container = document.getElementById('dadosLidos');
    const idade = dados.nascimento ? (new Date().getFullYear() - new Date(dados.nascimento).getFullYear()) : 'Não informada';
    
    container.innerHTML = `
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg text-left">
            <h3 class="text-xl font-bold text-red-800 mb-4">🚨 INFORMAÇÕES MÉDICAS DE EMERGÊNCIA</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">👤 Dados Pessoais</h4>
                    <p><strong>Nome:</strong> ${dados.nome || 'Não informado'}</p>
                    <p><strong>Idade:</strong> ${idade}</p>
                    <p><strong>Tipo Sanguíneo:</strong> <span class="bg-red-200 px-2 py-1 rounded font-bold">${dados.tipoSanguineo || 'Não informado'}</span></p>
                    <p><strong>Telefone:</strong> ${dados.telefone || 'Não informado'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">🚨 Contato de Emergência</h4>
                    <p class="bg-yellow-100 p-2 rounded">${dados.contatoEmergencia || 'Não informado'}</p>
                </div>
                
                <div class="md:col-span-2">
                    <h4 class="font-semibold text-red-700 mb-2">⚠️ ALERGIAS</h4>
                    <p class="bg-red-100 p-3 rounded font-semibold">${dados.alergias || 'Nenhuma alergia conhecida'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">🏥 Condições Médicas</h4>
                    <p>${dados.condicoes || 'Nenhuma condição registrada'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">💊 Medicamentos</h4>
                    <p>${dados.medicamentos || 'Nenhum medicamento registrado'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">👨‍⚕️ Médico Responsável</h4>
                    <p>${dados.medico || 'Não informado'}</p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-gray-800 mb-2">📝 Observações Especiais</h4>
                    <p>${dados.observacoes || 'Nenhuma observação'}</p>
                </div>
            </div>
        </div>
    `;
    
    container.classList.remove('hidden');
    container.classList.add('fade-in');
}

// Filtros da lista
document.getElementById('filtroTipo').addEventListener('change', atualizarLista);
document.getElementById('buscaNome').addEventListener('input', atualizarLista);
document.getElementById('buscaCPF').addEventListener('input', atualizarLista);

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    carregarEstados();
    showSection('cadastro');
});
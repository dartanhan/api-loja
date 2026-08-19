@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">💬 Assistente KN Intelligence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.kn_intelligence.dashboard') }}">KN Intelligence</a></li>
        <li class="breadcrumb-item active">Assistente IA</li>
    </ol>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-robot text-info"></i> Chatbot Analítico (Google Gemini)</h5>
                    <span class="badge bg-success">Online (Dados Conectados)</span>
                </div>
                <div class="card-body p-4" style="background-color: #f8f9fa;">
                    <!-- Área do Histórico do Chat -->
                    <div id="chat-box" style="height: 420px; overflow-y: auto; padding: 15px; border: 1px solid #e3e6f0; border-radius: 8px; background: #ffffff;" class="mb-3">
                        <div class="d-flex mb-3">
                            <div class="p-3 bg-light rounded text-dark border" style="max-width: 80%;">
                                <strong>🤖 KN Intelligence:</strong><br>
                                Olá! Sou o assistente de inteligência de dados da <strong>KN Cosméticos</strong>. Como posso te ajudar hoje? Posso analisar seu faturamento, sugestões de compras ou margens de lucro!
                            </div>
                        </div>
                    </div>

                    <!-- Form de Pergunta -->
                    <form id="chat-form" onsubmit="enviarPergunta(event)">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="pergunta-input" class="form-control form-control-lg" placeholder="Digite sua pergunta (Ex: Qual foi o faturamento este mês e quais produtos comprar?)..." required>
                            <button type="submit" id="btn-enviar" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i> Pergunta
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentConversationId = null;

function enviarPergunta(e) {
    e.preventDefault();
    const input = document.getElementById('pergunta-input');
    const btn = document.getElementById('btn-enviar');
    const chatBox = document.getElementById('chat-box');
    const pergunta = input.value.trim();

    if (!pergunta) return;

    // Adiciona pergunta do usuário na tela
    chatBox.innerHTML += `
        <div class="d-flex justify-content-end mb-3">
            <div class="p-3 bg-primary text-white rounded" style="max-width: 80%;">
                <strong>Você:</strong><br>
                ${pergunta}
            </div>
        </div>
    `;
    
    input.value = '';
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analisando...';
    chatBox.scrollTop = chatBox.scrollHeight;

    // Envia requisição via Fetch API
    fetch('{{ route("admin.kn_intelligence.ask") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            pergunta: pergunta,
            conversation_id: currentConversationId
        })
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Pergunta';

        if (data.success) {
            currentConversationId = data.conversation_id;
            const respostaFormatada = data.resposta.replace(/\n/g, '<br>');
            chatBox.innerHTML += `
                <div class="d-flex mb-3">
                    <div class="p-3 bg-light rounded text-dark border" style="max-width: 80%;">
                        <strong>🤖 KN Intelligence:</strong><br>
                        ${respostaFormatada}
                    </div>
                </div>
            `;
        } else {
            chatBox.innerHTML += `
                <div class="d-flex mb-3">
                    <div class="p-3 bg-danger text-white rounded" style="max-width: 80%;">
                        <strong>Erro:</strong> Não foi possível processar a resposta da IA.
                    </div>
                </div>
            `;
        }
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Pergunta';
        console.error(err);
    });
}
</script>
@endsection

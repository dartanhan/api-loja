@extends('layouts.layout')

@section('menu')
    @include('admin.menu')
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">💬 Assistente KN Intelligence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.kn_intelligence.dashboard') }}">KN Intelligence</a></li>
        <li class="breadcrumb-item active">Assistente IA</li>
    </ol>

    <div class="row">
        <!-- Sidebar Histórico -->
        <div class="col-md-3">
            <div class="card shadow border-0 mb-3 h-100">
                <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                    <h6 class="text-uppercase text-muted fw-bold mb-0">Histórico de Conversas</h6>
                </div>
                <div class="card-body p-0" style="overflow-y: auto; max-height: 500px;">
                    <div class="list-group list-group-flush rounded-0 mt-2">
                        @forelse($conversations as $conv)
                            <a href="{{ route('admin.kn_intelligence.assistant', ['c' => $conv->id]) }}" class="list-group-item list-group-item-action d-flex align-items-center {{ isset($currentConversation) && $currentConversation->id == $conv->id ? 'active bg-primary text-white' : '' }}">
                                <i class="fas fa-comment-alt {{ isset($currentConversation) && $currentConversation->id == $conv->id ? 'text-white' : 'text-secondary' }} me-3"></i>
                                <div class="text-truncate" style="max-width: 80%;">
                                    <small class="d-block fw-bold">{{ $conv->titulo }}</small>
                                    <small class="{{ isset($currentConversation) && $currentConversation->id == $conv->id ? 'text-light' : 'text-muted' }}" style="font-size: 0.75rem;">{{ $conv->updated_at->diffForHumans() }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="p-3 text-center text-muted">
                                <small>Nenhuma conversa anterior.</small>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('admin.kn_intelligence.assistant') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-plus"></i> Nova Conversa
                    </a>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-9">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-robot text-info"></i> Chatbot Analítico (Google Gemini) {{ isset($currentConversation) ? '- '.$currentConversation->titulo : '' }}</h5>
                    <span class="badge bg-success">Online (Dados Conectados)</span>
                </div>
                <div class="card-body p-4" style="background-color: #f8f9fa;">
                    <!-- Área do Histórico do Chat -->
                    <div id="chat-box" style="height: 480px; overflow-y: auto; padding: 15px; border: 1px solid #e3e6f0; border-radius: 8px; background: #ffffff;" class="mb-3">
                        <div class="d-flex mb-3">
                            <div class="p-3 bg-light rounded text-dark border" style="max-width: 80%;">
                                <strong>🤖 KN Intelligence:</strong><br>
                                Olá! Sou o assistente de inteligência de dados da <strong>KN Cosméticos</strong>. Como posso te ajudar hoje? Posso analisar seu faturamento, sugestões de compras ou margens de lucro!
                            </div>
                        </div>

                        @if(isset($messages))
                            @foreach($messages as $msg)
                                @if($msg->role == 'user')
                                    <div class="d-flex justify-content-end mb-3">
                                        <div class="p-3 bg-primary text-white rounded" style="max-width: 80%;">
                                            <strong>Você:</strong><br>
                                            {!! nl2br(e($msg->content)) !!}
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex mb-3">
                                        <div class="p-3 bg-light rounded text-dark border" style="max-width: 80%;">
                                            <strong>🤖 KN Intelligence:</strong><br>
                                            {!! nl2br(preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', e($msg->content))) !!}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Form de Pergunta -->
                    <form id="chat-form" onsubmit="enviarPergunta(event)">
                        @csrf
                        <div class="input-group">
                            <input type="text" id="pergunta-input" class="form-control form-control-lg" placeholder="Digite sua pergunta (Ex: Qual foi o faturamento este mês e quais produtos comprar?)..." required autocomplete="off">
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
let currentConversationId = {{ isset($currentConversation) ? $currentConversation->id : 'null' }};

window.onload = function() {
    const chatBox = document.getElementById('chat-box');
    chatBox.scrollTop = chatBox.scrollHeight;
};

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
    
    // Adiciona indicador visual de digitando (thinking)
    chatBox.innerHTML += `
        <div class="d-flex mb-3" id="loading-indicator">
            <div class="p-3 bg-light rounded text-dark border" style="max-width: 80%;">
                <strong>🤖 KN Intelligence:</strong><br>
                <div class="d-flex align-items-center mt-1">
                    <span class="spinner-grow spinner-grow-sm text-info me-1" role="status" aria-hidden="true" style="animation-delay: 0s;"></span>
                    <span class="spinner-grow spinner-grow-sm text-info me-1" role="status" aria-hidden="true" style="animation-delay: 0.2s;"></span>
                    <span class="spinner-grow spinner-grow-sm text-info me-2" role="status" aria-hidden="true" style="animation-delay: 0.4s;"></span>
                    <em class="text-muted" style="font-size: 0.85rem; margin-left: 5px;">Processando dados e analisando...</em>
                </div>
            </div>
        </div>
    `;
    
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
        // Remove o indicador de carregamento
        const loading = document.getElementById('loading-indicator');
        if (loading) loading.remove();

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Pergunta';

        if (data.success) {
            if (!currentConversationId && data.conversation_id) {
                window.history.pushState({}, '', '?c=' + data.conversation_id);
            }
            currentConversationId = data.conversation_id;
            // Markdown simples para HTML (negrito)
            let respostaFormatada = data.resposta.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
            respostaFormatada = respostaFormatada.replace(/\n/g, '<br>');
            
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
        const loading = document.getElementById('loading-indicator');
        if (loading) loading.remove();

        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Pergunta';
        console.error(err);
    });
}
</script>
@endsection

@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">⚙️ Configurações do KN Intelligence</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.kn_intelligence.dashboard') }}">KN Intelligence</a></li>
        <li class="breadcrumb-item active">Configurações de IA</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-key text-warning"></i> Credenciais do Provedor de IA (Google Gemini)</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.kn_intelligence.salvarConfiguracoes') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label for="provedor" class="font-weight-bold">Provedor de IA</label>
                            <input type="text" class="form-control" id="provedor" value="Google Gemini" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label for="api_key" class="font-weight-bold">Chave de API (Gemini API Key)</label>
                            <div class="input-group">
                                <input type="password" name="api_key" id="api_key" class="form-control" value="{{ $config->api_key ?? 'AIzaSyDmJBY2EfGzshE9r2dj1MRrMbSufHMJcRw' }}" required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="eye-icon"></i>
                                </button>
                            </div>
                            <small class="text-muted">Chave utilizada para realizar chamadas ao modelo do Google Gemini.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="modelo" class="font-weight-bold">Modelo de Inteligência Artificial</label>
                            <select name="modelo" id="modelo" class="form-control">
                                <option value="gemini-2.5-flash" {{ ($config->modelo ?? '') == 'gemini-2.5-flash' ? 'selected' : '' }}>gemini-2.5-flash (Recomendado - Rápido & Inteligente)</option>
                                <option value="gemini-1.5-pro" {{ ($config->modelo ?? '') == 'gemini-1.5-pro' ? 'selected' : '' }}>gemini-1.5-pro (Avançado - Raciocínio Profundo)</option>
                                <option value="gemini-1.5-flash" {{ ($config->modelo ?? '') == 'gemini-1.5-flash' ? 'selected' : '' }}>gemini-1.5-flash (Padrão)</option>
                            </select>
                        </div>

                        <div class="form-group form-check mb-4">
                            <input type="checkbox" name="ativo" id="ativo" class="form-check-input" value="1" {{ ($config->ativo ?? true) ? 'checked' : '' }}>
                            <label class="form-check-input-label font-weight-bold" for="ativo">Módulo KN Intelligence Ativo</label>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow border-0 bg-light">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Sobre o KN Intelligence</h6>
                </div>
                <div class="card-body">
                    <p>O módulo de IA analisa os dados operacionais da sua loja em tempo real, fornecendo insights sobre:</p>
                    <ul>
                        <li>Desempenho Comercial & Ticket Médio</li>
                        <li>Risco de Ruptura de Estoque</li>
                        <li>Análise de Margem e Descontos</li>
                    </ul>
                    <p class="text-muted small">Suas chaves ficam armazenadas com segurança no banco de dados local da aplicação.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('api_key');
    const icon = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection

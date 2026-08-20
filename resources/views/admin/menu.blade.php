<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">KN COSMÉTICOS</div>
                @if( Auth::user()->is_admin)
                    <a class="nav-link {{ Route::current()->getName() === 'admin.home' ? 'active' : '' }}" href="{{route('admin.home')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                        Home
                    </a>

                    <!-- Módulo KN Intelligence -->
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseKNIntelligence" aria-expanded="false" aria-controls="collapseKNIntelligence">
                        <div class="sb-nav-link-icon"><i class="fas fa-brain text-info"></i></div>
                        KN Intelligence
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse" id="collapseKNIntelligence" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link {{ Route::current()->getName() === 'admin.kn_intelligence.dashboard' ? 'active' : '' }}" href="{{route('admin.kn_intelligence.dashboard')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Painel de IA
                            </a>
                            <a class="nav-link {{ Route::current()->getName() === 'admin.kn_intelligence.assistant' ? 'active' : '' }}" href="{{route('admin.kn_intelligence.assistant')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-comments"></i></div>
                                Assistente Chat
                            </a>
                            <a class="nav-link {{ Route::current()->getName() === 'admin.kn_intelligence.configuracoes' ? 'active' : '' }}" href="{{route('admin.kn_intelligence.configuracoes')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                Configurações IA
                            </a>
                        </nav>
                    </div>

                    <a class="nav-link {{ Route::current()->getName() === 'dre.index' ? 'active' : '' }}" href="{{route('dre.index')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-bar-chart"></i></div>
                        Dre
                    </a>
                    <a class="nav-link {{ Route::current()->getName() === 'admin.dashboard' ? 'active' : '' }}" href="{{route('admin.dashboard')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <a class="nav-link {{ Route::current()->getName() === 'dashboardDiario.index' ? 'active' : '' }}" href="{{route('dashboardDiario.index')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard Diário
                    </a>
                    <a class="nav-link {{ Route::current()->getName() === 'reposicao-produto.index' ? 'active' : '' }}" href="{{route('reposicao-produto.index')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-sync-alt"></i></div>
                        Reposição
                    </a>
                    <a class="nav-link {{ Route::current()->getName() === 'sales.index' ? 'active' : '' }}" href="{{route('sales.index')}}">
                        <div class="sb-nav-link-icon"><i class="fa fa-shopping-cart"></i></div>
                        Vendas
                    </a>
                    <a class="nav-link {{ Route::current()->getName() === 'monitoramento.index' ? 'active' : '' }}" href="{{route('monitoramento.index')}}">
                        <div class="sb-nav-link-icon"><i class="fa fa-eye"></i></div>
                        Monitoramento
                    </a>
                @endif

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesProdutos" aria-expanded="false" aria-controls="collapsePagesProdutos">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-bag"></i></div>
                    @if( Auth::user()->is_admin) Gerenciar Produtos @else Menu @endif
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesProdutos" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'produto.index' ? 'active' : '' }}" href="{{route('produto.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-cubes"></i></div>
                                Produtos
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'produtoInativo.index' ? 'active' : '' }}" href="{{route('produtoInativo.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-archive"></i></div>
                                Produtos Inativos
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'variacao.index' ? 'active' : '' }}" href="{{route('variacao.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-boxes"></i></div>
                                Variacão de Produtos
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'fornecedor.index' ? 'active' : '' }}" href="{{route('fornecedor.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-people-carry"></i></div>
                                Fornecedores
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'cor.index' ? 'active' : '' }}" href="{{route('cor.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-palette"></i></div>
                                Cores
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'categoria.index' ? 'active' : '' }}" href="{{route('categoria.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-ul"></i></div>
                                Categorias
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'usuario.index' ? 'active' : '' }}" href="{{route('usuario.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                                Usuários
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'cliente.index' ? 'active' : '' }}" href="{{route('cliente.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
                                Clientes
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'payment.index' ? 'active' : '' }}" href="{{route('payment.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-bill-alt"></i></div>
                                Formas de Pagamentos
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'forma.index' ? 'active' : '' }}" href="{{route('forma.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                                Forma de Entrega
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'tipoTroca.index' ? 'active' : '' }}" href="{{route('tipoTroca.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-sync-alt"></i></div>
                                Tipo de Troca
                        </a>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages2" aria-expanded="false" aria-controls="collapsePages2">
                        <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Dados da NFCe
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages2" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ Route::current()->getName() === 'origem.index' ? 'active' : '' }}" href="{{route('origem.index')}}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-money-check-alt"></i></div>
                                        Origem da Mercadoria
                                </a>
                            </nav>
                        </div>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesDespesas" aria-expanded="false" aria-controls="collapsePagesDespesas">
                    <div class="sb-nav-link-icon"><i class="fas fa-shopping-bag"></i></div>
                    Gerenciar Receitas/Despesas
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesDespesas" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'despesa.index' ? 'active' : '' }}" href="{{route('despesa.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-bill-wave"></i></div>
                                Despesas
                        </a>
                    </nav>
                </div>

                <div class="sb-sidenav-menu-heading">INTERFACE</div>

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesConfiguracoes" aria-expanded="false" aria-controls="collapsePagesConfiguracoes">
                    <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                    Configurações
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesConfiguracoes" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'admin.kn_intelligence.configuracoes' ? 'active' : '' }}" href="{{route('admin.kn_intelligence.configuracoes')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-brain text-info"></i></div>
                                Configurações de IA (Gemini)
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'usuario.index' ? 'active' : '' }}" href="{{route('usuario.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Usuários do Sistema
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'payment.index' ? 'active' : '' }}" href="{{route('payment.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-credit-card"></i></div>
                                Formas de Pagamento
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'forma.index' ? 'active' : '' }}" href="{{route('forma.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                                Formas de Entrega
                        </a>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesRelatorios" aria-expanded="false" aria-controls="collapsePagesRelatorios">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                        Relatórios
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesRelatorios" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'categoria.index' ? 'active' : '' }}" href="{{route('categoria.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-check-alt"></i></div>
                                Contas a Receber
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'gastosfixo.index' ? 'active' : '' }}" href="{{route('gastosfixo.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-check"></i></div>
                                Contas a Pagar
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'estoque.index' ? 'active' : '' }}" href="{{route('estoque.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                                Estoque
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'fluxo.index' ? 'active' : '' }}" href="{{route('fluxo.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                                Fluxo de Caixa
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'productbestsellers.index' ? 'active' : '' }}" href="{{route('productbestsellers.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                                Mais Vendidos por Categorias
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'productbestsellers.index' ? 'active' : '' }}" href="{{route('productbestsellers.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-alt"></i></div>
                                Mais Vendidos
                        </a>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesResposicao" aria-expanded="false" aria-controls="collapsePagesResposicao">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                        Resposição
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesResposicao" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'reposicao.index' ? 'active' : '' }}" href="{{route('reposicao.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Visão Trimestre
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'listaCompras.index' ? 'active' : '' }}" href="{{route('listaCompras.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-check"></i></div>
                                Lista de Compras
                        </a>
                    </nav>
                </div>

                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesAudits" aria-expanded="false" aria-controls="collapsePagesAudits">
                    <div class="sb-nav-link-icon">
                        <i class="fas fa-check-square"></i>
                    </div>
                        Auditoria
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesAudits" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'audit.index' ? 'active' : '' }}" href="{{route('audit.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-cube"></i></div>
                                Produtos
                        </a>
                    </nav>
                </div>

                <div class="sb-sidenav-menu-heading">ADDONS</div>
                <a class="nav-link" href="charts.html">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                    Charts
                </a>
                <a class="nav-link" href="tables.html">
                    <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                    Tables
                </a>
        </div>
    </div>

    <div class="sb-sidenav-footer">
        <div class="small">Bem Vindo:
            @if(Auth::check()) {{Auth::user()->name}} @endif
        </div>
    </div>
</nav>

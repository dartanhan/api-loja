<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">KN COSMÉTICOS</div>
                @if( Auth::user()->is_admin)
                    <a class="nav-link {{ Route::current()->getName() === 'admin.home' ? 'active' : '' }}" href="{{route('admin.home')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                        Home
                    </a>
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
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesProdutos" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-weight-hanging"></i></div>
                    @if( Auth::user()->is_admin) Gerenciar Produtos @else Menu @endif
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesProdutos" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav p-0">
                        @if( Auth::user()->is_admin)
                            <a href="{{ route('produto.produto_create') }}" title="Adicionar Produtos"
                               class="nav-link p-0 mb-3 {{ request()->routeIs('produto.produto_create') ? 'active' : '' }}">
                                <div class="sb-nav-link-icon"><i class="nav-icon fas fa-add"></i></div>
                                Adicionar Produtos
                            </a>
                            <a href="{{ route('produtos.produtos_ativos') }}" title="Produtos Ativos"
                                    class="nav-link p-0 mb-3 {{ request()->routeIs('produtos.produtos_ativos') ? 'active' : '' }}">
                                <div class="sb-nav-link-icon"><i class="nav-icon fas fa-box"></i></div>
                                Listar Ativos
                            </a>

                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'produtoInativo.index' ? 'active' : '' }}" href="{{route('produtoInativo.index')}}">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-lock"></i></div>
                                Listar Inativos
                            </a>

                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'produto.index' ? 'active' : '' }}" href="{{route('produto.index')}}">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-unlock"></i></div>
                                Produtos Ativos (Antigo)
                            </a>
{{--                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'produto.indexNew' ? 'active' : '' }}" href="{{route('produto.indexNew')}}">--}}
{{--                                <div class="sb-nav-link-icon"><i class="fa-solid fa-unlock"></i></div>--}}
{{--                                Produtos Ativos(New)--}}
{{--                            </a>--}}
                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'produtoInativo.index' ? 'active' : '' }}" href="{{route('produtoInativo.index')}}">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-lock"></i></div>
                                Produtos Inativos (Antigo)
                            </a>
                        @endif
                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'produtos.baixo_estoque' ? 'active' : '' }}" href="{{route('produtos.baixo_estoque')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-cube"></i></div>
                                Baixo Estoque
                            </a>

                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'product.index' ? 'active' : '' }}" href="{{route('product.index')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-cube"></i></div>
                                Produtos
                            </a>
                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'listaCompras.index' ? 'active' : '' }}" href="{{route('listaCompras.index')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-money-check"></i></div>
                                    Lista de Compras
                            </a>
                            <a class="nav-link p-0 mb-3 {{ Route::current()->getName() === 'reposicao.index' ? 'active' : '' }}" href="{{route('reposicao.index')}}">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Visão Trimestre
                            </a>
                    </nav>
                </div>

                @if( Auth::user()->is_admin)
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesReceitaDespesa" aria-expanded="false" aria-controls="collapsePages">
                    <div class="sb-nav-link-icon"><i class="fas fa-weight-hanging"></i></div>
                    @if( Auth::user()->is_admin) Gerenciar Receitas/Despesas @else Menu @endif
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePagesReceitaDespesa" aria-labelledby="headingTwo" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav p-0">
                        @if( Auth::user()->is_admin)
                            <a href="{{ route('despesa.index') }}" title="Adicionar Despesa"
                               class="nav-link p-0 mb-3 {{ request()->routeIs('despesa.index') ? 'active' : '' }}">
                                <div class="sb-nav-link-icon"><i class="nav-icon fas fa-minus-circle"></i></div>
                                Adicionar Despesas
                            </a>
                           {{-- <a href="{{ route('despesa.index') }}" title="Adicionar Receita"
                               class="nav-link p-0 mb-3 {{ request()->routeIs('despesa.index') ? 'active' : '' }}">
                                <div class="sb-nav-link-icon"><i class="nav-icon fas fa-add"></i></div>
                               Adicionar Receita
                            </a>--}}
                        @endif
                    </nav>
                </div>
                <div class="sb-sidenav-menu-heading">Interface</div>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesConfiguracoes" aria-expanded="false" aria-controls="collapseLayouts">
                    <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                    Configurações
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>

                <div class="collapse" id="collapsePagesConfiguracoes" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link {{ Route::current()->getName() === 'cashback.index' ? 'active' : '' }}" href="{{route('cashback.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-bill"></i></div>
                            Cashback
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'categoria.index' ? 'active' : '' }}" href="{{route('categoria.index')}}">
                        <div class="sb-nav-link-icon"><i class="fas fa-newspaper"></i></div>
                            Categorias
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'cor.index' ? 'active' : '' }}" href="{{route('cor.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-paint-roller"></i></div>
                                Cores
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'payment.index' ? 'active' : '' }}" href="{{route('payment.index')}}">
                            <div class="sb-nav-link-icon"><i class="far fa-credit-card"></i></div>
                                Forma de Pagamentos
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'forma.index' ? 'active' : '' }}" href="{{route('forma.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-motorcycle"></i></div>
                            Forma de Entrega
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'fornecedor.index' ? 'active' : '' }}" href="{{route('fornecedor.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>
                                Fornecedor
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'gastosfixo.index' ? 'active' : '' }}" href="{{route('gastosfixo.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-dollar-sign"></i></div>
                                Gastos Fixos
                        </a>

                        <a class="nav-link {{ Route::current()->getName() === 'tarifa.index' ? 'active' : '' }}" href="{{route('tarifa.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-percent"></i></div>
                                Tarifas
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'tipoTroca.index' ? 'active' : '' }}" href="{{route('tipoTroca.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-recycle"></i></div>
                            Tipo Troca
                        </a>
                        <a class="nav-link {{ Route::current()->getName() === 'usuario.index' ? 'active' : '' }}" href="{{route('usuario.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                Usuários
                        </a>
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages2" aria-expanded="false" aria-controls="collapsePages2">
                        <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                            Dados da NFCe
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages2" aria-labelledby="headingTwo" data-parent="#sidenavAccordion2">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link {{ Route::current()->getName() === 'origem.index' ? 'active' : '' }}" href="{{route('origem.index')}}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-money-check-alt"></i></div>
                                        Origem da Mercadoria
                                </a>
                            </nav>
                        </div>
                    </nav>
                </div>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesRelatorios" aria-expanded="false" aria-controls="collapsePages">
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
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesResposicao" aria-expanded="false" aria-controls="collapsePages">
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
                        <!--a class="nav-link {{ Route::current()->getName() === 'estoque.index' ? 'active' : '' }}" href="{{route('estoque.index')}}">
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
                        </a-->
                    </nav>
                </div>
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePagesAudits" aria-expanded="false" aria-controls="collapsePages">
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
                <div class="sb-sidenav-menu-heading">Addons</div>
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
            @endif

            <div class="sb-sidenav-footer">
                <div class="small">Bem Vindo:
                    @if(Auth::check()) {{Auth::user()->name}} @endif
                </div>
            </div>

</nav>





        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">KN COSMÉTICOS</div>
                        <a class="nav-link {{ Route::current()->getName() === 'admin.home' ? 'active' : '' }}" href="{{route('admin.home')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                            Home
                        </a>
                        
                        <a class="nav-link {{ Route::current()->getName() === 'admin.dashboard' ? 'active' : '' }}" href="{{route('admin.dashboard')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        
                        <a class="nav-link {{ Route::current()->getName() === 'produto.index' ? 'active' : '' }}" href="{{route('produto.index')}}">
                            <div class="sb-nav-link-icon"><i class="fas fa-weight-hanging"></i></div>
                            Produtos
                        </a>

                        <div class="sb-sidenav-menu-heading">Interface</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Configurações
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>

                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
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
                                <a class="nav-link {{ Route::current()->getName() === 'usuario.index' ? 'active' : '' }}" href="{{route('usuario.index')}}">
                                    <div class="sb-nav-link-icon"><i class="fas fa-user-circle"></i></div>
                                        Usuários
                                </a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                            <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                                Relatórios
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
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
                        <!--div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                    Authentication
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="login.html">Login</a>
                                        <a class="nav-link" href="register.html">Register</a>
                                        <a class="nav-link" href="password.html">Forgot Password</a>
                                    </nav>
                                </div>
                                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                    Error
                                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                </a>
                                <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <a class="nav-link" href="401.html">401 Page</a>
                                        <a class="nav-link" href="404.html">404 Page</a>
                                        <a class="nav-link" href="500.html">500 Page</a>
                                    </nav>
                                </div>
                            </nav>
                        </div-->
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
                <div class="sb-sidenav-footer">
                    <div class="small">Logado como:</div>
                      @if(Auth::check()) {{Auth::user()->name}} @endif
                </div>
            </nav>
        </div>



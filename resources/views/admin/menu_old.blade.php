@extends('layouts.layout.blade.php')

@section('menu')

<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="sidebar-sticky pt-3">
          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Dashboard</span>
              <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                  <span data-feather="plus-circle"></span>
              </a>
          </h6>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link {{ Route::current()->getName() === 'admin.dashboard' ? 'active' : '' }}" href="{{route('admin.dashboard')}}">
              <span data-feather="home"></span>
              Dashboard <span class="sr-only">(current)</span>
            </a>
          </li>
        </ul>
          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Produto</span>
              <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                  <span data-feather="plus-circle"></span>
              </a>
          </h6>

              <a class="nav-link {{ Route::current()->getName() === 'product.index' ? 'active' : '' }}" href="{{route('product.index')}}">
                  <span data-feather="credit-card"></span>
                  Produtos
              </a>
              <a class="nav-link {{ Route::current()->getName() === 'productBlock.index' ? 'active' : '' }}" href="{{route('productBlock.index')}}">
                  <span data-feather="lock"></span>
                  Bloqueados
              </a>
              <a class="nav-link {{ Route::current()->getName() === 'productMin.index' ? 'active' : '' }}" href="{{route('productMin.index')}}">
                  <span data-feather="alert-triangle"></span>
                  Quantidade Minima
              </a>
              <a class="nav-link {{ Route::current()->getName() === 'productBestSellers.index' ? 'active' : '' }}" href="{{route('productBestSellers.index')}}">
                  <span data-feather="award"></span>
                  Mais Vendidos
              </a>
              <a class="nav-link {{ Route::current()->getName() === 'productSaleDay.index' ? 'active' : '' }}" href="{{route('productSaleDay.index')}}">
                  <span data-feather="dollar-sign"></span>
                  Vendidos no Dia
              </a>


            <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                <span>Configurações</span>
                <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                    <span data-feather="plus-circle"></span>
                </a>
            </h6>
              <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'categoria.index' ? 'active' : '' }}" href="{{route('categoria.index')}}">
                        <span data-feather="share-2"></span>
                        Categorias
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'cor.index' ? 'active' : '' }}" href="{{route('cor.index')}}">
                        <span data-feather="slack"></span>
                            Cores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'payment.index' ? 'active' : '' }}" href="{{route('payment.index')}}">
                        <span data-feather="shopping-bag"></span>
                        Forma de Pagamentos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'fornecedor.index' ? 'active' : '' }}" href="{{route('fornecedor.index')}}">
                        <span data-feather="truck"></span>
                        Fornecedores
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'gastosfixo.index' ? 'active' : '' }}" href="{{route('gastosfixo.index')}}">
                      <span data-feather="dollar-sign"></span>
                      Gastos Fixos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'tarifa.index' ? 'active' : '' }}" href="{{route('tarifa.index')}}">
                      <span data-feather="percent"></span>
                      Tarifas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::current()->getName() === 'usuario.index' ? 'active' : '' }}" href="{{route('usuario.index')}}">
                        <span data-feather="users"></span>
                        Usuários
                    </a>
                </li>
            </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Clientes</span>
              <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                  <span data-feather="plus-circle"></span>
              </a>
          </h6>
          <ul class="nav flex-column mb-2">
              <li class="nav-item">
                  <a class="nav-link {{ Route::current()->getName() === 'cliente.index' ? 'active' : '' }}" href="{{route('cliente.index')}}">
                      <span data-feather="user-check"></span>
                      Clientes
                  </a>
              </li>
          </ul>
          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Relatórios</span>
              <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                  <span data-feather="plus-circle"></span>
              </a>
          </h6>
          <ul class="nav flex-column mb-2">
              <li class="nav-item">
                  <a class="nav-link {{ Route::current()->getName() === 'flux.index' ? 'active' : '' }}" href="{{route('flux.index')}}">
                      <span data-feather="dollar-sign"></span>
                      Fluxo Caixa
                  </a>
              </li>
              <li class="nav-item">
                  <a class="nav-link" {{ Route::current()->getName() === 'relatorio.index' ? 'active' : '' }}" href="{{route('relatorio.index')}}">
                      <span data-feather="clock"></span>
                      Vendas no Dia
                  </a>
              </li>
              <!--li class="nav-item">
                  <a class="nav-link" {{ Route::current()->getName() === 'conferenciames.index' ? 'active' : '' }}" href="{{route('conferenciames.index')}}">
                  <span data-feather="calendar"></span>
                    Conferência Mês
                  </a>
              </li-->
              <li class="nav-item">
                  <a class="nav-link {{ Route::current()->getName() === 'admin.chart' ? 'active' : '' }}" href="{{route('admin.chart')}}">
                      <span data-feather="bar-chart-2"></span>
                      Gráficos
                  </a>
              </li>
          </ul>

          <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
              <span>Calendário</span>
              <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
                  <span data-feather="plus-circle"></span>
              </a>
          </h6>
          <ul class="nav flex-column mb-2">
              <li class="nav-item">
                  <a class="nav-link {{ Route::current()->getName() === 'calendario.index' ? 'active' : '' }}" href="{{route('calendario.index')}}">
                      <span data-feather="calendar"></span>
                      Calendário
                  </a>
              </li>
          </ul>
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Saved reports</span>
          <a class="d-flex align-items-center text-muted" href="#" aria-label="Add a new report">
            <span data-feather="plus-circle"></span>
          </a>
        </h6>
            <ul class="nav flex-column mb-2">
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Current month
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Last quarter
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Social engagement
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <span data-feather="file-text"></span>
                  Year-end sale
                </a>
              </li>
            </ul>
      </div>
    </nav>

@endsection

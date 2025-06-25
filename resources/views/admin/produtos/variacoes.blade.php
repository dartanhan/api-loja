<table class="table table-bordered">
    <thead>
    <tr>
        <th>Subcódigo</th>
        <th>Variação</th>
        <th>Quantidade</th>
        <th>Preço</th>
        <th>Ações</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($variacoes as $variacao)
        <tr>
            <form action="{{ route('variacao.update', $variacao->id) }}" method="POST">
                @csrf
                @method('PUT')
                <td>{{ $variacao->subcodigo }}</td>
                <td><input type="text" name="variacao" value="{{ $variacao->variacao }}" class="form-control" /></td>
                <td>
                    <div class="input-group">
                        <button class="btn btn-outline-secondary" type="submit" name="operacao" value="subtrair">−</button>
                        <input type="text" name="quantidade" value="{{ $variacao->quantidade }}" class="form-control text-center" />
                        <button class="btn btn-outline-secondary" type="submit" name="operacao" value="adicionar">+</button>
                    </div>
                </td>
                <td><input type="text" name="valor_varejo" value="{{ $variacao->valor_varejo }}" class="form-control" /></td>
                <td><button type="submit" class="btn btn-success btn-sm">Salvar</button></td>
            </form>
        </tr>
    @endforeach
    </tbody>
</table>

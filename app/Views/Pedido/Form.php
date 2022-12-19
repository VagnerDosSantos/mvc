<script>
    function drawTable() {
        let html = '';
        let total = 0;
        let itens = localStorage.getItem('carrinho');

        itens = JSON.parse(itens);

        if (itens == null) {
            return
        }

        itens.forEach((item, index) => {
            let preco = parseFloat(item.preco);
            html += `
                    <tr>
                        <td>${item.produto_id}</td>
                        <td>${item.nome}</td>
                        <td>${preco.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        })}</td>
                        <td>${item.quantidade}</td>
                        <td>${item.subtotal.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        })}</td>
                        <td>
                            <button type="button" class="btn btn-small red accent-4" onclick="remove(${index})">
                                <i class="material-icons">delete</i>
                            </button>
                        </td>
                    </tr>
                `;

            total += parseFloat(item.subtotal);
        });

        $('#itens').html(html);

        $('#valor-total').html(total.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }));
    }

    function remove(index) {
        let itens = localStorage.getItem('carrinho');

        itens = JSON.parse(itens);

        itens.splice(index, 1);

        localStorage.setItem('carrinho', JSON.stringify(itens));

        drawTable();
    }
</script>
<div class="table-card">
    <div class="table-card-header">
        <span>
            <i class="material-icons">inventory_2</i>
        </span>

        <span class="table-card-title">
            Pedido
        </span>
    </div>

    <form method="<?= $method ?>">
        <div class="table-card-body">
            <div class="row">
                <div class="col m4 s12" style="border-right: 1px solid rgba(0,0,0,0.12);">
                    <div class="row">
                        <div class="input-field col s12">
                            <select name="cliente" id="cliente">
                                <option value="" disabled selected>Selecione um cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?= $cliente['id'] ?>"><?= $cliente['nome'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="cliente">Cliente</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <select name="produto" id="produto">
                                <option value="" disabled selected>Selecione um produto</option>
                                <?php foreach ($produtos as $produto) : ?>
                                    <option value="<?= $produto['id'] ?>"><?= $produto['nome'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="produto">Produto</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="text" readonly placeholder="0,00" name="preco" id="preco">
                            <label for="preco">Preço Unitário</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input type="text" readonly name="estoque" placeholder="0" id="estoque">
                            <label for="estoque">Estoque</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <input type="number" name="quantidade" id="quantidade" min="1" value="1">
                            <label for="quantidade">Quantidade</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <button type="button" id="add" class="btn btn-small blue accent-4" style="width: 100%;">
                                Adicionar
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col m8 s12">
                    <table>
                        <thead>
                            <tr>
                                <th class="w-1">ID</th>
                                <th>Produto</th>
                                <th>Preço Unitário</th>
                                <th>Quantidade</th>
                                <th class="w-1 valor">Subtotal</th>
                                <th class="w-1">#</th>
                            </tr>
                        </thead>

                        <tbody id="itens"></tbody>

                        <tfoot>
                            <tr>
                                <td colspan="4" class="right-align">
                                    <strong>Total</strong>
                                </td>
                                <th id="valor-total">
                                    R$ 0,00
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col s12 right-align">
                    <a href="<?= url("pedido/index") ?>" class="btn btn-small red accent-4">
                        <i class="material-icons left">arrow_back</i> Voltar
                    </a>

                    <button type="submit" class="btn btn-small green accent-4">
                        <i class="material-icons left">send</i> Finalizar
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(window).ready(function() {
        $('select').formSelect();

        var carrinho = localStorage.getItem('carrinho') ?? [];

        drawTable();

        $('#add').click(function() {
            let produto = parseInt($('#produto').val());
            let quantidade = parseInt($('#quantidade').val());
            let preco = parseFloat($('#preco').val().replace('.', '').replace(',', '.'));
            let estoque = parseInt($('#estoque').val());
            let nome = $('#produto option:selected').text();

            if (produto == null || quantidade == 0) {
                return Swal.fire({
                    title: 'Atenção',
                    text: 'Preencha todos os campos para adicionar um produto.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }

            if (quantidade > estoque) {
                return Swal.fire({
                    title: 'Atenção',
                    text: 'Quantidade de produtos informada não pode ser maior que o estoque disponível.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }

            if (carrinho == null || carrinho.length == 0) {
                carrinho = [];
            } else {
                carrinho = JSON.parse(localStorage.getItem('carrinho'));
            }

            let item = carrinho.filter(item => item.produto_id == produto);

            let quantidadeNoCarrinho = item.reduce((acc, item) => {
                return acc + item.quantidade;
            }, 0);
            let quantidadeTotal = parseInt(quantidade) + parseInt(quantidadeNoCarrinho);

            if (quantidadeTotal > estoque) {
                return Swal.fire({
                    title: 'Atenção',
                    text: 'Quantidade de produtos informada não pode ser maior que o estoque disponível.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }

            carrinho.push({
                produto_id: parseInt(produto),
                quantidade: parseInt(quantidade),
                nome: nome,
                preco: preco,
                subtotal: quantidade * preco
            });

            localStorage.setItem('carrinho', JSON.stringify(carrinho));

            $('#produto').val(null);
            $('#quantidade').val(1);
            $('#preco').val(0);
            $('#estoque').val(0);
            $('select').formSelect();

            drawTable();
        });

        $('#produto').change(function() {
            let id = $(this).val();
            let url = `<?= url("produto/get") ?>/${id}`;

            axios(url, {
                    method: 'GET',
                    responseType: 'json',
                }).then(function(response) {
                    if (response.status == 200) {
                        $('#preco').val(response.data.preco);
                        $('#estoque').val(response.data.quantidade);

                        $('#preco').maskMoney({
                            allowNegative: false,
                            thousands: '.',
                            decimal: ',',
                        }).trigger('focus').trigger('blur');
                    }
                })
                .catch(function(error) {
                    console.log(error);
                });
        });

        $('form').submit(function(e) {
            e.preventDefault();
            let order = JSON.parse(localStorage.getItem('carrinho'));
            let client = $('#cliente').val();

            if (client == null || client.length == 0) {
                return Swal.fire({
                    title: 'Atenção',
                    text: 'Selecione um cliente.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }

            if (order == null || order.length == 0) {
                return Swal.fire({
                    title: 'Atenção',
                    text: 'Adicione pelo menos um produto ao carrinho.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }

            let data = {
                cliente: parseInt(client),
                pedido: order
            };

            let url = location.href;
            let method = $(this).attr('method');

            axios(url, {
                    method: method,
                    data: data,
                    responseType: 'json',
                }).then(function(response) {
                    if (response.status == 201 && method == 'POST') {
                        FlashToast.Success("Pedido cadastrado com sucesso!");
                        localStorage.removeItem('carrinho');
                    } else {
                        Toast.Error(response.data.mensagem ?? "Ocorreu um erro inesperado ao cadastrar o pedido!");
                        return
                    }

                    window.location.reload();
                })
                .catch(function(error) {
                    if (error.response.status == 422) {
                        let errors = error.response.data.errors;

                        for (let key in errors) {
                            Toast.Error(errors[key][0]);
                        }
                    } else {
                        Toast.Error(error.response.data.mensagem ?? "Ocorreu um erro inesperado ao cadastrar o pedido!");
                    }
                });
        });
    });
</script>
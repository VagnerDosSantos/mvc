<div class="table-card">
    <div class="table-card-header">
        <span>
            <i class="material-icons">inventory_2</i>
        </span>

        <span class="table-card-title">
            Produto
        </span>
    </div>

    <form method="<?= $method ?>">
        <div class="table-card-body">
            <div class="row">
                <div class="row s12">
                    <div class="input-field col m6 s12">
                        <input type="text" name="nome" id="nome" placeholder="Nome do Produto" value="<?= $nome ?? '' ?>" />
                        <label for="nome">Nome</label>
                        <span class="error-text" id="error_nome"></span>
                    </div>

                    <div class="input-field col m3 s6">
                        <input type="text" name="preco" id="preco" placeholder="Preço do Produto" value="<?= $preco ?? null ?>" />
                        <label for="preco">Preço</label>
                        <span class="error-text" id="error_preco"></span>
                    </div>

                    <div class="input-field col m3 s6">
                        <input type="number" min="0" name="quantidade" id="quantidade" placeholder="Quantidade do produto" value="<?= $quantidade ?? 0 ?>" />
                        <label for="quantidade">Quantidade</label>
                        <span class="error-text" id="error_quantidade"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="input-field col s12">
                    <textarea id="descricao" class="materialize-textarea"><?= $descricao ?? null ?></textarea>
                    <label for="descricao">Descrição</label>
                    <span class="error-text" id="error_descricao"></span>
                </div>
            </div>

            <div class="row">
                <div class="col s12 right-align">
                    <a href="<?= url("produto/index") ?>" class="btn btn-small red accent-4">
                        <i class="material-icons left">arrow_back</i> Voltar
                    </a>

                    <button type="submit" class="btn btn-small green accent-4">
                        <i class="material-icons left">send</i> Salvar
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    $(window).ready(function() {
        $('#preco').maskMoney({
            thousands: '.',
            decimal: ',',
            allowZero: true,
        }).trigger('focus').trigger('blur');

        $('form').submit(function(e) {
            e.preventDefault();
            let data = {
                nome: $('#nome').val(),
                preco: $('#preco').val().replace('.', '').replace(',', '.'),
                quantidade: parseInt($('#quantidade').val()),
                descricao: $('#descricao').val(),
            };

            let errors = Array.from(document.querySelectorAll('[id^=error_]'));
            errors.forEach(element => {
                $(element).html('');
            });

            let url = location.href;
            let method = $(this).attr('method');

            axios(url, {
                    method: method,
                    data: data,
                    responseType: 'json',
                }).then(function(response) {
                    console.log(response)
                    if (response.status == 201 && method == 'POST') {
                        FlashToast.Success("Produto cadastrado com sucesso!");
                    } else if (response.status == 200 && method == 'PUT') {
                        FlashToast.Success("Produto atualizado com sucesso!");
                        window.location.href = "<?= url("produto/index") ?>";
                        return;
                    } else {
                        FlashToast.Error("Erro ao cadastrar o produto!");
                    }

                    window.location.reload();
                })
                .catch(function(error) {
                    if (error.response.status == 422) {
                        let errors = error.response.data.dados;
                        for (const key in errors) {
                            if (Object.hasOwnProperty.call(errors, key)) {
                                const element = errors[key];
                                $("#error_" + key).html(element);
                            }
                        }
                    } else {
                        FlashToast.Error("Ocorreu um erro inesperado ao cadastrar o produto!");
                        window.location.reload();
                    }
                });
        });
    });
</script>
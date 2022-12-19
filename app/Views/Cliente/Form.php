<div class="table-card">
    <div class="table-card-header">
        <span>
            <i class="material-icons">person</i>
        </span>

        <span class="table-card-title">
            Cliente
        </span>
    </div>

    <form method="<?= $method ?>">
        <div class="table-card-body">
            <div class="row">
                <div class="row s12">
                    <div class="input-field col m4 s12">
                        <input type="text" name="nome" id="nome" placeholder="Nome do Cliente" value="<?= $nome ?? '' ?>" />
                        <label for="nome">Nome</label>
                        <span class="error-text" id="error_nome"></span>
                    </div>

                    <div class="input-field col m4 s6">
                        <input type="date" name="data_nascimento" id="data_nascimento" value="<?= $data_nascimento ?? null ?>" />
                        <label for="quantidade">Data de Nascimento</label>
                        <span class="error-text" id="error_data_nascimento"></span>
                    </div>

                    <div class="input-field col m4 s12">
                        <input type="text" name="cpf" id="cpf" placeholder="CPF do Cliente" value="<?= $cpf ?? null ?>" />
                        <label for="preco">CPF</label>
                        <span class="error-text" id="error_cpf"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col s12 right-align">
                    <a href="<?= url("cliente/index") ?>" class="btn btn-small red accent-4">
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
        $('#cpf').mask('000.000.000-00', {
            reverse: true
        });

        $('form').submit(function(e) {
            e.preventDefault();
            let data = {
                nome: $('#nome').val(),
                data_nascimento: $('#data_nascimento').val(),
                cpf: $('#cpf').val().replace(/\D/g, '')
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
                    if (response.status == 201 && method == 'POST') {
                        FlashToast.Success("Cliente cadastrado com sucesso!");
                    } else if (response.status == 200 && method == 'PUT') {
                        FlashToast.Success("Cliente atualizado com sucesso!");
                        window.location.href = "<?= url("cliente/index") ?>";
                        return;
                    } else {
                        Toast.Error("Erro ao cadastrar o cliente!");
                        return
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
                        Toast.Error(error.response.data.mensagem ?? "Ocorreu um erro inesperado ao cadastrar o cliente!");
                    }
                });
        });
    });
</script>
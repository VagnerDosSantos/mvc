<div class="row" style="width: 400px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Cadastro</span>
                <div class="row">
                    <form class="col s12" method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="email" type="email" name="email">
                                <label for="email">Email</label>
                                <span class="error-text" id="error_email"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="senha" type="password" name="senha">
                                <label for="senha">Senha</label>
                                <span class="error-text" id="error_senha"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 right-align">
                                <a href="<?= url('login') ?>" class="left-align">Já possui uma conta?</a>
                                <button class="btn btn-small green accent-4" type="submit" name="action">Cadastrar
                                    <i class="material-icons right">send</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('form').submit(function(e) {
            e.preventDefault();

            let errors = Array.from(document.querySelectorAll('[id^=error_]'));
            errors.forEach(element => {
                $(element).html('');
            });

            let data = {
                email: $("#email").val(),
                senha: $("#senha").val(),
            };

            axios("<?= url('usuario/cadastro') ?>", {
                    method: 'POST',
                    data: data,
                    responseType: 'json',
                }).then(function(response) {
                    console.log(response)
                    if (response.status == 201) {
                        FlashToast.Success("Usuário cadastrado com sucesso!");
                        window.location.href = "<?= url('login') ?>";
                        return;
                    }

                    Toast.Error("Erro ao cadastrar o produto!");
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
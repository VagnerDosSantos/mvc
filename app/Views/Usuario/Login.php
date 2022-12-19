<div class="row" style="width: 400px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <div class="col s12">
        <div class="card">
            <div class="card-content">
                <span class="card-title">Login</span>
                <div class="row">
                    <form class="col s12" id="formLogin" method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="email" type="email" name="email">
                                <label for="email">Email</label>
                                <span id="error_email" class="red-text"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="senha" type="password" name="senha">
                                <label for="senha">Senha</label>
                                <span id="error_senha" class="red-text"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 right-align">
                                <a href="<?= url('/usuario/cadastro') ?>" class="left-align">Cadastre-se</a>

                                <button class="btn btn-small green accent-4" type="submit" name="action">Entrar
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

            axios("<?= url('/login') ?>", {
                    method: 'POST',
                    data: data,
                    responseType: 'json',
                }).then(function(response) {
                    console.log(response)
                    if (response.status == 200) {
                        FlashToast.Success("Usuário logado com sucesso!");
                        window.location.href = "<?= url('/') ?>";
                        return;
                    }

                    Toast.Error("Erro ao logar!");
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
                        FlashToast.Error("Ocorreu um erro inesperado ao logar!");
                        window.location.reload();
                    }
                });
        });
    });
</script>
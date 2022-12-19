<div class="table-card">
    <div class="table-card-header">
        <span>
            <i class="material-icons">person</i>
        </span>

        <span class="table-card-title">
            Clientes
        </span>

        <span class="table-card-action">
            <a href="<?= url("cliente") ?>" class="waves-effect waves-light btn btn-small green accent-4">
                <i class="material-icons left">add</i>Novo
            </a>
        </span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-1">ID</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Data Nascimento</th>
                <th class="w-1">Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($clients) && !empty($clients)) : ?>
                <?php foreach ($clients as $client) : ?>
                    <tr>
                        <td><?= $client['id'] ?></td>
                        <td><?= $client['nome'] ?></td>
                        <td class="cpf"><?= $client['cpf'] ?></td>
                        <td class="data-nascimento"><?= $client['data_nascimento'] ?></td>
                        <td class="nowrap">
                            <button type="button" data-id="<?= $client['id'] ?>" class="btn btn-delete btn-small red accent-4">
                                <i class="material-icons">delete</i>
                            </button>

                            <a href="<?= url("cliente/editar/{$client['id']}") ?>" class="btn btn-small blue accent-4">
                                <i class="material-icons">edit</i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>

                <?php include_pagination($pagination) ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="text-center">
                        Nenhum cliente cadastrado
                    </td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.cpf').mask('000.000.000-00', {
            reverse: true
        });

        $('.data-nascimento').each(function() {
            let data = $(this).text();
            data = data.split('-').reverse().join('/');
            $(this).text(data);
        });

        $('.btn-delete').click(function() {
            const id = $(this).data('id')

            Swal.fire({
                title: 'Deseja realmente excluir este cliente?',
                text: "Esta ação não poderá ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= url("cliente/deletar") ?>/${id}`,
                        type: 'DELETE',
                        success: function(response, status, xhr) {
                            if (xhr.status == 204) {
                                Swal.fire(
                                    'Excluído!',
                                    'O Cliente foi excluído com sucesso.',
                                    'success'
                                ).then(() => {
                                    location.reload()
                                })
                            } else {
                                Swal.fire(
                                    'Erro!',
                                    'Ocorreu um erro ao excluir o cliente.',
                                    'error'
                                )
                            }
                        }
                    })
                }
            })
        })
    })
</script>
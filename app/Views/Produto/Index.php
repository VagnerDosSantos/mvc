<div class="table-card">
    <div class="table-card-header">
        <span>
            <i class="material-icons">inventory_2</i>
        </span>

        <span class="table-card-title">
            Produtos
        </span>

        <span class="table-card-action">
            <a href="<?= url("produto") ?>" class="waves-effect waves-light btn btn-small green accent-4">
                <i class="material-icons left">add</i>Novo
            </a>
        </span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-1">ID</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th class="w-1">Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($products) && !empty($products)) : ?>
                <?php foreach ($products as $product) : ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $product['nome'] ?></td>
                        <td class="valor"><?= $product['preco'] ?></td>
                        <td><?= $product['quantidade'] ?></td>
                        <td class="nowrap">
                            <button type="button" data-id="<?= $product['id'] ?>" class="btn btn-delete btn-small red accent-4">
                                <i class="material-icons">delete</i>
                            </button>

                            <a href="<?= url("produto/editar/{$product['id']}") ?>" class="btn btn-small blue accent-4">
                                <i class="material-icons">edit</i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>

                <?php include_pagination($pagination) ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" class="text-center">
                        Nenhum produto cadastrado
                    </td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('.valor').each(function() {
            let valor = parseFloat($(this).text());

            $(this).html(valor.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));
        });

        $('.btn-delete').click(function() {
            const id = $(this).data('id')

            Swal.fire({
                title: 'Deseja realmente excluir este produto?',
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
                        url: `<?= url("produto/deletar") ?>/${id}`,
                        type: 'DELETE',
                        success: function(response, status, xhr) {
                            if (xhr.status == 204) {
                                Swal.fire(
                                    'Excluído!',
                                    'O produto foi excluído com sucesso.',
                                    'success'
                                ).then(() => {
                                    location.reload()
                                })
                            } else {
                                Swal.fire(
                                    'Erro!',
                                    'Ocorreu um erro ao excluir o produto.',
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
<div class="table-card">
    <div class="table-card-header">
        <span>
            <i class="material-icons">inventory_2</i>
        </span>

        <span class="table-card-title">
            Pedidos
        </span>

        <span class="table-card-action">
            <a href="<?= url("pedido") ?>" class="waves-effect waves-light btn btn-small green accent-4">
                <i class="material-icons left">add</i>Novo
            </a>
        </span>
    </div>

    <table>
        <thead>
            <tr>
                <th class="w-1">Pedido</th>
                <th>Cliente</th>
                <th>Data Pedido</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th class="w-1">Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($orders) && !empty($orders)) : ?>
                <?php foreach ($orders as $orders) : ?>
                    <tr>
                        <td>#<?= $orders['id'] ?></td>
                        <td><?= $orders['nome_cliente'] ?></td>
                        <td class="data-recebimento"><?= $orders['data_recebimento'] ?></td>
                        <td class="valor"><?= $orders['valor_pedido'] ?></td>
                        <td><?= $status[$orders['status']]->name  ?></td>
                        <td class="nowrap">
                            <button type="button" data-id="<?= $orders['id'] ?>" class="btn btn-cancel btn-small red accent-4">
                                <i class="material-icons">cancel</i>
                            </button>

                            <button type="button" data-id="<?= $orders['id'] ?>" class="btn btn-pay btn-small blue accent-4">
                                <i class="material-icons">paid</i>
                            </button>

                            <button type="button" data-id="<?= $orders['id'] ?>" class="btn btn-detail btn-small green accent-4">
                                <i class="material-icons">receipt_long</i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach ?>

                <?php include_pagination($pagination) ?>
            <?php else : ?>
                <tr>
                    <td colspan="6" class="text-center">
                        Nenhum pedido foi encontrado.
                    </td>
                </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>

<div id="modal1" class="modal">
    <div class="modal-content">
        <h4>Detalhamento do Pedido</h4>
    </div>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        
        <tbody id="table-detail">
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="3"></td>
                <td id="total-pedido"></td>
            </tr>
        </tfoot>
    </table>
    <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Fechar</a>
    </div>
</div>


<script>
    $(document).ready(function() {
        function drawTable(data) {
            let table = $('#table-detail');
            let totalPedido = 0;
            table.empty();

            data.forEach(function(item) {
                let valorUnitario = parseFloat(item.valor_unitario);
                let valor_total = valorUnitario * parseInt(item.quantidade);
                totalPedido += valor_total;

                table.append(`
                    <tr>
                        <td>${item.nome}</td>
                        <td>${item.quantidade}</td>
                        <td>${valorUnitario.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        })}</td>
                        <td>${valor_total.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        })}</td>
                    </tr>
                `);
            });

            $('#total-pedido').html(totalPedido.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));

            $('.modal').modal('open');
        }

        $('.modal').modal();

        $('.valor').each(function() {
            let valor = parseFloat($(this).text());

            $(this).html(valor.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }));
        });

        $('.data-recebimento').each(function() {
            let date = moment($(this).text(), 'YYYY/MM/DD');

            $(this).html(date.format('DD/MM/YYYY'));
        });

        $('.btn-detail').click(function() {
            const id = $(this).data('id')

            axios(`<?= url("pedido/detalhamento") ?>/${id}`, {
                    method: 'GET',
                    responseType: 'json',
                }).then(function(response) {
                    console.log()
                    if (response.status == 200 && response.data.length > 0) {
                        drawTable(response.data);
                    } else {
                        Toast.Error(response.data.mensagem ?? "Ocorreu um erro inesperado ao exibir o detalhamento!");
                        return
                    }
                })
                .catch(function(error) {
                    if (error.response.status == 422) {
                        let errors = error.response.data.errors;

                        for (let key in errors) {
                            Toast.Error(errors[key][0]);
                        }
                    } else {
                        Toast.Error(error.response.data.mensagem ?? "Ocorreu um erro inesperado ao exibir o detalhamento!");
                    }
                });
        })

        $('.btn-cancel').click(function() {
            const id = $(this).data('id')

            Swal.fire({
                title: 'Deseja realmente cancelar este pedido?',
                text: "Esta ação não poderá ser desfeita!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, cancelar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= url("pedido/cancelar") ?>/${id}`,
                        type: 'DELETE',
                        success: function(response, status, xhr) {
                            if (xhr.status == 204) {
                                Swal.fire(
                                    'Cancelado!',
                                    'O pedido foi cancelado com sucesso.',
                                    'success'
                                ).then(() => {
                                    location.reload()
                                })
                            } else {
                                Swal.fire(
                                    'Erro!',
                                    'Ocorreu um erro ao cancelar o pedido.',
                                    'error'
                                )
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                'Erro!',
                                error.responseJSON.mensagem,
                                'error'
                            )
                        }
                    })
                }
            })
        })

        $('.btn-pay').click(function() {
            const id = $(this).data('id')

            Swal.fire({
                title: 'Deseja realmente baixar este pedido?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, baixar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= url("pedido/baixar") ?>/${id}`,
                        type: 'PATCH',
                        success: function(response, status, xhr) {
                            if (xhr.status == 204) {
                                Swal.fire(
                                    'Baixado!',
                                    'O pedido foi baixado com sucesso.',
                                    'success'
                                ).then(() => {
                                    location.reload()
                                })
                            } else {
                                Swal.fire(
                                    'Erro!',
                                    'Ocorreu um erro ao baixar o pedido.',
                                    'error'
                                )
                            }
                        },
                        error: function(error) {
                            Swal.fire(
                                'Erro!',
                                error.responseJSON.mensagem,
                                'error'
                            )
                        }
                    })
                }
            })
        })
    })
</script>
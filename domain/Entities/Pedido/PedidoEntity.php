<?php

namespace Domain\Entities\Pedido;

use Domain\Entities\Interfaces\Entity;
use Infra\Core\Request\Validate;

class PedidoEntity implements Entity
{
    private int $clienteId;
    private array $pedido;

    public function getClienteId(): int
    {
        return $this->clienteId;
    }

    public function getPedido(): array
    {
        return $this->pedido;
    }

    public function from(array $dados)
    {
        $validated = Validate::handle($dados, [
            'cliente' => 'required|integer|greater_than:0',
            'pedido' => 'required|array'
        ]);

        $this->clienteId = $validated['cliente'];

        foreach ($validated['pedido'] as $produto) {
            Validate::handle($produto, [
                'produto_id' => 'required|integer|greater_than:0',
                'quantidade' => 'required|integer|greater_than:0'
            ]);

            $quantidade = $this->pedido[$produto['produto_id']]['quantidade'] ?? 0;

            $this->pedido[$produto['produto_id']]['produto_id'] = $produto['produto_id'];
            $this->pedido[$produto['produto_id']]['quantidade'] = $quantidade + $produto['quantidade'];
        }
    }
}

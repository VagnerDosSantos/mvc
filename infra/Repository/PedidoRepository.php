<?php

namespace Infra\Repository;

use App\Enums\StatusPedido;
use Domain\Entities\Pedido\PedidoEntity;
use Domain\Entities\Produto\ProdutoEntity;
use Infra\Database\Database;

class PedidoRepository
{
    private Database $database;
    private ProdutoRepository $produtoRepository;

    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->produtoRepository = new ProdutoRepository($database);
    }

    public function paginate(int $pagina = 1, int $limite = 20)
    {
        $sql = 'SELECT COUNT(id) FROM pedidos';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->execute();
        $items = $stmt->fetchColumn();
        $pagesQuantity = ceil($items / $limite);

        $offset = ($pagina - 1) * $limite;

        $sql = 'SELECT 
                p.*, 
                c.nome as nome_cliente,
                SUM(i.valor_unitario * i.quantidade) as valor_pedido 
            FROM pedidos as p
            INNER JOIN 
                itens_pedido as i ON i.pedido_id = p.id
            INNER JOIN 
                clientes as c ON c.id = p.cliente_id
            GROUP BY i.pedido_id
            ORDER BY p.id DESC
            LIMIT :limit 
            OFFSET :offset';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':limit', $limite, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'orders' => $stmt->fetchAll(),
            'pagination' => [
                'currentPage' => $pagina,
                'nextPage' => $pagina + 1,
                'previousPage' => $pagina - 1,
                'lastPage' => $pagesQuantity,
                'itemsPerPage' => $limite
            ]
        ];
    }

    public function detailing(int $id)
    {
        $sql = "SELECT 
                i.quantidade,
                i.valor_unitario,
                p.nome,
                p.preco as preco_atual
            FROM itens_pedido as i 
            INNER JOIN produtos as p ON p.id = i.produto_id
            WHERE i.pedido_id = :pedido_id";

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':pedido_id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): array|bool
    {
        $sql = 'SELECT * FROM pedidos WHERE id = :id';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(PedidoEntity $pedido): bool
    {
        $sql = 'INSERT INTO pedidos (status, cliente_id) VALUES (:status, :cliente_id)';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':status', StatusPedido::Aberto->value, \PDO::PARAM_STR);
        $stmt->bindValue(':cliente_id', $pedido->getClienteId(), \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new \Exception('Erro ao cadastrar pedido', 500);
        }

        $pedidoId = $this->database->getInstance()->lastInsertId();

        foreach ($pedido->getPedido() as $pedido) {
            $this->salvarItensPedido($pedidoId, $pedido);
        }

        return true;
    }

    private function salvarItensPedido(int $pedidoId, array $pedido)
    {
        $produto = $this->produtoRepository->find($pedido['produto_id']);

        if (!$produto) {
            throw new \Exception('Produto não encontrado', 404);
        }

        if ($produto['quantidade'] < $pedido['quantidade']) {
            throw new \Exception("Quantidade indisponível em estoque para o produto de ID: {$produto['id']}", 400);
        }

        $updateQuantidade = $this->produtoRepository->updateQuantidade($produto['id'], $produto['quantidade'] - $pedido['quantidade']);

        if (!$updateQuantidade) {
            throw new \Exception('Erro ao atualizar o estoque do produto', 500);
        }

        $sql = "INSERT INTO itens_pedido (produto_id, pedido_id, quantidade, valor_unitario) VALUES (:produto_id, :pedido_id, :quantidade, :valor_unitario)";

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':produto_id', $produto['id'], \PDO::PARAM_INT);
        $stmt->bindValue(':pedido_id', $pedidoId, \PDO::PARAM_INT);
        $stmt->bindValue(':quantidade', $pedido['quantidade'], \PDO::PARAM_INT);
        $stmt->bindValue(':valor_unitario', $produto['preco'], \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception('Erro ao cadastrar itens do pedido', 500);
        }
    }

    public function pay(int $id): bool
    {
        $existePedido = $this->find($id);

        if (!$existePedido) {
            throw new \Exception('Pedido não encontrado', 404);
        }

        if ($existePedido['status'] != StatusPedido::Aberto->value) {
            throw new \Exception('Pedido não pode ser pago', 400);
        }

        $sql = 'UPDATE pedidos SET status = :status WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':status', StatusPedido::Pago->value, \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function cancel(int $id): bool
    {
        $existePedido = $this->find($id);

        if (!$existePedido) {
            throw new \Exception('Pedido não encontrado', 404);
        }

        if ($existePedido['status'] == StatusPedido::Cancelado->value) {
            throw new \Exception('Pedido já cancelado', 400);
        }

        $sql = 'UPDATE pedidos SET status = :status WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':status', StatusPedido::Cancelado->value, \PDO::PARAM_STR);

        if (!$stmt->execute()) {
            throw new \Exception('Erro ao cancelar pedido', 500);
        }

        $this->retonarProdutosAoEstoque($id);

        return true;
    }

    private function retonarProdutosAoEstoque(int $id)
    {
        $sql = "SELECT * FROM itens_pedido WHERE pedido_id = :pedido_id";
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':pedido_id', $id, \PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new \Exception('Erro ao buscar itens do pedido', 500);
        }

        $itensPedido = $stmt->fetchAll();

        foreach ($itensPedido as $item) {
            $produto = $this->produtoRepository->find($item['produto_id']);

            if (!$produto) {
                throw new \Exception('Produto não encontrado para realizar o estorno', 404);
            }

            $updateQuantidade = $this->produtoRepository->updateQuantidade($produto['id'], $produto['quantidade'] + $item['quantidade']);

            if (!$updateQuantidade) {
                throw new \Exception('Erro ao atualizar o estoque do produto', 500);
            }
        }
    }
}

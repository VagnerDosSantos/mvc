<?php

namespace App\Controllers;

use App\Enums\HttpStatus;
use App\Enums\StatusPedido;
use Domain\Usecases\Pedido\CadastrarPedido;
use Infra\Core\Request\Exception;
use Infra\Core\Request\Response;
use Infra\Database\MySQLConnection;
use Infra\Repository\ClienteRepository;
use Infra\Repository\PedidoRepository;
use Infra\Repository\ProdutoRepository;

class PedidoController extends Controller
{
    private PedidoRepository $pedidoRepository;
    private ProdutoRepository $produtoRepository;
    private ClienteRepository $clienteRepository;
    private MySQLConnection $connection;

    public function __construct()
    {
        parent::__construct();

        $this->connection = new MySQLConnection();
        $this->pedidoRepository = new PedidoRepository($this->connection);
        $this->produtoRepository = new ProdutoRepository($this->connection);
        $this->clienteRepository = new ClienteRepository($this->connection);
    }

    public function index()
    {
        $pedidos = $this->pedidoRepository->paginate($this->request->get('page', 1), 20);

        return view('Pedido.Index', [
            'status' => StatusPedido::cases(),
            ...$pedidos
        ]);
    }

    public function create()
    {
        $produtos = $this->produtoRepository->all();
        $clientes = $this->clienteRepository->all();

        return view('Pedido.Form', [
            'method' => 'POST',
            'produtos' => $produtos,
            'clientes' => $clientes
        ]);
    }

    public function store()
    {
        $this->connection->getInstance()->beginTransaction();

        try {
            CadastrarPedido::handle($this->request->all());
        } catch (\Throwable $th) {
            $this->connection->getInstance()->rollBack();
            return Exception::handle($th);
        }

        $this->connection->getInstance()->commit();

        return Response::json([
            'mensagem' => 'Pedido criado com sucesso!'
        ], HttpStatus::CREATED->value);
    }

    public function detalhamento()
    {
        $detalhamento = $this->pedidoRepository->detailing($this->request->get('id'));

        return Response::json($detalhamento);
    }

    public function pay()
    {
        $this->connection->getInstance()->beginTransaction();

        try {
            $this->pedidoRepository->pay($this->request->get('id'));
        } catch (\Throwable $th) {
            $this->connection->getInstance()->rollBack();
            return Exception::handle($th);
        }

        $this->connection->getInstance()->commit();
        return Response::noContent();
    }

    public function destroy()
    {
        $this->connection->getInstance()->beginTransaction();

        try {
            $this->pedidoRepository->cancel($this->request->get('id'));
        } catch (\Throwable $th) {
            $this->connection->getInstance()->rollBack();
            return Exception::handle($th);
        }

        $this->connection->getInstance()->commit();
        return Response::noContent();
    }
}

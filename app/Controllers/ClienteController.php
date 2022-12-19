<?php

namespace App\Controllers;

use App\Enums\HttpStatus;
use Domain\Usecases\Cliente\CadastrarCliente;
use Domain\Usecases\Cliente\EditarCliente;
use Infra\Core\Request\Exception;
use Infra\Core\Request\Response;
use Infra\Database\MySQLConnection;
use Infra\Repository\ClienteRepository;

class ClienteController extends Controller
{
    private ClienteRepository $clienteRepository;

    public function __construct()
    {
        parent::__construct();
        $this->clienteRepository = new ClienteRepository(new MySQLConnection);
    }

    public function index()
    {
        $clientes = $this->clienteRepository->paginate($this->request->get('page', 1), 20);
        return view('Cliente.Index', $clientes);
    }

    public function create()
    {
        return view('Cliente.Form', ['method' => 'POST']);
    }

    public function store()
    {
        try {
            CadastrarCliente::handle($this->request->all());
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::json(['mensagem' => 'Cliente cadastrado com sucesso!'], HttpStatus::CREATED->value);
    }

    public function show()
    {
        $cliente = $this->clienteRepository->find($this->request->get('id'));

        if (!$cliente) {
            redirect('cliente/index');
        }

        return view('Cliente.Form', [
            'method' => 'PUT',
            ...$cliente
        ]);
    }

    public function edit()
    {
        try {
            EditarCliente::handle($this->request->all());
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::json(['mensagem' => 'Cliente editado com sucesso!'], HttpStatus::OK->value);
    }

    public function destroy()
    {
        try {
            $this->clienteRepository->delete($this->request->get('id'));
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::noContent();
    }
}

<?php

namespace App\Controllers;

use App\Enums\HttpStatus;
use Domain\Usecases\Produto\CadastrarProduto;
use Domain\Usecases\Produto\EditarProduto;
use Infra\Core\Request\Exception;
use Infra\Core\Request\Response;
use Infra\Database\MySQLConnection;
use Infra\Repository\ProdutoRepository;

class ProdutoController extends Controller
{
    private ProdutoRepository $produtoRepository;

    public function __construct()
    {
        parent::__construct();
        $this->produtoRepository = new ProdutoRepository(new MySQLConnection);
    }

    public function index()
    {
        $dados = $this->produtoRepository->paginate($this->request->get('page', 1), 20);

        return view('Produto.Index', $dados);
    }

    public function create()
    {
        return view('Produto.Form', ['method' => 'POST']);
    }

    public function show()
    {
        $produto = $this->produtoRepository->find($this->request->get('id'));

        if (!$produto) {
            return redirect('/produto');
        }

        return view('Produto.Form', [
            'method' => 'PUT',
            ...$produto
        ]);
    }

    public function getProduto()
    {
        $produto = $this->produtoRepository->find($this->request->get('id'));

        return Response::json($produto);
    }

    public function destroy()
    {
        $this->produtoRepository->delete($this->request->get('id'));

        return Response::noContent();
    }

    public function edit()
    {
        try {
            EditarProduto::handle($this->request->all());
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::json([
            'mensagem' => 'Produto editado com sucesso!'
        ]);
    }

    public function store()
    {
        try {
            CadastrarProduto::handle($this->request->all());
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::json([
            'message' => 'Produto cadastrado com sucesso!'
        ], HttpStatus::CREATED->value);
    }
}

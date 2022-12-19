<?php

namespace App\Controllers;

use App\Enums\HttpStatus;
use Domain\Usecases\Usuario\AutenticarUsuario;
use Domain\Usecases\Usuario\CadastrarUsuario;
use Infra\Core\Request\Exception;
use Infra\Core\Request\Response;
use Infra\Core\Request\Validate;

class UserController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (is_logged()) {
            return redirect('/');
        }

        return view('Usuario.Login');
    }

    public function login()
    {
        try {
            $validated = Validate::handle($this->request->all(), [
                'email' => 'required|email',
                'senha' => 'required'
            ]);

            AutenticarUsuario::handle($validated);
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::json([
            'mensagem' => 'Usuário logado com sucesso!'
        ], HttpStatus::OK->value);
    }

    public function logout()
    {
        session_destroy();

        return redirect('/login');
    }

    public function create()
    {
        return view('Usuario.Cadastro');
    }

    public function store()
    {
        try {
            CadastrarUsuario::handle($this->request->all());
        } catch (\Throwable $th) {
            return Exception::handle($th);
        }

        return Response::json([
            'mensagem' => 'Usuário cadastrado com sucesso!'
        ], HttpStatus::CREATED->value);
    }
}

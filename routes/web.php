<?php

use App\Controllers\ClienteController;
use App\Controllers\HomeController;
use App\Controllers\PedidoController;
use App\Controllers\ProdutoController;
use App\Controllers\UserController;
use App\Middleware\Auth;
use Infra\Core\Route\Router;

$router = new Router();

$router->get("/login", [UserController::class, 'index']);
$router->get("/logout", [UserController::class, 'logout']);
$router->post("/login", [UserController::class, 'login']);

$router->get("/usuario/cadastro", [UserController::class, 'create']);
$router->post("/usuario/cadastro", [UserController::class, 'store']);

$router->middleware(Auth::class, function () use ($router) {
    $router->get("/", [HomeController::class, 'index']);

    $router->prefix('produto', function () use ($router) {
        $router->get('/get/{id}', [ProdutoController::class, 'getProduto']);
        $router->get('/', [ProdutoController::class, 'create']);
        $router->post('/', [ProdutoController::class, 'store']);
        $router->get('index', [ProdutoController::class, 'index']);
        $router->get('editar/{id}', [ProdutoController::class, 'show']);
        $router->put('editar/{id}', [ProdutoController::class, 'edit']);
        $router->delete('deletar/{id}', [ProdutoController::class, 'destroy']);
    });

    $router->prefix('cliente', function () use ($router) {
        $router->get('/', [ClienteController::class, 'create']);
        $router->post('/', [ClienteController::class, 'store']);
        $router->get('index', [ClienteController::class, 'index']);
        $router->get('editar/{id}', [ClienteController::class, 'show']);
        $router->put('editar/{id}', [ClienteController::class, 'edit']);
        $router->delete('deletar/{id}', [ClienteController::class, 'destroy']);
    });

    $router->prefix('pedido', function () use ($router) {
        $router->get('/detalhamento/{id}', [PedidoController::class, 'detalhamento']);
        $router->get('/', [PedidoController::class, 'create']);
        $router->post('/', [PedidoController::class, 'store']);
        $router->get('index', [PedidoController::class, 'index']);
        $router->patch('baixar/{id}', [PedidoController::class, 'pay']);
        $router->delete('cancelar/{id}', [PedidoController::class, 'destroy']);
    });
});

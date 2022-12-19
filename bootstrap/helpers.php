<?php

function view(string $file, array $data = [], string $template = 'template')
{
    extract($data);

    $file = ucfirst(str_replace('.php', '', $file));
    $file = str_replace('.', '/', $file);
    $file = "{$file}.php";

    require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR . "{$template}.php";
}

function is_logged()
{
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function redirect(string $location = '')
{
    $location = trim($location, '/');
    header("Location: /{$location}");
}

function public_path(string $path = '')
{
    $path = trim($path, '/');
    return __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "{$path}";
}

function asset(string $path = '')
{
    $path = trim($path, '/');
    return "/{$path}";
}

function url(string $url)
{
    $url = trim($url, '/');
    return APP_URL . "/{$url}";
}

function include_pagination($pagination)
{
    require_once ROOT_PATH . "App" . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR . "Pagination.php";
}

function sanitize_uri(string $uri)
{
    $uri = strtok($uri, '?');
    $uri = trim($uri, '/');
    $uri = explode('/', $uri);
    $uri = array_filter($uri);
    $uri = array_values($uri);
    $uri = implode('/', $uri);

    if (empty($uri)) {
        $uri = '/';
    }

    return $uri;
}

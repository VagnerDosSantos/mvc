<?php

declare(strict_types=1);

namespace Infra\Core\Request;

class Validate
{
    public static function handle(array $data, array $rules): array
    {
        $errors = [];
        $keys = array_keys($rules);

        foreach ($rules as $key => $rule) {
            $rules = explode('|', $rule);

            foreach ($rules as $rule) {
                $rule = explode(':', $rule);

                $method = $rule[0];
                $param = $rule[1] ?? null;

                $error = self::{"{$method}"}($key, $data[$key] ?? null, $param);

                if ($error) {
                    $errors[$key] = $error;
                    break;
                }
            }
        }

        if (count($errors) > 0) {
            throw new \Exception(json_encode($errors), 422);
        }

        return array_filter($data, function ($key) use ($keys) {
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    private static function required(string $key, $value): ?string
    {
        if (empty($value) && !is_numeric($value)) {
            return "O campo {$key} é obrigatório";
        }

        return null;
    }

    private static function min(string $key, $value, $param): ?string
    {
        if (!empty($value) && $value < $param) {
            return "O campo {$key} deve ser maior ou igual a {$param}";
        }

        return null;
    }

    private static function greater_than(string $key, $value, $param): ?string
    {
        if (!empty($value) && $value <= $param) {
            return "O campo {$key} deve ser maior que {$param}";
        }

        return null;
    }

    private static function less_than(string $key, $value, $param): ?string
    {
        if (!empty($value) && $value >= $param) {
            return "O campo {$key} deve ser menor que {$param}";
        }

        return null;
    }

    private static function max(string $key, $value, $param): ?string
    {
        if (!empty($value) && $value > $param) {
            return "O campo {$key} deve ser menor ou igual a {$param}";
        }

        return null;
    }

    private static function min_digits(string $key, $value, $param): ?string
    {
        if (!empty($value) && mb_strlen($value) < $param) {
            return "O campo {$key} deve ter no mínimo {$param} caracteres";
        }

        return null;
    }

    private static function max_digits(string $key, $value, $param): ?string
    {
        if (!empty($value) && mb_strlen($value) > $param) {
            return "O campo {$key} deve ter no máximo {$param} caracteres";
        }

        return null;
    }

    private static function numeric(string $key, $value): ?string
    {
        if (!empty($value) && !is_numeric($value)) {
            return "O campo {$key} deve ser um número";
        }

        return null;
    }

    private static function integer(string $key, $value): ?string
    {
        if (!empty($value) && !is_int($value)) {
            return "O campo {$key} deve ser um número inteiro";
        }

        return null;
    }

    public static function nullable(string $key, $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return self::required($key, $value);
    }

    private static function email(string $key, $value): ?string
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return "O campo {$key} deve ser um email válido";
        }

        return null;
    }

    private static function cpf(string $key, $value): ?string
    {
        if (!empty($value) && !self::validateCpf($value)) {
            return "O campo {$key} deve ser um CPF válido";
        }

        return null;
    }

    private static function validateCpf($cpf): bool
    {
        $cpf = preg_replace('/[^0-9]/', '', (string) $cpf);

        if (strlen($cpf) != 11) {
            return false;
        }

        for ($i = 0, $j = 10, $sum = 0; $i < 9; $i++, $j--) {
            $sum += $cpf[$i] * $j;
        }

        $rest = $sum % 11;

        if ($cpf[9] != ($rest < 2 ? 0 : 11 - $rest)) {
            return false;
        }

        for ($i = 0, $j = 11, $sum = 0; $i < 10; $i++, $j--) {
            $sum += $cpf[$i] * $j;
        }

        $rest = $sum % 11;

        return $cpf[10] == ($rest < 2 ? 0 : 11 - $rest);
    }

    private static function date(string $key, $value): ?string
    {
        if (!empty($value) && !self::validateDate($value)) {
            return "O campo {$key} deve ser uma data válida";
        }

        return null;
    }

    private static function validateDate($date, $format = 'Y-m-d'): bool
    {
        $dateTime = \DateTime::createFromFormat($format, $date);

        return $dateTime && $dateTime->format($format) === $date;
    }

    private static function array(string $key, $value): ?string
    {
        if (!empty($value) && !is_array($value)) {
            return "O campo {$key} deve ser um array";
        }

        return null;
    }
}

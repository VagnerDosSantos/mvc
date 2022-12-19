<?php

namespace App\Enums;

enum StatusPedido: int
{
    case Cancelado = 0;
    case Aberto = 1;
    case Pago = 2;
}

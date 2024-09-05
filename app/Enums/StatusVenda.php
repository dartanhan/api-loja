<?php


namespace App\Enums;


class StatusVenda
{
    const PENDENTE = 'PENDENTE';
    const PAGO = 'PAGO';
    const CANCELADO = 'CANCELADO';
    const ABERTO = 'ABERTO';

    public static function getValues()
    {
        return [
            self::PENDENTE,
            self::PAGO,
            self::CANCELADO,
            self::ABERTO
        ];
    }
}

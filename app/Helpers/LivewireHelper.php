<?php


namespace App\Helpers;


use Illuminate\Support\Carbon;
use Exception;

class LivewireHelper
{
    static function formatCurrencyToBD($valor, $formatter)
    {
        return $formatter->parse(str_replace(['R$', ' '], '', $valor));
    }

    public static function formatCurrency($value)
    {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public static function slugify($string)
    {
        return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $string));
    }

    public static function formatarData($data)
    {
        if ($data === "00/00/0000" || empty($data)) {
            return "0000-00-00";
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $data)->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }

}

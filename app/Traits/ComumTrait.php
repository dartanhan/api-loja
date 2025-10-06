<?php


namespace App\Traits;

use NumberFormatter;

trait ComumTrait
{
    /**
     * Retorna o NumberFormatter
     */
    public function NumberFormatter()
    {
        return  new NumberFormatter('pt_BR', NumberFormatter::DECIMAL);
    }
}

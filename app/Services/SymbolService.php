<?php
namespace App\Services;

class SymbolService
{
    public static function generateRandomSymbol()
    {
        $allowedsSymbols = config('symbol.alloweds');
        return $allowedsSymbols[array_rand($allowedsSymbols)];
    }
}

<?php


namespace App\Services;


use App\Interfaces\PayOutInterface;

class PayOutBetService implements PayOutInterface
{
    private $countSymbol;
    private $betAmount;

    public function __construct($countSymbol, $betAmount = 100)
    {
        $this->countSymbol = $countSymbol;
        $this->betAmount = $betAmount;
    }

    public function getPay() : float
    {
        $payOutPercent = config('bet.pay_out_percent');

        if(empty($payOutPercent[$this->countSymbol])) {
            throw new \Exception('Number of symbols is not valid for payment');
        }

        return $this->betAmount * ($payOutPercent[$this->countSymbol] / 100);
    }


}

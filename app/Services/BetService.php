<?php

namespace App\Services;
use App\Interfaces\BetInterface;

class BetService implements BetInterface
{

    public $table;
    public $symbolsMatched = [];
    public $betAmount;

    public function doBet($betAmount = 100)
    {
        $this->betAmount = $betAmount;
        $this->generateRandomTable();
        $this->calculatePayLine();
    }

    public function getBetResult() : string
    {
        return json_encode([
            'board' => '[' . $this->generateViewTable().']',
            'paylines' => $this->generatePayLineText(),
            'bet_amount' => $this->betAmount,
            'win_total' => $this->getTotalWin()
        ], JSON_PRETTY_PRINT);
    }

    public function calculatePayLine()
    {
        $this->symbolsMatched[] = 3;
        $this->symbolsMatched[] = 4;
    }

    public function generatePayLineText()
    {
        foreach ($this->table as $line) {
            $payLineText[] = [join(" ", $line) => 3];
        }

        return $payLineText;
    }

    public function generateRandomTable()
    {
        $this->table[0] = $this->generateLine([0, 3, 6, 9, 12]);
        $this->table[1] = $this->generateLine([1, 4, 7, 10, 13]);
        $this->table[2] = $this->generateLine([2, 5, 8, 11, 14]);
    }

    public function generateViewTable()
    {
        foreach ($this->table as $line) {
            $textTable[] = join(',', $line);
        }
        return join(',', $textTable);
    }

    private function generateLine(array $baseElements) : array
    {
        foreach ($baseElements as $element) {
            $line[$element] = SymbolService::generateRandomSymbol();
        }
        return $line;
    }

    public function getTotalWin()
    {
        $totalWin = [];
        foreach ($this->symbolsMatched as $symbolMatched) {
            $totalWin[] = (new PayOutBetService($symbolMatched, $this->betAmount))->getPay();
        }

        return array_sum($totalWin);
    }
}

<?php

namespace App\Services;

use App\Interfaces\BetInterface;

class BetService implements BetInterface
{

    public $table;
    public $betAmount;
    protected $arrayValue = [];
    protected $payLine;
    protected $symbolsMatched = [];

    public function doBet($betAmount = 100)
    {
        $this->betAmount = $betAmount;
        $this->generateRandomTable();
        $this->calculatePayLines();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getBetResult(): string
    {
        return json_encode([
            'board' => '[' . $this->generateViewTable() . ']',
            'paylines' => $this->getPayLines(),
            'bet_amount' => $this->betAmount,
            'win_total' => $this->getTotalWin()
        ], JSON_PRETTY_PRINT);
    }

    /**
     * @return string
     */
    protected function getPayLines()
    {
        return !empty($this->payLine) ? $this->payLine : 'Unfortunately there were no hits';
    }

    /**
     * @throws \Exception
     */
    protected function calculatePayLines()
    {
        $possibleWinSequences = config('bet.win_sequence');

        foreach ($possibleWinSequences as $possibleWinSequence) {
            $this->calculatePayLine($possibleWinSequence);
        }
    }

    /**
     * @param $possibleWinSequence
     * @throws \Exception
     */
    protected function calculatePayLine($possibleWinSequence)
    {
        $count = 0;
        foreach ($possibleWinSequence as $sequence) {
            if (empty($symbol = $this->getSymbol($sequence))) {
                throw new \Exception('Error to find symbol on table');
            }

            if ($count == 0) {
                $previous = $symbol;
                $count = 1;
                continue;
            }

            $count = $symbol === $previous ? $count + 1 : 0;
            $previous = $symbol;
        }

        if ($count >= 3) {
            $this->symbolsMatched[] = $count;
            $this->generatePayLineText($possibleWinSequence, $count);
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    protected function getSymbol($key)
    {
        return $this->arrayValue[$key];
    }

    /**
     * @param $sequence
     * @param $count
     */
    public function generatePayLineText($sequence, $count)
    {
        $this->payLine[] = [join(" ", $sequence) => $count];
    }

    public function generateRandomTable()
    {
        $baseTable = config('bet.base_table');

        foreach ($baseTable as $key => $value) {
            $this->table[$key] = $this->generateLine($value, $key);
        }
    }

    /**
     * @return string
     */
    public function generateViewTable(): string
    {
        foreach ($this->table as $line) {
            $textTable[] = join(',', $line);
        }
        return join(',', $textTable);
    }

    /**
     * @param array $baseElements
     * @return array
     */
    protected function generateLine(array $baseElements): array
    {
        foreach ($baseElements as $element) {
            $this->arrayValue[$element] = $line[$element] = SymbolService::generateRandomSymbol();
        }
        return $line;
    }

    /**
     * @return float
     * @throws \Exception
     */
    public function getTotalWin(): float
    {
        $totalWin = [];
        foreach ($this->symbolsMatched as $symbolMatched) {
            $totalWin[] = (new PayOutBetService($symbolMatched, $this->betAmount))->getPay();
        }

        return array_sum($totalWin);
    }
}

<?php

use App\Services\BetService;

class BetTest extends  TestCase
{

    const board = '';

    public function testBaseCase()
    {
        $service = new BetService();

        $service->table[0] = ['J', 'J', 'J', 'Q', 'K'];
        $service->table[2] = ['cat', 'J', 'Q', 'monkey', 'bird'];
        $service->table[3] = ['bird', 'bird', 'J', 'Q', 'A'];
        $service->arrayValue = ['J', 'cat', 'bird', 'J', 'J', 'bird', 'J', 'Q', 'J', 'Q', 'monkey', 'Q', 'K', 'bird', 'A'];

        $service->calculatePayLines();
        $result = json_decode($service->getBetResult(), true);


        $this->assertArrayHasKey('board', $result);
        $this->assertArrayHasKey('paylines', $result);
        $this->assertArrayHasKey('bet_amount', $result);
        $this->assertArrayHasKey('win_total', $result);


        $this->assertEquals('[J,J,J,Q,K,cat,J,Q,monkey,bird,bird,bird,J,Q,A]', $result['board']);
        $this->assertEquals([["0 3 6 9 12" => 3], ["0 4 8 10 12" => 3]], $result['paylines']);
        $this->assertEquals(100, $result['bet_amount']);
        $this->assertEquals(40, $result['win_total']);
    }

    public function setUp(): void
    {
        parent::setUp();
        config('bet');
        config('symbol');
    }

}

<?php
use PHPUnit\Framework\TestCase;

class MathPluginTest extends TestCase
{
    const CONVERTION_DATA_SAMPLE = [
        '0' => [
            '2' => '0',
            '4' => '0',
            '8' => '0',
            '10' => '0',
            '16' => '0',
            '32' => '0',
            '64' => '0'
        ],
        '1' => [
            '2' => '1',
            '4' => '1',
            '8' => '1',
            '10' => '1',
            '16' => '1',
            '32' => '1',
            '64' => '1'
        ],
        '2' => [
            '2' => '10',
            '4' => '2',
            '8' => '2',
            '10' => '2',
            '16' => '2',
            '32' => '2',
            '64' => '2'
        ],
        '4' => [
            '2' => '100',
            '4' => '10',
            '8' => '4',
            '10' => '4',
            '16' => '4',
            '32' => '4',
            '64' => '4'
        ],
        '5' => [
            '2' => '101',
            '4' => '11',
            '8' => '5',
            '10' => '5',
            '16' => '5',
            '32' => '5',
            '64' => '5'
        ],
        '8' => [
            '2' => '1000',
            '4' => '20',
            '8' => '10',
            '10' => '8',
            '16' => '8',
            '32' => '8',
            '64' => '8'
        ],
        '9' => [
            '2' => '1001',
            '4' => '21',
            '8' => '11',
            '10' => '9',
            '16' => '9',
            '32' => '9',
            '64' => '9'
        ],
        '101' => [
            '2' => '1100101',
            '4' => '1211',
            '8' => '145',
            '10' => '101',
            '16' => '65',
            '32' => '35',
            '64' => '1B'
        ],
        '777' => [
            '2' => '1100001001',
            '4' => '30021',
            '8' => '1411',
            '10' => '777',
            '16' => '309',
            '32' => 'o9',
            '64' => 'c9'
        ],
        '99999' => [
            '2' => '11000011010011111',
            '4' => '120122133',
            '8' => '303237',
            '10' => '99999',
            '16' => '1869f',
            '32' => '31kv',
            '64' => 'oqv'
        ],
        '16000000032' => [
            '2' => '1110111001101011001010000000100000',
            '4' => '32321223022000200',
            '8' => '167153120040',
            '10' => '16000000032',
            '16' => '3b9aca020',
            '32' => 'esqp810',
            '64' => 'eVHa0w'
        ]
    ];

    public function testDec2base64()
    {
        $math = (new CommonCore)->initPlugin('math');

        foreach (static::CONVERTION_DATA_SAMPLE as $inputValue => $inputSet) {
            $inputValue = (int) $inputValue;

            foreach ($inputSet as $base => $expectedValue) {
                $base = (int) $base;

                $result = $math->dec2base64($inputValue, $base);

                $this->assertEquals($result, $expectedValue);
            }
        }
    }
}
?>
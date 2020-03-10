<?php

use Omnipay\Paymongo\CardGateway;
use Omnipay\Paymongo\Utils\Payment;
use Omnipay\Paymongo\Utils\Token;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCanAuthorize()
    {
        $mockToken = new Token('tok_xxxxx', 'token');
        $mockery   = Mockery::mock(CardGateway::class);

        $mockery->allows()->authorize([
            'number'      => '4123 4501 3100 0508',
            'expiryMonth' => '1',
            'expiryYear'  => '22',
            'cvv'         => '123',
        ])->andReturns($mockToken);

        $token = $mockery->authorize([
            'number'      => '4123 4501 3100 0508',
            'expiryMonth' => '1',
            'expiryYear'  => '22',
            'cvv'         => '123',
        ]);

        $this->assertEquals($mockToken, $token);
    }

    /**
     * Set can purchase.
     *
     * @throws Exception
     */
    public function testCanPurchase()
    {
        $mockery = Mockery::mock(CardGateway::class);

        $token = new Token('tok_xxxxx', 'token');

        $mockPayment = new Payment(['id' => 'id_xxx', 'type' => 'payment']);

        $mockery->allows()->purchase([
            'amount'               => '123.45',
            'currency'             => 'PHP',
            'description'          => 'Just a purchase!',
            'statement_descriptor' => 'MyCo',
            'token'                => $token,
        ])->andReturns($mockPayment);

        $oayment = $mockery->purchase([
            'amount'               => '123.45',
            'currency'             => 'PHP',
            'description'          => 'Just a purchase!',
            'statement_descriptor' => 'MyCo',
            'token'                => $token,
        ]);

        $this->assertEquals(
            $oayment->all()->get('id'),
            $mockPayment->all()->get('id')
        );
    }
}

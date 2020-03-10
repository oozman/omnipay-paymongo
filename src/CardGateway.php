<?php

namespace Omnipay\Paymongo;

use Exception;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Paymongo\Utils\Payment;
use Omnipay\Paymongo\Utils\Token;
use Zttp\Zttp;

class CardGateway extends AbstractGateway
{
    protected $baseUri = 'https://api.paymongo.com/v1';
    protected $secretKey;
    protected $publicKey;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Paymongo_Card';
    }

    /**
     * Set secret key.
     *
     * @param $publicKey
     * @param $secretKey
     */
    public function setKeys($publicKey, $secretKey)
    {
        $this->publicKey = $publicKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Get api url with path.
     *
     * @param  string  $path
     *
     * @return string
     */
    public function apiUrl($path = '')
    {
        return $this->baseUri.'/'.trim($path, '/');
    }

    /**
     * Authorize a credit or debit card.
     * Generates a Paymongo token.
     *
     * @param  array  $options
     *
     * @return RequestInterface|Token
     * @throws Exception
     */
    public function authorize(array $options)
    {
        $card = new CreditCard($options);

        $response = Zttp::withBasicAuth($this->publicKey, '')
            ->post($this->apiUrl('/tokens'), [
                'data' => [
                    'attributes' => [
                        'number'    => $card->getNumber(),
                        'exp_month' => (float)$card->getExpiryMonth(),
                        'exp_year'  => (float)$card->getExpiryYear(),
                        'cvc'       => $card->getCvv(),
                    ],
                ],
            ]);

        if (! $response->isOk()) {
            throw new Exception($response->body());
        }

        $data = $response->json()['data'];

        return new Token($data['id'], $data['type']);
    }

    /**
     * Start a purchase.
     * This will create a new Paymongo payment.
     *
     * @param  array  $options
     *
     * @return ResponseInterface
     * @throws Exception
     */
    public function purchase(array $options)
    {
        $options = collect($options);

        /** @var Token $token */
        $token = $options->get('token');

        $response = Zttp::withBasicAuth($this->secretKey, '')
            ->post($this->apiUrl('/payments'), [
                'data' => [
                    'attributes' => [
                        'amount'               => $this->convertAmountToCents($options->get('amount')),
                        'currency'             => $options->get('currency'),
                        'description'          => $options->get('description'),
                        'statement_descriptor' => $options->get('statement_descriptor'),
                        'source'               => [
                            'id'   => $token->id(),
                            'type' => $token->type(),
                        ],
                    ],
                ],
            ]);

        if (! $response->isOk()) {
            throw new Exception($response->body());
        }

        return new Payment($response->json()['data']);
    }

    /**
     * Convert amount to cents.
     *
     * @param $amount
     *
     * @return float|int
     * @throws Exception
     */
    public function convertAmountToCents($amount)
    {
        if (! is_numeric($amount)) {
            throw new Exception('Please make sure your amount is a number.');
        }

        return number_format($amount, 2) * 100;
    }
}

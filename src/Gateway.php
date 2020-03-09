<?php

namespace Omnipay\Paymongo;

use Exception;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\CreditCard;
use Omnipay\Paymongo\Utils\Token;
use Zttp\Zttp;

class Gateway extends AbstractGateway
{
    protected $baseUri = 'https://api.paymongo.com/v1';
    protected $secretKey;
    protected $publicKey;

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'Paymongo';
    }

    /**
     * Set secret key.
     *
     * @param  string  $key
     */
    public function setSecretKey($key = '')
    {
        $this->secretKey = $key;
    }

    /**
     * Set public key.
     *
     * @param  string  $key
     */
    public function setPublicKey($key = '')
    {
        $this->publicKey = $key;
    }

    /**
     * Get api url with path.
     *
     * @param  string  $path
     *
     * @return string
     */
    private function apiUrl($path = '')
    {
        return $this->baseUri.'/'.trim($path, '/');
    }

    public function authorize(array $options)
    {
        $card = new CreditCard($options);

        $response = Zttp::withBasicAuth($this->secretKey, '')
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

    public function purchase(array $options)
    {
        $options = collect($options);

        /** @var Token $token */
        $token = $options->get('token');

        $response = Zttp::withBasicAuth($this->secretKey, '')
            ->post($this->apiUrl('/payments'), [
                'data' => [
                    'attributes' => [
                        'amount'               => $options->get('amount'),
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

        return $response->json()['data'];
    }
}

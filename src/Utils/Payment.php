<?php

namespace Omnipay\Paymongo\Utils;

use Omnipay\Common\Message\ResponseInterface;

class Payment implements ResponseInterface
{
    protected $id;
    protected $type;
    protected $currency;
    protected $description;
    protected $fee;
    protected $netAmount;
    protected $status;
    protected $paidAt;

    public function __construct(array $parameters)
    {
        $parameters = collect($parameters);
        $attributes = collect($parameters->get('attributes'));

        $this->id          = $parameters->get('id');
        $this->type        = $parameters->get('type');
        $this->currency    = $attributes->get('currency');
        $this->description = $attributes->get('description');
        $this->fee         = $attributes->get('fee');
        $this->netAmount   = $attributes->get('net_amount');
        $this->status      = $attributes->get('status');
        $this->source      = $attributes->get('source');
        $this->paidAt      = $attributes->get('paid_at');
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return collect([
            'id'          => $this->id,
            'type'        => $this->type,
            'currency'    => $this->currency,
            'description' => $this->description,
            'fee'         => $this->fee,
            'net_amount'  => $this->netAmount,
            'status'      => $this->status,
            'source'      => $this->source,
            'paid_at'     => $this->paidAt,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->all();
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this->all();
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return ! empty($this->id);
    }

    /**
     * @inheritDoc
     */
    public function isRedirect()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isCancelled()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return \GuzzleHttp\json_encode($this->all());
    }

    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return 200;
    }

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        return $this->id;
    }
}

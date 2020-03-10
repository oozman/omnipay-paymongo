<?php

namespace Omnipay\Paymongo\Utils;

class Payment
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
}

<?php

namespace Omnipay\Paymongo\Utils;

class Token
{
    protected $id;
    protected $type;

    /**
     * Token constructor.
     *
     * @param  string  $id
     * @param  string  $type
     */
    public function __construct($id, $type)
    {
        $this->id   = $id;
        $this->type = $type;
    }

    /**
     * Get token id.
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get token type.
     *
     * @return string
     */
    public function type()
    {
        return $this->type;
    }
}

<?php

declare(strict_types=1);

namespace Common\Ui\Http\Restful\Authorization\JwtToken;

use Common\Ui\Http\Restful\Authorization\TokenValidator;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\ValidationData;

class JwtTokenValidator implements TokenValidator
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var Signer
     */
    private $signer;

    public function __construct(array $configuration, Signer $signer)
    {
        $this->configuration = $configuration;
        $this->signer        = $signer;
    }

    public function isValid(string $token): bool
    {
        $data  = new ValidationData();
        $token = (new Parser())->parse($token);

        return $token->validate($data) && $token->verify($this->signer, $this->configuration[JwtTokenFactory::KEY]);
    }
}

<?php

declare(strict_types=1);

namespace UserIdentity\Infrastructure\Domain;

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\ValidationData;
use UserIdentity\Domain\TokenValidator;

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

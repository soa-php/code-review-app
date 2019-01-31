<?php

declare(strict_types=1);

namespace UserIdentityTest\UserIdentity\Double;

use UserIdentity\Domain\ContentValidationResult;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommand;
use UserIdentity\Domain\UserWithPasswordContentValidator;

class UserWithPasswordContentValidatorStub extends UserWithPasswordContentValidator
{
    /**
     * @var ContentValidationResult
     */
    private $result;

    public function __construct(ContentValidationResult $result)
    {
        $this->result = $result;
    }

    public function validate(LogUserInWithPasswordCommand $command): ContentValidationResult
    {
        return $this->result;
    }
}

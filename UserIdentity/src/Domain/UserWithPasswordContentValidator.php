<?php

declare(strict_types=1);

namespace UserIdentity\Domain;

use Assert\Assert;
use Assert\LazyAssertionException;
use UserIdentity\Domain\UseCase\LogUserInWithPasswordCommand;

class UserWithPasswordContentValidator
{
    public function validate(LogUserInWithPasswordCommand $command): ContentValidationResult
    {
        try {
            foreach ($command->roles() as $role) {
                Assert::that($role)->inArray(AllowedRoles::$roles);
            }

            Assert::lazy()
                ->that($command->email(), 'email')->email()
                ->that($command->username(), 'username')->notEmpty()
                ->that($command->password(), 'password')->notEmpty()
                ->verifyNow();
        } catch (\InvalidArgumentException | LazyAssertionException $exception) {
            return ContentValidationResult::failed($exception->getMessage());
        }

        return ContentValidationResult::succeed();
    }
}

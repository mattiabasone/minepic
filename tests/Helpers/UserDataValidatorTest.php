<?php

declare(strict_types=1);

namespace MinepicTests\Helpers;

use Minepic\Helpers\UserDataValidator;
use MinepicTests\TestCase;

class UserDataValidatorTest extends TestCase
{
    public function shouldValidateUsernameDataProvider(): array
    {
        return [
            ['ZioPino'],
            ['Elon_Musk'],
            ['AVeryLongUsername'],
        ];
    }

    /**
     * @dataProvider shouldValidateUsernameDataProvider
     */
    public function testShouldValidateUsername(string $value): void
    {
        $this->assertTrue(
            UserDataValidator::isValidUsername($value)
        );
    }

    public function shouldNotValidateUsernameDataProvider(): array
    {
        return [
            ['!LOL'],
            ['EB&ACA'],
            ['Invalid@Username'],
        ];
    }

    /**
     * @dataProvider shouldNotValidateUsernameDataProvider
     */
    public function testShouldNotValidateUsername(string $value): void
    {
        $this->assertFalse(
            UserDataValidator::isValidUsername($value)
        );
    }

    public function shouldValidateUuidDataProvider(): array
    {
        return [
            ['6257a83ef36c4ba29115f4213869d5fb'],
            ['45f50155c09f4fdcb5cee30af2ebd1f0'],
            ['f498513ce8c84773be26ecfc7ed5185d'],
        ];
    }

    /**
     * @dataProvider shouldValidateUuidDataProvider
     */
    public function testShouldValidateUuid(string $value): void
    {
        $this->assertTrue(
            UserDataValidator::isValidUuid($value)
        );
    }

    public function shouldNotValidateUuidDataProvider(): array
    {
        return [
            ['____a83ef36c4ba29115f4213869d5fb'],
            ['45f50155c09f4fdcb5cee30af2e!!!!'],
            ['f498513🤬84773be26ecfc7ed5185d'],
        ];
    }

    /**
     * @dataProvider shouldNotValidateUuidDataProvider
     * @param mixed $value
     */
    public function testShouldNotValidateUuid(string $value): void
    {
        $this->assertFalse(
            UserDataValidator::isValidUuid($value)
        );
    }

    public function shouldValidateEmailDataProvider(): array
    {
        return [
            ['test@example.org'],
            ['user.test+lol@example.com'],
            ['abcd@lol.test.com'],
        ];
    }

    /**
     * @dataProvider shouldValidateEmailDataProvider
     */
    public function testShouldValidateEmail(string $email): void
    {
        $this->assertTrue(
            UserDataValidator::isValidEmail($email)
        );
    }

    public function shouldNotValidateEmailDataProvider(): array
    {
        return [
            ['@test@example.org'],
            ['user.test+lole_xample.com'],
            ['abcd@lol.pl@lol'],
        ];
    }

    /**
     * @dataProvider shouldNotValidateEmailDataProvider
     */
    public function testShouldNotValidateEmail(string $email): void
    {
        $this->assertFalse(
            UserDataValidator::isValidEmail($email)
        );
    }
}

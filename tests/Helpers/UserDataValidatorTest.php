<?php

declare(strict_types=1);

namespace MinepicTests\Helpers;

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
    public function testShouldValidateUsername($value): void
    {
        $this->assertTrue(
            \Minepic\Helpers\UserDataValidator::isValidUsername($value)
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
    public function testShouldNotValidateUsername($value): void
    {
        $this->assertFalse(
            \Minepic\Helpers\UserDataValidator::isValidUsername($value)
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
    public function testShouldValidateUuid($value): void
    {
        $this->assertTrue(
            \Minepic\Helpers\UserDataValidator::isValidUuid($value)
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
     */
    public function testShouldNotValidateUuid($value): void
    {
        $this->assertFalse(
            \Minepic\Helpers\UserDataValidator::isValidUuid($value)
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
    public function testShouldValidateEmail($value): void
    {
        $this->assertTrue(
            \Minepic\Helpers\UserDataValidator::isValidEmail($value)
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
    public function testShouldNotValidateEmail($value): void
    {
        $this->assertFalse(
            \Minepic\Helpers\UserDataValidator::isValidEmail($value)
        );
    }
}

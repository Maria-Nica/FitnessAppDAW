<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/TestHelper.php';
require_once __DIR__ . '/../app/Core/Escaper.php';

class SecurityTest extends TestCase {

    public function testSqlInjectionInCreateUserIsHandled() {
        $um = new UserModel();
        $email = "inject+'; DROP TABLE users; --" . time() . "@example.com";
        $name = 'SQLi Test';
        $password = 'Password123!';

        // Emailul este invalid si trebuie respins, nu inserat
        $this->expectException(ValidationException::class);
        $um->createUser($name, $email, $password);
    }

    public function testXssEscaping() {
        $raw = '<script>alert("xss")</script>';
        $escaped = Escaper::escape($raw);
        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }
}

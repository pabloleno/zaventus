<?php

namespace App\Tests\Libraries;

use App\Libraries\UploadSecurityPolicy;
use CodeIgniter\Test\CIUnitTestCase;

final class UploadSecurityPolicyTest extends CIUnitTestCase
{
    public function testAllowsValidProductImageMetadata(): void
    {
        $policy = new UploadSecurityPolicy();

        $this->assertSame([], $policy->validateProductImageMetadata('png', 'image/png', 1024, true));
    }

    public function testRejectsProductImageWithExecutableExtension(): void
    {
        $policy = new UploadSecurityPolicy();

        $errors = $policy->validateProductImageMetadata('php', 'text/x-php', 1024, false);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Formato invalido', implode(' ', $errors));
        $this->assertStringContainsString('Tipo de arquivo invalido', implode(' ', $errors));
    }

    public function testRejectsProductImageAboveLimit(): void
    {
        $policy = new UploadSecurityPolicy();

        $errors = $policy->validateProductImageMetadata(
            'jpg',
            'image/jpeg',
            UploadSecurityPolicy::MAX_PRODUCT_IMAGE_BYTES + 1,
            true
        );

        $this->assertStringContainsString('maximo 2 MB', implode(' ', $errors));
    }

    public function testAllowsCertificateMetadata(): void
    {
        $policy = new UploadSecurityPolicy();

        $this->assertSame([], $policy->validateCertificateMetadata('pfx', 'application/x-pkcs12', 2048));
    }

    public function testRejectsCertificateWithWrongExtension(): void
    {
        $policy = new UploadSecurityPolicy();

        $errors = $policy->validateCertificateMetadata('txt', 'text/plain', 2048);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Formato invalido', implode(' ', $errors));
    }
}

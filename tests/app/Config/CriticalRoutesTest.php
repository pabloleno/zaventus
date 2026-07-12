<?php

namespace App\Tests\Config;

use CodeIgniter\Config\Services;
use CodeIgniter\Test\CIUnitTestCase;

final class CriticalRoutesTest extends CIUnitTestCase
{
    public function testLoginAuthenticationHasExplicitPostRoute(): void
    {
        $this->assertArrayHasKey('login/autenticar', $this->routesFor('POST'));
        $this->assertArrayNotHasKey('login/autenticar', $this->routesFor('GET'));
    }

    public function testUploadAndFiscalActionsHaveExplicitPostRoutes(): void
    {
        $postRoutes = $this->routesFor('POST');

        $this->assertArrayHasKey('produtos/store', $postRoutes);
        $this->assertArrayHasKey('configs/store_nfe', $postRoutes);
        $this->assertArrayHasKey('configs/store_nfce', $postRoutes);
        $this->assertArrayHasKey('controleFiscal/cancelar', $postRoutes);
        $this->assertArrayHasKey('NFe/cancelar', $postRoutes);
    }

    public function testFinancialMutationsHaveExplicitPostRoutes(): void
    {
        $postRoutes = $this->routesFor('POST');

        $this->assertArrayHasKey('contasPagar/store', $postRoutes);
        $this->assertArrayHasKey('contasPagar/delete/([0-9]+)', $postRoutes);
        $this->assertArrayHasKey('contasReceber/store', $postRoutes);
        $this->assertArrayHasKey('contasReceber/delete/([0-9]+)', $postRoutes);
        $this->assertArrayHasKey('cobrancas/concluir/([0-9]+)', $postRoutes);
    }

    /**
     * @return array<string, string>
     */
    private function routesFor(string $verb): array
    {
        return Services::routes()->getRoutes($verb);
    }
}

<?php

namespace App\Tests\Config;

use CodeIgniter\Config\Services;
use CodeIgniter\Test\CIUnitTestCase;

final class CriticalRoutesTest extends CIUnitTestCase
{
    public function testAutomaticRoutingIsDisabled(): void
    {
        $this->assertFalse(Services::routes()->shouldAutoRoute());
    }

    public function testLoginAuthenticationHasExplicitPostRoute(): void
    {
        $this->assertArrayHasKey('login/autenticar', $this->routesFor('POST'));
        $this->assertArrayNotHasKey('login/autenticar', $this->routesFor('GET'));
        $this->assertArrayHasKey('login/logout', $this->routesFor('POST'));
        $this->assertArrayNotHasKey('login/logout', $this->routesFor('GET'));
    }

    public function testMutableLegacyActionsArePostOnly(): void
    {
        $postRoutes = $this->routesFor('POST');
        $getRoutes  = $this->routesFor('GET');

        $this->assertArrayHasKey('produtos/store', $postRoutes);
        $this->assertArrayHasKey('caixas/reabrir/([0-9]+)', $postRoutes);
        $this->assertArrayHasKey('inventarioDoEstoque/create_1', $postRoutes);
        $this->assertArrayHasKey('configs/backupDataBase', $postRoutes);
        $this->assertArrayNotHasKey('caixas/reabrir/([0-9]+)', $getRoutes);
        $this->assertArrayNotHasKey('inventarioDoEstoque/create_1', $getRoutes);
        $this->assertArrayNotHasKey('configs/backupDataBase', $getRoutes);
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

    public function testAtendimentoUsesAuditedActionsAndReadOnlyGetRoutes(): void
    {
        $post = $this->routesFor('POST');
        $get = $this->routesFor('GET');
        foreach (['salvarAtendimento', 'statusAtendimento', 'excluirAtendimento', 'restaurarAtendimento', 'apagarAtendimento', 'receberAtendimento', 'estornarRecebimentoAtendimento', 'consumirAtendimento', 'estornarConsumoAtendimento', 'anexarAtendimento'] as $acao) {
            $this->assertArrayHasKey('ordensDeServicos/' . $acao, $post);
            $this->assertArrayNotHasKey('ordensDeServicos/' . $acao, $get);
        }
        $this->assertStringContainsString('atendimentoNovo', $get['ordensDeServicos/create']);
        $this->assertStringContainsString('atendimentoFormularioAtual', $post['ordensDeServicos/finalizaOrdemDeServico']);
        $this->assertStringContainsString('atendimentoFormularioAtual', $post['ordensDeServicos/addServicoMaoDeObraEdit']);
    }

    public function testFiscalAndProductSaleRoutesAreNotRegistered(): void
    {
        foreach (['GET', 'POST'] as $verb) {
            $routes = $this->routesFor($verb);

            $this->assertArrayNotHasKey('configs/nfe', $routes);
            $this->assertArrayNotHasKey('configs/nfce', $routes);
            $this->assertArrayNotHasKey('controleFiscal', $routes);
            $this->assertArrayNotHasKey('NFe/cancelar', $routes);
            $this->assertArrayNotHasKey('pdv', $routes);
            $this->assertArrayNotHasKey('vendaRapida', $routes);
            $this->assertArrayNotHasKey('pedidos', $routes);
            $this->assertArrayNotHasKey('orcamentos', $routes);
            $this->assertArrayNotHasKey('produtos/add_por_xml', $routes);
            $this->assertArrayNotHasKey('ordensDeServicos/delete/([0-9]+)', $routes);
        }
    }

    /**
     * @return array<string, string>
     */
    private function routesFor(string $verb): array
    {
        return Services::routes()->getRoutes($verb);
    }
}

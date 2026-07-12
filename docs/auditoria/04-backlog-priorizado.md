# Auditoria 04 - Backlog Priorizado

Data da auditoria: 2026-07-12
Modo aplicado: MODO 1 - AUDITORIA

## Correcao imediata

### FIN-001 - Conversao de orcamento atomica e idempotente

- Modulo: Orcamentos / Vendas
- Descricao: transformar `Orcamentos::finalizarVenda` em operacao transacional com lock/status/idempotencia.
- Evidencia: `app/Controllers/Orcamentos.php:181-194`.
- Causa: regra em controller, sem service transacional.
- Impacto: venda duplicada ou parcial.
- Risco: P0.
- Severidade: P0.
- Probabilidade: Alta.
- Recomendacao: criar `OrcamentoVendaService`, adicionar referencia da venda ao orcamento ou tabela de vinculo, bloquear reconversao.
- Esforco estimado: M.
- Dependencias: decisao de status de orcamento e campo/vinculo de origem.
- Criterios de aceite: orcamento convertido uma unica vez; falha no meio faz rollback; venda referencia origem.
- Testes necessarios: sucesso, falha no item, clique duplo, orcamento ja convertido.
- Rollback: reverter service/rotas e manter backup antes de migration.

### FIN-002 - Finalizacao de pedido com rastreabilidade financeira

- Modulo: Pedidos / Vendas
- Descricao: impedir pedido marcado como pago sem movimento financeiro ou estado financeiro coerente.
- Evidencia: `app/Controllers/Pedidos.php:177-190`.
- Causa: fluxo legado simplificado.
- Impacto: venda paga sem baixa.
- Risco: P0.
- Severidade: P0.
- Probabilidade: Alta.
- Recomendacao: usar service transacional, status controlado e integracao com financeiro.
- Esforco estimado: M/G.
- Dependencias: modelo de recebimento/ledger.
- Criterios de aceite: pedido finalizado gera venda e financeiro coerentes.
- Testes necessarios: a vista, parcelado, falha no item, repeticao.
- Rollback: manter caminho antigo apenas desativado por feature flag.

### FIN-003 - Bloquear exclusao fisica de pagamentos/contas financeiras

- Modulo: Financeiro
- Descricao: substituir exclusao por cancelamento/estorno rastreavel onde houver impacto financeiro.
- Evidencia: `PagamentosDoCliente.php:108-110`, `ContasReceber.php:100-102`, `ContasPagar.php` CRUD equivalente.
- Causa: CRUD administrativo aplicado a dados financeiros.
- Impacto: perda de historico.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Alta.
- Recomendacao: criar estados `CANCELADA`/`ESTORNADA`; preservar registros.
- Esforco estimado: M.
- Dependencias: regra de negocio para o que pode ser cancelado.
- Criterios de aceite: registros quitados nao sao apagados; estorno gera trilha.
- Testes necessarios: exclusao de quitado, estorno, relatorio.
- Rollback: reabilitar delete apenas para registros nao financeiros.

### SEC-001 - Reduzir superficie do auto-routing

- Modulo: Rotas
- Descricao: migrar endpoints sensiveis para rotas explicitas por metodo HTTP.
- Evidencia: `Routes.php:23`; 324 rotas automaticas identificadas.
- Causa: auto-route legado.
- Impacto: endpoints publicos acidentais.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Alta.
- Recomendacao: iniciar por financeiro/fiscal/vendas; depois desativar auto-route.
- Esforco estimado: G.
- Dependencias: mapa completo de rotas usadas.
- Criterios de aceite: rotas sensiveis so aceitam metodo esperado; GET mutavel retorna 405.
- Testes necessarios: `filter:check` e testes HTTP.
- Rollback: manter auto-route enquanto grupos explicitos sao homologados.

### SEC-002 - Autorizacao por objeto

- Modulo: Autorizacao
- Descricao: validar propriedade/escopo de venda, cliente, caixa, conta e fiscal.
- Evidencia: `AuthGuard.php:82-87`, `Vendas.php:163`.
- Causa: RBAC por modulo sem escopo de dados.
- Impacto: IDOR.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Alta.
- Recomendacao: criar policies por recurso e filtros no model/query.
- Esforco estimado: G.
- Dependencias: definir perfis e escopo por empresa/filial/vendedor.
- Criterios de aceite: usuario sem propriedade recebe 403.
- Testes necessarios: acesso cruzado entre usuarios.
- Rollback: permitir excecao para admin.

## Curto prazo

### TST-001 - Suite minima de testes do produto

- Modulo: Testes
- Descricao: instalar dependencias de dev e criar testes de negocio para fluxos criticos.
- Evidencia: sem `vendor/`, sem `composer.lock`, sem `vendor/bin/phpunit`.
- Causa: ambiente sem dependencias e sem testes de app.
- Impacto: regressao invisivel.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Alta.
- Recomendacao: gerar `composer.lock`, fixtures e testes para venda/orcamento/caixa/auth.
- Esforco estimado: M.
- Dependencias: decidir banco de teste.
- Criterios de aceite: suite roda localmente e no CI.
- Testes necessarios: a propria suite.
- Rollback: remover somente testes instaveis, nao codigo de producao.

### SEC-003 - Rate limiting de login

- Modulo: Autenticacao
- Descricao: limitar tentativas por usuario/IP e registrar falhas.
- Evidencia: `Login.php:231-280`.
- Causa: autenticacao custom simples.
- Impacto: brute force.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Media.
- Recomendacao: throttle com TTL e auditoria.
- Esforco estimado: P/M.
- Dependencias: storage de tentativas.
- Criterios de aceite: limite bloqueia temporariamente e nao registra senha.
- Testes necessarios: tentativas sucessivas e login valido apos expirar.
- Rollback: flag para desativar throttle.

### SEC-004 - Hardening de uploads

- Modulo: Produtos / Configuracoes
- Descricao: validar tipo/tamanho de imagens e certificados.
- Evidencia: `Produtos.php:238-255`, `Configs.php:94-100`, `Configs.php:155-161`.
- Causa: validacao incompleta.
- Impacto: upload indevido.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Media.
- Recomendacao: whitelist, limite, reprocessamento de imagem e armazenamento fora de `public` quando aplicavel.
- Esforco estimado: P.
- Dependencias: tipos permitidos.
- Criterios de aceite: arquivos invalidos sao recusados.
- Testes necessarios: MIME invalido, tamanho excessivo, upload valido.
- Rollback: restaurar validacao anterior temporariamente.

### FIN-006 - Corrigir saldo/somatorio do caixa

- Modulo: Caixa
- Descricao: considerar retiradas e natureza dos lancamentos no saldo.
- Evidencia: `Caixas.php:158-160`.
- Causa: calculo local no controller.
- Impacto: fechamento incorreto.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Media.
- Recomendacao: criar calculadora de saldo ou ledger.
- Esforco estimado: P/M.
- Dependencias: regra de tipos de lancamento.
- Criterios de aceite: saldo = entradas - saidas em casos testados.
- Testes necessarios: caixa com venda, lancamento entrada, retirada, despesa.
- Rollback: manter relatorio antigo comparativo.

## Medio prazo

### FIN-007 - Ledger financeiro

- Modulo: Financeiro
- Descricao: criar fonte unica de movimentos financeiros com estorno compensatorio.
- Evidencia: inexistencia de tabela geral de movimentos; pagamentos e contas sao CRUDs separados.
- Causa: ausencia de modelo financeiro central.
- Impacto: fluxo de caixa nao reconciliavel.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Alta.
- Recomendacao: tabela append-only com origem, conta, natureza, valor, status, usuario e idempotencia.
- Esforco estimado: G.
- Dependencias: validacao financeira/contabil.
- Criterios de aceite: saldo derivavel dos movimentos.
- Testes necessarios: recebimento, pagamento, estorno, transferencia, conciliacao.
- Rollback: migration reversivel e modo leitura paralelo.

### DB-001 - Status e constraints

- Modulo: Banco
- Descricao: controlar estados de orcamento, venda, conta, caixa e fiscal.
- Evidencia: status livres em migrations e models.
- Causa: schema legado.
- Impacto: dados invalidos e relatorios divergentes.
- Risco: P1/P2.
- Severidade: P1.
- Probabilidade: Media.
- Recomendacao: enums de dominio, validação backend e constraints possiveis.
- Esforco estimado: M/G.
- Dependencias: saneamento de dados existentes.
- Criterios de aceite: status invalido e recusado.
- Testes necessarios: transicoes permitidas/proibidas.
- Rollback: manter coluna antiga ou tabela de mapeamento.

### OBS-001 - Auditoria operacional

- Modulo: Observabilidade
- Descricao: registrar eventos de login, venda, caixa, pagamento, permissao e fiscal.
- Evidencia: apenas eventos fiscais identificados.
- Causa: requisito ainda ausente.
- Impacto: pouca rastreabilidade.
- Risco: P1.
- Severidade: P1.
- Probabilidade: Alta.
- Recomendacao: audit log append-only com antes/depois mascarado.
- Esforco estimado: M/G.
- Dependencias: politica de retencao/LGPD.
- Criterios de aceite: operacoes sensiveis geram evento.
- Testes necessarios: eventos sem senha/token/dados sensiveis desnecessarios.
- Rollback: flag para desabilitar listener.

## Evolucao arquitetural

### ARQ-001 - Services de dominio

- Modulo: Arquitetura
- Descricao: extrair services para conversao, venda, caixa, recebimento, estoque e fiscal.
- Evidencia: `app/Services` vazio; controllers grandes.
- Causa: crescimento incremental.
- Impacto: baixa testabilidade.
- Risco: P2.
- Severidade: P2.
- Probabilidade: Alta.
- Recomendacao: extrair por fluxo, sem refactor amplo de uma vez.
- Esforco estimado: G.
- Dependencias: testes de regressao.
- Criterios de aceite: service com teste unitario e controller fino.
- Testes necessarios: unitarios de regras financeiras.
- Rollback: controller volta a chamar implementacao antiga.

### OPS-001 - Pipeline de produto

- Modulo: Operacao
- Descricao: substituir workflow herdado por pipeline do produto: install, lint, tests, build/deploy.
- Evidencia: `.github/workflows/deploy.yml` referencia repos `codeigniter4/framework` e `codeigniter4/appstarter`.
- Causa: scaffold herdado.
- Impacto: deploy nao confiavel.
- Risco: P2.
- Severidade: P2.
- Probabilidade: Media.
- Recomendacao: criar workflow proprio com secrets revisados.
- Esforco estimado: M.
- Dependencias: ambiente alvo.
- Criterios de aceite: CI roda testes e deploy do produto.
- Testes necessarios: pipeline em branch.
- Rollback: desativar workflow novo.

## Plano de implementacao sugerido

Ciclo 1: seguranca e testes de base

- Objetivo: impedir regressao e reduzir superficie critica.
- Itens: TST-001, SEC-003, SEC-004, primeiras rotas explicitas de financeiro/fiscal.
- Riscos: ajustes podem expor dependencias faltantes.
- Testes: PHPUnit HTTP/auth/upload.
- Rollback: feature flags e commits atomicos.
- Criterio de aceite: testes rodam local e CI.

Ciclo 2: conversoes comerciais

- Objetivo: corrigir duplicidade/inconsistencia em orcamento/pedido.
- Itens: FIN-001, FIN-002.
- Dependencias: status e referencia de origem.
- Riscos: impacto direto em receita.
- Testes: conversao, concorrencia, rollback.
- Rollback: preservar fluxo antigo temporariamente.
- Criterio de aceite: conversao atomica e idempotente.

Ciclo 3: financeiro operacional

- Objetivo: contas, pagamentos, caixa e saldo confiaveis.
- Itens: FIN-003, FIN-004, FIN-006, FIN-007.
- Dependencias: validacao financeira do negocio.
- Riscos: migracao de dados e relatorios.
- Testes: baixa, parcial, estorno, caixa.
- Rollback: ledger paralelo inicialmente.
- Criterio de aceite: relatorios reconciliam com movimentos.

Ciclo 4: auditoria e autorizacao por registro

- Objetivo: rastreabilidade e controle de acesso real.
- Itens: SEC-002, OBS-001, DB-001.
- Dependencias: matriz de perfis e escopos.
- Riscos: bloqueios indevidos para usuarios atuais.
- Testes: acesso cruzado, logs, status invalido.
- Rollback: politica permissiva temporaria para admin.
- Criterio de aceite: IDOR coberto por testes e eventos auditados.

## Recomendacao sobre producao/venda

Nao recomendar venda ampla para clientes finais ainda.

Recomendacao segura:

- Operacao piloto controlada somente com clientes internos/beta.
- Desabilitar ou restringir fluxos financeiros criticos que ainda nao possuem ledger/estorno.
- Executar ciclos de correcao P0/P1 antes de comercializacao.
- Validar regras financeiras com responsavel do negocio e contador.
- Validar LGPD com responsavel juridico/encarregado de dados.

## Limitacoes

- Esta auditoria nao executou banco real.
- Esta auditoria nao executou testes automatizados por ausencia de dependencias instaladas.
- Esta auditoria nao alterou regras de negocio.
- O arquivo local `writable/backup_mysql/BACKUP_DATABASE_SISTEMA.sql` ja estava modificado e nao foi utilizado para gravar dados.

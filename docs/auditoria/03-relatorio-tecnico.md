# Auditoria 03 - Relatorio Tecnico

Data da auditoria: 2026-07-12
Modo aplicado: MODO 1 - AUDITORIA
Conclusao resumida: o sistema possui uma base operacional ampla, mas ainda nao deve ser tratado como pronto para venda em clientes sem ciclos de correcao. Ha riscos P0/P1 em rastreabilidade financeira, conversao duplicada, exclusao de dados financeiros, autorizacao por objeto e ausencia de testes de negocio.

## Nota de qualidade

Nota geral estimada: 58/100.

| Dimensao | Peso | Nota | Evidencia resumida |
| --- | ---: | ---: | --- |
| Integridade financeira e de dados | 20 | 8 | Ausencia de ledger, baixa parcial/estorno, conversoes sem transacao em orcamento/pedido |
| Seguranca e controle de acesso | 20 | 12 | CSRF e AuthGuard existem; falta autorizacao por objeto e rate limit |
| Cobertura funcional | 15 | 9 | CRUDs amplos; fluxos financeiros avancados incompletos |
| Arquitetura e manutenibilidade | 15 | 8 | Controllers grandes e regra em controller |
| Testes e qualidade | 10 | 1 | Sem vendor/bin/phpunit e sem testes de negocio identificados |
| Banco e desempenho | 10 | 7 | Migrations existem; indices parciais; sem validacao de planos |
| Auditoria e observabilidade | 5 | 2 | Eventos fiscais existem; auditoria geral ausente |
| Operacao, backup e recuperacao | 5 | 3 | Backup existe, mas restore/deploy de produto nao evidenciado |

## Achados

### FIN-001

ID: FIN-001
Titulo: Conversao de orcamento em venda nao e transacional nem idempotente
Modulo: Orcamentos / Vendas
Severidade: P0
Probabilidade: Alta
Arquivo e linha: `app/Controllers/Orcamentos.php:181-194`
Evidencia: o metodo busca orcamento e itens, insere venda, insere itens e so depois atualiza status, sem `transBegin`, sem lock e sem verificar status anterior.
Comportamento atual: uma falha no meio pode deixar venda sem todos os itens ou orcamento sem status coerente; clique duplo pode gerar vendas duplicadas.
Comportamento esperado: conversao atomica, idempotente, com status validado e referencia da venda ao orcamento.
Causa provavel: regra de negocio implementada diretamente no controller sem service transacional.
Impacto tecnico: inconsistencias entre orcamentos, vendas e produtos.
Impacto financeiro: duplicidade de venda ou venda parcial.
Impacto de seguranca: usuario com permissao do modulo pode repetir acao por ID.
Recomendacao: criar service de conversao com transacao, lock, validacao de status e chave de idempotencia.
Arquivos afetados: `Orcamentos.php`, models de orcamento/venda/produtos, migrations futuras.
Banco afetado: `orcamentos`, `produtos_do_orcamento`, `vendas`, `produtos_da_venda`.
Teste necessario: conversao normal, falha simulada no item, clique duplo, tentativa de converter orcamento finalizado.
Risco da correcao: medio, pois toca fluxo de receita.
Rollback: manter metodo antigo atras de feature flag ou reverter service/rotas.

### FIN-002

ID: FIN-002
Titulo: Finalizacao de pedido cria venda paga sem baixa financeira rastreavel
Modulo: Pedidos / Vendas
Severidade: P0
Probabilidade: Alta
Arquivo e linha: `app/Controllers/Pedidos.php:177-190`
Evidencia: o metodo cria venda e define situacao `"Pago - Finalizado"` sem transacao, sem conta a receber, pagamento ou movimento de caixa.
Comportamento atual: pedido pode virar venda paga sem registro financeiro auditavel.
Comportamento esperado: venda deve gerar movimentacao/conta/pagamento conforme condicao definida.
Causa provavel: fluxo legado simplificado.
Impacto tecnico: relatorios podem divergir do financeiro.
Impacto financeiro: saldo e recebiveis podem ficar incorretos.
Impacto de seguranca: acao sensivel depende apenas da permissao do modulo.
Recomendacao: alinhar pedido -> venda ao mesmo motor financeiro do PDV/vendas, com transacao e ledger.
Arquivos afetados: `Pedidos.php`, models de pedido/venda/financeiro.
Banco afetado: `pedidos`, `produtos_do_pedido`, `vendas`, `produtos_da_venda`, futuras tabelas de movimentos.
Teste necessario: finalizar pedido a vista, parcelado, falha no item, repeticao.
Risco da correcao: alto.
Rollback: feature flag e restore de fluxo antigo se houver regressao.

### FIN-003

ID: FIN-003
Titulo: Contas a receber nao representam recebimentos, parcelas ou saldo
Modulo: Contas a receber
Severidade: P1
Probabilidade: Alta
Arquivo e linha: `app/Controllers/ContasReceber.php:86-102`, `app/Models/ContaReceberModel.php:9-18`
Evidencia: controller salva `getVar()` diretamente e o model contem apenas status, tipo, nome, vencimento, valor e observacoes.
Comportamento atual: conta e um cadastro manual simples.
Comportamento esperado: conta deve ter origem, cliente, venda, parcela, valor original, saldo, pagamentos, juros, multa, desconto, estorno e status controlado.
Causa provavel: modulo financeiro inicial/administrativo.
Impacto tecnico: impossibilidade de reconciliar vendas e recebimentos.
Impacto financeiro: inadimplencia, saldo e fluxo de caixa podem estar incorretos.
Impacto de seguranca: payload permitido pelo model ainda pode alterar status sem regra de negocio.
Recomendacao: criar modelo de parcela/recebimento ou ledger antes de usar como financeiro final.
Arquivos afetados: `ContasReceber.php`, `ContaReceberModel.php`, relatorios.
Banco afetado: `contas_a_receber`, futuras tabelas de recebimentos.
Teste necessario: recebimento total, parcial, multiplo, estorno, pagamento duplicado.
Risco da correcao: alto.
Rollback: migracao reversivel e importacao de dados preservada.

### FIN-004

ID: FIN-004
Titulo: Pagamentos de cliente sao registros avulsos, excluiveis e sem estorno
Modulo: Clientes / Pagamentos
Severidade: P1
Probabilidade: Alta
Arquivo e linha: `app/Controllers/PagamentosDoCliente.php:85-110`
Evidencia: `store()` salva o request e `delete()` remove por `id_pagamento`.
Comportamento atual: pagamento pode ser apagado.
Comportamento esperado: pagamento financeiro deve ser imutavel ou estornado por movimento compensatorio.
Causa provavel: historico simples de cliente.
Impacto tecnico: perda de trilha.
Impacto financeiro: saldo e comprovacao podem ser perdidos.
Impacto de seguranca: risco de remocao indevida por usuario com permissao.
Recomendacao: bloquear exclusao de pagamentos financeiros e criar estorno rastreavel.
Arquivos afetados: `PagamentosDoCliente.php`, `PagamentoDoClienteModel.php`.
Banco afetado: `pagamentos_do_cliente`, futura tabela de movimentos.
Teste necessario: registrar, tentar excluir quitado, estornar, auditar.
Risco da correcao: medio.
Rollback: restaurar exclusao apenas para registros de teste nao conciliados.

### FIN-005

ID: FIN-005
Titulo: Caixa pode ser fechado/reaberto sem auditoria, conciliacao ou transacao
Modulo: Caixas
Severidade: P1
Probabilidade: Media
Arquivo e linha: `app/Controllers/Caixas.php:218-245`
Evidencia: `fechar()` e `reabrir()` apenas salvam status/valor; nao exigem motivo, nao travam movimentacoes e nao geram auditoria.
Comportamento atual: caixa fechado pode ser reaberto silenciosamente.
Comportamento esperado: fechamento deve conciliar entradas/saidas, registrar responsavel e motivo para reabertura.
Causa provavel: fluxo operacional simplificado.
Impacto tecnico: divergencia operacional.
Impacto financeiro: saldo de caixa nao confiavel.
Impacto de seguranca: usuario autorizado pode alterar estado sem trilha.
Recomendacao: introduzir eventos de caixa e bloqueios para movimentacao apos fechamento.
Arquivos afetados: `Caixas.php`, relatorios de caixa.
Banco afetado: `caixas`, `lancamentos`, `retiradas`, `vendas`.
Teste necessario: fechamento com divergencia, reabertura com motivo, tentativa de inserir apos fechamento.
Risco da correcao: medio.
Rollback: manter compatibilidade de leitura dos caixas antigos.

### FIN-006

ID: FIN-006
Titulo: Relatorio/somatorio de caixa nao subtrai retiradas no total calculado
Modulo: Caixas / Relatorios financeiros
Severidade: P1
Probabilidade: Media
Arquivo e linha: `app/Controllers/Caixas.php:158-160`
Evidencia: o somatorio soma lancamentos e vendas; retiradas sao carregadas em `Caixas.php:155`, mas nao entram na conta.
Comportamento atual: total do caixa pode ficar maior que o saldo real.
Comportamento esperado: saldo deve derivar de entradas menos saidas.
Causa provavel: calculo duplicado em controller.
Impacto tecnico: indicador incorreto.
Impacto financeiro: fechamento pode ser conferido contra valor errado.
Impacto de seguranca: sem impacto direto.
Recomendacao: centralizar calculo de saldo em service/ledger e validar contra casos de retirada.
Arquivos afetados: `Caixas.php`, views de caixa, relatorios.
Banco afetado: `lancamentos`, `vendas`, `retiradas`.
Teste necessario: caixa com venda, lancamento e retirada.
Risco da correcao: medio, pois altera numero exibido.
Rollback: documentar formula antiga e nova; reverter service se necessario.

### SEC-001

ID: SEC-001
Titulo: Auto-routing expõe 324 rotas automaticas
Modulo: Rotas / Seguranca
Severidade: P1
Probabilidade: Alta
Arquivo e linha: `app/Config/Routes.php:23`
Evidencia: `$routes->setAutoRoute(true)`; `php spark routes` lista rotas automaticas para metodos publicos, inclusive construtores bloqueados posteriormente pelo filtro.
Comportamento atual: qualquer metodo publico novo pode virar endpoint se nao for lembrado no AuthGuard.
Comportamento esperado: rotas explicitas por metodo HTTP e controller.
Causa provavel: padrao legado CodeIgniter.
Impacto tecnico: superficie de ataque alta.
Impacto financeiro: endpoints sensiveis podem ser expostos por engano.
Impacto de seguranca: risco de bypass por metodo nao listado.
Recomendacao: migrar gradualmente para rotas explicitas e desativar auto-routing.
Arquivos afetados: `Routes.php`, todos controllers.
Banco afetado: nenhum direto.
Teste necessario: matriz de rotas GET/POST/403/405.
Risco da correcao: alto se feito de uma vez.
Rollback: manter grupo de rotas legado temporario.

### SEC-002

ID: SEC-002
Titulo: Autorizacao nao valida propriedade do registro
Modulo: Autorizacao
Severidade: P1
Probabilidade: Alta
Arquivo e linha: `app/Filters/AuthGuard.php:82-87`, `app/Controllers/Vendas.php:163`, `app/Controllers/Clientes.php:160`
Evidencia: AuthGuard valida permissao por modulo/funcionalidade; controllers buscam registros por ID puro.
Comportamento atual: usuario com acesso ao modulo pode tentar IDs de outros registros.
Comportamento esperado: validar empresa/filial/usuario/proprietario do objeto.
Causa provavel: RBAC sem escopo de dados.
Impacto tecnico: IDOR.
Impacto financeiro: acesso indevido a vendas/clientes/caixa.
Impacto de seguranca: exposicao de dados pessoais e comerciais.
Recomendacao: criar policy por recurso e aplicar em show/edit/delete/store sensiveis.
Arquivos afetados: AuthGuard, controllers e models.
Banco afetado: possivel inclusao de empresa/filial/responsavel.
Teste necessario: usuario A acessa venda/cliente de usuario B e recebe 403.
Risco da correcao: medio/alto, depende de regra de negocio.
Rollback: liberar escopo apenas para perfis administradores em feature flag.

### SEC-003

ID: SEC-003
Titulo: Login nao possui rate limiting ou bloqueio de tentativa
Modulo: Autenticacao
Severidade: P1
Probabilidade: Media
Arquivo e linha: `app/Controllers/Login.php:231-280`
Evidencia: autenticacao busca usuario e valida senha; nao ha contador de falhas, cooldown ou captcha.
Comportamento atual: tentativas repetidas dependem apenas do servidor.
Comportamento esperado: limitar tentativas por IP/usuario e auditar falhas.
Causa provavel: autenticacao custom simples.
Impacto tecnico: brute force.
Impacto financeiro: comprometimento de contas.
Impacto de seguranca: acesso indevido.
Recomendacao: implementar throttle e log de falhas sem registrar senha.
Arquivos afetados: `Login.php`, possivel tabela de auditoria.
Banco afetado: futura tabela de login_attempts/auditoria.
Teste necessario: exceder limite e receber bloqueio temporario.
Risco da correcao: baixo/medio.
Rollback: desabilitar throttle por configuracao.

### SEC-004

ID: SEC-004
Titulo: Upload de imagem de produto nao valida tipo/tamanho explicitamente
Modulo: Produtos
Severidade: P1
Probabilidade: Media
Arquivo e linha: `app/Controllers/Produtos.php:238-255`
Evidencia: arquivo recebido e movido para `public/assets/img/produtos` com `getRandomName()`, sem validacao explicita de MIME/extensao/tamanho no trecho.
Comportamento atual: upload depende das validacoes internas/minimas do framework/servidor.
Comportamento esperado: whitelist de extensoes/MIME, tamanho maximo e reprocessamento seguro de imagem.
Causa provavel: fluxo legado de upload.
Impacto tecnico: arquivos indevidos em pasta publica.
Impacto financeiro: indisponibilidade/comprometimento.
Impacto de seguranca: upload inseguro.
Recomendacao: reutilizar padrao de `ImagemCadastro` ou validar com regras do CI4.
Arquivos afetados: `Produtos.php`, views de produto.
Banco afetado: `produtos.arquivo`.
Teste necessario: rejeitar PHP disfarçado, arquivo grande e MIME invalido.
Risco da correcao: baixo.
Rollback: voltar ao upload antigo se houver falsa rejeicao.

### SEC-005

ID: SEC-005
Titulo: Segredo local em `.env` deve ser controlado fora do repositorio
Modulo: Configuracao
Severidade: P2
Probabilidade: Media
Arquivo e linha: `.env:97`
Evidencia: arquivo local possui `encryption.key`; `.env` esta ignorado por Git, mas existe no workspace.
Comportamento atual: segredo local presente em arquivo de desenvolvimento.
Comportamento esperado: segredo nunca deve ser commitado; deve ser rotacionado se houve exposicao.
Causa provavel: configuracao local necessaria.
Impacto tecnico: baixo se nunca sair do ambiente.
Impacto financeiro: potencial comprometimento de credenciais criptografadas.
Impacto de seguranca: exposicao de chave se arquivo for compartilhado.
Recomendacao: manter `.env` ignorado, revisar historico Git e rotacionar chave em ambientes reais.
Arquivos afetados: `.env`, `.gitignore`.
Banco afetado: campos criptografados por chave de app.
Teste necessario: `git ls-files .env` vazio; escaneamento de secrets no CI.
Risco da correcao: alto se rotacionar sem plano para dados criptografados.
Rollback: restaurar backup seguro da chave apenas em ambiente autorizado.

### ARQ-001

ID: ARQ-001
Titulo: Regras de negocio criticas estao em controllers grandes
Modulo: Arquitetura
Severidade: P2
Probabilidade: Alta
Arquivo e linha: `app/Controllers/Pdv.php:635-690`, `app/Controllers/Orcamentos.php:181-194`, `app/Controllers/OrdensDeServicos.php`
Evidencia: controllers fazem calculos, persistencia, fluxo fiscal e transacoes; `app/Services` esta vazio.
Comportamento atual: regras ficam acopladas a HTTP.
Comportamento esperado: services de dominio testaveis.
Causa provavel: evolucao incremental.
Impacto tecnico: baixa testabilidade e maior risco em alteracoes.
Impacto financeiro: bugs de calculo podem se repetir em views/controllers/relatorios.
Impacto de seguranca: regras podem ser contornadas por endpoint alternativo.
Recomendacao: extrair services pequenos por fluxo: conversao, venda, caixa, recebimento.
Arquivos afetados: controllers e futuras services.
Banco afetado: indireto.
Teste necessario: unitarios de services sem HTTP.
Risco da correcao: medio.
Rollback: manter controller chamando service com contrato simples.

### TST-001

ID: TST-001
Titulo: Nao ha suite local executavel para regras do produto
Modulo: Testes
Severidade: P1
Probabilidade: Alta
Arquivo e linha: `composer.json`, workspace sem `vendor/bin/phpunit`
Evidencia: scripts existem, mas `vendor/`, `composer.lock` e binarios de teste nao existem; testes localizados sao do framework.
Comportamento atual: regressao financeira depende de teste manual.
Comportamento esperado: testes automatizados para orcamento, venda, caixa, recebimento, permissao e CSRF.
Causa provavel: projeto baseado em framework sem suite de app.
Impacto tecnico: correcoes podem quebrar fluxos.
Impacto financeiro: bugs em saldo/conversao podem passar despercebidos.
Impacto de seguranca: falhas de permissao podem voltar.
Recomendacao: criar suite minima antes de grandes refactors.
Arquivos afetados: `tests/`, `composer.lock`, configs de teste.
Banco afetado: fixtures SQLite/MySQL de teste.
Teste necessario: a propria suite inicial.
Risco da correcao: baixo.
Rollback: remover testes novos se bloquearem indevidamente, sem alterar codigo de producao.

### DB-001

ID: DB-001
Titulo: Constraints financeiras e de status sao insuficientes
Modulo: Banco
Severidade: P1
Probabilidade: Media
Arquivo e linha: `app/Database/Migrations/2020-03-05-162749_contas_a_receber.php:20-35`, `2020-03-12-143912_orcamentos.php:20-38`
Evidencia: status em `VARCHAR`, valores antigos como `DOUBLE`; migration posterior converte valores, mas nao cria constraints de status nem check constraints.
Comportamento atual: status e transicoes dependem da aplicacao.
Comportamento esperado: status controlado por dominio e constraints/indices minimos.
Causa provavel: schema legado.
Impacto tecnico: dados invalidos podem ser persistidos.
Impacto financeiro: relatorios podem ignorar status inesperados.
Impacto de seguranca: baixo.
Recomendacao: definir enums de aplicacao, constraints possiveis e migrations reversiveis.
Arquivos afetados: migrations, models, forms.
Banco afetado: tabelas financeiras e transacionais.
Teste necessario: tentar persistir status invalido.
Risco da correcao: medio/alto se houver dados legados.
Rollback: migration reversivel com mapeamento de status.

### OBS-001

ID: OBS-001
Titulo: Auditoria operacional geral ausente
Modulo: Auditoria / Observabilidade
Severidade: P1
Probabilidade: Alta
Arquivo e linha: nao foi encontrada tabela geral de auditoria; existe apenas `documentos_fiscais_eventos` para fiscal.
Evidencia: migrations criam eventos fiscais, mas nao eventos para login, permissao, venda, pagamento, estorno, caixa.
Comportamento atual: a maioria das alteracoes nao registra antes/depois.
Comportamento esperado: trilha de auditoria para operacoes sensiveis.
Causa provavel: requisito ainda nao implementado.
Impacto tecnico: dificuldade de diagnostico.
Impacto financeiro: impossibilidade de explicar divergencias.
Impacto de seguranca: investigacao limitada.
Recomendacao: criar audit log append-only, com mascaramento de dados sensiveis.
Arquivos afetados: controllers/services sensiveis.
Banco afetado: futura tabela de auditoria.
Teste necessario: operacao gera evento sem senha/token.
Risco da correcao: medio.
Rollback: desabilitar listener por configuracao.

## Pontos positivos

- CSRF global para metodos mutaveis em `Filters.php:102-107`.
- HTTPS/cookie seguro condicionado a producao em `App.php:172` e `Cookie.php`.
- Senhas novas com bcrypt e rehash de legado em `Login.php:260-263`.
- Sessao regenerada no login em `Login.php:243`.
- PDV principal usa transacao para venda/estoque em `Pdv.php:635-690`.
- Cobrancas independentes usam transacao para salvar e sincronizar alertas em `Cobrancas.php:118-125`.
- Fiscal consolidado com consulta/cancelamento e eventos fiscais recentes.

## Limitacoes

- Sem banco conectado, nao foram executadas consultas de consistencia real.
- Sem `vendor/`, nao foram executados PHPUnit/PHPStan/Rector.
- Sem credenciais fiscais, nao houve teste SEFAZ.
- Sem regra de negocio validada com o cliente, a matriz de acesso e os estados sugeridos permanecem recomendacoes.

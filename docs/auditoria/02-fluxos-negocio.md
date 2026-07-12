# Auditoria 02 - Fluxos de Negocio

Data da auditoria: 2026-07-12
Modo aplicado: MODO 1 - AUDITORIA

## Fluxo cliente -> orcamento -> venda

Fluxo observado:

```text
Cliente
  -> Orcamento
  -> Produtos do orcamento
  -> Finalizar venda
  -> Venda
  -> Produtos da venda
  -> Status do orcamento = "Finalizado"
```

Evidencia:

- `Orcamentos.php:183-184` busca orcamento e itens por ID.
- `Orcamentos.php:186` insere os dados do orcamento diretamente em `vendas`.
- `Orcamentos.php:188-192` copia itens para `produtos_da_venda`.
- `Orcamentos.php:194` marca o orcamento como `"Finalizado"`.

Riscos observados:

- Nao ha transacao no metodo.
- Nao ha trava ou idempotencia contra conversao duplicada.
- Nao ha campo explicito `id_orcamento` em `vendas` para rastrear origem.
- Status possiveis sao livres, sem maquina de estados formal.
- Nao ha validacao de validade do orcamento.
- Nao ha aprovacao/rejeicao/expiracao formal.

## Fluxo pedido -> venda

Fluxo observado:

```text
Pedido
  -> Produtos do pedido
  -> Finalizar pedido
  -> Venda
  -> Produtos da venda
  -> Situacao do pedido = "Pago - Finalizado"
```

Evidencia:

- `Pedidos.php:179-180` busca pedido e produtos por ID.
- `Pedidos.php:182` cria venda.
- `Pedidos.php:184-188` copia itens.
- `Pedidos.php:190` marca situacao como `"Pago - Finalizado"`.

Riscos observados:

- Nao ha transacao.
- Nao ha idempotencia.
- Situacao usa texto livre.
- Pedido finalizado como pago sem evidencia de baixa financeira.
- Nao ha referencia formal da venda ao pedido original.

## Fluxo PDV -> venda -> estoque

Fluxo observado:

```text
Caixa aberto
  -> Produtos do PDV
  -> Finalizar venda
  -> Venda
  -> Produtos da venda
  -> Baixa estoque
  -> Limpa carrinho PDV
  -> Opcional: emite NFCe
```

Evidencia:

- `Pdv.php:635` inicia transacao.
- `Pdv.php:638` insere venda.
- `Pdv.php:643-682` valida estoque, cria itens e atualiza quantidade.
- `Pdv.php:684` remove produtos do PDV.
- `Pdv.php:690` confirma transacao.
- `Pdv.php:705` faz rollback em excecao.

Pontos positivos:

- Usa transacao.
- Valida estoque antes de baixar.
- Remove carrinho somente dentro do fluxo transacional.

Riscos remanescentes:

- Ainda usa valores recebidos do cliente/JS para `valor_a_pagar`, desconto, recebimento e troco.
- Nao foi evidenciada chave de idempotencia contra clique duplo/retry.
- Nao foi evidenciada criacao de parcela ou conta a receber a partir da venda.
- Baixa de estoque nao parece usar lock pessimista/otimista.

## Fluxo venda -> fiscal

Fluxo observado:

```text
Venda
  -> Emitir NFe/NFCe
  -> Registra NFe/NFCe local
  -> Consulta SEFAZ
  -> Cancelamento via Controle Fiscal ou tela da venda
  -> Registra eventos fiscais
```

Evidencia:

- `ControleFiscal.php:54-65` consulta status do servico.
- `ControleFiscal.php:71-82` consulta documento na SEFAZ.
- `ControleFiscal.php:88-103` cancela documento.
- `NFe.php:787-803` mantem cancelamento legado apontando para `SefazFiscalService`.
- Migration `2026-07-12-000001_gestao_fiscal_documentos.php` cria eventos fiscais.

Riscos observados:

- Cancelamento fiscal nao evidencia estorno financeiro ou cancelamento da venda.
- Operacoes fiscais dependem de status local e retorno da SEFAZ, mas a auditoria nao validou comunicacao real.
- Nao ha evidencia de fila/retry/idempotencia para emissao fiscal.

## Fluxo contas a receber

Fluxo observado:

```text
Conta a receber manual
  -> Cadastro/edicao
  -> Relatorios por periodo/status
  -> Exclusao
```

Evidencia:

- `ContasReceber.php:86-94` salva dados diretamente do request.
- `ContasReceber.php:100-105` exclui conta.
- `Relatorios.php:749-793` consulta contas a receber por vencimento/status/tipo.

Riscos observados:

- Nao ha vinculo obrigatorio com venda, cliente, parcela, recebimento ou ledger.
- Nao ha fluxo de baixa parcial/total.
- Nao ha saldo, juros, multa, desconto, estorno ou pagamento em excesso.
- Exclusao fisica pode apagar historico financeiro.

## Fluxo pagamentos do cliente

Fluxo observado:

```text
Cliente
  -> Novo pagamento
  -> Registro em pagamentos_do_cliente
  -> Exibicao no historico do cliente
```

Evidencia:

- `PagamentosDoCliente.php:85-102` salva pagamento diretamente.
- `PagamentosDoCliente.php:108-115` exclui pagamento.

Riscos observados:

- Nao ha associacao obrigatoria com conta a receber ou parcela.
- Nao ha transacao com caixa, venda ou saldo.
- Nao ha estorno: exclusao remove o registro.

## Fluxo contas a pagar

Fluxo observado:

```text
Conta a pagar manual
  -> Cadastro/edicao
  -> Relatorios por vencimento/status
  -> Exclusao
```

Evidencia:

- `ContasPagar.php` possui CRUD simples equivalente ao de contas a receber.
- `Relatorios.php:690-734` consulta contas a pagar.

Riscos observados:

- Nao ha aprovacao, baixa, conta financeira, recorrencia, anexos ou estorno formal.
- Exclusao fisica pode apagar obrigacoes historicas.

## Fluxo caixa

Fluxo observado:

```text
Abrir caixa
  -> Lancamentos / vendas / retiradas
  -> Fechar caixa
  -> Reabrir caixa
```

Evidencia:

- `Caixas.php:152-160` monta caixa com lancamentos, vendas e retiradas.
- `Caixas.php:218-229` fecha caixa.
- `Caixas.php:240-245` reabre caixa.
- `Caixas.php:281-283` exclui caixa.

Riscos observados:

- Fechamento nao usa transacao.
- Reabertura nao exige motivo.
- Nao ha conciliacao ou ledger.
- Excluir caixa pode afetar historico financeiro.
- Soma do caixa considera lancamentos e vendas; retiradas aparecem em tela, mas nao sao subtraidas no somatorio de `Caixas.php:158-160`.

## Fluxo cobrancas independentes

Fluxo observado:

```text
Cobranca independente
  -> Gera ocorrencias/parcelas de alerta
  -> Alerta na navbar/dashboard
  -> Marcar ocorrencia como realizada
  -> Concluir cobranca quando todas realizadas
```

Evidencia:

- `Cobrancas.php:107-125` salva cobranca e sincroniza recorrencia em transacao.
- `Cobrancas.php:135-148` marca ocorrencia como realizada e conclui cobranca quando nao ha pendentes.
- `Cobrancas.php:159-180` retorna alertas para navbar.
- Migration `2026-06-10-000002_cobrancas_recorrentes.php:30-31` declara que a cobranca e sem vinculo financeiro.

Riscos observados:

- Nao integra venda/compra/conta a receber.
- "Realizada" nao equivale a recebimento financeiro.
- Alerta pode induzir baixa operacional sem movimentacao de caixa.

## Fluxos nao evidenciados ou incompletos

- Aprovacao formal de orcamento.
- Rejeicao de orcamento.
- Expiracao automatica de orcamento.
- Conversao com referencia rastreavel.
- Pagamento parcial de contas a receber.
- Multiplos recebimentos para uma parcela.
- Pagamento em excesso.
- Renegociacao.
- Estorno.
- Devolucao.
- Transferencia entre contas financeiras.
- Conciliacao bancaria.
- Ledger financeiro imutavel.
- Auditoria de alteracoes financeiras com antes/depois.
- Job agendado para vencimentos/alertas/expiracao.
- Controle de concorrencia para dois usuarios na mesma parcela/venda.

## Estado recomendado dos fluxos

Orcamento:

```text
RASCUNHO -> ENVIADO -> APROVADO -> CONVERTIDO
                    -> REJEITADO
                    -> EXPIRADO
                    -> CANCELADO
```

Venda:

```text
ABERTA -> CONFIRMADA -> FATURADA -> PARCIALMENTE_PAGA -> PAGA
                        -> CANCELADA
                        -> DEVOLVIDA
```

Conta a receber:

```text
ABERTA -> PARCIAL -> PAGA
       -> VENCIDA
       -> CANCELADA
       -> RENEGOCIADA
```

Movimento financeiro:

```text
PREVISTO -> EFETIVADO -> CONCILIADO
         -> ESTORNADO por movimento compensatorio
```

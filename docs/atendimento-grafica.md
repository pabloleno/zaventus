# Atendimento da gráfica

Orçamentos e ordens de serviço usam o mesmo atendimento em `ordens_de_servicos`. O número e o histórico acompanham o trabalho da proposta à conclusão.

## Operação

1. Abra **Orçamentos → Novo orçamento** (`/ordensDeServicos/create`), selecione cliente e vendedor. O atendente é o usuário conectado; o técnico pode ficar a definir.
2. Adicione serviços do catálogo e confira quantidade, preço, medidas e unidade das medidas. **Adicionar brinde** cria uma cortesia de valor zero, que continua podendo ter arte, execução e consumo de materiais.
3. Informe desconto, entrada e condições de pagamento. Descontos monetários e percentuais são normalizados para duas casas. Em pagamento misto, distribua o total nas parcelas; a soma deve corresponder ao orçamento. Entrada combinada é planejamento: registre o recebimento quando ela for paga.
4. Para execução externa ou instalação, informe endereço, contato e agendamento. Salve e acompanhe a situação na ficha. O histórico registra responsáveis e alterações. Uma edição concorrente exige recarregar a ficha antes de salvar.
5. Em **Registrar recebimento**, informe o valor efetivamente recebido e uma forma habilitada. Caixa e vínculo à parcela são opcionais. Com caixa, o recebimento também compõe o movimento daquele caixa. Para desfazer, use **Estornar** e informe o motivo; o registro permanece no histórico.
6. Em **Consumo de materiais**, registre o que foi utilizado, inclusive em cortesias. O movimento reduz o estoque, sem acrescentar preço ao orçamento. Estorno devolve a quantidade ao estoque e preserva a auditoria.
7. Anexe PDF, JPG, PNG ou WEBP de até 20 MB, escolhendo categoria e, se necessário, serviço. Os arquivos ficam no armazenamento privado; o download exige acesso ao sistema. O limite de upload do PHP/Apache também precisa comportar o arquivo.
8. Use **Imprimir / PDF** para conferir o documento e salvar como PDF pelo navegador. A impressão usa os dados da empresa, condições gerais, itens, cortesias e pagamento. Observações internas não são impressas. Configure e-mail e condições em **Configurações → Empresa**.

**Orçamentos** reúne elaboração e aprovação; **Ordens de serviço** reúne as demais etapas. A opção **Incluir todas as etapas do atendimento** permite consultar o conjunto. Os filtros são aplicados antes do limite de 200 resultados.

Formas de pagamento e responsáveis desativados permanecem identificados em planos anteriores. Eles não podem ser atribuídos a novos planos ou atendimentos. Cancelar exige motivo e permite reabertura. **Excluídos** permite restaurar em até 30 dias; a exclusão definitiva é manual e bloqueada quando existem vínculos financeiros ou de estoque.

## Financeiro e histórico

- `pagamentos_os` e suas parcelas guardam o combinado. `pagamentos_do_cliente` registra recebimentos efetivos, inclusive estornos.
- `contas_a_receber` mantém um título consolidado por atendimento, com saldo em `valor` e recebido acumulado em `valor_pago`. A aprovação ou o primeiro recebimento torna o saldo exigível; cancelamento e exclusão retiram a cobrança ativa.
- Recebimentos vinculados ao caixa usam `lancamentos.natureza = recebimento_os`. O faturamento conta a OS concluída e evita contar sua entrada novamente como receita. Estornos não compõem o saldo do caixa.
- Edição e exclusão manuais de contas, recebimentos e lançamentos vinculados encaminham para a ficha. Itens e parcelas removidos preservam seus vínculos históricos.

## Instalação e validação

As migrations `2026-09-07-000001_atendimento_grafica.php`, `2026-09-07-000002_recebimentos_atendimento.php` e `2026-09-07-000003_reposicoes_rastreaveis.php` ampliam as tabelas existentes, ajustam quantidades para quatro casas e normalizam datas de exclusão vazias. Não oferecem `down` destrutivo. Em outro ambiente, confirme o backup e sua restauração em cópia antes de aplicar. Reversão exige restauração do backup validado.

As três migrations foram aplicadas ao banco principal local em **13/09/2026 às 19:00:43**, lote 17. A comparação com o backup anterior confirmou a preservação de **5.648 registros em 58 tabelas**, conferindo todos os campos anteriores e considerando as normalizações previstas. Backup anterior à migração: `C:\zaventus-backups\20260913-antes-migracao`. Cópia física preservada para a recuperação do MariaDB: `C:\zaventus-backups\20260913-mariadb-acesso`.

O executor de testes lê as credenciais locais sem imprimi-las e exige um banco distinto do principal, com nome `zaventus_restore_*_test`. Exemplo para o clone usado na validação local:

```powershell
$env:ZAVENTUS_TEST_DATABASE = 'zaventus_restore_20260912_atendimento_test'
& C:\xampp\php\php.exe tools/phpunit-atendimento.php --testsuite application
```

As suítes de domínio cobrem cálculo, transações, parcelas, recebimentos, estornos, consumo, anexos, exclusão e dados legados. Execute suítes que compartilham o clone em sequência. Para validar a interface, confira pelo navegador criação, edição, cortesia, pagamento misto, recebimento/estorno, consumo/estorno, anexos, impressão, cancelamento e restauração. Os helpers `tools/atendimento-qa-server.php` e `tools/atendimento-qa-fixtures.php` servem à validação local em cópia; não substituem a implantação.

## Resultado da validação local

- Suíte completa: 130 testes, 1.508 verificações aprovadas em banco exclusivo.
- Sintaxe PHP: 843 arquivos conferidos; Composer validado com extensões disponíveis.
- Interface: fachada 5 x 1,2 m, instalação, cortesia, desconto e total R$ 2.000; recebimento R$ 1.000 e saldo R$ 1.000; edição preservou valores, vínculos e autoria.
- Consumo da cortesia: 0,25 m², estoque de 50 para 49,75, sem alterar o total comercial.
- Impressão conferida visualmente; observação interna de teste ausente do documento.
- Upload HTTP autenticado e download conferidos por hash; acesso anônimo encaminhado ao login.
- Banner: duas unidades de R$ 70, total R$ 140, sem técnico, entrada ou instalação obrigatórios; fluxo até concluído validado por HTTP e banco.
- Após a migração, login do ambiente principal respondeu HTTP 200 com formulário de acesso, sem erro de aplicação. A conferência visual dos fluxos acima foi feita na cópia de testes.
- Em 14/09/2026, todas as migrations, incluindo a atualização da marca padrão, foram executadas em banco vazio exclusivo. Os 130 testes e 1.508 verificações passaram também nesse ambiente.

A migration `2026-09-14-000001_atualiza_marca_padrao.php` leva a logo completa e o favicon aos demais ambientes, preservando arquivos personalizados. A página de login exibe a marca sem repetir o nome em texto.

O MariaDB exigiu reparo do Aria e restauração das tabelas internas de permissões a partir de cópia física preservada. Os dados da aplicação foram conferidos antes e depois. O acesso persistiu após reinício controlado. Para desligar o ambiente, use a parada normal do MySQL no XAMPP.

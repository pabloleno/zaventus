# NX Gestão

ERP web para pequenas empresas, desenvolvido em **PHP 8.2+**, **CodeIgniter 4.7.2** e **MySQL/MariaDB**. O sistema centraliza vendas, PDV, estoque, financeiro, cadastros, relatórios gerenciais, controle fiscal e permissões de usuários em uma única aplicação.

## Visão Geral

O NX Gestão foi pensado para operações comerciais que precisam controlar produtos, clientes, vendedores, fornecedores, caixa, contas, orçamento, pedidos, vendas e indicadores do negócio sem depender de planilhas separadas.

Principais áreas do sistema:

- **Dashboard inicial** com indicadores, caixas abertos e resumo operacional.
- **PDV** com seleção de caixa aberto, produtos, clientes, vendedores, descontos e formas de pagamento.
- **Venda rápida** para operações simplificadas.
- **Histórico de vendas** com detalhamento, impressão e emissão fiscal.
- **Financeiro** com abertura/fechamento de caixa, lançamentos, retiradas, despesas, contas a pagar e contas a receber.
- **DRE** com apuração de faturamento, impostos, despesas variáveis, despesas fixas, gastos com pessoas e pró-labore por período.
- **Estoque** com produtos, categorias, fornecedores, reposições, saída de mercadorias, inventário e controle de validade.
- **Orçamentos e pedidos** para registrar etapas antes da venda.
- **Cadastros gerais** de clientes, fornecedores, funcionários, vendedores, técnicos e serviços/mão de obra.
- **Ordens de serviço** com equipamentos, peças, serviços e pagamentos.
- **Relatórios** de vendas, estoque, financeiro, contas, clientes, fornecedores, funcionários e vendedores.
- **Controle fiscal** com configurações e registros de NFe/NFCe.
- **Usuários e permissões** por módulo e funcionalidade.
- **Backup de banco de dados** via tela de configurações.

## Funcionalidades

### Vendas, PDV e OS

- PDV vinculado a um caixa aberto.
- Inclusão de produtos por código de barras ou seleção por nome.
- Ajuste de quantidade, valor unitário e desconto dos itens.
- Associação da venda a cliente, vendedor e forma de pagamento.
- Venda rápida para fluxo mais simples.
- Histórico completo de vendas.
- Emissão, impressão, cancelamento e reemissão fiscal quando configurado.
- Ordens de serviço com técnicos, equipamentos, peças, serviços e pagamentos.

### Estoque e Produtos

- Cadastro de produtos com categoria, fornecedor, unidade, localização, código de barras, NCM, CSOSN, CFOP, validade e imagem.
- Upload e troca de imagem do produto em `public/assets/img/produtos`.
- Pesquisa de produto por nome ou código de barras.
- Controle de quantidade e quantidade mínima.
- Margem de lucro, valor de custo, valor de venda e lucro.
- Reposição de estoque.
- Saída de mercadorias.
- Inventário de estoque.
- Importação/apoio a cadastro e reposição de produtos via XML.

### Financeiro

- Abertura, fechamento e reabertura de caixas.
- Lançamentos financeiros vinculados ao caixa.
- Retiradas do caixa.
- Despesas classificadas por tipo.
- Contas a pagar.
- Contas a receber.
- Pagamentos de clientes.
- Orçamentos e pedidos.
- Relatório DRE por período.

### Relatórios e Indicadores

O sistema possui relatórios operacionais e gerenciais, incluindo:

- Vendas: histórico completo, por cliente e por vendedor.
- Estoque: produtos, estoque mínimo, inventário e validade dos produtos.
- Financeiro: faturamento diário, faturamento detalhado, lançamentos, retiradas e despesas.
- Administrativo: contas a pagar, contas a receber e DRE.
- Geral: clientes, fornecedores, funcionários e vendedores.
- Gráficos para análise, como o relatório de faturamento diário.

### Usuários e Permissões

O controle de acesso é configurado por usuário em formato granular. É possível liberar ou bloquear módulos e funcionalidades como:

- Vendas: venda rápida, PDV, pesquisa de produtos e histórico de vendas.
- Controle geral: clientes, fornecedores, funcionários e vendedores.
- Estoque: produtos, reposições, saídas e categorias.
- Financeiro: caixas, lançamentos, retiradas, despesas, contas, orçamentos, pedidos, DRE, inventário e fiscal.
- Relatórios: vendas, estoque, financeiro e geral.
- Configurações: NFe, NFCe, empresa, sistema, usuários e backup de dados.

Os menus são exibidos conforme as permissões do usuário autenticado.

### Fiscal

- Configuração de NFe e NFCe.
- Upload de certificado digital `.pfx`.
- Controle fiscal de NFe/NFCe emitidas.
- Impressão de DANFE/cupom fiscal quando a emissão está disponível.
- Armazenamento de chave, XML, protocolo, status e mensagens de erro.

## Tecnologias

- PHP `^8.2`
- CodeIgniter `4.7.2`
- MySQL/MariaDB
- MySQLi/PDO
- Bootstrap/AdminLTE no painel
- Chart.js em relatórios
- Biblioteca `sped-nfe` para recursos fiscais
- Biblioteca `mysqldump-php` para backup do banco

## Requisitos

Ambiente mínimo recomendado:

- PHP 8.2 ou superior.
- MySQL ou MariaDB.
- Servidor web Apache/Nginx apontando para a pasta `public`.
- Extensões PHP:
  - `intl`
  - `mbstring`
  - `mysqli`
  - `pdo_mysql`
  - `curl`
  - `xml`
  - `simplexml`
  - `openssl`
  - `fileinfo`
  - `json`

Para hospedagem compartilhada, confirme se o plano permite configurar o document root para `public/` ou criar redirecionamento equivalente.

## Configuração

As principais configurações ficam no arquivo `.env`.

Exemplo de banco local:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://local.nxgestao.com'

database.default.hostname = localhost
database.default.database = nxgestao
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

Em produção, ajuste:

```ini
CI_ENVIRONMENT = production
app.baseURL = 'https://seudominio.com'
```

## Instalação Local

1. Clone o repositório:

```bash
git clone https://github.com/plrbxx/nxgestao.git
```

2. Configure o servidor web para apontar para:

```text
public/
```

3. Configure o `.env` com os dados do banco.

4. Importe o banco MySQL do ambiente de implantação ou de um backup válido.

5. Garanta permissão de escrita nas pastas:

```text
writable/
public/assets/img/produtos/
```

6. Acesse a URL configurada no `app.baseURL`.

## Observações Sobre Banco de Dados

O projeto depende de um banco MySQL/MariaDB com as tabelas do ERP. O repositório não deve ser usado como substituto de backup de banco: dados de clientes, vendas, produtos, contas e fiscal precisam ser preservados por rotina própria.

O sistema possui uma rotina de backup em:

```text
/configs/backupDataBase
```

Ela gera um arquivo SQL em:

```text
writable/backup_mysql/BACKUP_DATABASE_SISTEMA.sql
```

## Estrutura Principal

```text
app/Controllers/        Controllers dos módulos do ERP
app/Models/             Models das tabelas do sistema
app/Views/              Telas e relatórios
app/Config/             Configurações da aplicação
app/ThirdParty/         Bibliotecas embarcadas
public/                 Document root público
public/assets/          CSS, JS, imagens e arquivos públicos
system/                 Núcleo CodeIgniter 4.7.2
writable/               Logs, cache, uploads, backups e arquivos gerados
```

## Módulos no Código

Controllers principais:

- `Pdv`, `VendaRapida`, `Vendas`, `Orcamentos`, `Pedidos`
- `Caixas`, `Lancamentos`, `Retiradas`, `Despesas`, `ContasPagar`, `ContasReceber`
- `Produtos`, `Reposicoes`, `SaidaDeMercadorias`, `InventarioDoEstoque`, `CategoriasDosProdutos`
- `Clientes`, `Fornecedores`, `Funcionarios`, `Vendedores`, `Tecnicos`
- `OrdensDeServicos`, `ServicosMaoDeObra`, `PagamentosDoCliente`
- `Relatorios`, `RelatorioDRE`
- `NFe`, `Pdv`, `ControleFiscal`, `ImprimeDanfe`
- `Login`, `Configs`, `Inicio`

## Segurança e Operação

- Use HTTPS em produção.
- Mantenha PHP e CodeIgniter atualizados.
- Restrinja permissões de arquivos sensíveis como `.env`.
- Faça backup recorrente do banco e dos arquivos enviados.
- Evite expor a raiz do projeto; o servidor deve apontar para `public/`.
- Revise permissões dos usuários antes de entregar o sistema a clientes.

## Status Atual

- Framework: CodeIgniter 4.7.2
- PHP alvo: 8.2+
- Banco: MySQL/MariaDB
- Projeto voltado a pequenas empresas com venda, financeiro, estoque, relatórios e fiscal.

## Licença

Este projeto utiliza CodeIgniter, distribuído sob licença MIT. Verifique também as licenças das bibliotecas embarcadas em `app/ThirdParty` e `system/ThirdParty`.

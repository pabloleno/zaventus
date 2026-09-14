# Zaventus | Gráfica Rápida e Comunicação Visual

Sistema web interno para administrar o fluxo comercial, operacional e
financeiro da Zaventus. A aplicação é voltada à venda de serviços gráficos,
orçamentos, ordens de serviço, produção, arte, instalação, clientes,
fornecedores e matérias-primas.

Este projeto não emite NF-e, NFS-e, NFC-e, SAT nem cupom não fiscal. Os fluxos
legados de PDV e emissão fiscal estão fora da lista de rotas permitidas e serão
mantidos no código somente até a validação e aposentadoria segura dos dados
históricos.

## Estado da evolução

O sistema existente está sendo modernizado por módulos, sem reescrita total:

1. Fase 0: ambiente reproduzível, backup, rotas explícitas, testes e isolamento
   da interface legada.
2. Fase 1: autenticação, usuários e RBAC.
3. Fases seguintes: clientes, catálogo/SKU, CRM, orçamentos, vendas, OS,
   arte/produção, instalações, financeiro, dashboard e relatórios.

Durante a transição:

- telas legadas permanecem em AdminLTE 3 e Bootstrap 4;
- telas novas usarão Bootstrap 5 em layout isolado;
- módulos antigo e novo não devem gravar simultaneamente a mesma informação;
- exclusões e mudanças estruturais só ocorrerão após backup e homologação.

Já implementado no atendimento da gráfica: orçamento e OS compartilham o
mesmo registro, com preços por medida, cortesias, aprovação, produção,
instalação, recebimentos e estornos, consumo de materiais, anexos privados,
histórico e impressão. As telas atuais desse fluxo reutilizam AdminLTE 3 e
Bootstrap 4. Consulte o [guia de uso](docs/atendimento-grafica.md).

A identidade visual inclui a logo completa no login e o favicon colorido.
A migration `2026-09-14-000001_atualiza_marca_padrao.php` atualiza a marca
padrão em outros ambientes e preserva imagens personalizadas.

## Stack

- PHP 8.2+ com BCMath, Fileinfo, Intl, Mbstring e MySQLi
- CodeIgniter 4.7.2
- MariaDB/MySQL com driver MySQLi
- Apache
- Bootstrap 4/AdminLTE apenas nas telas legadas
- Bootstrap 5 nas telas novas
- JavaScript simples e `fetch` quando necessário

## Ambiente local

O host de desenvolvimento é:

```text
local.zaventus.com
```

O protocolo é definido por `app.baseURL` no `.env`. HTTP e HTTPS podem ser
usados localmente, desde que Apache, certificado e a configuração da aplicação
estejam alinhados.

Passos resumidos:

1. Configure o VirtualHost para apontar exclusivamente para `public/`.
2. Adicione `127.0.0.1 local.zaventus.com` ao arquivo `hosts` do Windows.
3. Copie `.env.example` para `.env`.
4. Crie o banco de desenvolvimento e um banco separado para testes.
5. Instale as dependências do Composer.
6. Execute as migrations e as verificações de qualidade.

O procedimento completo está em
[`docs/fase-0/README.md`](docs/fase-0/README.md).

## Configuração

Nunca versione o arquivo `.env`. Configuração mínima:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://local.zaventus.com/'
app.indexPage = ''

database.default.hostname = localhost
database.default.database = zaventus
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi

database.tests.hostname = localhost
database.tests.database = zaventus_test
database.tests.username = root
database.tests.password =
database.tests.DBDriver = MySQLi
```

Em produção, utilize HTTPS, credenciais próprias, `CI_ENVIRONMENT=production`
e permissões restritas no filesystem.

## Dependências e testes

O `composer.lock` faz parte do projeto. Depois de instalar o Composer:

```bash
composer install
php tools/spark-test-db.php migrate -n App
composer quality
```

Em um XAMPP onde PHP/Composer não estejam no `PATH`, execute os mesmos comandos
informando os caminhos dos executáveis:

```powershell
C:\xampp\php\php.exe C:\caminho\para\composer.phar install
C:\xampp\php\php.exe tools\spark-test-db.php migrate -n App
C:\xampp\php\php.exe C:\caminho\para\composer.phar quality
```

Comandos individuais:

```bash
composer lint
composer test
php tools/phase0_baseline.php
php spark migrate:status
php spark routes
php tools/spark-test-db.php migrate:status -n App
```

O script de baseline é somente leitura e não exibe credenciais.
Crie e configure previamente o banco exclusivo de testes. As suítes precisam
do schema migrado antes de executar; não execute seeds legados para prepará-lo.
O invólucro `spark-test-db.php` força a conexão para um banco cujo nome termine
em `_test` ou `_testing`; use-o para executar migrations descartáveis em vez de
passar apenas `-g tests` ao Spark.

## Banco e migrations

- Alterações de schema devem ser feitas somente por migrations.
- Tabelas novas usarão nomes consistentes em inglês.
- Migrações de dados serão aditivas e reversíveis.
- Registros críticos não devem ser apagados fisicamente.
- Valores monetários usarão `DECIMAL`; quantidades de materiais poderão ser
  fracionárias.
- Conversões, pagamentos e movimentos de estoque devem usar transações.

O banco local de testes nunca deve apontar para o banco de desenvolvimento ou
produção.

## Backups e uploads

O Git não é mecanismo de backup de dados operacionais.

Devem ficar fora do controle de versão:

- dumps SQL;
- `.env`;
- certificados e chaves;
- fotos e uploads de clientes;
- imagens cadastradas de produtos/matérias-primas;
- arquivos de arte e instalação.

Antes de migrations ou deploys, gere um dump externo e valide a restauração em
um banco temporário. O runbook contém os comandos seguros de backup e restore.

## Rotas e módulos legados

O auto-routing está desativado. Somente rotas declaradas em
`app/Config/Routes.php` e `app/Config/Routes/legacy.php` são acessíveis.

Estão deliberadamente sem rota:

- PDV e venda rápida;
- pedidos e orçamento antigo baseado em produtos;
- importação de produtos por XML fiscal;
- NF-e, NFC-e, DANFE e controle fiscal;
- exclusão de vendas históricas.

O histórico de vendas continua disponível apenas para consulta.

## Estrutura principal

```text
app/                    aplicação CodeIgniter
app/Config/Routes/      mapas explícitos de rotas
app/Database/           migrations e seeders
app/Views/              interface atual e futura
docs/fase-0/            runbooks e decisões da preparação
public/                 document root do Apache
tests/app/              testes da aplicação
tools/                  verificações locais e de CI
writable/               cache, logs e uploads não versionados
```

## Segurança

- Rotas mutáveis usam POST e CSRF.
- Saída de usuário deve ser escapada.
- Autorização deve negar acesso quando não houver regra definida.
- Uploads exigem validação de extensão, MIME, tamanho e conteúdo.
- Senhas, tokens e valores do `.env` não podem aparecer em logs.
- O document root deve permanecer em `public/`.

## Documentação da Fase 0

- [Runbook de ambiente, backup e testes](docs/fase-0/README.md)
- [Plano de migração Bootstrap 5](docs/fase-0/bootstrap5-migration.md)
- [Exemplo de VirtualHost](docs/fase-0/vhost-local.zaventus.com.conf.example)

## Licenças

O sistema Zaventus é software proprietário. CodeIgniter e dependências de
terceiros mantêm suas respectivas licenças.

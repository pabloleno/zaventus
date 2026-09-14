# Fase 0 - Runbook do ambiente

Este runbook prepara um ambiente reproduzivel para o Zaventus em Windows com
XAMPP e deixa explicito o caminho de migracao para Linux. Ele nao substitui um
backup validado e nao autoriza alterar o banco de uso diario sem uma copia de
seguranca.

## Baseline validada em 06/09/2026

- Projeto: `C:\xampp\htdocs\zaventus`.
- PHP CLI: 8.2.12.
- Apache: 2.4.58, portas 80 e 443.
- MariaDB: 10.4.32, porta 3306.
- CodeIgniter: 4.7.2.
- Host local: `local.zaventus.com`.
- Document root: `C:/xampp/htdocs/zaventus/public`.
- Banco atual: `zaventus`, em `utf8mb4`.

O ambiente atual responde em HTTP e HTTPS. O esquema deve ser escolhido por
ambiente e declarado em `.env`; nao se deve depender de URL fixa no codigo.

## Regras de seguranca

1. Nunca versione `.env`, dumps SQL, certificados, chaves ou uploads reais.
2. Nunca use o banco `zaventus` para testes automatizados.
3. Antes de migrations, valide um backup restaurando-o em outro banco.
4. Use o document root `public/`; nao publique a raiz do repositorio.
5. Execute comandos de banco com o Apache parado somente quando a operacao
   exigir manutencao exclusiva. Para backup transacional comum, isso nao e
   necessario.
6. O seeder `AutoInsert` atual possui credencial inicial fraca e dados legados.
   Nao o execute ate que seja substituido por um bootstrap seguro e idempotente.

## 1. Preparar o XAMPP

Instale o XAMPP em `C:\xampp` e habilite Apache e MySQL no painel. Verifique o
ambiente no PowerShell:

```powershell
& C:\xampp\php\php.exe -v
& C:\xampp\apache\bin\httpd.exe -v
& C:\xampp\mysql\bin\mysqld.exe --version
& C:\xampp\php\php.exe -m
```

As extensoes PHP minimas sao `intl`, `mbstring`, `mysqli`, `pdo_mysql`, `curl`,
`openssl`, `fileinfo`, `json`, `xml` e `SimpleXML`. Para imagens, mantenha `gd`
habilitada. Confirme ainda que o Apache carrega `mod_rewrite`; HTTPS exige
`mod_ssl` e um certificado local confiavel.

Se uma porta estiver ocupada, descubra o processo antes de mudar configuracoes:

```powershell
Get-NetTCPConnection -State Listen -LocalPort 80,443,3306 |
    Select-Object LocalAddress, LocalPort, OwningProcess
```

## 2. Criar bancos e usuarios locais

Abra o cliente MariaDB sem colocar senha na linha de comando:

```powershell
& C:\xampp\mysql\bin\mysql.exe --user=root --password
```

Crie bancos separados. Troque as senhas ilustrativas antes de executar:

```sql
CREATE DATABASE IF NOT EXISTS zaventus
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE DATABASE IF NOT EXISTS zaventus_test
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

CREATE USER IF NOT EXISTS 'zaventus_app'@'localhost'
  IDENTIFIED BY 'TROQUE_ESTA_SENHA';
CREATE USER IF NOT EXISTS 'zaventus_test'@'localhost'
  IDENTIFIED BY 'TROQUE_ESTA_SENHA_DE_TESTE';

GRANT ALL PRIVILEGES ON zaventus.* TO 'zaventus_app'@'localhost';
GRANT ALL PRIVILEGES ON zaventus_test.* TO 'zaventus_test'@'localhost';
FLUSH PRIVILEGES;
```

O usuario de teste nao deve receber permissao sobre `zaventus`.

## 3. Configurar `.env`

Crie o `.env` local a partir da referencia do projeto e mantenha-o fora do Git.
Exemplo para HTTP:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://local.zaventus.com/'
app.indexPage = ''
app.forceGlobalSecureRequests = false
cookie.secure = false

database.default.hostname = localhost
database.default.database = zaventus
database.default.username = zaventus_app
database.default.password = 'TROQUE_ESTA_SENHA'
database.default.DBDriver = MySQLi
database.default.charset = utf8mb4
database.default.DBCollat = utf8mb4_general_ci
database.default.port = 3306
```

Para HTTPS local, altere somente os valores relacionados ao esquema:

```ini
app.baseURL = 'https://local.zaventus.com/'
app.forceGlobalSecureRequests = true
cookie.secure = true
```

Use um unico esquema por ambiente. `baseURL`, VirtualHost, links e cookies devem
concordar. Gere a chave da aplicacao somente depois de o gerenciamento Composer
estar normalizado:

```powershell
& C:\xampp\php\php.exe spark key:generate
```

Nao copie a chave local para producao.

## 4. Configurar hosts e VirtualHost

Abra como administrador:

```text
C:\Windows\System32\drivers\etc\hosts
```

Adicione uma unica entrada:

```text
127.0.0.1 local.zaventus.com
```

Garanta que `C:\xampp\apache\conf\httpd.conf` inclua o arquivo de VirtualHosts:

```apache
Include conf/extra/httpd-vhosts.conf
```

Copie para `C:\xampp\apache\conf\extra\httpd-vhosts.conf` somente os blocos
necessarios do arquivo
[`vhost-local.zaventus.com.conf.example`](vhost-local.zaventus.com.conf.example).
Valide antes de reiniciar:

```powershell
& C:\xampp\apache\bin\httpd.exe -t
& C:\xampp\apache\bin\httpd.exe -S
```

O resultado deve associar `local.zaventus.com` ao diretorio `public`, nunca a
`C:/xampp/htdocs/zaventus`.

### HTTPS local

Use um certificado com `DNS:local.zaventus.com` no Subject Alternative Name e
confie apenas na CA local usada para desenvolvimento. No exemplo, os arquivos
ficam em:

```text
C:\xampp\apache\conf\ssl.crt\local.zaventus.com.crt
C:\xampp\apache\conf\ssl.key\local.zaventus.com.key
```

Nao versione a chave privada. Se HTTPS ainda nao estiver pronto, mantenha apenas
o VirtualHost HTTP e os valores HTTP no `.env`. Se HTTPS for canonico, habilite
o redirecionamento do bloco HTTP somente depois de validar o certificado.

## 5. Migrations e banco de teste

Primeiro confira o ambiente e o estado, sem escrever no banco:

```powershell
& C:\xampp\php\php.exe spark env
& C:\xampp\php\php.exe spark migrate:status
```

Nesta versao do CodeIgniter, a opcao `migrate -g tests` filtra migrations, mas
nao troca com seguranca a conexao que o runner abriu inicialmente. Use o
involucro do projeto, que aborta se o nome do banco nao terminar em `_test` ou
`_testing`:

```powershell
Set-Item Env:ZAVENTUS_TEST_DATABASE 'zaventus_test'
Set-Item Env:ZAVENTUS_TEST_HOSTNAME '127.0.0.1'
Set-Item Env:ZAVENTUS_TEST_USERNAME 'zaventus_test'
Set-Item Env:ZAVENTUS_TEST_PASSWORD 'TROQUE_ESTA_SENHA_DE_TESTE'
Set-Item Env:ZAVENTUS_TEST_PORT '3306'

& C:\xampp\php\php.exe tools\spark-test-db.php config:check Database
```

Confirme na saida que o grupo efetivo aponta para `zaventus_test` antes de rodar
qualquer migration. Depois da validacao, monte o schema `App` no banco de teste:

```powershell
& C:\xampp\php\php.exe tools\spark-test-db.php migrate -n App
& C:\xampp\php\php.exe tools\spark-test-db.php migrate:status -n App
```

Ao terminar a sessao, remova as variaveis locais:

```powershell
Get-ChildItem Env:ZAVENTUS_TEST_* | Remove-Item
```

Nao rode `migrate:refresh` no banco diario. A migration
`2026-07-13-000001_login_foto_usuario.php` ja consta aplicada no banco local e
precisa permanecer no historico versionado para que uma instalacao nova seja
reproduzivel.

## 6. Backup e teste de restauracao

Salve dumps fora do repositorio. O exemplo solicita a senha interativamente e
usa `--result-file` para evitar alteracao de encoding pelo PowerShell:

```powershell
$backupRoot = 'C:\zaventus-backups'
$stamp = Get-Date -Format 'yyyyMMdd-HHmmss'
$dumpPath = Join-Path $backupRoot "zaventus-$stamp.sql"

New-Item -ItemType Directory -Path $backupRoot -Force | Out-Null

& C:\xampp\mysql\bin\mysqldump.exe `
    --user=zaventus_app `
    --password `
    --single-transaction `
    --routines `
    --triggers `
    --default-character-set=utf8mb4 `
    --result-file="$dumpPath" `
    zaventus

Get-Item -LiteralPath $dumpPath | Select-Object FullName, Length, LastWriteTime
Get-FileHash -Algorithm SHA256 -LiteralPath $dumpPath
```

Um dump so e valido depois de restaurado. Crie um banco vazio com nome exclusivo,
por exemplo `zaventus_restore_20260906`, e restaure nele, nunca sobre `zaventus`:

```powershell
& C:\xampp\mysql\bin\mysql.exe --user=root --password `
    --execute="CREATE DATABASE zaventus_restore_20260906 CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"

$mysqlDumpPath = $dumpPath.Replace('\\', '/')
& C:\xampp\mysql\bin\mysql.exe --user=root --password `
    --database=zaventus_restore_20260906 `
    --execute="source $mysqlDumpPath"

& C:\xampp\mysql\bin\mysql.exe --user=root --password `
    --database=zaventus_restore_20260906 `
    --execute="SHOW TABLES; SELECT COUNT(*) AS migrations FROM migrations;"
```

Compare tabelas e totais essenciais sem expor dados pessoais. A remocao do banco
de conferencia deve ser uma decisao manual posterior. Inclua tambem os arquivos
enviados no plano de backup; eles nao ficam dentro do MySQL.

## 7. Composer e PHPUnit

Instale Composer 2 e confirme:

```powershell
composer --version
```

O manifesto agora representa a aplicacao, o `composer.lock` e versionado e os
scripts herdados que apontavam para ferramentas inexistentes foram removidos.
Instale exatamente as versoes travadas e rode a suite da aplicacao:

```powershell
composer validate --strict
composer install
composer quality
```

Alternativamente, para diagnostico direto:

```powershell
& C:\xampp\php\php.exe vendor\phpunit\phpunit\phpunit --testsuite application
```

O PHPUnit deve usar apenas `zaventus_test`. Em implantacao, use
`composer install --no-dev --prefer-dist --optimize-autoloader`, nunca copie um
`vendor/` gerado em plataforma incompatível sem validacao.

## 8. Validacao final local

Execute a lista apos qualquer mudanca de ambiente:

```powershell
& C:\xampp\apache\bin\httpd.exe -t
& C:\xampp\apache\bin\httpd.exe -S
& C:\xampp\php\php.exe spark migrate:status
& C:\xampp\php\php.exe spark routes

curl.exe -sS -o NUL -w "HTTP %{http_code}`n" http://local.zaventus.com/login
curl.exe -k -sS -o NUL -w "HTTPS %{http_code}`n" https://local.zaventus.com/login
curl.exe -k -sS -o NUL -w "Protegida %{http_code}`n" https://local.zaventus.com/inicio
```

Resultados esperados:

- `/login`: 200 no esquema canonico;
- `/inicio` sem sessao: 302 para `/login`;
- Apache sem erro de sintaxe;
- banco acessivel e migrations coerentes;
- nenhuma URL aponta para fora de `public/`;
- ao final da Fase 0, zero rotas `auto` em `spark routes`;
- suite da aplicacao aprovada.

## 9. Portabilidade para Linux

- Use PHP e extensoes equivalentes, MariaDB/MySQL e Apache ou Nginx; XAMPP nao
  deve ser dependencia da aplicacao.
- Configure o document root para `<projeto>/public` e crie `.env` proprio no
  servidor. Nunca envie o `.env` local.
- Use `APPPATH`, `WRITEPATH`, `FCPATH` e `DIRECTORY_SEPARATOR` no PHP; nao grave
  caminhos `C:\...` no codigo ou no banco.
- Linux diferencia maiusculas de minusculas. Padronize nomes de arquivos,
  namespaces, rotas e tabelas antes da migracao.
- Conceda escrita apenas ao usuario do servidor web em `writable/` e nos
  diretorios de upload necessarios. Codigo e configuracoes ficam somente leitura.
- Use certificado valido e `CI_ENVIRONMENT=production`; desative exibicao de
  erros e instale somente dependencias de producao.
- Valide timezone `America/Manaus`, charset `utf8mb4`, collation e SQL mode no
  destino antes de restaurar dados.
- Automatize backup de banco e uploads para armazenamento fora do servidor e
  teste restauracoes periodicamente.
- Execute migrations uma vez por release, com backup, janela controlada e plano
  de rollback. Nao use `migrate:refresh` em producao.

## Criterio de conclusao da Fase 0

- Ambiente documentado e reproduzivel em uma segunda maquina.
- HTTP ou HTTPS escolhido e consistente entre `.env`, Apache e testes.
- Banco diario, banco de teste e banco de restore isolados.
- Backup de banco e uploads fora do Git, com restore comprovado.
- Dependencias fixadas por `composer.lock` e PHPUnit executavel.
- Migrations reproduzem um banco vazio sem seed inseguro.
- Apache publica apenas `public/`.
- Rotas automaticas removidas e testes de acesso aprovados.
- Procedimento Linux nao depende de caminhos ou ferramentas exclusivas do
  Windows.

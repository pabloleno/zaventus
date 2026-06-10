# Guia Tecnico do Zaventus Gestao

Este documento apresenta a arquitetura e as principais regras de negocio do
sistema. Ele complementa os PHPDocs e JSDocs presentes no codigo e deve ser
atualizado quando um fluxo estrutural for alterado.

## Escopo do Codigo Autoral

O codigo mantido pela equipe esta concentrado nestes diretorios:

- `app/Controllers`: recebe requisicoes, coordena regras e renderiza views.
- `app/Models`: configura o acesso as tabelas e callbacks de persistencia.
- `app/Libraries`: concentra regras reutilizaveis e integracoes internas.
- `app/Filters`: aplica autenticacao, autorizacao e configuracoes globais.
- `app/Views`: contem os templates e o JavaScript especifico das telas.
- `app/Database/Migrations`: registra alteracoes versionadas do banco.
- `public/assets/js`: contem o JavaScript compartilhado entre telas.

Os diretorios `system`, `app/ThirdParty` e `public/theme/plugins` pertencem ao
framework ou a fornecedores externos. Eles nao devem receber personalizacoes,
pois isso dificulta atualizacoes e auditorias futuras.

## Fluxo de uma Requisicao

1. As rotas explicitas ficam em `app/Config/Routes.php`; o sistema tambem
   utiliza o auto routing legado do CodeIgniter.
2. `AuthGuard` valida sessao e permissao por modulo antes do controller.
3. `SystemSettings` aplica idioma, fuso horario e configuracoes da empresa.
4. O controller consulta models e bibliotecas, monta os dados e renderiza as
   views de cabecalho, conteudo e rodape.
5. Models derivados de `PadraoModel` normalizam campos comuns antes de gravar.

Ao criar uma nova acao publica, confirme se ela precisa de uma rota explicita
e inclua a permissao correspondente no `AuthGuard`.

## Regras Centrais de Negocio

### Produtos e Servicos

- **Produtos** correspondem as vendas registradas na tabela `vendas`.
- **Servicos** correspondem a mao de obra das ordens de servico concretizadas,
  somada a frete e outros valores e descontado o desconto da ordem.
- Pecas de uma ordem de servico podem gerar uma venda de produtos; por isso,
  nao devem ser somadas novamente como receita de servicos.
- **Geral/Administrativo** identifica movimentos financeiros que nao pertencem
  exclusivamente a produtos ou servicos.

As constantes e normalizacoes ficam em `App\Libraries\TipoNegocio`. Consultas
de faturamento devem reutilizar `FaturamentoNegocio`, evitando definicoes
divergentes entre dashboard e relatorios.

### Dashboard

`DashboardNegocio` monta todos os indicadores da tela inicial para um unico
mes/ano selecionado. A biblioteca separa faturamento, operacao, contas
pendentes e movimentacao financeira por tipo de negocio.

Uma conta aberta com vencimento anterior ao dia atual e exibida como vencida
na dashboard, mesmo que o status persistido ainda nao tenha sido atualizado.

### Vendas e Exclusao

Vendas utilizam exclusao logica. O campo `deleted_at` preserva o historico e
permite que relacionamentos fiscais, financeiros e de estoque continuem
auditaveis. Consultas de vendas e faturamento devem ignorar registros
excluidos logicamente.

### Ordens de Servico

Uma ordem passa por estados de orcamento e execucao. Somente ordens
`Concretizada` entram no faturamento de servicos. A concretizacao pode:

- registrar a venda das pecas utilizadas;
- baixar o estoque vinculado;
- consolidar pagamentos e parcelas;
- registrar automaticamente as datas de entrada e saida.

Mudancas nesse fluxo devem ser testadas em conjunto com vendas, estoque e
financeiro, porque a ordem coordena dados dessas tres areas.

### Valores Monetarios e Campos Padrao

`Moeda` converte valores entre entrada humana, decimal persistido e exibicao.
`CampoPadrao` valida e normaliza datas, contatos, documentos e textos curtos.
Controllers novos devem reutilizar essas bibliotecas antes de persistir dados.

No navegador, `moeda-padrao.js` e `campos-padrao.js` aplicam a mesma intencao
de normalizacao antes do envio do formulario.

### Cadastros e Contatos

`ContatoPadrao`, `EnderecoPadrao` e `ImagemCadastro` evitam regras duplicadas
nos cadastros de clientes, fornecedores, funcionarios e tecnicos. Alteracoes
nesses componentes devem ser verificadas em todos esses cadastros.

### Seguranca

`AuthGuard` e a fonte central de autorizacao. Nao confie apenas na ocultacao
de menus: toda funcionalidade sensivel precisa ser bloqueada no servidor.
Metodos internos de controller devem ser `private` ou `protected` sempre que
nao forem endpoints.

## Banco de Dados

Toda alteracao estrutural deve ser criada como migration. O metodo `up()`
aplica a mudanca e `down()` deve reverte-la quando isso puder ser feito sem
perda inesperada de dados.

Antes de alterar uma coluna usada por vendas, estoque, financeiro ou fiscal,
pesquise seu uso em controllers, models, bibliotecas, views e relatorios.

## Padrao de Documentacao

Funcoes nomeadas do codigo autoral devem possuir PHPDoc ou JSDoc imediatamente
antes da declaracao. O comentario deve explicar a responsabilidade ou a regra,
sem repetir cada linha da implementacao.

Para auditar a cobertura:

```bash
php tools/documentation_coverage.php
```

Para preencher comentarios ausentes com descricoes iniciais:

```bash
php tools/documentation_coverage.php --write
```

Para atualizar descricoes curtas geradas pelo utilitario:

```bash
php tools/documentation_coverage.php --refresh
```

Descricoes geradas devem ser revisadas quando a funcao possuir uma regra de
negocio especifica que nao possa ser inferida pelo nome.

## Validacao Antes de Publicar

1. Execute o auditor de documentacao.
2. Rode `php -l` em todos os PHPs alterados.
3. Execute migrations pendentes em um banco de desenvolvimento.
4. Teste os fluxos afetados pelo navegador e confirme respostas HTTP.
5. Revise `git diff --check` e mantenha backups, logs e arquivos gerados fora
   do commit.

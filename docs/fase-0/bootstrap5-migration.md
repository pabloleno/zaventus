# Fase 0 - Inventario e plano de migracao para Bootstrap 5

## Objetivo

Este documento define como a interface do Zaventus sera modernizada sem
interromper os fluxos que ja funcionam. O destino e uma interface renderizada
pelo CodeIgniter 4, em portugues do Brasil, com Bootstrap 5, JavaScript simples
e uma unica navegacao lateral.

A migracao visual nao autoriza mudancas de banco, regras de negocio, rotas,
permissoes ou remocao de modulos. Esses trabalhos devem ser executados nas
fases funcionais correspondentes. Nesta fase, o foco e inventariar dependencias,
definir o isolamento entre as duas interfaces e estabelecer uma ordem segura
de migracao.

## Estado atual confirmado

- O tema instalado e AdminLTE 3.0.1 e incorpora Bootstrap 4.3.1 em
  `public/theme/dist/css/adminlte.css`.
- O JavaScript global carrega Bootstrap 4 por
  `public/theme/plugins/bootstrap/js/bootstrap.bundle.js`.
- `app/Views/templates/header.php` carrega AdminLTE, temas Bootstrap 4 de
  SweetAlert2, DataTables e Select2, alem de jQuery.
- `app/Views/templates/footer.php` inicializa Bootstrap 4, Select2, DataTables,
  Bootstrap Switch, InputMask, AdminLTE e funcoes globais da aplicacao.
- O cabecalho escolhe entre sidebar e navegacao horizontal conforme o valor de
  `tema` na sessao. Os menus estao duplicados em
  `app/Views/templates/sidebar_tema_0.php` e
  `app/Views/templates/navbar_tema_1.php`.
- O login e o PDV possuem documentos HTML proprios e repetem inclusoes do tema,
  respectivamente em `app/Views/login/index.php` e `app/Views/pdv/start.php`.
- O dashboard possui uma camada visual autoral extensa em
  `public/assets/css/style.css`. Ela deve ser avaliada seletor por seletor; nao
  deve ser copiada integralmente para o novo layout.

Levantamento mecanico em `app/Views`, feito nesta fase:

| Padrao legado | Ocorrencias | Arquivos | Impacto principal |
|---|---:|---:|---|
| `data-toggle` | 46 | 21 | Deve virar `data-bs-toggle` ou JavaScript equivalente. |
| `data-dismiss` | 64 | 20 | Deve virar `data-bs-dismiss`. |
| `data-target` | 29 | 19 | Deve virar `data-bs-target`. |
| `custom-file` | 12 | 3 | Componente removido; usar `form-control` em `input[type=file]`. |
| `custom-control` | 21 | 3 | Substituir por `form-check` ou `form-switch`. |
| `input-group-append` | 13 | 12 | Estrutura nao utilizada pelo Bootstrap 5. |
| `ml-*` | 3 | 3 | Substituir por `ms-*`. |
| `text-right` | 5 | 2 | Substituir por `text-end`. |
| `float-right` | 1 | 1 | Substituir por `float-end`. |
| `font-weight-bold` | 2 | 1 | Substituir por `fw-bold`. |
| `btn-block` | 8 | 6 | Substituir por utilitarios de largura, normalmente `w-100`. |

Os numeros sao uma fotografia do repositorio e devem ser recalculados antes da
retirada definitiva do legado.

## Regra de isolamento

Bootstrap 4/AdminLTE e Bootstrap 5 nao podem ser carregados no mesmo documento
HTML. Durante a convivencia, uma pagina pertence integralmente a um dos dois
ambientes.

### Ambiente legado

- Continua usando `templates/header.php`, `templates/footer.php`, AdminLTE 3,
  Bootstrap 4, jQuery e `public/assets/css/style.css`.
- Nao recebe novas funcionalidades visuais, exceto correcoes criticas de
  regressao ou seguranca.
- Mantem URLs, formularios, JavaScript e comportamento existentes ate a rota
  ser formalmente migrada.
- Mantem a opcao de tema atual somente enquanto existirem paginas legadas.

### Ambiente novo

- Usa um layout proprio, por exemplo `app/Views/layouts/app.php`, baseado apenas
  em Bootstrap 5 e CSS autoral novo.
- Usa arquivos de estilo e script separados, por exemplo
  `public/assets/css/app-v2.css` e `public/assets/js/app-v2.js`.
- Nao importa `adminlte.css`, `adminlte.js`, o bundle Bootstrap 4 nem adaptadores
  com sufixo `bs4`.
- Nao depende de `main-sidebar`, `content-wrapper`, `small-box`, `treeview`,
  `pushmenu` ou outros widgets do AdminLTE.
- Usa `extend`, `section` e componentes/partials do CodeIgniter para evitar a
  composicao manual `header + view + footer` nas novas telas.
- Carrega assets localmente para continuar funcionando no XAMPP sem depender de
  CDN.
- Recebe um marcador inequivoco no elemento `body`, como `zv-app-v2`, para
  limitar os seletores autorais e impedir vazamento de CSS.

Controllers e regras de negocio podem ser compartilhados entre os ambientes,
mas cada resposta deve selecionar explicitamente apenas um layout. Uma rota so
deixa de ser legada depois de cumprir todos os criterios de aceite deste
documento.

## Layout Bootstrap 5 proposto

### Estrutura global

- Sidebar esquerda fixa em telas grandes e `offcanvas` em telas pequenas.
- Topo com acionador da sidebar, busca global, acao rapida `Novo`, central de
  alertas e menu do usuario.
- Area principal com titulo, breadcrumb curto, acoes primarias e conteudo.
- Rodape discreto, sem informacoes essenciais para a operacao.
- Um unico menu, gerado a partir de uma estrutura compartilhada e filtrado por
  permissao. A navegacao horizontal legada nao sera recriada.

### Navegacao alvo do MVP

1. Dashboard.
2. Comercial: Atendimentos, Orcamentos e Vendas.
3. Operacao: Ordens de Servico, Producao/Kanban, Artes e Instalacoes.
4. Cadastros: Clientes, Catalogo de Servicos/SKUs, Materias-primas/Estoque e
   Fornecedores.
5. Financeiro: Contas a receber, Contas a pagar, Caixa e Cobrancas.
6. Relatorios.
7. Configuracoes.

Modulos fora do novo escopo, como PDV e fiscal, nao devem ser migrados apenas
para conservar aparencia. A retirada funcional e de acesso deve ser decidida e
validada separadamente.

### Componentes compartilhados

Antes de migrar telas completas, devem existir componentes simples para:

- titulo da pagina e breadcrumb;
- botoes e grupos de acoes;
- mensagens flash e erros de validacao;
- badges de status com mapa central de cor e rotulo;
- filtros persistentes e estado vazio;
- tabela responsiva e paginacao;
- modal e `offcanvas`;
- campos monetarios, data, telefone e upload;
- confirmacao de acao destrutiva ou cancelamento;
- timeline/historico;
- cartao de OS usado na lista e no Kanban;
- indicador de carregamento e erro de requisicao.

Os componentes devem escapar conteudo por padrao e receber dados prontos. Nao
devem decidir transicoes de status, calcular valores ou consultar banco.

## Inventario de incompatibilidades

### AdminLTE e navegacao

AdminLTE 3 depende do Bootstrap 4. A troca isolada do arquivo
`bootstrap.bundle.js` quebra sidebar, dropdowns, modais e estilos do tema. Os
atributos `data-widget="pushmenu"` e `data-widget="treeview"`, assim como as
classes `has-treeview` e `nav-treeview`, sao exclusivos do AdminLTE e devem ser
substituidos por `offcanvas`, `collapse` e navegacao Bootstrap 5 ou JavaScript
autoral minimo.

### Modais, dropdowns, tabs e collapse

Todos os componentes interativos devem trocar os atributos `data-*` legados
pelos atributos `data-bs-*`. O botao de fechar antigo com classe `close` deve
virar `btn-close`. Depois da conversao, devem ser testados foco, tecla Escape,
backdrop, atributos ARIA e retorno do foco ao elemento acionador.

### Formularios

- `custom-file` e `custom-control` nao existem no Bootstrap 5.
- `input-group-append` e `input-group-prepend` deixam de envolver os elementos.
- `form-group` nao fornece mais o espacamento esperado; o novo layout deve usar
  utilitarios `mb-*` de forma explicita.
- Validacao deve usar classes e mensagens Bootstrap 5 sem retirar a validacao
  do servidor.
- Campos Select2 nao devem herdar o tema `select2-bootstrap4`.

### Utilitarios e componentes visuais

Utilitarios direcionais mudam de `left/right` para `start/end`. Classes como
`badge-primary`, `badge-success` e equivalentes devem ser revistas conforme a
API da versao Bootstrap 5 escolhida. `btn-block` deve ser substituido por
utilitario de largura. Componentes `small-box` e demais blocos AdminLTE devem
ser redesenhados com `card` e utilitarios Bootstrap 5.

### Plugins JavaScript

| Dependencia atual | Decisao para o ambiente novo |
|---|---|
| jQuery | Nao sera dependencia global. Pode permanecer somente no legado. |
| AdminLTE 3 | Nao sera carregado. Sidebar e topo serao componentes proprios. |
| Bootstrap 4 bundle | Substituir por bundle Bootstrap 5 fixado e local. |
| DataTables Bootstrap 4 | Preferir paginacao/filtro no servidor; se indispensavel, usar integracao Bootstrap 5 validada. |
| Select2 + tema Bootstrap 4 | Usar `select` nativo quando viavel; em listas grandes, validar integracao compativel e isolada. |
| Bootstrap Switch | Substituir por `form-switch`. |
| iCheck Bootstrap | Substituir por `form-check`. |
| SweetAlert2 tema Bootstrap 4 | Remover o tema BS4; usar dialogo nativo ou tema compativel validado. |
| InputMask jQuery | Manter no legado; no novo layout, usar JavaScript simples e validacao no servidor. |
| Chart.js | Pode ser reaproveitado apos teste isolado, pois nao depende do Bootstrap. |
| Font Awesome | Pode ser reaproveitado, desde que a versao seja fixada e os icones sejam revisados. |

### JavaScript global existente

O rodape legado hoje tambem:

- injeta CSRF em formularios e requisicoes jQuery;
- inicializa Select2 e DataTables;
- apresenta erros flash;
- cria formularios POST para confirmacoes;
- marca o menu ativo.

Essas responsabilidades devem ser reimplementadas de forma explicita no novo
layout. O helper de `fetch` deve enviar o cabecalho CSRF, tratar respostas de
erro e atualizar o token quando a configuracao da aplicacao exigir. O estado do
menu deve vir da rota/capacidade atual, nao de IDs numericos espalhados pelas
views.

## Ordem de migracao

1. **Congelar o inventario:** registrar rotas, screenshots, perfis e fluxos
   criticos do legado; recalcular as ocorrencias desta pagina.
2. **Fixar dependencias:** escolher e registrar a versao Bootstrap 5, obter os
   assets locais e documentar licencas. Nenhum asset antigo e sobrescrito.
3. **Criar a fundacao isolada:** layout novo, tokens de cor/espacamento,
   componentes basicos, CSRF, mensagens flash, menu por permissao e pagina de
   demonstracao sem substituir rota operacional.
4. **Executar um piloto de baixo risco:** migrar uma listagem somente de leitura
   ou o novo Catalogo de Servicos e validar convivencia entre layouts.
5. **Migrar cadastros do MVP:** Clientes e Catalogo de Servicos/SKUs, incluindo
   formularios e validacao responsiva.
6. **Migrar o fluxo comercial:** Atendimentos, Orcamentos e Vendas, preservando
   impressao e conversoes.
7. **Migrar a operacao:** OS em lista e detalhe, timeline, status independentes,
   Kanban, arte e instalacao.
8. **Migrar financeiro e alertas:** contas, caixa, cobrancas e central unificada
   de notificacoes conforme as permissoes.
9. **Migrar o dashboard:** somente depois que os novos status e agregados forem
   fontes confiaveis.
10. **Migrar telas administrativas restantes:** usuarios, configuracoes,
    relatorios e login. Telas descontinuadas nao sao portadas.
11. **Retirar o legado:** remover a opcao de tema horizontal, referencias BS4 e
    AdminLTE somente quando nenhuma rota ativa depender delas.

Cada passo deve ser pequeno, reversivel por commit e validado antes do passo
seguinte. Nao deve haver uma troca global de assets no inicio da migracao.

## Criterios de aceite por tela migrada

Uma tela so pode usar o novo layout quando todos os itens abaixo forem
atendidos:

- carrega Bootstrap 5 uma unica vez e nao carrega Bootstrap 4 ou AdminLTE;
- nao referencia adaptadores `bootstrap4`, `bs4`, `adminlte.js` ou
  `adminlte.css`;
- nao possui `data-toggle`, `data-dismiss`, `data-target`, `custom-file`,
  `custom-control`, `input-group-append`, `ml-*`, `mr-*`, `text-right`,
  `float-right`, `font-weight-bold` ou `btn-block`;
- nao apresenta erro ou aviso novo no console do navegador;
- nao produz requisicao de asset com erro HTTP;
- formularios GET e POST preservam valores, validacao, CSRF e mensagens de
  erro;
- acoes por `fetch` possuem estado de carregamento, tratamento de falha e
  alternativa segura quando JavaScript falha, quando aplicavel;
- links, botoes e acoes respeitam as permissoes do perfil autenticado;
- foco visivel, ordem de tabulacao, labels, ARIA e operacao por teclado foram
  verificados;
- layout foi verificado em larguras de 375, 768, 1024 e 1440 pixels;
- tabelas e Kanban nao causam rolagem horizontal da pagina inteira; quando a
  rolagem interna for necessaria, ela e visivel e utilizavel;
- datas aparecem em `dd/mm/aaaa`, valores em BRL e textos em portugues do
  Brasil;
- impressao/PDF continua legivel nas telas que oferecem esse recurso;
- dados dinamicos sao escapados e nenhuma regra critica depende apenas de
  JavaScript;
- a navegacao entre uma pagina nova e uma pagina ainda legada preserva sessao,
  tema permitido, mensagens e retorno esperado.

## Criterios para concluir a migracao global

- Todas as rotas ativas usam o layout Bootstrap 5 ou estao formalmente
  descontinuadas.
- Nao existem inclusoes ativas de Bootstrap 4, AdminLTE 3 ou temas `bs4` nas
  views da aplicacao.
- Existe apenas uma implementacao de sidebar/topo/menu.
- O valor legado de tema nao seleciona mais estruturas HTML diferentes.
- A busca por marcadores incompatíveis retorna zero nas views ativas.
- Os fluxos Clientes -> Atendimento -> Orcamento -> Venda -> OS -> Producao ->
  Entrega/Instalacao -> Financeiro passam pelo teste funcional acordado.
- Administrador/socio, atendente e designer foram testados separadamente.
- A interface funciona no Apache/PHP do XAMPP sem CDN e sem processo Node em
  producao.
- O layout legado e seus assets so sao removidos depois de backup, inventario de
  referencias e validacao final.

## Comandos de auditoria sugeridos

Executar a partir da raiz do projeto antes de cada marco:

```powershell
rg -n "data-(toggle|dismiss|target)" app/Views
rg -n "custom-file|custom-control|input-group-(append|prepend)" app/Views
rg -n "ml-|mr-|pl-|pr-|text-right|float-right|font-weight-bold|btn-block" app/Views
rg -n "theme/plugins|theme/dist|bootstrap4|bs4|adminlte" app/Views
rg -n "main-sidebar|content-wrapper|small-box|treeview|pushmenu" app/Views public/assets
```

Resultados devem ser classificados entre paginas legadas ainda autorizadas e
paginas migradas. Durante a convivencia, ocorrencias no legado sao esperadas;
em uma pagina declarada como Bootstrap 5, qualquer ocorrencia e falha de aceite.

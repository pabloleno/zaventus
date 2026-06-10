<?php

/**
 * Audita e, opcionalmente, documenta funcoes nomeadas do codigo autoral.
 *
 * Uso:
 *   php tools/documentation_coverage.php
 *   php tools/documentation_coverage.php --write
 *
 * O escopo exclui o framework, bibliotecas de terceiros e assets externos.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$refresh = in_array('--refresh', $argv, true);
$write = in_array('--write', $argv, true) || $refresh;

$phpFiles = filesFrom($root . '/app', static function (string $path): bool {
    return str_ends_with($path, '.php')
        && ! str_contains($path, '/ThirdParty/')
        && ! str_contains($path, '/Views/');
});

$javascriptFiles = array_merge(
    filesFrom($root . '/app/Views', static fn (string $path): bool => str_ends_with($path, '.php')),
    filesFrom($root . '/public/assets/js', static fn (string $path): bool => str_ends_with($path, '.js'))
);

$statistics = [
    'php' => ['total' => 0, 'documented' => 0, 'changed' => 0],
    'javascript' => ['total' => 0, 'documented' => 0, 'changed' => 0],
];
$missing = [];

foreach ($phpFiles as $path) {
    auditFile($path, 'php', $write, $refresh, $statistics, $missing);
}

foreach ($javascriptFiles as $path) {
    auditFile($path, 'javascript', $write, $refresh, $statistics, $missing);
}

foreach ($statistics as $language => $values) {
    printf(
        "%s: %d/%d documentadas; %d arquivo(s) alterado(s).\n",
        strtoupper($language),
        $values['documented'],
        $values['total'],
        $values['changed']
    );
}

if ($missing !== []) {
    echo "\nFuncoes sem documentacao:\n";

    foreach ($missing as $item) {
        echo "- {$item}\n";
    }

    exit(1);
}

exit(0);

/**
 * Localiza recursivamente os arquivos que pertencem ao escopo informado.
 *
 * @return list<string>
 */
function filesFrom(string $directory, callable $accept): array
{
    if (! is_dir($directory)) {
        return [];
    }

    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }

        $path = str_replace('\\', '/', $file->getPathname());

        if ($accept($path)) {
            $files[] = $path;
        }
    }

    sort($files);

    return $files;
}

/**
 * Verifica a cobertura de um arquivo e grava os comentarios ausentes quando solicitado.
 *
 * @param array<string, array<string, int>> $statistics
 * @param list<string>                      $missing
 */
function auditFile(string $path, string $language, bool $write, bool $refresh, array &$statistics, array &$missing): void
{
    $contents = file_get_contents($path);

    if ($contents === false) {
        throw new RuntimeException("Nao foi possivel ler {$path}.");
    }

    $newline = str_contains($contents, "\r\n") ? "\r\n" : "\n";
    $pattern = $language === 'php'
        ? '/^([ \t]*)(?:(?:final|abstract|public|protected|private|static)\s+)*function\s+&?\s*([A-Za-z_][A-Za-z0-9_]*)\s*\(/m'
        : '/^([ \t]*)function\s+([A-Za-z_][A-Za-z0-9_]*)\s*\(/m';

    preg_match_all($pattern, $contents, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $changed = false;

    foreach (array_reverse($matches) as $match) {
        $statistics[$language]['total']++;

        $declarationOffset = $match[0][1];
        $indent = $match[1][0];
        $name = $match[2][0];
        $summary = summaryFor($name, $path, $language);
        $docblock = immediateDocblock($contents, $declarationOffset);

        if ($docblock !== null) {
            $statistics[$language]['documented']++;

            if ($write && ! docblockHasSummary($docblock['text'])) {
                $replacement = addSummaryToDocblock($docblock['text'], $summary, $indent, $newline);
                $contents = substr_replace($contents, $replacement, $docblock['offset'], strlen($docblock['text']));
                $changed = true;
            } elseif ($refresh && docblockHasGenericSummary($docblock['text'])) {
                $replacement = replaceDocblockSummary($docblock['text'], $summary);
                $contents = substr_replace($contents, $replacement, $docblock['offset'], strlen($docblock['text']));
                $changed = $replacement !== $docblock['text'] || $changed;
            } elseif ($refresh && docblockIsSingleSummary($docblock['text'])) {
                $replacement = singleSummaryDocblock($summary, $indent, $newline);
                $contents = substr_replace($contents, $replacement, $docblock['offset'], strlen($docblock['text']));
                $changed = $replacement !== $docblock['text'] || $changed;
            }

            continue;
        }

        if (! $write) {
            $relative = str_replace(str_replace('\\', '/', dirname(__DIR__)) . '/', '', $path);
            $line = substr_count(substr($contents, 0, $declarationOffset), "\n") + 1;
            $missing[] = "{$relative}:{$line} {$name}()";
            continue;
        }

        $comment = $indent . '/**' . $newline
            . $indent . ' * ' . $summary . $newline
            . $indent . ' */' . $newline;

        $contents = substr_replace($contents, $comment, $declarationOffset, 0);
        $statistics[$language]['documented']++;
        $changed = true;
    }

    if ($write && $changed) {
        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException("Nao foi possivel atualizar {$path}.");
        }

        $statistics[$language]['changed']++;
    }
}

/**
 * Retorna o PHPDoc/JSDoc imediatamente anterior a declaracao.
 *
 * @return array{offset:int, text:string}|null
 */
function immediateDocblock(string $contents, int $declarationOffset): ?array
{
    $before = substr($contents, 0, $declarationOffset);
    $trimmed = rtrim($before);

    if (! str_ends_with($trimmed, '*/')) {
        return null;
    }

    $open = strrpos($trimmed, '/**');
    $close = strrpos($trimmed, '*/');

    if ($open === false || $close === false || $open > $close) {
        return null;
    }

    return [
        'offset' => $open,
        'text' => substr($trimmed, $open, ($close + 2) - $open),
    ];
}

/**
 * Informa se o bloco possui uma descricao alem das tags de metadados.
 */
function docblockHasSummary(string $docblock): bool
{
    $lines = preg_split('/\R/', $docblock) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        $line = trim($line, "/* \t");

        if ($line !== '' && ! str_starts_with($line, '@')) {
            return true;
        }
    }

    return false;
}

/**
 * Informa se o bloco contem apenas uma descricao curta, sem tags.
 */
function docblockIsSingleSummary(string $docblock): bool
{
    $descriptions = [];

    foreach (preg_split('/\R/', $docblock) ?: [] as $line) {
        $line = trim(trim($line), "/* \t");

        if ($line === '') {
            continue;
        }

        if (str_starts_with($line, '@')) {
            return false;
        }

        $descriptions[] = $line;
    }

    return count($descriptions) === 1;
}

/**
 * Informa se o bloco ainda contem uma descricao generica criada pelo utilitario.
 */
function docblockHasGenericSummary(string $docblock): bool
{
    return preg_match('/^\s*\*\s+Executa a (acao|regra|etapa) de /m', $docblock) === 1;
}

/**
 * Substitui a primeira descricao do bloco sem remover tags PHPDoc existentes.
 */
function replaceDocblockSummary(string $docblock, string $summary): string
{
    return preg_replace(
        '/^(\s*\*\s+)(?!@)(.+)$/m',
        '$1' . $summary,
        $docblock,
        1
    ) ?? $docblock;
}

/**
 * Monta um bloco de documentacao contendo uma unica descricao.
 */
function singleSummaryDocblock(string $summary, string $indent, string $newline): string
{
    return '/**' . $newline
        . $indent . ' * ' . $summary . $newline
        . $indent . ' */';
}

/**
 * Acrescenta uma descricao ao inicio de um bloco que continha apenas tags.
 */
function addSummaryToDocblock(string $docblock, string $summary, string $indent, string $newline): string
{
    $position = strpos($docblock, $newline);

    if ($position === false) {
        return '/**' . $newline . $indent . ' * ' . $summary . $newline . $indent . ' */';
    }

    return substr_replace(
        $docblock,
        $newline . $indent . ' * ' . $summary,
        $position,
        0
    );
}

/**
 * Produz uma descricao curta baseada na responsabilidade e no nome da funcao.
 */
function summaryFor(string $name, string $path, string $language): string
{
    $exact = [
        '__construct' => 'Inicializa as dependencias usadas por este componente.',
        'after' => 'Mantem o ponto de extensao executado depois da requisicao.',
        'aplicaFiltros' => 'Aplica os filtros informados pelo usuario na consulta da listagem.',
        'abrir' => 'Abre um novo caixa para registrar as movimentacoes do operador.',
        'autenticar' => 'Valida as credenciais e inicia a sessao do usuario.',
        'before' => 'Valida e prepara a requisicao antes de ela chegar ao controller.',
        'buscarCep' => 'Consulta o CEP informado e preenche os campos de endereco disponiveis.',
        'campoMonetario' => 'Informa se o campo representa um valor monetario.',
        'campoAnterior' => 'Consulta a definicao anterior de uma coluna antes de altera-la.',
        'campoCnpj' => 'Informa se o nome identifica um campo de CNPJ.',
        'campoData' => 'Informa se o nome identifica um campo de data.',
        'campoEmail' => 'Informa se o nome identifica um campo de e-mail.',
        'campoTelefone' => 'Informa se o nome identifica um campo de telefone.',
        'campoTelefoneFixo' => 'Informa se o nome identifica especificamente um telefone fixo.',
        'campoTextoCurto' => 'Informa se o campo deve respeitar o limite de texto curto.',
        'cancelar' => 'Cancela o documento fiscal solicitado e registra o retorno da SEFAZ.',
        'carregarCidades' => 'Carrega as cidades da UF selecionada e restaura a selecao anterior.',
        'codigoFiscalFormaPagamento' => 'Converte a forma de pagamento no codigo exigido pelo documento fiscal.',
        'configPermission' => 'Define a permissao exigida para uma acao de configuracao.',
        'configParecePadraoAntigo' => 'Informa se a configuracao fiscal ainda possui os valores padrao legados.',
        'contasPendentes' => 'Consulta contas a pagar e receber que ainda exigem liquidacao.',
        'create' => 'Prepara os dados e exibe o formulario de cadastro.',
        'create_1' => 'Prepara e exibe a etapa complementar de criacao do inventario.',
        'createFormaDePagamento' => 'Prepara e exibe o formulario de uma nova forma de pagamento.',
        'csosnProduto' => 'Normaliza o CSOSN utilizado na emissao fiscal do produto.',
        'dataHoraDaLinha' => 'Converte os valores de data e hora da linha em um instante comparavel.',
        'dataInicio' => 'Define a data inicial usada na vinculacao do registro.',
        'dadosFormulario' => 'Monta os dados compartilhados pelo formulario de cadastro e edicao.',
        'dataVendaOs' => 'Define a data usada para registrar a venda das pecas da ordem.',
        'decimal' => 'Converte um valor monetario para a representacao decimal persistida.',
        'decimalMonetario' => 'Converte um valor monetario da interface para decimal.',
        'decimal_monetario' => 'Converte um valor monetario para a representacao decimal persistida.',
        'delete' => 'Remove o registro solicitado e retorna para a listagem.',
        'doughnutFinanceiro' => 'Monta um grafico de composicao financeira para o conjunto informado.',
        'down' => 'Reverte as alteracoes de banco aplicadas por esta migration.',
        'edit' => 'Carrega o registro solicitado e exibe o formulario de edicao.',
        'editFormaDePagamento' => 'Carrega e exibe a forma de pagamento solicitada para edicao.',
        'editProduto' => 'Carrega e exibe o produto do inventario para edicao.',
        'editDadosResponsaveis_e_DadosFinaisOrdemDeServico' => 'Atualiza responsaveis e dados finais de uma ordem de servico.',
        'email' => 'Retorna o primeiro e-mail disponivel no cadastro.',
        'emailLink' => 'Normaliza o e-mail para uso seguro em um link.',
        'ehCep' => 'Informa se o nome identifica um campo de CEP.',
        'ehCnpj' => 'Informa se o nome identifica um campo de CNPJ.',
        'ehCpf' => 'Informa se o nome identifica um campo de CPF.',
        'exibirErro' => 'Exibe uma mensagem de erro de validacao na interface.',
        'faturamentoMensal' => 'Agrupa o faturamento de produtos e servicos por mes.',
        'fechar' => 'Fecha o caixa informado depois de validar seus valores finais.',
        'filtros' => 'Le e normaliza os filtros informados na requisicao.',
        'financeiroPorTipo' => 'Agrupa contas abertas e vencidas por tipo de negocio.',
        'formaDePagamentoVendaOs' => 'Identifica a forma de pagamento usada na venda das pecas da ordem.',
        'format' => 'Formata o valor recebido conforme a configuracao da aplicacao.',
        'formataCampoDecimalOs' => 'Formata um campo monetario da ordem de servico para exibicao.',
        'formataDecimalOs' => 'Formata um valor decimal da ordem de servico para exibicao.',
        'formataMoeda' => 'Formata um valor monetario para uso no documento fiscal ou cupom.',
        'formatar' => 'Formata o valor para exibicao conforme o padrao monetario.',
        'horaVendaOs' => 'Define a hora usada para registrar a venda das pecas da ordem.',
        'identificarCamposTemporais' => 'Identifica as colunas de data e hora usadas pelo filtro da tabela.',
        'index' => 'Carrega os dados e exibe a tela principal deste modulo.',
        'inicializa' => 'Inicializa os comportamentos compartilhados da interface.',
        'inicializar' => 'Inicializa os comportamentos compartilhados da interface.',
        'initController' => 'Inicializa os recursos compartilhados pelos controllers da aplicacao.',
        'isBlockedMethod' => 'Informa se o metodo nao pode ser acessado como endpoint.',
        'isPublicRoute' => 'Informa se a rota pode ser acessada sem sessao autenticada.',
        'label' => 'Retorna o rotulo legivel usado nas mensagens de validacao.',
        'limparCidade' => 'Remove complementos do texto de cidade retornado pelo servico de CEP.',
        'limitar' => 'Limita um texto ao tamanho maximo informado.',
        'listaProdutos' => 'Lista os produtos vinculados ao inventario selecionado.',
        'loadWithoutPsrLog' => 'Carrega uma dependencia de terceiros sem substituir a implementacao de log da aplicacao.',
        'longestFirst' => 'Ordena traducoes da mais longa para a mais curta para evitar substituicoes parciais.',
        'logout' => 'Encerra a sessao autenticada e retorna para o login.',
        'marcarConfigurado' => 'Marca o campo para impedir que o comportamento seja configurado novamente.',
        'marcarFuncionariosComCargoVendedor' => 'Marca funcionarios com cargo de vendedor antes de criar os vinculos.',
        'media' => 'Calcula uma media protegendo a divisao por zero.',
        'moeda' => 'Formata um valor monetario para exibicao.',
        'montar' => 'Monta o conjunto completo de indicadores da dashboard.',
        'movimentacaoPorTipo' => 'Agrupa lancamentos e despesas do periodo por tipo de negocio.',
        'municipiosPorUf' => 'Lista os municipios pertencentes a UF informada.',
        'municipioPorCodigo' => 'Localiza um municipio pelo codigo IBGE informado.',
        'nomeCampo' => 'Obtem o identificador normalizado de um campo da interface.',
        'numeroMonetario' => 'Converte a entrada monetaria da interface em numero.',
        'normalizar' => 'Normaliza os dados recebidos para o formato esperado pela aplicacao.',
        'normalizaTotaisDaOrdem' => 'Normaliza frete, outros valores e desconto da ordem de servico.',
        'observarCamposNovos' => 'Observa campos inseridos dinamicamente e aplica a configuracao padrao.',
        'ordensDeServicosDaListagem' => 'Consulta as ordens de servico exibidas na listagem.',
        'ordensServicos' => 'Consulta ordens concretizadas e calcula o faturamento de servicos do periodo.',
        'orcamentos' => 'Carrega e exibe os orcamentos de ordens de servico.',
        'partesDataHora' => 'Separa e normaliza as partes de um valor de data e hora.',
        'percentual' => 'Calcula um percentual protegendo a divisao por zero.',
        'prepara_campos_padrao' => 'Valida e normaliza campos compartilhados antes da persistencia.',
        'preparar' => 'Valida e normaliza os dados antes da persistencia.',
        'primeiroValor' => 'Retorna o primeiro valor preenchido entre os campos informados.',
        'productPermission' => 'Define a permissao exigida para uma acao relacionada a produtos.',
        'produtoEstoqueDaPecaOs' => 'Localiza o produto de estoque vinculado a peca da ordem de servico.',
        'redireciona_erros_campos_padrao' => 'Retorna ao formulario preservando os dados e exibindo os erros de validacao.',
        'reabrir' => 'Reabre o caixa informado para permitir novas movimentacoes.',
        'reemitir' => 'Reemite o documento fiscal solicitado e registra o retorno da SEFAZ.',
        'registrarVendaDosProdutosDaOs' => 'Registra como venda os produtos e pecas usados na ordem de servico.',
        'reportPermission' => 'Define a permissao exigida para acessar um relatorio.',
        'requiredPermission' => 'Determina a permissao exigida para a rota solicitada.',
        'reposicao_por_xml' => 'Prepara a reposicao de estoque a partir dos produtos encontrados no XML.',
        'resumo' => 'Monta o resumo consolidado de faturamento para o periodo e tipo de negocio.',
        'resumoDiarioMes' => 'Agrupa diariamente o faturamento do mes informado.',
        'rotaListagemDaSituacao' => 'Define a listagem adequada para a situacao atual da ordem de servico.',
        'rotulo' => 'Retorna o rotulo legivel do tipo de negocio.',
        'routeParts' => 'Extrai o controller e o metodo solicitados a partir da URL.',
        'run' => 'Executa a carga de dados definida por este seeder.',
        'saidaDaOrdemRegistrada' => 'Informa se a data e a hora de saida da ordem ja foram registradas.',
        'selecionarCidade' => 'Seleciona a cidade correspondente ao codigo e nome informados.',
        'senhaConfere' => 'Compara a senha informada com o hash armazenado.',
        'senhaPrecisaAtualizar' => 'Informa se o hash da senha deve ser atualizado.',
        'salvar' => 'Valida e armazena a imagem enviada para o cadastro.',
        'settings' => 'Carrega as configuracoes globais da empresa com valores padrao seguros.',
        'show' => 'Carrega e exibe os detalhes do registro solicitado.',
        'shouldTranslate' => 'Informa se a resposta atual pode ser traduzida.',
        'shouldTranslateScriptString' => 'Informa se um texto de script pode ser traduzido com seguranca.',
        'start' => 'Prepara e exibe a etapa inicial do fluxo.',
        'statusConta' => 'Determina se uma conta deve ser exibida como aberta ou vencida.',
        'store' => 'Valida e persiste os dados enviados pelo formulario.',
        'store_produto' => 'Valida e persiste o produto vinculado ao inventario.',
        'tipoFinalizacaoPdv' => 'Define o fluxo de finalizacao configurado para o PDV.',
        'tipoFuncionario' => 'Normaliza o tipo de atuacao selecionado para o funcionario.',
        'tipoPedido' => 'Prepara os dados necessarios para transformar o fluxo em pedido.',
        'tipoVenda' => 'Prepara os dados necessarios para transformar o fluxo em venda.',
        'telefone' => 'Retorna o primeiro telefone disponivel no cadastro.',
        'telefoneLink' => 'Normaliza o telefone para uso seguro em um link.',
        'tipoNegocio' => 'Normaliza o tipo de negocio usado nos indicadores financeiros.',
        'tooltipMoeda' => 'Formata valores monetarios exibidos nas dicas dos graficos.',
        'ufs' => 'Retorna a lista de unidades federativas disponiveis.',
        'ufPorCodigoMunicipio' => 'Extrai a UF correspondente a partir do codigo IBGE do municipio.',
        'up' => 'Aplica as alteracoes de banco definidas por esta migration.',
        'updateStack' => 'Atualiza a pilha de tags usada durante a traducao do HTML.',
        'url' => 'Monta a URL publica da imagem cadastrada.',
        'validar' => 'Valida os dados recebidos e retorna os erros encontrados.',
        'validLanguage' => 'Informa se o idioma esta entre as opcoes suportadas.',
        'validTimezone' => 'Informa se o fuso horario esta entre as opcoes suportadas.',
        'valorDecimalDoCampo' => 'Le um campo monetario da ordem e retorna seu valor decimal.',
        'valorDecimalOs' => 'Converte uma entrada monetaria da ordem de servico para decimal.',
        'valorNumerico' => 'Converte uma entrada monetaria em numero decimal.',
        'valorString' => 'Converte valores escalares ou convertiveis em texto para validacao.',
        'valorTotalDaOrdem' => 'Calcula o total da ordem considerando servicos, frete, outros valores e desconto.',
        'vendasProdutos' => 'Consulta as vendas de produtos do periodo, respeitando os filtros informados.',
        'whatsapp' => 'Retorna o primeiro WhatsApp disponivel no cadastro.',
        'whatsappLink' => 'Normaliza o WhatsApp para uso seguro em um link.',
        'digitos' => 'Remove todos os caracteres que nao sejam digitos.',
        'somenteDigitos' => 'Remove todos os caracteres que nao sejam digitos.',
        'tamanho' => 'Calcula o tamanho do texto com suporte a caracteres multibyte.',
        'dataValida' => 'Informa se o valor representa uma data de calendario valida.',
        'hasPermission' => 'Informa se o usuario possui a permissao exigida para a rota.',
        'idiomaValido' => 'Informa se o idioma selecionado e suportado.',
        'fusoHorarioValido' => 'Informa se o fuso horario selecionado e suportado.',
        'fusosHorarios' => 'Retorna os fusos horarios disponiveis para configuracao.',
        'rotuloFusoHorario' => 'Monta o rotulo legivel de um fuso horario.',
        'nfce' => 'Carrega os dados necessarios para a operacao com NFC-e.',
        'nfe' => 'Carrega os dados necessarios para a operacao com NFe.',
        'mostraErro' => 'Exibe a mensagem retornada pela operacao fiscal.',
        'mostraErroCamposObr' => 'Exibe os campos obrigatorios ausentes na operacao fiscal.',
        'showErroNFCe' => 'Exibe os detalhes do erro registrado na NFC-e.',
        'showErroNFe' => 'Exibe os detalhes do erro registrado na NFe.',
        'nomeClienteParaCupom' => 'Define o nome do cliente exibido no cupom nao fiscal.',
        'telefoneEmpresaParaCupom' => 'Define o telefone da empresa exibido no cupom nao fiscal.',
        'pesquisar' => 'Pesquisa produtos por nome ou codigo para uso no fluxo atual.',
        'provisorio_add_produtos_por_xml' => 'Lista os produtos importados provisoriamente de um XML.',
        'provisorio_reposicao_produtos_por_xml' => 'Lista os produtos preparados provisoriamente para reposicao via XML.',
        'salvarImagemPersonalizacao' => 'Valida e armazena uma imagem de personalizacao da empresa.',
        'redirecionarErroPersonalizacao' => 'Retorna ao formulario exibindo o erro de personalizacao encontrado.',
        'usuarios' => 'Carrega e exibe os usuarios e suas permissoes.',
        'configSistema' => 'Carrega as opcoes globais usadas pela configuracao do sistema.',
        'empresa' => 'Carrega e exibe os dados configurados da empresa.',
        'sistema' => 'Carrega e exibe as configuracoes globais do sistema.',
        'alteraSituacaoDaOS' => 'Atualiza a situacao da ordem de servico selecionada.',
        'atualizaTotalOs' => 'Recalcula o total da ordem de servico na interface.',
        'calculaPagamentoAVista' => 'Registra o pagamento integral a vista da ordem de servico.',
        'calculaPagamentoAVistaEdit' => 'Atualiza o pagamento integral a vista durante a edicao da ordem.',
        'calculaParcelasOs' => 'Calcula e registra as parcelas da ordem de servico.',
        'calculaParcelasOsEdit' => 'Recalcula as parcelas durante a edicao da ordem de servico.',
        'emiteNFCe' => 'Inicia a emissao da NFC-e para a venda selecionada.',
        'emiteNFe' => 'Inicia a emissao da NFe para a venda selecionada.',
        'finalizaVendaEmiteNFCe' => 'Finaliza a venda do PDV e inicia a emissao da NFC-e.',
        'finalizarOuEditarOdemDeServicos' => 'Normaliza os totais e envia o formulario de finalizacao ou edicao da ordem.',
        'opcoesPadrao' => 'Monta as opcoes compartilhadas das tabelas de listagem.',
        'quantidadeOsAbertas' => 'Calcula a quantidade de ordens de servico em aberto.',
    ];

    if (isset($exact[$name])) {
        return $exact[$name];
    }

    $words = humanize($name);
    $prefixes = [
        'add' => 'Adiciona ',
        'adiciona' => 'Adiciona ',
        'adicionar' => 'Adiciona ',
        'altera' => 'Atualiza ',
        'aplica' => 'Aplica ',
        'aplicar' => 'Aplica ',
        'atualiza' => 'Atualiza ',
        'backup' => 'Gera o backup de ',
        'baixa' => 'Prepara o download de ',
        'buscar' => 'Busca ',
        'calcula' => 'Calcula ',
        'carregar' => 'Carrega ',
        'configura' => 'Configura ',
        'configurar' => 'Configura ',
        'criar' => 'Cria ',
        'dados' => 'Monta os dados de ',
        'delete' => 'Remove ',
        'descricao' => 'Retorna a descricao de ',
        'eh' => 'Informa se atende a regra de ',
        'emite' => 'Emite ',
        'endereco' => 'Monta o endereco de ',
        'escape' => 'Escapa o conteudo de ',
        'escapa' => 'Escapa o conteudo de ',
        'finaliza' => 'Finaliza ',
        'finalizar' => 'Finaliza ',
        'garantir' => 'Garante a existencia de ',
        'has' => 'Informa se possui ',
        'is' => 'Informa se atende a regra de ',
        'limpar' => 'Limpa e normaliza ',
        'monta' => 'Monta ',
        'montar' => 'Monta ',
        'normaliza' => 'Normaliza ',
        'normalizar' => 'Normaliza ',
        'ocultar' => 'Oculta ',
        'opcoes' => 'Retorna as opcoes de ',
        'prepara' => 'Prepara ',
        'preparar' => 'Prepara ',
        'quantidade' => 'Calcula a quantidade de ',
        'registrar' => 'Registra ',
        'registra' => 'Registra ',
        'remove' => 'Remove ',
        'remover' => 'Remove ',
        'resumo' => 'Monta o resumo de ',
        'sincronizar' => 'Sincroniza ',
        'store' => 'Valida e persiste ',
        'tem' => 'Informa se existe ',
        'total' => 'Calcula o total de ',
        'translate' => 'Traduz ',
        'validar' => 'Valida ',
        'verifica' => 'Verifica ',
        'vincular' => 'Vincula ',
    ];

    uksort($prefixes, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));

    foreach ($prefixes as $prefix => $action) {
        if (str_starts_with(strtolower($name), $prefix)) {
            $subject = humanize(substr($name, strlen($prefix)));

            if (str_ends_with($subject, ' edit')) {
                $subject = substr($subject, 0, -5) . ' durante a edicao';
            }

            return $action . ($subject !== '' ? $subject : $words) . '.';
        }
    }

    if ($language === 'javascript') {
        return 'Controla a interacao de ' . $words . ' na interface.';
    }

    if (str_ends_with($path, '/Controllers/Relatorios.php')) {
        return 'Monta e exibe o relatorio de ' . $words . '.';
    }

    if (str_contains($path, '/Controllers/')) {
        return 'Executa a acao de ' . $words . ' deste modulo.';
    }

    if (str_contains($path, '/Models/')) {
        return 'Executa a consulta ou persistencia de ' . $words . '.';
    }

    return 'Executa a regra de ' . $words . ' deste componente.';
}

/**
 * Converte camelCase e snake_case em uma frase legivel.
 */
function humanize(string $name): string
{
    $name = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $name) ?? $name;
    $name = str_replace('_', ' ', $name);

    return strtolower(trim((string) preg_replace('/\s+/', ' ', $name)));
}

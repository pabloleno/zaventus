<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\Files\UploadedFile;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class AnexoAtendimento
{
    public const CATEGORIAS = ['cliente' => 'Arquivo do cliente', 'arte' => 'Arte', 'local' => 'Foto do local', 'producao' => 'Produção', 'instalacao' => 'Instalação', 'concluido' => 'Serviço concluído'];
    private const MAX_BYTES = 20 * 1024 * 1024;
    private BaseConnection $db;

    public function __construct(?BaseConnection $db = null)
    {
        $this->db = $db ?? db_connect();
    }

    public function salvar(int $id, ?UploadedFile $file, array $dados, int $user): int
    {
        // O arquivo só pode ser confirmado junto à transação que este método encerra.
        if ($this->db->transDepth > 0) {
            throw new RuntimeException('Salve o anexo em uma operação própria, após concluir a transação anterior.');
        }
        if ($id <= 0 || $user <= 0) {
            throw new InvalidArgumentException('Selecione um atendimento e um usuário válidos.');
        }
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            throw new InvalidArgumentException('Selecione um arquivo PDF, JPG, PNG ou WEBP de até 20 MB.');
        }
        $size = filesize($file->getTempName());
        if ($size === false || $size <= 0 || $size > self::MAX_BYTES) {
            throw new InvalidArgumentException('Selecione um arquivo PDF, JPG, PNG ou WEBP de até 20 MB.');
        }
        if (! extension_loaded('fileinfo')) {
            throw new RuntimeException('A extensão Fileinfo precisa estar habilitada para validar anexos.');
        }
        $mimes = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = $file->getMimeType();
        $ext = strtolower($file->getClientExtension());
        if (! isset($mimes[$mime]) || ! in_array($ext, $mime === 'image/jpeg' ? ['jpg', 'jpeg'] : [$mimes[$mime]], true)) {
            throw new InvalidArgumentException('Formato de arquivo inválido. Use PDF, JPG, PNG ou WEBP.');
        }
        if ($mime !== 'application/pdf' && @getimagesize($file->getTempName()) === false) {
            throw new InvalidArgumentException('A imagem não pôde ser lida.');
        }
        if ($mime === 'application/pdf' && file_get_contents($file->getTempName(), false, null, 0, 5) !== '%PDF-') {
            throw new InvalidArgumentException('O PDF não pôde ser lido.');
        }
        $categoria = $dados['categoria'] ?? 'cliente';
        if (! is_string($categoria) || ! isset(self::CATEGORIAS[$categoria])) {
            throw new InvalidArgumentException('Selecione a categoria do arquivo.');
        }
        $nome = mb_substr(basename(str_replace('\\', '/', $file->getClientName())), 0, 240);
        $nome = preg_replace('/[\x00-\x1F\x7F]/u', '', $nome);
        if ($nome === null || trim($nome) === '') {
            throw new InvalidArgumentException('O nome do arquivo não é válido.');
        }
        $item = $dados['id_servico_os'] ?? null;
        if ($item === null || $item === '' || $item === 0 || $item === '0') {
            $item = null;
        } elseif (filter_var($item, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 2147483647]]) === false) {
            throw new InvalidArgumentException('Selecione um serviço válido deste atendimento.');
        } else {
            $item = (int) $item;
        }
        $relative = 'uploads/atendimentos/' . $id . '/' . bin2hex(random_bytes(24)) . '.' . $mimes[$mime];
        if (! $this->db->transStatus()) {
            throw new RuntimeException('Desfaça a transação anterior antes de anexar um arquivo.');
        }
        if (! $this->db->transBegin()) {
            throw new RuntimeException('Não foi possível iniciar o registro do anexo.');
        }
        $moved = false;
        try {
            $sql = $this->db->table('ordens_de_servicos')->where('id_ordem', $id)->getCompiledSelect();
            $result = $this->db->query($sql . ($this->db->getPlatform() === 'SQLite3' ? '' : ' FOR UPDATE'));
            if ($result === false) {
                throw new RuntimeException('Não foi possível consultar o atendimento. Tente novamente.');
            }
            $os = $result->getRowArray();
            if (! $os || (! empty($os['deleted_at']) && $os['deleted_at'] !== '0000-00-00 00:00:00') || AtendimentoGrafica::status($os) === 'cancelado') {
                throw new InvalidArgumentException('Restaure ou reabra o atendimento para anexar arquivos.');
            }
            if (! $this->db->table('login')->where('id_login', $user)->countAllResults()) {
                throw new InvalidArgumentException('Entre novamente no sistema.');
            }
            if ($item !== null && ! $this->db->table('servicos_mao_de_obra_da_os')->where('id_ordem', $id)->where('id_servico', $item)
                ->where('removido_at', null)->where('deleted_at', null)->countAllResults()) {
                throw new InvalidArgumentException('Selecione um serviço deste atendimento.');
            }
            $directory = self::diretorio($id);
            if (! $file->move($directory, basename($relative))) {
                throw new RuntimeException('Não foi possível armazenar o arquivo enviado.');
            }
            $moved = true;
            if (self::caminho($relative) === null) {
                throw new RuntimeException('O arquivo não foi encontrado no armazenamento privado.');
            }
            $ok = $this->db->table('anexos_os')->insert([
                'id_ordem' => $id, 'id_servico_os' => $item, 'categoria' => $categoria, 'nome' => $nome,
                'arquivo' => $relative, 'mime' => $mime, 'tamanho' => $size, 'created_by' => $user, 'created_at' => date('Y-m-d H:i:s'),
            ]);
            $idAnexo = (int) $this->db->insertID();
            $ok = $ok && $this->db->table('ordens_de_servicos_historico')->insert([
                'id_ordem' => $id, 'evento' => 'anexado', 'id_login' => $user, 'observacoes' => self::CATEGORIAS[$categoria] . ': ' . $nome, 'created_at' => date('Y-m-d H:i:s'),
                'status_anterior' => AtendimentoGrafica::status($os), 'status_novo' => AtendimentoGrafica::status($os),
            ]);
            if (! $ok || ! $this->db->transStatus()) {
                throw new RuntimeException('Não foi possível registrar o arquivo. Tente novamente.');
            }
            if (! $this->db->transCommit()) {
                throw new RuntimeException('Não foi possível concluir o registro do arquivo.');
            }
            return $idAnexo;
        } catch (Throwable $e) {
            try {
                $this->db->transRollback();
                $this->db->resetTransStatus();
            } finally {
                if ($moved || is_file(WRITEPATH . $relative)) {
                    self::removerArquivo($relative);
                }
            }
            throw $e;
        }
    }

    public function obter(int $id): array
    {
        $anexo = $this->db->table('anexos_os a')->select('a.*')->join('ordens_de_servicos os', 'os.id_ordem = a.id_ordem')->where('a.id_anexo', $id)->where('os.deleted_at', null)->get()->getRowArray();
        if (! $anexo || ! str_starts_with($anexo['arquivo'], 'uploads/atendimentos/' . $anexo['id_ordem'] . '/') || self::caminho($anexo['arquivo']) === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Arquivo não encontrado.');
        }
        return $anexo;
    }

    private static function caminho(string $relative): ?string
    {
        if (! preg_match('#\Auploads/atendimentos/[1-9][0-9]*/[a-f0-9]{48}\.(pdf|jpg|png|webp)\z#', $relative)) {
            return null;
        }
        $writable = realpath(WRITEPATH);
        $base = realpath(WRITEPATH . 'uploads/atendimentos');
        $path = realpath(WRITEPATH . $relative);
        if ($writable === false || $base === false || $path === false || ! is_file($path)
            || ! self::dentro($base, $writable) || ! self::dentro($path, $base)) {
            return null;
        }
        // O caminho canônico deve corresponder ao registro, inclusive ao diretório da OS.
        return self::normalizaCaminho($path) === self::normalizaCaminho($writable . DIRECTORY_SEPARATOR . $relative) ? $path : null;
    }

    private static function diretorio(int $id): string
    {
        $raiz = realpath(WRITEPATH);
        if ($raiz === false) {
            throw new RuntimeException('O armazenamento privado não está disponível.');
        }
        $diretorio = $raiz;
        foreach (['uploads', 'atendimentos', (string) $id] as $parte) {
            $diretorio .= DIRECTORY_SEPARATOR . $parte;
            if (! is_dir($diretorio) && ! mkdir($diretorio, 0770) && ! is_dir($diretorio)) {
                throw new RuntimeException('Não foi possível preparar o armazenamento do arquivo.');
            }
            $real = realpath($diretorio);
            if ($real === false || ! self::dentro($real, $raiz) || self::normalizaCaminho($real) !== self::normalizaCaminho($diretorio)) {
                throw new RuntimeException('O diretório de anexos deve permanecer no armazenamento privado.');
            }
        }
        return $diretorio;
    }

    private static function dentro(string $path, string $base): bool
    {
        return str_starts_with(self::normalizaCaminho($path), rtrim(self::normalizaCaminho($base), '/') . '/');
    }

    private static function normalizaCaminho(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($path) : $path;
    }

    public static function removerArquivo(string $relative): void
    {
        if (($path = self::caminho($relative)) !== null && ! unlink($path)) {
            log_message('error', 'Arquivo de atendimento removido do cadastro não pôde ser limpo: {arquivo}', ['arquivo' => $relative]);
        }
    }
}

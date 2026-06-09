<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;
use InvalidArgumentException;

class ImagemCadastro
{
    private const DIRETORIO = 'uploads/cadastros';
    private const IMAGEM_PADRAO = 'assets/img/user.png';
    private const TAMANHO_MAXIMO = 2097152;

    private const EXTENSOES_POR_MIME = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    public static function salvar(?UploadedFile $arquivo, string $cadastro): ?string
    {
        if ($arquivo === null || $arquivo->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (! $arquivo->isValid()) {
            throw new InvalidArgumentException('Não foi possível enviar a foto selecionada.');
        }

        if ($arquivo->getSize() > self::TAMANHO_MAXIMO) {
            throw new InvalidArgumentException('A foto deve ter no máximo 2 MB.');
        }

        $informacoes = @getimagesize($arquivo->getTempName());
        $mime = is_array($informacoes) ? ($informacoes['mime'] ?? '') : '';

        if (! isset(self::EXTENSOES_POR_MIME[$mime])) {
            throw new InvalidArgumentException('Selecione uma foto válida nos formatos PNG, JPG ou WEBP.');
        }

        $cadastro = preg_replace('/[^a-z0-9_-]/', '', strtolower($cadastro));
        $diretorioRelativo = self::DIRETORIO . '/' . $cadastro;
        $diretorioAbsoluto = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $diretorioRelativo);

        if (! is_dir($diretorioAbsoluto) && ! mkdir($diretorioAbsoluto, 0755, true) && ! is_dir($diretorioAbsoluto)) {
            throw new InvalidArgumentException('Não foi possível preparar o diretório para salvar a foto.');
        }

        $nome = bin2hex(random_bytes(16)) . '.' . self::EXTENSOES_POR_MIME[$mime];
        $arquivo->move($diretorioAbsoluto, $nome);

        return $diretorioRelativo . '/' . $nome;
    }

    public static function remover(?string $caminho): void
    {
        $caminho = self::normalizar($caminho);

        if (preg_match('#^' . preg_quote(self::DIRETORIO, '#') . '/[a-z0-9_-]+/[a-f0-9]{32}\.(jpg|png|webp)$#', $caminho) !== 1) {
            return;
        }

        $arquivo = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $caminho);

        if (is_file($arquivo)) {
            @unlink($arquivo);
        }
    }

    public static function url(?string $caminho): string
    {
        $caminho = self::normalizar($caminho);
        $arquivo = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $caminho);

        if ($caminho === '' || ! is_file($arquivo)) {
            $caminho = self::IMAGEM_PADRAO;
        }

        return base_url($caminho);
    }

    private static function normalizar(?string $caminho): string
    {
        return ltrim(str_replace('\\', '/', trim((string) $caminho)), '/');
    }
}

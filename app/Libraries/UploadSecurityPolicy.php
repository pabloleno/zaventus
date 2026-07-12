<?php

namespace App\Libraries;

use CodeIgniter\HTTP\Files\UploadedFile;

class UploadSecurityPolicy
{
    public const MAX_PRODUCT_IMAGE_BYTES = 2097152;
    public const MAX_CERTIFICATE_BYTES = 2097152;

    private const PRODUCT_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];
    private const PRODUCT_IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
    private const CERTIFICATE_EXTENSIONS = ['pfx', 'p12'];
    private const CERTIFICATE_MIME_TYPES = [
        'application/octet-stream',
        'application/pkcs12',
        'application/x-pkcs12',
        'application/x-pkcs7-certificates',
    ];

    /**
     * @return list<string>
     */
    public function validateProductImage(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            return ['Falha no envio da imagem do produto: ' . $file->getErrorString()];
        }

        return $this->validateProductImageMetadata(
            $file->getClientExtension(),
            (string) $file->getMimeType(),
            (int) $file->getSize(),
            @getimagesize($file->getTempName()) !== false
        );
    }

    /**
     * @return list<string>
     */
    public function validateProductImageMetadata(string $extension, string $mimeType, int $size, bool $isReadableImage): array
    {
        $errors = [];
        $extension = $this->normalizeExtension($extension);
        $mimeType = $this->normalizeMimeType($mimeType);

        if ($size <= 0) {
            $errors[] = 'A imagem do produto esta vazia.';
        } elseif ($size > self::MAX_PRODUCT_IMAGE_BYTES) {
            $errors[] = 'A imagem do produto deve ter no maximo 2 MB.';
        }

        if (! in_array($extension, self::PRODUCT_IMAGE_EXTENSIONS, true)) {
            $errors[] = 'Formato invalido para imagem do produto. Use: jpg, jpeg, png ou webp.';
        }

        if (! in_array($mimeType, self::PRODUCT_IMAGE_MIME_TYPES, true)) {
            $errors[] = 'Tipo de arquivo invalido para imagem do produto.';
        }

        if (! $isReadableImage) {
            $errors[] = 'O arquivo selecionado nao e uma imagem valida.';
        }

        return $errors;
    }

    /**
     * @return list<string>
     */
    public function validateCertificate(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            return ['Falha no envio do certificado digital: ' . $file->getErrorString()];
        }

        return $this->validateCertificateMetadata(
            $file->getClientExtension(),
            (string) $file->getMimeType(),
            (int) $file->getSize()
        );
    }

    /**
     * @return list<string>
     */
    public function validateCertificateMetadata(string $extension, string $mimeType, int $size): array
    {
        $errors = [];
        $extension = $this->normalizeExtension($extension);
        $mimeType = $this->normalizeMimeType($mimeType);

        if ($size <= 0) {
            $errors[] = 'O certificado digital esta vazio.';
        } elseif ($size > self::MAX_CERTIFICATE_BYTES) {
            $errors[] = 'O certificado digital deve ter no maximo 2 MB.';
        }

        if (! in_array($extension, self::CERTIFICATE_EXTENSIONS, true)) {
            $errors[] = 'Formato invalido para certificado digital. Use: pfx ou p12.';
        }

        if ($mimeType !== '' && ! in_array($mimeType, self::CERTIFICATE_MIME_TYPES, true)) {
            $errors[] = 'Tipo de arquivo invalido para certificado digital.';
        }

        return $errors;
    }

    private function normalizeExtension(string $extension): string
    {
        return strtolower(ltrim(trim($extension), '.'));
    }

    private function normalizeMimeType(string $mimeType): string
    {
        return strtolower(trim(explode(';', $mimeType)[0]));
    }
}

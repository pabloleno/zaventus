#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Cross-platform PHP syntax check for the application-owned source files.
 *
 * Usage: php tools/php-lint.php app tests/app
 */

$requestedPaths = array_slice($argv, 1);
$requestedPaths = $requestedPaths !== [] ? $requestedPaths : ['app', 'tests/app'];
$projectRoot    = dirname(__DIR__);
$phpFiles       = [];

foreach ($requestedPaths as $requestedPath) {
    $absolutePath = $projectRoot . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $requestedPath);

    if (is_file($absolutePath)) {
        if (strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) === 'php') {
            $phpFiles[] = $absolutePath;
        }

        continue;
    }

    if (! is_dir($absolutePath)) {
        fwrite(STDERR, "Caminho inexistente: {$requestedPath}" . PHP_EOL);
        exit(2);
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($absolutePath, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if ($fileInfo->isFile() && strtolower($fileInfo->getExtension()) === 'php') {
            $phpFiles[] = $fileInfo->getPathname();
        }
    }
}

$phpFiles = array_values(array_unique($phpFiles));
sort($phpFiles, SORT_STRING);
$failures = [];

foreach ($phpFiles as $phpFile) {
    $output   = [];
    $exitCode = 0;
    exec(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($phpFile), $output, $exitCode);

    if ($exitCode !== 0) {
        $relativePath = str_replace($projectRoot . DIRECTORY_SEPARATOR, '', $phpFile);
        $failures[]   = $relativePath . ': ' . implode(' ', $output);
    }
}

if ($failures !== []) {
    fwrite(STDERR, implode(PHP_EOL, $failures) . PHP_EOL);
    fwrite(STDERR, sprintf('Falha de sintaxe em %d arquivo(s).', count($failures)) . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, sprintf('Sintaxe valida em %d arquivo(s) PHP.', count($phpFiles)) . PHP_EOL);

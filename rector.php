<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->parallel();
    $rectorConfig->paths([
        __DIR__.'/src',
    ]);

    // Define what rule sets will be applied
    $rectorConfig->phpVersion(PhpVersion::PHP_80);
    $rectorConfig->importNames();
    $rectorConfig->disableImportShortClasses();

    $rectorConfig->import(SetList::PHP_80);
};

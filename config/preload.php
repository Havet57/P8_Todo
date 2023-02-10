<?php

$filePath = __DIR__ . '/var/cache/prod/App_KernelProdContainer.preload.php';
if (file_exists($filePath)) {
    require $filePath;
}
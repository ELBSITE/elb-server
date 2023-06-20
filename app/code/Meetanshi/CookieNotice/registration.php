<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Meetanshi_CookieNotice',
    __DIR__
);

if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Model/System/Config/Source/Box/License/License.php')) {
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Model/System/Config/Source/Box/License/License.php');
}

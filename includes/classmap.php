<?php
/**
 * A map of classname => filename for SPL autoloading.
 *
 * @package AuthorizeNet
 */

$libDir = __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR;

return array(
    'Neokurir_Request' => $libDir . 'Request.php',
    'Neokurir_Config'  => $libDir . 'Config.php',
    'Neokurir_Api'     => $libDir . 'Api.php',
);

<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Entry Point | =================================================================================================

// ROOT_PATH is defined as the absolute path (without a trailing slash) of the document root or the scriptdir if cli.
// NOTE: We use this as the current working directory may change for the lifetime of the script in cli.
// With the addition of gPath() from the utils will normalize all paths making cross-platform possible without much
// trouble.
define('ROOT_PATH', empty($_SERVER['DOCUMENT_ROOT']) ? __DIR__ : $_SERVER['DOCUMENT_ROOT']);

const kDebugDomain = 'preview.binaryoutcast.com';

// Load fundamental utils
require_once(ROOT_PATH . '/base/src/utils.php');

// ====================================================================================================================

?>
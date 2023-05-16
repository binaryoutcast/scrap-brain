<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Entry Point | =================================================================================================

// kRootPath is defined as the absolute path (without a trailing slash) of the document root or the scriptdir if cli.
// NOTE: We use this as the current working directory may change for the lifetime of the script in cli.
// With the addition of gPath() from the utils will normalize all paths making cross-platform possible without much
// trouble.
define('kRootPath', empty($_SERVER['DOCUMENT_ROOT']) ? __DIR__ : $_SERVER['DOCUMENT_ROOT']);

const kDebugDomain = 'preview.binaryoutcast.com';
const kAppName = 'Binary Outcast';
const kAppVersion = '28.0.0pre';

// Load fundamental utils
require_once(kRootPath . '/base/src/utils.php');

// ====================================================================================================================

?>
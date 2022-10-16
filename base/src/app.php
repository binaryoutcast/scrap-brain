<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Setup | =======================================================================================================

const kDisableSpecialComponent = false;

gRegisterIncludes('COMPONENTS', ['site']);
gRegisterIncludes('MODULES', ['content']);
gRegisterIncludes('LIBRARIES', array(
  'smarty' => 'smarty/Smarty.class.php',
));

const kPrettyComps = EMPTY_ARRAY;

gRegistrySet('console.content.siteName', 'BinOC Preview');

// ====================================================================================================================

// == | Global Functions | ============================================================================================

//gContent('test');
//print(var_export(gRegistryUtils::GetStore(), true)); exit();

// ====================================================================================================================


?>
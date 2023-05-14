<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Main | ========================================================================================================

const kDisableSpecialComponent = false;
gRegSet('console.content.siteName', 'Binary Outcast');
gRegisterIncludes('COMPONENTS', ['site']);
gRegisterIncludes('MODULES', ['content']);
gRegisterIncludes('LIBRARIES', array(
  'smarty' => 'smarty/Smarty.class.php',
));

// --------------------------------------------------------------------------------------------------------------------

if (gMetropolis::Component('aus')) {
  gRegSet('app.component', 'aus');
  gConUtils::extensibleMarkup(gAppUtils::TAG_XML . kNewLine . kLessThan . 'updates' . kSlash . kGreaterThan, true);
}

// --------------------------------------------------------------------------------------------------------------------

// Fall through to utils loading the component which will be the Site component by default.

// ====================================================================================================================

?>
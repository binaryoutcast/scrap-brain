<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Main | ========================================================================================================

const kDisableSpecialComponent = true;
gSetConfig('console.content.siteName', 'Binary Outcast');
gRegisterIncludes('COMPONENTS', ['site']);
gRegisterIncludes('MODULES', ['content']);
gRegisterIncludes('LIBRARIES', array(
  'markdown' => 'markdown/Markdown.inc.php',
  'smarty' => 'smarty/Smarty.class.php',
));

// --------------------------------------------------------------------------------------------------------------------

// Site Offline
if (gGetConfig('app.offline', 'true')) {
  $content = 'This site is currently unavailable. Please try again later.';

  switch (gGetConfig('app.component', 'site')) {
    case 'aus':
      gConUtils::extensibleMarkup(gAppUtils::TAG_XML . kNewLine . kLessThan . 'updates' . kSlash . kGreaterThan, true);
      break;
    default: gfError($content);
  }
}

// --------------------------------------------------------------------------------------------------------------------

// Fall through to utils loading the component which will be the Site component by default.

// XXXTobin: This is temporary.
if (gMetropolis::Component('aus')) {
  gConUtils::extensibleMarkup(gAppUtils::TAG_XML . kNewLine . kLessThan . 'updates' . kSlash . kGreaterThan, true);
}

// ====================================================================================================================

?>
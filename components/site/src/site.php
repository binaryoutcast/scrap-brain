<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Main | ========================================================================================================

function xGenerateAFuckinBinOCWebsite($aContent = null) {
  $contentPath = gRegistry('app.componentPath') . kSlash . 'content';
  $skinPath = gAppUtils::StripStr(gRegistry('app.componentPath'), kRootPath) . kSlash . 'skin';
  $template = gReadFile(gBuildPath(kRootPath, $skinPath, 'template.html'));

  //$stylesheet = gReadFile(gBuildPath($skinPath, 'template.tpl'));

  if (!$template) { gError('There is no spoon.. I mean template.'); }

  $substs = array(
  //'xtplStyleSheet'      => $stylesheet ?? kEmptyString,
    'xtplContentBody'     => $aContent ?? 'No content supplied.',
    'xtplComnSkinPath'    => '/base/skin',
    'xtplCompSkinPath'    => gAppUtils::StripStr($skinPath, kRootPath),
  //'xtplContentPath'     => gAppUtils::StripStr($contentPath, kRootPath),
    'xtplSiteName'        => gGetConfig('console.content.siteName', kAppName),
    'xtplAppPath0'        => gGetConfig('app.path.0', kEmptyString),
    'xtplAppPath1'        => gGetConfig('app.path.1', kEmptyString),
    'xtplAppPath2'        => gGetConfig('app.path.2', kEmptyString),
    'xtplAppPath3'        => gGetConfig('app.path.3', kEmptyString),
    'xtplPageTitle'       => (gGetConfig('app.path.0') == 'root') ? 'Welcome back to BinOC.. Again!' : gGetConfig('app.path.0'),
    'xtplPageTagline'     => (gGetConfig('app.path.0') == 'root') ? 'Front Page (Home)' : 'No tagline supplied.',
    'xtplCurrentYear'     => date("Y"),
  );
  
  gOutput(gSubstEx($template, $substs), 'html');
}

// --------------------------------------------------------------------------------------------------------------------

gRegSet('console.content.siteName', 'Binary Outcast');

switch (gGetConfig('app.path.0')) {
  case 'projects':
    (gGetConfig('app.path.1') == 'interlink') ? gRedirect($ilFilesURL) : gRedirect($codeAtGithubURL);  
    break;
  case 'root':
    //gOutput(gReadFile(gBuildPath(kRootPath, 'components', 'site', 'content', 'splash.content')), 'html');
    //break;
  case 'updates':
  case 'about':
    xGenerateAFuckinBinOCWebsite();
    break;
  default:
    gRedirect('/updates/');
}

// ====================================================================================================================

?>
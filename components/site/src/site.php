<?php /****************************************************************************************************************
* PHP Code is filmed before a live studio audience.
**********************************************************************************************************************/

// == | Main | ========================================================================================================

$ilFilesURL = 'https://projects.binaryoutcast.com/interlink/releases/latest/';
$ilAddonsURL = 'https://interlink-addons.binaryoutcast.com/';
$codeAtGithubURL = 'https://github.com/binaryoutcast/';

$menubar    = ['/updates/'              => 'Updates',
               $ilFilesURL              => 'Interlink Mail Downloads',
               $ilAddonsURL             => '& Add-ons',
               $codeAtGithubURL         => 'Code @ Github',
               '/about/'                => 'About'];

$cmdbar     = ['/'                      => 'Home',
               '/updates/'              => 'Updates',
               '/about/'                => 'About'];

gRegSet('console.content.menubar', $menubar);
gRegSet('console.content.commandbar', $cmdbar);

// --------------------------------------------------------------------------------------------------------------------

function xGenerateAFuckinBinOCWebsite($aContent) {
  $contentPath = gBuildPath(gRegistry('app.componentPath'), 'content');
  $skinPath = gBuildPath(gRegistry('app.componentPath'), 'skin');
  $template = gReadFile(gBuildPath($skinPath, 'template.tpl'));
  $stylesheet = gReadFile(gBuildPath($skinPath, 'template.tpl'));

  if (!$template) { gError('There is no template.'); }

  $substs = array(
    'xtplStyleSheet'      => $stylesheet ?? kEmptyString,
    'xtplContentBody'     => $content ?? 'No content supplied.',
    'xtplComnSkinPath'    => '/base/skin/',
    'xtplCompSkinPath'    => $skinPath,
    'xtplContentPath'     => $contentPath,
    'xtplSiteName'        => SITE_NAME,
    'xtplPageTitle'       => $this->contentURL == SLASH ? 'Front Page' : $title,
    'xtplCurrentYear'     => date("Y"),
  );
}

// --------------------------------------------------------------------------------------------------------------------

gRegSet('console.content.siteName', 'BinOC Preview');

switch (gRegistry('app.path.0')) {
  case 'projects':
    (gRegistry('app.path.1') == 'interlink') ? gRedirect($ilFilesURL) : gRedirect($codeAtGithubURL);  
    break;
  case 'root':
    gOutput(gReadFile(gBuildPath(kRootPath, 'components', 'site', 'content', 'splash.content')), 'html');
    break;
  case 'updates':
    gContent('BinOC Updates');
    break;
  case 'about':
    gContent('About BinOC');
    break;
  default:
    gRedirect('/updates/');
}

// ====================================================================================================================

?>
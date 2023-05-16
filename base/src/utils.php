<?php /****************************************************************************************************************
* This Source Code Form is subject to the terms of the Mozilla Public
* License, v. 2.0. If a copy of the MPL was not distributed with this
* file, You can obtain one at http://mozilla.org/MPL/2.0/.
*
* Portions of this file are under other licenses separated by namespace
* boundaries. This is noted were applicable.
**********************************************************************************************************************/

namespace { // == | Setup and Global Constants | ======================================================================

// Check for kRootPath
if (!defined('kRootPath')) {
  die('BinOC Metropolis Utilities: You MUST define kRootPath.');
}

// Do not allow this to be included more than once...
if (defined('kUtilities')) {
  die('BinOC Metropolis Utilities: You may not include this file more than once.');
}

// Define that this is a thing which can double as a version check.
define('kUtilities', '2.0.0b1');

// --------------------------------------------------------------------------------------------------------------------

// These constants must be defined because they might be used before gMetropolis is init'd
if (!defined('kAppVendor'))             { define('kAppVendor', 'Binary Outcast'); }
if (!defined('kAppName'))               { define('kAppName', 'Metropolis-based Software'); }
if (!defined('kAppVersion'))            { define('kAppVersion', kUtilities); }
if (!defined('kAppRepository'))         { define('kAppRepository', '#'); }
if (!defined('kUtilsGlobalWrappers'))   { define('kUtilsGlobalWrappers', true); }
if (!defined('kBootstrap'))             { define('kBootstrap', true); }

// --------------------------------------------------------------------------------------------------------------------

// We like CLI
define('SAPI_IS_CLI', php_sapi_name() == "cli");
define('CLI_NO_LOGO', in_array('--nologo', $GLOBALS['argv'] ?? []));

// Enable PHP Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
const E_EXCEPTION = 65536;

// Debug flag (CLI always triggers debug mode)
define('kDebugMode', $_GET['debug'] ?? SAPI_IS_CLI);

// --------------------------------------------------------------------------------------------------------------------

// Define basic symbol constants
const kNewLine              = "\n";
const kCR                   = "\r";
const kLF                   = "\n";
const kTab                  = "\t";
const kSpace                = " ";
const kQuote                = '"';
const kApostrophe           = "'";
const kLessThan             = "<";
const kGreaterThan          = ">";
const kAmpersand            = "&";
const kSlash                = "/";
const kBackSlash            = "\\";
const kEqual                = "=";
const kDash                 = "-";
const kPlus                 = "+";
const kExclamation          = "!";
const kSemicolon            = ";";
const kHashsign             = "#";
const kAsterisk             = "*";
const kUnderbar             = "_";
const kComma                = ",";
const kLeftParen            = "(";
const kRightParen           = ")";
const kLeftBrace            = "{";
const kRightBrace           = "}";
const kQuestionMark         = "?";
const kLeftSquareBracket    = "[";
const kRightSquareBracket   = "]";
const kNullCh               = "\0";

const kForwardSlash         = kSlash;
const kMinus                = kDash;

const kDot                  = ".";
const kPipe                 = "|";
const kDollar               = "\$";
const kColon                = ":";

const kScopeOperator        = kColon . kColon;
const kResolutionOperator   = kDash . kGreaterThan;
const kDotDot               = kDot . kDot;
const kSpaceDashSpace       = kSpace . kDash . kSpace;
const kColonSlashSlash      = kColon . kSlash . kSlash;

const kEmptyString          = "";
const kEmptyArray           = [];

const kNegOne               = -1;
const kZero                 = 0;
const kPosOne               = 1;

// --------------------------------------------------------------------------------------------------------------------

const kJsonPrettyEncode     = gAppUtils::JSON_ENCODE_FLAGS['display'];

// --------------------------------------------------------------------------------------------------------------------

const kDefaultSkinName      = 'default';
const kDefaultSkinPath      = '/base/skin';
const kDefaultMenu          = ['/' => 'Front Page (Home)'];
const kSpecialComponent     = 'special';
const kSpecialComponentName = 'Special Component';

const PALEMOON_GUID         = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';

// ====================================================================================================================



// ====================================================================================================================

// == | Static Application Class | ====================================================================================

class gMetropolis {
  private static $sInitialized = false;
  private static $sRuntimeConfiguration = kEmptyArray;
  
  /********************************************************************************************************************
  * Application Init
  *********************************************************************************************************************/
  public static function init() {
    if (gMetropolis::$sInitialized) {
      return gMetropolis::$sInitialized;
    }

    // ------------------------------------------------------------------------------------------------------------------

    $path = gAppUtils::ExplodePath(gMetropolis::SuperGlobal('get', 'path', kSlash));

    $config = array(
      'app' => array(
        'component'   => gMetropolis::SuperGlobal('get', 'component', 'site'),
        'path'        => $path,
        'depth'       => count($path ?? kEmptyArray),
        'debug'       => kDebugMode,
        'offline'     => file_exists(gAppUtils::BuildPath(kRootPath, '.offline')),
      ),
      'network' => array(
        'scheme'      => gMetropolis::SuperGlobal('server', 'SCHEME') ?? (gMetropolis::SuperGlobal('server', 'HTTPS') ? 'https' : 'http'),
        'baseDomain'  => gConUtils::GetDomain(gMetropolis::SuperGlobal('server', 'SERVER_NAME', 'localhost')),
        'subDomain'   => gConUtils::GetDomain(gMetropolis::SuperGlobal('server', 'SERVER_NAME', 'localhost'), true),
        'remoteAddr'  => gMetropolis::SuperGlobal('server', 'HTTP_X_FORWARDED_FOR', gMetropolis::SuperGlobal('server', 'REMOTE_ADDR', '127.0.0.1')),
        'userAgent'   => gMetropolis::SuperGlobal('server', 'HTTP_USER_AGENT', 'php' . kDash . PHP_SAPI . kSlash . PHP_VERSION),
      ),
      'console' => array(
        'output' => array(
          'contentType' => 'text/plain',
          'responseCode'  => 200,
          'httpHeaders' => kEmptyArray,
        ),
        'content' => array(
          'skin'          => kDefaultSkinName,
          'skinPath'      => kDefaultSkinPath,
          'template'      => null,
          'stylesheet'    => null,
          'mainmenu'      => kDefaultMenu,
          'commandbar'    => kDefaultMenu,
          'statustext'    => 'Done',
        ),
      ),
    );

    if (defined('kDebugDomain') && !SAPI_IS_CLI) {
      $config['app']['debug'] = (gMetropolis::SuperGlobal('server', 'SERVER_NAME', 'localhost') != constant('kDebugDomain') ?? kEmptyString) ?
                                file_exists(gAppUtils::BuildPath(kRootPath, '.debugMode')) :
                                !kDebugMode;
    }

    // ------------------------------------------------------------------------------------------------------------------

    gMetropolis::$sRuntimeConfiguration = new \Adbar\Dot($config);

    // ------------------------------------------------------------------------------------------------------------------
    
    // Init any other static classes
    gErrorUtils::init();

    // ------------------------------------------------------------------------------------------------------------------

    // These are global wrapping functions. Most are optional except for the most basic functions

    // Application
      function gError                       (...$args) { return gMetropolis::Error(...$args); }
      function gNotFound                    (...$args) { return gMetropolis::NotFound(...$args); }
      function gReadFile                    (...$args) { return gMetropolis::ReadFile(...$args); }
      function gGetConfig                   (...$args) { return gMetropolis::GetConfig(...$args); }
      function gSetConfig                   (...$args) { return gMetropolis::SetConfig(...$args); }

    // Console
      function gOutput                      (...$args) { return gConUtils::Output(...$args); }

    // ------------------------------------------------------------------------------------------------------------------

    if (kUtilsGlobalWrappers) {
      // Application
      function gEnsureValue                 (...$args) { return gMetropolis::EnsureValue(...$args); }
      function gRegisterIncludes            (...$args) { return gMetropolis::RegisterIncludes(...$args); }
      function gLoadComponent               (...$args) { return gMetropolis::LoadComponent(...$args); }

      // Application Utilities
      function gSubst                       (...$args) { return gAppUtils::Subst(...$args); }
      function gSubstEx                     (...$args) { return gAppUtils::SubstEx(...$args); }
      function gContains                    (...$args) { return gAppUtils::Contains(...$args); }
      function gExplodeStr                  (...$args) { return gAppUtils::ExplodeStr(...$args); }
      function gBuildPath                   (...$args) { return gAppUtils::BuildPath(...$args); }
      function gStripStr                    (...$args) { return gAppUtils::StripStr(...$args); }
      function gHexString                   (...$args) { return gAppUtils::HexString(...$args); }
      function gPasswordHash                (...$args) { return gAppUtils::PasswordHash(...$args); }
      function gPasswordVerify              (...$args) { return gAppUtils::PasswordVerify(...$args); }
      function gGlobalIdentifer             (...$args) { return gAppUtils::GlobalIdentifer(...$args); }

      // Registry
      function gRegistry                    (...$args) { return gMetropolis::GetConfig(...$args); }
      function gRegSet                      (...$args) { return gMetropolis::SetConfig(...$args); }

      // Console
      function gHeader                      (...$args) { return gConUtils::Header(...$args); }
      function gContentType                 (...$args) { return gConUtils::ContentType(...$args); }
      function gSendHeaders                 (...$args) { return gConUtils::SendHeaders(...$args); }
      function gRedirect                    (...$args) { return gConUtils::Redirect(...$args); }
      function gContent                     (...$args) { return gConUtils::Content(...$args); }
    }

    // ------------------------------------------------------------------------------------------------------------------

    gMetropolis::$sInitialized = true;

    // This will bootstrap the Metropolis-based application loading. You may set kBoostrap to false in the entry point to
    // completely bypass this execution and return to the entry point.
    //
    // Bootstrap has THREE modes of operation:
    // App is Special "Component"     - Simply forces the application to always load the Special "Component".
    // App is ./base/src/app.php      - In this mode we will load the application from this file and if execution isn't
    //                                  otherwise terminated it will terminate here with a 404/PC LOAD LETTER.
    // App is Entry point             - Return to the entry point but allow for the possibility of switching to one of
    //                                  the other modes without adding or removing kBootstrap
    //
    // NOTE: Future behavior will have kBootstrap set to false by default.. like in a few weeks if not sooner.
    if (kBootstrap) {
      // The application effectively IS the special component
      if (gMetropolis::GetConfig('constant.appIsSpecialComponent')) {
        gMetropolis::LoadComponent(kSpecialComponent);
      }

      // App is ./base/src/app.php
      if (file_exists(gAppUtils::BuildPath(kRootPath, 'base', 'src', 'app.php'))) {
        require_once(gAppUtils::BuildPath(kRootPath, 'base', 'src', 'app.php'));

        if (gMetropolis::GetConfig('app.path.0') == kSpecialComponent) {
          gMetropolis::SetConfig('app.component', kSpecialComponent);
        }

        gMetropolis::LoadComponent(gMetropolis::GetConfig('app.component'));
        gMetropolis::NotFound('PC LOAD LETTER');
      }
    }
  }

  /********************************************************************************************************************
  * Set app config key
  *********************************************************************************************************************/
  public static function GetConfig(string $aDottedKey, mixed $aFallback = null) {
    $rv = null;

    if (gAppUtils::Contains($aDottedKey, gAppUtils::VIRTUAL_NODES, gAppUtils::STARTS_WITH)) {
      $keyNodes = gAppUtils::ExplodeStr(kDot, $aDottedKey) ?? kEmptyArray;
      $firstNode = $keyNodes[array_key_first($keyNodes)] ?? kEmptyString;
      $dot = new \Adbar\Dot();
      switch($firstNode) {
        case 'constant':
          if (count($keyNodes) < 2) { return $aFallback; }

          $ucConst = strtoupper($keyNodes[1]);
          $prefixConst = 'k' . ucfirst($keyNodes[1]);

          switch (true) {
            case defined($prefixConst):
              $rv = constant($prefixConst);
              break;
            case defined($ucConst):
              $rv = constant($ucConst);
              break;
            case defined($keyNodes[1]):
              $rv = constant($keyNodes[1]);
              break;
            default:
              return null;
          }

          if (!(is_array($rv) || $rv instanceof ArrayAccess)) { return $rv ?? $aFallback; }
          unset($keyNodes[0], $keyNodes[1]);
          $rv = $dot->setArray($rv)->get(implode(kDot, $keyNodes), $aFallback);
          break;
        case 'superglobal':
          if (count($keyNodes) < 3) { return $aFallback; }
          $rv = gMetropolis::SuperGlobal($keyNodes[1], $keyNodes[2]);
          if (!(is_array($rv) || $rv instanceof ArrayAccess)) { return $rv ?? $aFallback; }
          unset($keyNodes[0], $keyNodes[1]);
          $rv = $dot->setArray($rv)->get(implode(kDot, $keyNodes), $aFallback);
          break;
        default:
          return null;
      }
    }
    else { $rv = gMetropolis::$sRuntimeConfiguration->get($aDottedKey, $aFallback); }

    return $rv; 
  }

  /********************************************************************************************************************
  * Set app config key
  *********************************************************************************************************************/
  public static function SetConfig(string $aDottedKey, mixed $aNewValue) {
    $keyNodes = gAppUtils::ExplodeStr(kDot, $aDottedKey) ?? kEmptyArray;
    $firstNode = $keyNodes[array_key_first($keyNodes)] ?? kEmptyString;
    $lastNode = $keyNodes[array_key_last($keyNodes)] ?? kEmptyString;

    if (gAppUtils::Contains(gAppUtils::VIRTUAL_NODES, $firstNode, gAppUtils::IN_ARRAY)) {
      gMetropolis::Error('Setting values on virtual nodes is not supported.');
    }

    if (gAppUtils::Contains($lastNode, '[]', gAppUtils::ENDS_WITH)) {
      $aDottedKey = substr($aDottedKey, 0, -2);
      
      $oldValue = gMetropolis::$sRuntimeConfiguration->get($aDottedKey, kEmptyArray);

      if ($oldValue && !is_array($oldValue)) {
        $oldValue = [$oldValue];
      }

      $oldValue[] = $aNewValue;
      $aNewValue = $oldValue;
    }
    else if (is_numeric($lastNode)) {
      array_pop($keyNodes);
      $truncatedDottedKey = implode(kDot, $keyNodes);
      $oldValue = gMetropolis::$sRuntimeConfiguration->get(implode(kDot, $keyNodes));

      if (!$oldValue || !array_is_list($oldValue)) {
        gMetropolis::Error('Using indexed keys can only be done if the value is already an indexed list.');
      }

      $oldValue[(int)$lastNode] = $aNewValue;
      $aNewValue = $oldValue;
      $aDottedKey = $truncatedDottedKey;
    }

    return gMetropolis::$sRuntimeConfiguration->set($aDottedKey, $aNewValue);
  }

  /********************************************************************************************************************
  * Loads a component.
  *********************************************************************************************************************/
  public static function LoadComponent(string $aComponent) {
    if ($aComponent == kSpecialComponent) {
      gMetropolis::SpecialComponent();
    }

    $componentPath = gMetropolis::GetConfig('constant.components' . kDot . $aComponent);

    if (!$componentPath) {
      gMetropolis::NotFound('Unknown component.');
    }

    if (!file_exists($componentPath)) {
      gMetropolis::NotFound('Failed to load the' . kSpace . $aComponent . kSpace .'component.');
    }

    gMetropolis::SetConfig('app.componentPath', gAppUtils::BuildPath(kRootPath, 'components', $aComponent));
    require_once($componentPath);
  }

  /********************************************************************************************************************
  * Read a file
  *********************************************************************************************************************/
  public static function ReadFile(string $aFile) {
    $rv = @file_get_contents($aFile);
    return gMetropolis::EnsureValue($rv);
  }

  /********************************************************************************************************************
  * General Error Function
  *
  * @param $aMessage   Error message
  ********************************************************************************************************************/
  public static function Error(?string $aMessage = null) {
    if (!$aMessage) {
      $aMessage = 'No further details were provided.';
    }

    gErrorUtils::report(['code' => E_ALL, 'message' => $aMessage,
                         'file' => null, 'line' => null,
                         'trace' => debug_backtrace(2)]);
  }

  /********************************************************************************************************************
  * Sends 404 or prints error message if debug mode
  ********************************************************************************************************************/
  public static function NotFound(?string $aMessage = null) {
    if (!$aMessage) {
      $aMessage = 'HTTP/1.1 404 Not Found';
    }

    if (gMetropolis::Debug()) {
      gErrorUtils::report(['code' => E_ALL, 'message' => $aMessage,
                           'file' => null, 'line' => null,
                           'trace' => debug_backtrace(2)]);
    }
    gConUtils::Header(404);
  }

  /********************************************************************************************************************
  * Check if a value should be null
  ********************************************************************************************************************/
  public static function EnsureValue($aValue, $aFallback = null) {
    return (empty($aValue) || $aValue === 'none') ? $aFallback : $aValue;
  }

  /**********************************************************************************************************************
  * Special Component!
  ***********************************************************************************************************************/
  public static function SpecialComponent() {
    $spCurrentPath = gMetropolis::GetConfig('app.path');
    $spPathCount = gMetropolis::GetConfig('app.depth');

    if ($spCurrentPath[0] != kSpecialComponent) {
      gConUtils::Redirect(kSlash . kSpecialComponent . kSlash);
    }

    gMetropolis::SetConfig('app.component', kSpecialComponent);

    if (gMetropolis::GetConfig('constant.disableSpecialComponent')) {
      gMetropolis::NotFound('The special component has been disabled.');
    }

    gMetropolis::SetConfig('console.content.sectionName', kSpecialComponentName);

    // The Special Component never has more than one level below it
    // We still have to determine the root of the component though...
    if ($spPathCount == 1) {
      // URL /special/
      $spSpecialFunction = 'root';
    }
    else {
      // URL /special/xxx/
      if ($spPathCount > 2) {
        gMetropolis::NotFound('The special component only has one path level.');
      }
      $spSpecialFunction = $spCurrentPath[1];
    }

    $spCommandBar = array(
      '/special/'                 => kSpecialComponentName,
      '/special/test/'            => 'Test Cases',
      '/special/guid/'            => 'GUID',
      '/special/hex/'             => 'Hex String',
    );

    gMetropolis::SetConfig('console.content.commandbar', gMetropolis::GetConfig('constant.components.site') ?
                                               array_merge(kDefaultMenu, $spCommandBar) :
                                               $spCommandBar);

    unset($spCurrentPath, $spPathCount, $spCommandBar);

    switch ($spSpecialFunction) {
      case 'root':
        $spContent = '<h2>Welcome</h2>' .
                     '<p>Please select a special function from the command bar above.';
        gConUtils::Content($spContent, ['title' => 'Overview']);
        break;
      case 'test':
        if (!gMetropolis::Debug()) {
          gMetropolis::NotFound('This special function is not available when not in debug mode.');
        }
        $spCase = gMetropolis::GetConfig('superglobal.get.case');
        $spTestsPath = gAppUtils::BuildPath(kRootPath, 'base', 'tests');
        $spGlobTests = glob(gAppUtils::BuildPath($spTestsPath, kAsterisk . gAppUtils::FILE_EXT['php']));
        $spTests = kEmptyArray;

        foreach ($spGlobTests as $_value) {
          $spTests[] = gAppUtils::Subst($_value, [gAppUtils::FILE_EXT['php'] => kEmptyString, $spTestsPath . kSlash => kEmptyString]);
        }

        if ($spCase) {
          if (!gAppUtils::Contains($spCase, $spTests)) {
            gMetropolis::Error('Unknown test case.');
          }

          gMetropolis::SetConfig('special.testCase', $spCase);
          require_once(gAppUtils::BuildPath($spTestsPath, $spCase . gAppUtils::FILE_EXT['php']));
          headers_sent() ? exit() : gMetropolis::Error('The operation completed successfully.');
        }

        $spContent = kEmptyString;

        foreach ($spTests as $_value) {
          $spContent .= '<li><a href="/special/test/?case=' . $_value . '">' . $_value . '</a></li>';
        }

        $spContent = ($spContent == kEmptyString) ?
                     '<p>There are no test cases.</p>' :
                     '<h2>Please select a test case&hellip;</h2><ul>' . $spContent . '</ul>' . str_repeat('<br />', 3);

        gConUtils::Content($spContent, ['title' => 'Test Cases']);
        break;
      case 'guid':
        gConUtils::Content(gAppUtils::GlobalIdentifer(gMetropolis::GetConfig('superglobal.get.vendor'), true),
                 ['title' => 'Globally Unique Identifier (In XPIDL Notation)', 'textbox' => true]);
        break;
      case 'hex':
        gConUtils::Content(gAppUtils::HexString(gMetropolis::GetConfig('superglobal.get.length', 40)),
                 ['title' => 'Pseudo-Random Hex String', 'textbox' => true]);
        break;
      case 'system':
        if (!gMetropolis::Debug()) {
          gMetropolis::NotFound('This special function is not available when not in debug mode.');
        }
        gMetropolis::Header('html', true);
        phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_ENVIRONMENT | INFO_VARIABLES);
        break;
      default:
        gMetropolis::NotFound('There is no matching function in the special component.');
    }

    // We're done here
    exit();
  }
  
  /********************************************************************************************************************
  * Access Super Globals
  ********************************************************************************************************************/
  public static function SuperGlobal($aNode, $aKey, $aDefault = null) {
    $rv = null;

    // Turn the variable type into all caps prefixed with an underscore
    $aNode = kUnderbar . strtoupper($aNode);

    // This handles the superglobals
    switch($aNode) {
      case '_CHECK':
        $rv = gMetropolis::EnsureValue($aKey);
        break;
      case '_GET':
        if (SAPI_IS_CLI && $GLOBALS['argc'] > 1) {
          $args = kEmptyArray;

          foreach (array_slice($GLOBALS['argv'], 1) as $_value) {
            $arg = @explode('=', $_value);

            if (count($arg) < 2) {
              continue;
            }

            $attr = str_replace('--', kEmptyString, $arg[0]);
            $val = gMetropolis::EnsureValue(str_replace('"', kEmptyString, $arg[1]));

            if (!$attr && !$val) {
              continue;
            }

            $args[$attr] = $val;
          }

          $rv = $args[$aKey] ?? $aDefault;
          break;
        }
      case '_SERVER':
      case '_ENV':
      case '_FILES':
      case '_POST':
      case '_COOKIE':
      case '_SESSION':
        $rv = $GLOBALS[$aNode][$aKey] ?? $aDefault;
        break;
      default:
        // We don't know WHAT was requested but it is obviously wrong...
        gMetropolis::Error('Unknown system node.');
    }
    
    // We always pass $_GET values through a general regular expression
    // This allows only a-z A-Z 0-9 - / { } @ % whitespace and ,
    if ($rv && $aNode == "_GET") {
      $rv = preg_replace(gAppUtils::REGEX_PATTERNS['query'], kEmptyString, $rv);
    }

    // Files need special handling.. In principle we hard fail if it is anything other than
    // OK or NO FILE
    if ($rv && $aNode == "_FILES") {
      if (!in_array($rv['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE])) {
        gMetropolis::Error('Upload of ' . $aKey . ' failed with error code: ' . $rv['error']);
      }

      // No file is handled as merely being null
      if ($rv['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
      }

      // Cursory check the actual mime-type and replace whatever the web client sent
      $rv['type'] = mime_content_type($rv['tmp_name']);
    }
    
    return $rv;
  }

  /********************************************************************************************************************
  * Registers Files to be included such as components and modules
  *********************************************************************************************************************/
  public static function RegisterIncludes($aConst, $aIncludes) {
    $aConst = strtoupper($aConst);

    if (defined($aConst)) {
      gMetropolis::Error($aConst . kSpace . 'files are already registered and may not be updated.');
    }

    $includes = kEmptyArray;

    foreach($aIncludes as $_key => $_value) { 
      switch ($aConst) {
        case 'COMPONENTS':
          $includes[$_value] = gAppUtils::BuildPath(kRootPath, 'components', $_value, 'src', 'main' . gAppUtils::FILE_EXT['php']);
          break;
        case 'MODULES':
          $includes[$_value] = gAppUtils::BuildPath(kRootPath, 'modules', $_value . gAppUtils::FILE_EXT['php']);
          break;
        case 'LIBRARIES':
          if (str_contains($_value, kDot . kDot)) {
            return;
          }

          $includes[$_key] = gAppUtils::BuildPath(kRootPath, 'third_party', $_value);
          break;
        default:
          gfError('Unknown include type');
      }
    }

    define($aConst, $includes);
  }

  /********************************************************************************************************************
  * Get the registry property and return it
  ********************************************************************************************************************/
  public static function Component(?string $aCompareComponent = null) {
    $rv = (gMetropolis::$sRuntimeConfiguration) ? gMetropolis::GetConfig('app.component') : gMetropolis::SuperGlobal('get', 'component', 'site');

    if ($aCompareComponent) {
      $rv = ($rv === $aCompareComponent);
    }

    return $rv;
  }

  /********************************************************************************************************************
  * Get the registry property and return it
  ********************************************************************************************************************/
  public static function Debug() {
    return (gMetropolis::$sRuntimeConfiguration) ? gMetropolis::GetConfig('app.debug') : kDebugMode;
  }

  /********************************************************************************************************************
  * Get the registry property and return it
  ********************************************************************************************************************/
  public static function GetStore() {
    return gMetropolis::$sRuntimeConfiguration->get();
  }
}

// ====================================================================================================================

// == | Application Utilities | =======================================================================================

class gAppUtils {
  const CONTAINS = kZero;
  const STARTS_WITH = kPosOne;
  const ENDS_WITH = kNegOne;
  const IN_ARRAY = kZero;
  const KEY_EXISTS = kPosOne;

  // --------------------------------------------------------------------------------------------------------------------

  const PASSWORD_CLEARTEXT    = "clrtxt";
  const PASSWORD_HTACCESS     = "apr1";
  const BASE64_ALPHABET       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
  const APRMD5_ALPHABET       = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

  // --------------------------------------------------------------------------------------------------------------------

  const FILE_WRITE_FLAGS      = "w+";
  const FILE_EXT             = array(
    'php'                     => kDot . 'php',
    'ini'                     => kDot . 'ini',
    'html'                    => kDot . 'html',
    'xhtml'                   => kDot . 'xhtml',
    'xml'                     => kDot . 'xml',
    'rdf'                     => kDot . 'rdf',
    'json'                    => kDot . 'json',
    'content'                 => kDot . 'content',
    'tpl'                     => kDot . 'tpl',
    'xpinstall'               => kDot . 'xpi',
    'jar'                     => kDot . 'jar',
    'winstaller'              => kDot . 'installer' . kDot . 'exe',
    'winportable'             => kDot . 'portable' . kDot . 'exe',
    'mar'                     => kDot . 'mar',
    'mar-bz2'                 => kDot . 'complete' . kDot . 'mar',
    '7z'                      => kDot . '7z',
    'zip'                     => kDot . 'zip',
    'tz'                      => kDot . 'tz',
    't7z'                     => kDot . 't7z',
    'tgz'                     => kDot . 'tgz',
    'tbz'                     => kDot . 'tbz',
    'txz'                     => kDot . 'txz',
    'tar-zip'                 => kDot . 'tar' . kDot . 'zip',
    'tar-7z'                  => kDot . 'tar' . kDot . '7z',
    'tar-gz'                  => kDot . 'tar' . kDot . 'gz',
    'tar-bz2'                 => kDot . 'tar' . kDot . 'bz2',
    'tar-xz'                  => kDot . 'tar' . kDot . 'xz', 
  );

  // ----------------------------------------------------------------------------------------------------------------

  const TAG_XML               = '<?xml version="1.0" encoding="utf-8" ?>';

  // ----------------------------------------------------------------------------------------------------------------

  const JSON_ENCODE_FLAGS            = array(
    'display'                 => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
    'storage'                 => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
  );

  // ----------------------------------------------------------------------------------------------------------------

  const REGEX_PATTERNS        = array(
    'query'                   => "/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\s\,]/",
    'yaml'                    => "/\A---(.|\n)*?---/",
    'guid'                    => "/^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}$/i",
    'host'                    => "/[a-z0-9-\._]+\@[a-z0-9-\._]+/i",
  );

  // ------------------------------------------------------------------------------------------------------------------

  const VIRTUAL_NODES         = ['constant', 'superglobal'];

  /**********************************************************************************************************************
  * gAppUtils::Subst
  ***********************************************************************************************************************/
  public static function Subst(string $aString, array $aSubsts, bool $aRegEx = false) {
    $rv = $aString;
    $replaceFunction = $aRegEx ? 'preg_replace' : 'str_replace';

    foreach ($aSubsts as $_key => $_value) {
      $rv = call_user_func($replaceFunction, ($aRegEx ? kSlash . $_key . kSlash . 'iU' : $_key), $_value, $rv);
    }

    return !$rv ? gMetropolis::Error('Something has gone wrong...') : $rv;
  } 
    
  /**********************************************************************************************************************
  * gAppUtils::SubstEx
  ***********************************************************************************************************************/
  public static function SubstEx(string $aString, ...$aSubsts) {
    $keyPrefix = kLeftBrace;
    $keySuffix = kRightBrace;
    $substs = $aSubsts;

    // Accept an array as the second argument for named placeholder substitution further arguments or
    // changing the pre/post prefix.
    if (is_array($aSubsts[0])) {
      $substs = $aSubsts[0];
      $keyPrefix = $aSubsts[1] ?? kLeftBrace;
      $keySuffix = $aSubsts[2] ?? kRightBrace;
    }

    $keyCallback = function ($aKey) use ($keyPrefix, $keySuffix) { return $keyPrefix . $aKey . $keySuffix; };
    $substs = array_combine(array_map($keyCallback, array_keys($substs)), $substs);

    return gAppUtils::Subst($aString, $substs);
  }

  /**********************************************************************************************************************
  * gAppUtils::Contains
  ***********************************************************************************************************************/
  public static function Contains(string|array $aHaystack, string|array $aNeedle, int $aPos = kZero) {
    $rv = false;
    if (is_string($aNeedle)) {
      $aNeedle = [$aNeedle];
    }

    foreach ($aNeedle as $_value) {
      if (is_array($aHaystack)) {
        $rv = ($aPos === 1) ? array_key_exists($_value, $aHaystack) : in_array($_value, $aHaystack);
      }
      else {
        switch ($aPos) {
          case gAppUtils::STARTS_WITH:
            $rv = str_starts_with($aHaystack, $_value);
            break;
          case gAppUtils::ENDS_WITH:
            $rv = str_ends_with($aHaystack, $_value);
            break;
          case gAppUtils::CONTAINS:
          default:
            $rv = str_contains($aHaystack, $_value);
        }
      }

      if ($rv) {
        break;
      }
    }

    return $rv;
  }

  /**********************************************************************************************************************
  * gAppUtils::ExplodeStr
  ***********************************************************************************************************************/
  public static function ExplodeStr(string $aSeparator, string $aString) {
    return (!str_contains($aString, $aSeparator)) ? [$aString] :
            array_values(array_filter(explode($aSeparator, $aString), 'strlen'));
  }

  /**********************************************************************************************************************
  * gExplodePath
  ***********************************************************************************************************************/
  public static function ExplodePath(string $aPath) {
    return ($aPath == kSlash) ? ['root'] : gAppUtils::ExplodeStr(kSlash, $aPath);
  }

  /**********************************************************************************************************************
  * gAppUtils::BuildPath
  ***********************************************************************************************************************/
  public static function BuildPath(...$aParts) {
    $parts = kEmptyArray;
    $path = strtr(implode(kSlash, $aParts), '\\', kSlash);
    $prefix = kEmptyString;
    $absolute = false;

    // extract a prefix being a protocol://, protocol:, protocol://drive: or simply drive:
    if (preg_match('{^( [0-9a-z]{2,}+: (?: // (?: [a-z]: )? )? | [a-z]: )}ix', $path, $match)) {
      $prefix = $match[1];
      $path = substr($path, strlen($prefix));
    }

    if (substr($path, 0, 1) === kSlash) {
      $absolute = true;
      $path = substr($path, 1);
    }

    $up = false;

    foreach (explode(kSlash, $path) as $chunk) {
      if (kDotDot === $chunk && ($absolute || $up)) {
        array_pop($parts);
        $up = !(empty($parts) || kDotDot === end($parts));
      }
      elseif (kDot !== $chunk && kEmptyString !== $chunk) {
        $parts[] = $chunk;
        $up = kDotDot !== $chunk;
      }
    }

    return $prefix . ($absolute ? kSlash : kEmptyString) . implode(kSlash, $parts);
  }

  /**********************************************************************************************************************
  * gAppUtils::StripStr
  ***********************************************************************************************************************/
  public static function StripStr (string $aStr, string $aStrip = kEmptyString) {
    return str_replace($aStrip, kEmptyString, $aStr);
  }
  /**********************************************************************************************************************
  * Generate a random hexadecimal string
  *
  * @param $aLength   Desired number of final chars
  * @returns          Random hexadecimal string of desired length
  **********************************************************************************************************************/
  public static function HexString(int $aLength = 40) {
    return bin2hex(random_bytes(($aLength <= 1) ? 1 : (int)($aLength / 2)));
  }

  /********************************************************************************************************************
  * Hash a password
  *********************************************************************************************************************/
  public static function PasswordHash(string $aPassword, mixed $aCrypt = PASSWORD_BCRYPT, ?string $aSalt = null) {
    switch ($aCrypt) {
      case gAppUtils::PASSWORD_CLEARTEXT:
        // We can "hash" a cleartext password by prefixing it with the fake algo prefix $clear$
        if (str_contains($aPassword, kDollar)) {
          // Since the dollar sign is used as an identifier and/or separator for hashes we can't use passwords
          // that contain said dollar sign.
          gMetropolis::Error('Cannot "hash" this Clear Text password because it contains a dollar sign.');
        }

        return kDollar . gAppUtils::PASSWORD_CLEARTEXT . kDollar . time() . kDollar . $aPassword;
      case gAppUtils::PASSWORD_HTACCESS:
        // We want to be able to generate Apache APR1-MD5 hashes for use in .htpasswd situations.
        $salt = $aSalt;

        if (!$salt) {
          $salt = kEmptyString;

          for ($i = 0; $i < 8; $i++) {
            $offset = hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % 64;
            $salt .= gAppUtils::APRMD5_ALPHABET[$offset];
          }
        }

        $salt = substr($salt, 0, 8);
        $max = strlen($aPassword);
        $context = $aPassword . kDollar . gAppUtils::PASSWORD_HTACCESS . kDollar . $salt;
        $binary = pack('H32', md5($aPassword . $salt . $aPassword));

        for ($i = $max; $i > 0; $i -= 16) {
          $context .= substr($binary, 0, min(16, $i));
        }

        for ($i = $max; $i > 0; $i >>= 1) {
          $context .= ($i & 1) ? chr(0) : $aPassword[0];
        }

        $binary = pack('H32', md5($context));

        for ($i = 0; $i < 1000; $i++) {
          $new = ($i & 1) ? $aPassword : $binary;

          if ($i % 3) {
            $new .= $salt;
          }
          if ($i % 7) {
            $new .= $aPassword;
          }

          $new .= ($i & 1) ? $binary : $aPassword;
          $binary = pack('H32', md5($new));
        }

        $hash = kEmptyString;

        for ($i = 0; $i < 5; $i++) {
          $k = $i + 6;
          $j = $i + 12;
          if($j == 16) $j = 5;
          $hash = $binary[$i] . $binary[$k] . $binary[$j] . $hash;
        }

        $hash = chr(0) . chr(0) . $binary[11] . $hash;
        $hash = strtr(strrev(substr(base64_encode($hash), 2)), gAppUtils::BASE64_ALPHABET, gAppUtils::APRMD5_ALPHABET);

        return kDollar . gAppUtils::PASSWORD_HTACCESS . kDollar . $salt . kDollar . $hash;
      default:
        // Else, our standard (and secure) default is PASSWORD_BCRYPT hashing.
        // We do not allow custom salts for anything using password_hash as PHP generates secure salts.
        // PHP Generated passwords are also self-verifiable via password_verify.
        return password_hash($aPassword, $aCrypt);
    }
  }

  /********************************************************************************************************************
  * Check a password
  *********************************************************************************************************************/
  public static function PasswordVerify(string $aPassword, string $aHash) {
    // We can accept a pseudo-hash for clear text passwords in the format of $clrtxt$unix-epoch$clear-text-password
    if (str_starts_with($aHash, kDollar . gAppUtils::PASSWORD_CLEARTEXT)) {
      $password = gAppUtils::ExplodeStr(kDollar, $aHash) ?? null;

      if ($password == null || count($password) > 3) {
        gMetropolis::Error('Unable to "verify" this Clear Text "hashed" password.');
      }

      return $aPassword === $password[2];
    }

    // We can also accept an Apache APR1-MD5 password that is commonly used in .htpasswd
    if (str_starts_with($aHash, kDollar . gAppUtils::PASSWORD_HTACCESS)) {
      $salt = gAppUtils::ExplodeStr(kDollar, $aHash)[1] ?? null;

      if(!$salt) {
        gMetropolis::Error('Unable to verify this Apache APR1-MD5 hashed password.');
      }

      return gAppUtils::PasswordHash($aPassword, gAppUtils::PASSWORD_HTACCESS, $salt) === $aHash;
    }

    // For everything else send to the native password_verify function.
    // It is almost certain to be a BCRYPT2 hash but hashed passwords generated BY PHP are self-verifiable.
    return password_verify($aPassword, $aHash);
  }

  /********************************************************************************************************************
  * Generates a v4 random guid or a "v4bis" guid with static vendor node
  *********************************************************************************************************************/
  public static function GlobalIdentifer(?string $aVendor = null, ?bool $aXPCOM = null) {
    if ($aVendor) {
      if (strlen($aVendor) < 3) {
        gMetropolis::Error('v4bis GUIDs require a defined vendor of more than three characters long.');
      }

      // Generate 8 pseudo-random bytes
      $bytes = random_bytes(8);

      // Knock the vendor down to lowercase so we can simply use a switch case
      $vendor = strtolower($aVendor);

      // We want "v4bis" GUIDs with the static vendor part to match the broken version of GUIDGenX for known nodes
      // as Moonchild half-assed his tool that he wrote for this and by the time it was discovered several were already
      // using the previous incorrect GUIDs.
      $knownVendorNodes = array(
        'binoc'           => hex2bin('8b97957ad5f8ea47'),
        'binoc-legacy'    => hex2bin('9aa0aa0e607640b9'),
        'mcp'             => hex2bin('bfc5fc555c87dbc4'),
        'lootyhoof'       => hex2bin('b98e98e62085837f'),
        'pseudo-static'   => hex2bin('93763763d1ad1978')
      );

      switch ($vendor) {
        case 'binoc':
        case 'binaryoutcast':
        case 'binary outcast':
          $bytes .= $knownVendorNodes['binoc'];
          break;
        case 'pseudo-static':
        case 'pseudostatic':
        case 'addons':
        case 'add-ons':
        case 'apmo':
          $bytes .= $knownVendorNodes['pseudo-static'];
          break;
        case 'mcp':
        case 'moonchildproductions':
        case 'moonchild productions':
          $bytes .= $knownVendorNodes['mcp'];
          break;
        case 'binoc-legacy':
        case 'lootyhoof':
          $bytes .= $knownVendorNodes[$vendor];
          break;
        default:
          // Since this isn't a known busted vendor node then we should generate it ourselves.
          // This matches the fixed version of GUIDGenX 1.1 which is to md5 hash the vendor string then
          // split it in half and XOR the two parts for the final value

          // XXXTobin: A future update could do a sha256 by halving it and again then XORing the two sets
          // and XORing the final two results.
          $vendor = hash('md5', $aVendor);
          $bytes .= hex2bin(substr($vendor, 0, 16)) ^ hex2bin(substr($vendor, 16, 32));
      }
    }
    else {
      // This is a pure v4 UUID spec which is 16 pseudo-random bytes.
      $bytes = random_bytes(16);
    }

    // Set the version and variant
    // NOTE: Like everything Moonzilla does, he did not set the variant value when he initially came up with "v4bis"
    // putting a busted generator into production use for the whole team. Sad!
    $bytes[6] = chr(ord($bytes[6]) & 0x0f | 0x40);
    $bytes[8] = chr(ord($bytes[8]) & 0x3f | 0x80);

    $hex = bin2hex($bytes);
    $guid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($hex, 4));

    // We want the GUID in XPIDL/C++ Header notation
    if ($aXPCOM) {
      $explode = gAppUtils::ExplodeStr(kDash, $guid);
      $rv = "%{C++" . kNewLine . "//" . kSpace . kLeftBrace . $guid . kRightBrace . kNewLine .
            "#define NS_CHANGE_ME_IID" . kSpace . 
            vsprintf("{ 0x%s, 0x%s, 0x%s, { 0x%s, 0x%s, 0x%s, 0x%s, 0x%s, 0x%s, 0x%s, 0x%s } }",
                     [$explode[0], $explode[1], $explode[2],
                      substr($explode[3], 0, 2), substr($explode[3], 2, 2),
                      substr($explode[4], 0, 2), substr($explode[4], 2, 2), substr($explode[4], 4, 2),
                      substr($explode[4], 6, 2), substr($explode[4], 8, 2), substr($explode[4], 10, 2)]) . kNewLine .
            "%}";
    }
    else {
      // We like Microsoft GUID notation not UUID which means Lisa needs braces.. I mean the GUID.
      $rv = '{' . $guid . '}';
    }

    return $rv;
  }
}

// ====================================================================================================================

// == | Static Error Class | ==========================================================================================

class gErrorUtils {
  const kPhpErrorCodes = array(
    E_ERROR                   => 'System Error',
    E_WARNING                 => 'System Warning',
    E_PARSE                   => 'System Error (Parser)',
    E_NOTICE                  => 'System Notice',
    E_CORE_ERROR              => 'System Error (Core)',
    E_CORE_WARNING            => 'System Warning (Core)',
    E_COMPILE_ERROR           => 'System Error (Compiler)',
    E_COMPILE_WARNING         => 'System Warning (Compiler)',
    E_USER_ERROR              => 'System Error (Application)',
    E_USER_WARNING            => 'System Warning (Application)',
    E_USER_NOTICE             => 'System Notice (Application)',
    E_STRICT                  => 'System Error (Strict)',
    E_RECOVERABLE_ERROR       => 'System Error (Recoverable)',
    E_DEPRECATED              => 'System Deprecation',
    E_USER_DEPRECATED         => 'System Deprecation (Application)',
    E_ALL                     => 'Unable to Comply',
    E_EXCEPTION               => 'System Exception',
  );

  /******************************************************************************************************************
  * Static Class Public Init/Deinit
  ******************************************************************************************************************/
  public static function init() {
    set_error_handler(__CLASS__ . kScopeOperator . "phpErrorHandler");
    set_exception_handler(__CLASS__ . kScopeOperator . "phpExceptionHandler");
  }

  public static function uninit() { restore_error_handler(); restore_exception_handler(); }

  /******************************************************************************************************************
  * Output details about a failure condition
  ******************************************************************************************************************/
  public static function report(array $aMetadata) {
    if (gMetropolis::Debug() && gMetropolis::SuperGlobal('get', 'runtime')) {
      gConUtils::Output((self::kPhpErrorCodes[$aMetadata['code']] ?? self::kPhpErrorCodes[E_ALL]) . kSpaceDashSpace . $aMetadata['message']);
    }

    $traceline = fn($eFile, $eLine) => str_replace(kRootPath, kEmptyString, $eFile) . kColon . $eLine;
    $functions = ['phpErrorHandler', 'phpExceptionHandler', 'trigger_error'];
    $trace = ($aMetadata['file'] && $aMetadata['line']) ? [$traceline($aMetadata['file'], $aMetadata['line'])] : kEmptyArray;

    foreach ($aMetadata['trace'] as $_key => $_value) {
      if (in_array($_value['function'], $functions)) {
        continue;
      }

      $trace[] = $traceline($_value['file'], $_value['line']);
    }

    $title = self::kPhpErrorCodes[$aMetadata['code']] ?? self::kPhpErrorCodes[E_ALL];
    $content = $aMetadata['message'];

    if (!SAPI_IS_CLI) {
      $content = '<h2>Traceback</h2><ul>';

      foreach ($trace as $_value) {
        $content .= '<li>' .  str_replace(kSlash, kDot, str_replace('.php', kEmptyString, ltrim($_value, kSlash))) . '</li>';
      }

      $content .= '</ul>';
      $content .= is_string($aMetadata['message']) ?
                 '<hr /><p><strong>' . $aMetadata['message'] . '</strong></p>':
                 kEmptyString;

      $commandBar = ['onclick="history.back()"' => 'Go Back'];

      if (gMetropolis::Component(kSpecialComponent) || !gMetropolis::GetConfig('constant.components.site')) {
        gMetropolis::SetConfig('console.content.commandbar', array_merge($commandBar, ['/special/' => kSpecialComponentName]));
      }
      else {
        gMetropolis::SetConfig('console.content.commandbar', array_merge($commandBar, kDefaultMenu));
      }

      gMetropolis::SetConfig('console.content.sectionName', kEmptyString);
      gConUtils::Content($content, ['title' => $title, 'statustext' => '<span class="pulseText">Please contact a system administrator.</span>']);
    }

    gConUtils::Output(['title'=> $title, 'content' => ['errorMessage' => $content, 'traceback' => $trace]]);
  }

  /******************************************************************************************************************
  * PHP Handlers
  ******************************************************************************************************************/
  public static function phpErrorHandler($eCode, $eMessage, $eFile, $eLine) {
    if (!(error_reporting() & $eCode)) {
      // Don't do jack shit because the developers of PHP think users shouldn't be trusted.
      return;
    }

    self::report(['code' => $eCode, 'message' => $eMessage,
                  'file' => $eFile, 'line' => $eLine,
                  'trace' => debug_backtrace(2)]);
  }

  public static function phpExceptionHandler($ex) {
    self::report(['code' => E_EXCEPTION, 'message' => $ex->getMessage(),
                  'file' => $ex->getFile(), 'line' => $ex->getLine(),
                  'trace' => $ex->getTrace()]);
  }
}

// ====================================================================================================================

// == | Static Output Class | =========================================================================================

class gConUtils {
  const HTTP_HEADERS = array(
    404                       => 'HTTP/1.1 404 Not Found',
    501                       => 'HTTP/1.1 501 Not Implemented',
  );

  const HTTP_STATUS_CODE = array(
    200       => 'OK',
    204       => 'No Content',
    400       => 'Bad Request',
    401       => 'Unauthorized',
    402       => 'Payment Required',
    403       => 'Forbidden',
    404       => 'Not Found',
    405       => 'Method Not Allowed',
    406       => 'Not Acceptable',
    415       => 'Unsupported Media Type',
    418       => 'I\'m a teapot',
    500       => 'Internal Server Error',
    501       => 'Not Implemented',
    502       => 'Bad Gateway',
    503       => 'Service Unavailable',
  );

  const MIME_TYPES = array(
    'text'    => 'text/plain',
    'html'    => 'text/html',
    'rdf'     => 'text/rdf',
    'xul'     => 'application/vnd.mozilla.xul+xml',
    'xhtml'   => 'application/xhtml+xml',
    'xml'     => 'text/xml',
    'css'     => 'text/css',
    'js'      => 'application/js',
    'json'    => 'application/json',
    'bin'     => 'application/octet-stream',
    'xpi'     => 'application/x-xpinstall',
    'jar'     => 'application/java-archive',
    '7z'      => 'application/x-7z-compressed',
    'xz'      => 'application/x-xz',
    'zip'     => 'application/zip',
  );

  /**********************************************************************************************************************
  * Sends HTTP Headers to client using a short name
  *
  * @dep HTTP_HEADERS
  * @dep kDebugMode
  * @dep gMetropolis::Error()
  * @param $aHeader    Short name of header
  **********************************************************************************************************************/
  public static function Header(string|int $aHeader, bool $aSendAllOnContentType = false) { 
    if (is_int($aHeader) && gAppUtils::Contains(self::HTTP_STATUS_CODE, $aHeader, 1)) {
      self::HttpStatusCode($aHeader);
      
      if ($aHeader < 400) {
        return true;
      }
      else {
        self::SendHeaders();
        exit();
      }
    }

    if (gAppUtils::Contains(self::MIME_TYPES, $aHeader, 1)) {
      self::ContentType($aHeader);

      if ($aSendAllOnContentType) {
        return self::SendHeaders();
      }
      else {
        return true;
      }
    }

    return gMetropolis::SetConfig('console.output.httpHeaders[]', $aHeader);
  }

  /********************************************************************************************************************
  * Gets or sets the "default" content type so we don't have to output the header ourselves in most cases.
  ********************************************************************************************************************/
  public static function SendHeaders() {
    $responseCode = gMetropolis::GetConfig('console.output.responseCode', 200);
    gMetropolis::SetConfig('console.output.httpHeaders[]', 'Content-type'. kColon . kSpace . gMetropolis::GetConfig('console.output.contentType'));
    gMetropolis::SetConfig('console.output.httpHeaders[]', 'HTTP/1.1' . kSpace . $responseCode . kSpace . self::HTTP_STATUS_CODE[$responseCode]);

    $headers = gMetropolis::GetConfig('console.output.httpHeaders', kEmptyArray);

    foreach ($headers as $_value) {
      header(trim($_value), true);
    }
  }

  /********************************************************************************************************************
  * Gets or sets the "default" content type so we don't have to output the header ourselves in most cases.
  ********************************************************************************************************************/
  public static function HttpStatusCode(?string $aStatusCode = null) {
    if (!$aStatusCode) {
      gMetropolis::GetConfig('console.output.responseCode', 200);
    }

    if (gAppUtils::Contains(self::HTTP_STATUS_CODE, $aStatusCode, 1)) {
      gMetropolis::SetConfig('console.output.responseCode', $aStatusCode);
    }
  }

  /********************************************************************************************************************
  * Gets or sets the "default" content type so we don't have to output the header ourselves in most cases.
  ********************************************************************************************************************/
  public static function ContentType(?string $aContentType = null) {
     if ($aContentType === null) {
      return gMetropolis::GetConfig('console.output.contentType');
    }

    if (gAppUtils::Contains(self::MIME_TYPES, $aContentType, 1)) {
      return gMetropolis::SetConfig('console.output.contentType', self::MIME_TYPES[$aContentType]);
    }
  }

  /**********************************************************************************************************************
  * Sends HTTP Header to redirect the client to another URL
  *
  * @param $aURL   URL to redirect to
  **********************************************************************************************************************/
  public static function Redirect($aURL) { header('Location: ' . $aURL, true, 302); exit(); }

  /**********************************************************************************************************************
  * Get a subdomain or base domain from a host
  *
  * @dep kDot
  * @dep gAppUtils::ExplodeStr()
  * @param $aHost       Hostname
  * @param $aReturnSub  Should return subdmain
  * @returns            domain or subdomain
  ***********************************************************************************************************************/
  public static function GetDomain(string $aHost, ?bool $aReturnSub = null) {
    $host = gAppUtils::ExplodeStr(kDot, $aHost);
    return implode(kDot, $aReturnSub ? array_slice($host, 0, -2) : array_slice($host, -2, 2));
  }

  /**********************************************************************************************************************
  * var_export() + exit()
  **********************************************************************************************************************/
  public static function Export($aVariable) { print(var_export($aVariable, true)); exit(); }

  /******************************************************************************************************************
  * Simply prints output and sends header if not cli and exits
  ******************************************************************************************************************/
  public static function Output(mixed $aContent, ?string $aHeader = 'text') {
    $content = null;

    if (gMetropolis::Debug() && gMetropolis::SuperGlobal('get', 'runtime')) {
      $content = gMetropolis::GetStore();
      $content['console']['output']['responseBody'] = $aContent;
      $content['console']['output']['responseTime'] = microtime(true) - gMetropolis::SuperGlobal('server', 'REQUEST_TIME_FLOAT', 0);
      $content = json_encode($content, gAppUtils::JSON_ENCODE_FLAGS['display']);
      self::Header('text', true);
      print($content);
      exit();
    }

    if (is_array($aContent)) {
      $title = $aContent['title'] ?? 'Output';
      $content = $aContent['content'] ?? kEmptyString;

      if ($title == 'Output' && $content == kEmptyString) {
        $content = $aContent ?? $content;
      }
    }
    else {
      $title = 'Output';
      $content = $aContent ?? kEmptyString;
    }

    $content = (is_string($content) || is_int($content)) ? $content : json_encode($content, gAppUtils::JSON_ENCODE_FLAGS['display']);

    // Send the header if not cli
    if (SAPI_IS_CLI) {
      if (!CLI_NO_LOGO) {
        $software = $title . kSpaceDashSpace . kAppVendor . kSpace . kAppName . kSpace . kAppVersion;
        $titleLength = 120 - 8 - strlen($software);
        $titleLength = ($titleLength > 0) ? $titleLength : 2;
        $title = kNewLine . '==' . kSpace . kPipe . kSpace . $software . kSpace . kPipe . kSpace . str_repeat('=', $titleLength);
        $content = $title . kNewLine . kNewLine . $content . kNewLine . kNewLine . str_repeat('=', 120) . kNewLine;
      }
    }
    else {
      self::ContentType($aHeader);
      self::SendHeaders();
    }

    // Write out the content
    print($content);

    // We're done here...
    exit();
  }

  /******************************************************************************************************************
  * Basic Site Content Generation using a Special Template
  ******************************************************************************************************************/
  public static function Content(mixed $aContent, array $aMetadata = kEmptyArray) {
    $template = SAPI_IS_CLI ? false : gMetropolis::ReadFile(gAppUtils::BuildPath(kRootPath, 'base', 'skin', 'template.xhtml'));
    $stylesheet = SAPI_IS_CLI ? false : gMetropolis::ReadFile(gAppUtils::BuildPath(kRootPath, 'base', 'skin', 'stylesheet.css'));

    if (!$template) {
      gConUtils::Output(['content' => $aContent, 'title' => $aMetadata['title'] ?? 'Output']);
    }

    $content = $aContent;

    $metadata = function($val) use(&$aMetadata) {
      return $aMetadata[$val] ?? null;
    };

    $menuize = function($aMenu) {
      $rv = kEmptyString;

      foreach ($aMenu as $_key => $_value) {
        if (gAppUtils::Contains($_key, 'onclick=', 1)) {
          $rv .= '<li><a href="#"' . kSpace . $_key . '>' . $_value . '</a></li>';
        }
        else {
          $rv .= '<li><a href="' . $_key . '">' . $_value . '</a></li>';
        }
      }

      return $rv;
    };

    if ((is_string($content) || is_int($content)) && !$metadata('textbox') && !$metadata('iframe')) {
      if (!gAppUtils::Contains($content, ['<p', '<ul', '<ol', '<h1', '<h2', '<h3', '<table'])) {
        $content = '<p>' . $content . '</p>';
      }
    }
    else {
      $aMetadata['textbox'] = true;
    }

    if ($metadata('textbox')) {
      $content = (is_string($content) || is_int($content)) ? $content : json_encode($content, gAppUtils::JSON_ENCODE_FLAGS['display']);
      $content = '<form><textarea class="special-textbox" name="content" rows="30" readonly>' . $content . '</textarea></form>';
    }

    $siteName = gMetropolis::GetConfig('console.content.siteName', kAppName);
    $sectionName = gMetropolis::GetConfig('console.content.sectionName', kEmptyString);

    if ($sectionName) {
      $siteName = $sectionName . kSpaceDashSpace . $siteName;
    }

    $isTestCase = (!$metadata('title') && gMetropolis::GetConfig('special.testCase') && gMetropolis::Component(kSpecialComponent));

    $substs = array(
      '{$SITE_STYLESHEET}'  => $stylesheet ?? kEmptyString,
      '{$PAGE_CONTENT}'     => $content,
      '{$SITE_DOMAIN}'      => gMetropolis::SuperGlobal('server', 'SERVER_NAME'),
      '{$SITE_NAME}'        => $siteName,
      '{$SITE_MENU}'        => $menuize(gMetropolis::GetConfig('console.content.commandbar')),
      '{$SITE_SECTION}'     => $sectionName ?? kEmptyString,
      '{$PAGE_TITLE}'       => $isTestCase ? '[Test]' . kSpace . gMetropolis::GetConfig('special.testCase') : ($metadata('title') ?? 'Output'),
      '{$PAGE_STATUS}'      => $metadata('statustext') ?? gMetropolis::GetConfig('console.content.statustext'),
      '{$SKIN_PATH}'        => gAppUtils::BuildPath(kSlash, 'base', 'skin'),
      '{$SOFTWARE_VENDOR}'  => kAppVendor,
      '{$SOFTWARE_NAME}'    => kAppName,
      '{$SOFTWARE_VERSION}' => kAppVersion,
    );

    $content = gAppUtils::Subst($template, $substs);

    @ob_end_clean();
    gConUtils::Output($content, 'html');
  }

  /**********************************************************************************************************************
  * Create an XML Document 
  ***********************************************************************************************************************/
  public static function extensibleMarkup(string|array $aData, ?bool $aDirectOutput = null) {
    if (is_string($aData)) {
      $xml = $aData;
    }
    else {
      $dom = new DOMDocument('1.0');
      $dom->encoding = "UTF-8";
      $dom->formatOutput = true;

      $processChildren = function($aData) use (&$dom, &$processChildren) {
        if (!($aData['@elem'] ?? null)) {
          return false;
        }

        // Create the element
        $element = $dom->createElement($aData['@elem']);

        // Properly handle content using XML and not try and be lazy.. It almost never works!
        if (array_key_exists('@content', $aData) && is_string($aData['@content'])) {
          if (gAppUtils::Contains($aData['@content'], ["<", ">", "?", "&", "'", "\""])) {
            $content = $dom->createCDATASection($aData['@content'] ?? kEmptyString);
          }
          else {
            $content = $dom->createTextNode($aData['@content'] ?? kEmptyString);
          }

          $element->appendChild($content);
        }
       
        // Add any attributes
        if (!empty($aData['@attr']) && is_array($aData['@attr'])) {
          foreach ($aData['@attr'] as $_key => $_value) {
            $element->setAttribute($_key, $_value);
          }
        }
       
        // Any other items in the data array should be child elements
        foreach ($aData as $_key => $_value) {
          if (!is_numeric($_key)) {
            continue;
          }
       
          $child = $processChildren($_value);
          if ($child) {
            $element->appendChild($child);
          }
        }
       
        return $element;
      };

      $child = $processChildren($aData);
      $xml = null;

      if ($child) {
        $dom->appendChild($child);
      }

      $xml = $dom->saveXML();

      if (!$xml) {
        gMetropolis::Error('Could not generate extensible markup.');
      }
    }

    if ($aDirectOutput) {
      gConUtils::Output($xml, 'xml');
    }

    return $xml;
  }
}

} // ==================================================================================================================
// ====================================================================================================================

namespace mozilla\vc { // == | nsIVersionComparator | =================================================================

/**
* Implements Mozilla Toolkit's nsIVersionComparator
*
* Version strings are dot-separated sequences of version-parts.
*
* A version-part consists of up to four parts, all of which are optional:
* <number-a><string-b><number-c><string-d (everything else)>
* A version-part may also consist of a single asterisk "*" which indicates
* "infinity".
*
* Numbers are base-10, and are zero if left out.
* Strings are compared bytewise.
*
* For additional backwards compatibility, if "string-b" is "+" then
* "number-a" is incremented by 1 and "string-b" becomes "pre".
*
* 1.0pre1
* < 1.0pre2  
*   < 1.0 == 1.0.0 == 1.0.0.0
*     < 1.1pre == 1.1pre0 == 1.0+
*       < 1.1pre1a
*         < 1.1pre1
*           < 1.1pre10a
*             < 1.1pre10
*
* Although not required by this interface, it is recommended that
* numbers remain within the limits of a signed char, i.e. -127 to 128.
*/
class ToolkitVersionPart {
  public $numA = 0;
  public $strB = null;
  public $numC = 0;
  public $extraD = null;
}

class ToolkitVersionComparator {
  public static function compare($a, $b) {
    do {
      $va = new ToolkitVersionPart();
      $vb = new ToolkitVersionPart();
      $a = self::parseVersionPart($a, $va);
      $b = self::parseVersionPart($b, $vb);
      
      $result = self::compareVersionPart($va, $vb);
      
      if ($result != 0){
        break;
      }
    }

    while ($a != null || $b != null);

    if ($result >= 1) { $result = 1; }
    if ($result <= -1) { $result = -1; }

    return $result;
  }
  
  
  private static function parseVersionPart($aVersion, ToolkitVersionPart $result) {
    if ($aVersion === null || strlen($aVersion) == 0) {
      return $aVersion;
    }
    
    $tok = explode(".", trim($aVersion));
    $part = $tok[0];
    
    if ($part == "*") {
      $result->numA = PHP_INT_MAX;
      $result->strB = "";
    }
    else {
      $vertok = new ToolkitVersionPartTokenizer($part);
      $next = $vertok->nextToken();
      if (is_numeric($next)){
        $result->numA = $next;
      }
      else {
        $result->numA = 0;
      }
      
      if ($vertok->hasMoreElements()) {
        $str = $vertok->nextToken();
        // if part is of type "<num>+"
        if ($str[0] == '+') {
          $result->numA++;
          $result->strB = "pre";
        }
        else {
          // else if part is of type "<num><alpha>..."
          $result->strB = $str;
          
          if ($vertok->hasMoreTokens()) {
            $next = $vertok->nextToken();
            if (is_numeric($next)){
              $result->numC = $next;
            }
            else {
              $result->numC = 0;
            }
            if ($vertok->hasMoreTokens()) {
              $result->extraD = $vertok->getRemainder();
            }
          }
        }
      }
    }

    if (sizeOf($tok)>1) {
      // return everything after "."
      return substr($aVersion, strlen($part) + 1);
    }

    return null;
  }
  
  
  private static function compareVersionPart(ToolkitVersionPart $va, ToolkitVersionPart $vb) {
    $res = self::compareInt($va->numA, $vb->numA);
    if ($res != 0) { return $res; }
    
    $res = self::compareString($va->strB, $vb->strB);
    if ($res != 0) { return $res; }
    
    $res = self::compareInt($va->numC, $vb->numC);
    if ($res != 0) { return $res; }
    
    return self::compareString($va->extraD, $vb->extraD);
  }
  
  
  private static function compareInt($n1, $n2) { return $n1 - $n2; }
  
  
  private static function compareString($str1, $str2) {
    // any string is *before* no string
    if ($str1 === null) { return ($str2 !== null) ? 1 : 0; }
    if ($str2 === null) { return -1; }
    return strcmp($str1, $str2);
  } 
}

/**
 * Specialized tokenizer for Mozilla version strings.  A token can
 * consist of one of the four sections of a version string:
 * <number-a><string-b><number-c><string-d (everything else)>
 */
class ToolkitVersionPartTokenizer {
  private $part = '';
  
  public function __construct($aPart) { $this->part = $aPart; }
  
  public function hasMoreElements() { return strlen($this->part) != 0; }
  public function hasMoreTokens() { return strlen($this->part) != 0; }
  public function nextToken() { return $this->nextElement(); }
  
  public function nextElement() {
    if (preg_match('/^[\+\-]?[0-9].*/', $this->part)) {
      // if string starts with a number...
      $index = 0;
      if ($this->part[0] == '+' || $this->part[0] == '-') {
        $index = 1;
      }
      while (($index < strlen($this->part)) && is_numeric($this->part[$index])) {
        $index++;
      }
      $numPart = substr($this->part, 0, $index);
      $this->part = substr($this->part, $index);
      return $numPart;
    }
    else {
      // ... or if this is the non-numeric part of version string
      $index = 0;
      while (($index < strlen($this->part)) && !is_numeric($this->part[$index])) {
        $index++;
      }
      $alphaPart = substr($this->part, 0, $index);
      $this->part = substr($this->part, $index);
      return $alphaPart;
    }
  }

  /**
   * Returns what remains of the original string, without tokenization.  This
   * method is useful for getting the <string-d (everything else)>;
   * section of a version string.
   * 
   * @return remaining version string
   */
  public function getRemainder() { return $this->part; }
}

} // ==================================================================================================================
// ====================================================================================================================

namespace Adbar { // == | ArrayUtils | ===================================================================

use Countable;
use ArrayAccess;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;
use Traversable;

/**
 * Dot
 *
 * This class provides a dot notation access and helper functions for
 * working with arrays of data. Inspired by Laravel Collection.
 *
 * @template TKey of array-key
 * @template TValue mixed
 *
 * @implements \ArrayAccess<TKey, TValue>
 * @implements \IteratorAggregate<TKey, TValue>
 */
class Dot implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable
{
  /**
   * The stored items
   *
   * @var array<TKey, TValue>
   */
  protected $items = [];

  /**
   * The character to use as a delimiter, defaults to dot (.)
   *
   * @var non-empty-string
   */
  protected $delimiter = ".";

  // ====

  /**
   * Create a new Dot instance
   *
   * @param  mixed  $items
   * @param  bool  $parse
   * @param  non-empty-string  $delimiter
   * @return void
   */
  public function __construct($items = [], $parse = false, $delimiter = ".") {
    $items = $this->getArrayItems($items);
    $this->delimiter = $delimiter ?: ".";
    
    if ($parse) {
      $this->set($items);
    }
    else {
      $this->items = $items;
    }
  }

  /**
   * Set a given key / value pair or pairs
   * if the key doesn't exist already
   *
   * @param  array<TKey, TValue>|int|string  $keys
   * @param  mixed  $value
   * @return $this
   */
  public function add($keys, $value = null) {
    if (is_array($keys)) {
      foreach ($keys as $key => $value) {
        $this->add($key, $value);
      }
    }
    elseif ($this->get($keys) === null) {
      $this->set($keys, $value);
    }

    return $this;
  }

  /**
   * Return all the stored items
   *
   * @return array<TKey, TValue>
   */
  public function all() { return $this->items; }

  /**
   * Delete the contents of a given key or keys
   *
   * @param  array<TKey>|int|string|null  $keys
   * @return $this
   */
  public function clear($keys = null)
  {
    if ($keys === null) {
      $this->items = [];
      return $this;
    }

    $keys = (array) $keys;
    foreach ($keys as $key) { $this->set($key, []); }
    return $this;
  }

  /**
   * Delete the given key or keys
   *
   * @param  array<TKey>|array<TKey, TValue>|int|string  $keys
   * @return $this
   */
  public function delete($keys) {
    $keys = (array) $keys;

    foreach ($keys as $key) {
      if ($this->exists($this->items, $key)) {
        unset($this->items[$key]);
        continue;
      }

      $items = &$this->items;
      $segments = explode($this->delimiter, $key);
      $lastSegment = array_pop($segments);

      foreach ($segments as $segment) {
        if (!isset($items[$segment]) || !is_array($items[$segment])) {
          continue 2;
        }

        $items = &$items[$segment];
      }

      unset($items[$lastSegment]);
    }

    return $this;
  }

  /**
   * Checks if the given key exists in the provided array.
   *
   * @param  array<TKey, TValue>  $array Array to validate
   * @param  int|string  $key  The key to look for
   * @return bool
   */
  protected function exists($array, $key) { return array_key_exists($key, $array); }

  /**
   * Flatten an array with the given character as a key delimiter
   *
   * @param  string  $delimiter
   * @param  mixed  $items
   * @param  string  $prepend
   * @return array<TKey, TValue>
   */
  public function flatten($delimiter = '.', $items = null, $prepend = '') {
    $flatten = [];

    if ($items === null) {
      $items = $this->items;
    }

    foreach ($items as $key => $value) {
      if (is_array($value) && !empty($value)) {
        $flatten[] = $this->flatten($delimiter, $value, $prepend . $key . $delimiter);
      }
      else {
        $flatten[] = [$prepend . $key => $value];
      }
    }

    return array_merge(...$flatten);
  }

  /**
   * Return the value of a given key
   *
   * @param  int|string|null  $key
   * @param  mixed  $default
   * @return mixed
   */
  public function get($key = null, $default = null) {
    if ($key === null) { return $this->items; }

    if ($this->exists($this->items, $key)) {
      return $this->items[$key];
    }

    if (!is_string($key) || strpos($key, $this->delimiter) === false) {
      return $default;
    }

    $items = $this->items;

    foreach (explode($this->delimiter, $key) as $segment) {
      if (!is_array($items) || !$this->exists($items, $segment)) {
        return $default;
      }

      $items = &$items[$segment];
    }

    return $items;
  }

  /**
   * Return the given items as an array
   *
   * @param  array<TKey, TValue>|self<TKey, TValue>|object|string  $items
   * @return array<TKey, TValue>
   */
  protected function getArrayItems($items) {
    if (is_array($items)) { return $items; }

    if ($items instanceof self) {
      return $items->all();
    }

    return (array) $items;
  }

  /**
   * Check if a given key or keys exists
   *
   * @param  array<TKey>|int|string  $keys
   * @return bool
   */
  public function has($keys) {
    $keys = (array) $keys;

    if (!$this->items || $keys === []) {
      return false;
    }

    foreach ($keys as $key) {
      $items = $this->items;

      if ($this->exists($items, $key)) {
        continue;
      }

      foreach (explode($this->delimiter, $key) as $segment) {
        if (!is_array($items) || !$this->exists($items, $segment)) {
          return false;
        }

        $items = $items[$segment];
      }
    }

    return true;
  }

  /**
   * Check if a given key or keys are empty
   *
   * @param  array<TKey>|int|string|null  $keys
   * @return bool
   */
  public function isEmpty($keys = null) {
    if ($keys === null) {
      return empty($this->items);
    }

    $keys = (array) $keys;

    foreach ($keys as $key) {
      if (!empty($this->get($key))) {
        return false;
      }
    }

    return true;
  }

  // ====

  /**
   * Merge a given array or a Dot object with the given key
   * or with the whole Dot object
   *
   * @param  array<TKey, TValue>|self<TKey, TValue>|string  $key
   * @param  array<TKey, TValue>|self<TKey, TValue>  $value
   * @return $this
   */
  public function merge($key, $value = []) {
    if (is_array($key)) {
      $this->items = array_merge($this->items, $key);
    }
    elseif (is_string($key)) {
      $items = (array) $this->get($key);
      $value = array_merge($items, $this->getArrayItems($value));

      $this->set($key, $value);
    }
    elseif ($key instanceof self) {
      $this->items = array_merge($this->items, $key->all());
    }

    return $this;
  }

  // ====

  /**
   * Recursively merge a given array or a Dot object with the given key
   * or with the whole Dot object.
   *
   * Duplicate keys are converted to arrays.
   *
   * @param  array<TKey, TValue>|self<TKey, TValue>|string  $key
   * @param  array<TKey, TValue>|self<TKey, TValue>  $value
   * @return $this
   */
  public function mergeRecursive($key, $value = []) {
    if (is_array($key)) {
      $this->items = array_merge_recursive($this->items, $key);
    }
    elseif (is_string($key)) {
      $items = (array) $this->get($key);
      $value = array_merge_recursive($items, $this->getArrayItems($value));

      $this->set($key, $value);
    }
    elseif ($key instanceof self) {
      $this->items = array_merge_recursive($this->items, $key->all());
    }

    return $this;
  }

  // ====

  /**
   * Recursively merge a given array or a Dot object with the given key
   * or with the whole Dot object.
   *
   * Instead of converting duplicate keys to arrays, the value from
   * given array will replace the value in Dot object.
   *
   * @param  array<TKey, TValue>|self<TKey, TValue>|string  $key
   * @param  array<TKey, TValue>|self<TKey, TValue>  $value
   * @return $this
   */
  public function mergeRecursiveDistinct($key, $value = []) {
    if (is_array($key)) {
      $this->items = $this->arrayMergeRecursiveDistinct($this->items, $key);
    }
    elseif (is_string($key)) {
      $items = (array) $this->get($key);
      $value = $this->arrayMergeRecursiveDistinct($items, $this->getArrayItems($value));

      $this->set($key, $value);
    }
    elseif ($key instanceof self) {
      $this->items = $this->arrayMergeRecursiveDistinct($this->items, $key->all());
    }

    return $this;
  }

  // ====

  /**
   * Merges two arrays recursively. In contrast to array_merge_recursive,
   * duplicate keys are not converted to arrays but rather overwrite the
   * value in the first array with the duplicate value in the second array.
   *
   * @param  array<TKey, TValue>|array<TKey, array<TKey, TValue>>  $array1 Initial array to merge
   * @param  array<TKey, TValue>|array<TKey, array<TKey, TValue>>  $array2 Array to recursively merge
   * @return array<TKey, TValue>|array<TKey, array<TKey, TValue>>
   */
  protected function arrayMergeRecursiveDistinct(array $array1, array $array2) {
    $merged = &$array1;

    foreach ($array2 as $key => $value) {
      if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
        $merged[$key] = $this->arrayMergeRecursiveDistinct($merged[$key], $value);
      }
      else {
        $merged[$key] = $value;
      }
    }

    return $merged;
  }

  // ====

  /**
   * Return the value of a given key and
   * delete the key
   *
   * @param  int|string|null  $key
   * @param  mixed  $default
   * @return mixed
   */
  public function pull($key = null, $default = null) {
    if ($key === null) {
      $value = $this->all();
      $this->clear();

      return $value;
    }

    $value = $this->get($key, $default);
    $this->delete($key);

    return $value;
  }

  // ====

  /**
   * Push a given value to the end of the array
   * in a given key
   *
   * @param  mixed  $key
   * @param  mixed  $value
   * @return $this
   */
  public function push($key, $value = null) {
    if ($value === null) {
      $this->items[] = $key;

      return $this;
    }

    $items = $this->get($key);

    if (is_array($items) || $items === null) {
      $items[] = $value;
      $this->set($key, $items);
    }

    return $this;
  }

  // ====

  /**
   * Replace all values or values within the given key
   * with an array or Dot object
   *
   * @param  array<TKey, TValue>|self<TKey, TValue>|string  $key
   * @param  array<TKey, TValue>|self<TKey, TValue>  $value
   * @return $this
   */
  public function replace($key, $value = []) {
    if (is_array($key)) {
      $this->items = array_replace($this->items, $key);
    }
    elseif (is_string($key)) {
      $items = (array) $this->get($key);
      $value = array_replace($items, $this->getArrayItems($value));

      $this->set($key, $value);
    }
    elseif ($key instanceof self) {
      $this->items = array_replace($this->items, $key->all());
    }

    return $this;
  }

  // ====

  /**
   * Set a given key / value pair or pairs
   *
   * @param  array<TKey, TValue>|int|string  $keys
   * @param  mixed  $value
   * @return $this
   */
  public function set($keys, $value = null) {
    if (is_array($keys)) {
      foreach ($keys as $key => $value) {
        $this->set($key, $value);
      }

      return $this;
    }

    $items = &$this->items;

    if (is_string($keys)) {
      foreach (explode($this->delimiter, $keys) as $key) {
        if (!isset($items[$key]) || !is_array($items[$key])) {
          $items[$key] = [];
        }

        $items = &$items[$key];
      }
    }

    $items = $value;

    return $this;
  }

  // ====

  /**
   * Replace all items with a given array
   *
   * @param  mixed  $items
   * @return $this
   */
  public function setArray($items) {
    $this->items = $this->getArrayItems($items);

    return $this;
  }

  // ====

  /**
   * Replace all items with a given array as a reference
   *
   * @param  array<TKey, TValue>  $items
   * @return $this
   */
  public function setReference(array &$items) {
    $this->items = &$items;

    return $this;
  }

  // ====

  /**
   * Return the value of a given key or all the values as JSON
   *
   * @param  mixed  $key
   * @param  int  $options
   * @return string|false
   */
  public function toJson($key = null, $options = 0) {
    if (is_string($key)) {
      return json_encode($this->get($key), $options);
    }

    $options = $key === null ? 0 : $key;

    return json_encode($this->items, $options);
  }

  // ====

  /**
   * Output or return a parsable string representation of the
   * given array when exported by var_export()
   *
   * @param  array<TKey, TValue>  $items
   * @return object
   */
  public static function __set_state(array $items): object { return (object) $items; }

  // ====

  /*
   * --------------------------------------------------------------
   * ArrayAccess interface
   * --------------------------------------------------------------
   */

  /**
   * Check if a given key exists
   *
   * @param  int|string  $key
   * @return bool
   */
  public function offsetExists($key): bool { return $this->has($key); }

  // ====

  /**
   * Return the value of a given key
   *
   * @param  int|string  $key
   * @return mixed
   */
  #[\ReturnTypeWillChange]
  public function offsetGet($key) { return $this->get($key); }

  // ====

  /**
   * Set a given value to the given key
   *
   * @param int|string|null  $key
   * @param mixed  $value
   */
  public function offsetSet($key, $value): void {
    if ($key === null) {
      $this->items[] = $value;

      return;
    }

    $this->set($key, $value);
  }

  // ====

  /**
   * Delete the given key
   *
   * @param  int|string  $key
   * @return void
   */
  public function offsetUnset($key): void { $this->delete($key); }

  // ====

  /*
   * --------------------------------------------------------------
   * Countable interface
   * --------------------------------------------------------------
   */

  /**
   * Return the number of items in a given key
   *
   * @param  int|string|null  $key
   * @return int
   */
  public function count($key = null): int { return count($this->get($key)); }

  // ====

  /*
   * --------------------------------------------------------------
   * IteratorAggregate interface
   * --------------------------------------------------------------
   */

  /**
   * Get an iterator for the stored items
   *
   * @return \ArrayIterator<TKey, TValue>
   */
  public function getIterator(): Traversable { return new ArrayIterator($this->items); }

  // ====

  /*
   * --------------------------------------------------------------
   * JsonSerializable interface
   * --------------------------------------------------------------
   */

  /**
   * Return items for JSON serialization
   *
   * @return array<TKey, TValue>
   */
  public function jsonSerialize(): array { return $this->items; }
}

} // ==================================================================================================================
// ====================================================================================================================

namespace { // ========================================================================================================

// An application has been detected - Unimatrix 424, Grid 116 - activate.
gMetropolis::init();

} // ==================================================================================================================
// ====================================================================================================================
<?php /****************************************************************************************************************
* This Source Code Form is subject to the terms of the Mozilla Public
* License, v. 2.0. If a copy of the MPL was not distributed with this
* file, You can obtain one at http://mozilla.org/MPL/2.0/.
*
* Portions of this file are under other licenses. This is noted where
* it is relevant.
**********************************************************************************************************************/

namespace { // == | Setup and Global Constants | ======================================================================

const kUtilsPhpMinVersion = '8.1';

// Check that we can run on this version of PHP
if (gVersionCompare(PHP_VERSION, kUtilsPhpMinVersion) < 0) {
  die('BinOC Metropolis Utilities: PHP ' . kUtilsPhpMinVersion . ' or newer is required.');
}

// --------------------------------------------------------------------------------------------------------------------

// Check for ROOT_PATH
if (!defined('ROOT_PATH')) {
  die('BinOC Metropolis Utilities: You MUST define ROOT_PATH.');
}

// Do not allow this to be included more than once...
if (defined('kUtilities')) {
  die('BinOC Metropolis Utilities: You may not include this file more than once.');
}

// Define that this is a thing which can double as a version check.
define('kUtilities', '2.0.0a1');

// --------------------------------------------------------------------------------------------------------------------

// We like CLI
define('SAPI_IS_CLI', php_sapi_name() == "cli");
define('CLI_NO_LOGO', in_array('--nologo', $GLOBALS['argv'] ?? []));

// Enable Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
const E_EXCEPTION = 65536;

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


// ------------------------------------------------------------------------------------------------------------------

if (!defined('kAppVendor')) {
  define('kAppVendor', 'Binary Outcast');
}

if (!defined('kAppName')) {
  define('kAppName', 'Metropolis-based Software');
}

if (!defined('kAppVersion')) {
  define('kAppVersion', kUtilities);
}

if (!defined('kAppRepository')) {
  define('kAppRepository', '#');
}

// --------------------------------------------------------------------------------------------------------------------

// Debug flag (CLI always triggers debug mode)
define('kDebugMode', $_GET['debug'] ?? SAPI_IS_CLI);

// --------------------------------------------------------------------------------------------------------------------

const kFileWriteFlags      = "w+";
const kFileExt             = array(
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

// ------------------------------------------------------------------------------------------------------------------

const kXmlTag               = '<?xml version="1.0" encoding="utf-8" ?>';

// ------------------------------------------------------------------------------------------------------------------

const kJsonFlags            = array(
  'display'                 => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
  'storage'                 => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
);

// ------------------------------------------------------------------------------------------------------------------

const kRegexPatterns        = array(
  'query'                   => "/[^-a-zA-Z0-9_\-\/\{\}\@\.\%\s\,]/",
  'yaml'                    => "/\A---(.|\n)*?---/",
  'guid'                    => "/^\{[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}$/i",
  'host'                    => "/[a-z0-9-\._]+\@[a-z0-9-\._]+/i",
);

// ------------------------------------------------------------------------------------------------------------------

const PASSWORD_CLEARTEXT    = "clrtxt";
const PASSWORD_HTACCESS     = "apr1";
const BASE64_ALPHABET       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
const APRMD5_ALPHABET       = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

// ------------------------------------------------------------------------------------------------------------------

const kDefaultSkinName      = 'default';
const kDefaultSkinPath      = '/base/skin';
const kDefaultMenu          = ['/' => 'Front Page (Home)'];
const kSpecialComponent     = 'special';
const kSpecialComponentName = 'Special Component';

// XXX: Remove these!
const PHP_EXTENSION         = kFileExt['php'];
const JSON_EXTENSION        = kFileExt['json'];
const PALEMOON_GUID         = '{8de7fcbb-c55c-4fbe-bfc5-fc555c87dbc4}';
const REGEX_GET_FILTER      = kRegexPatterns['query'];
const JSON_ENCODE_FLAGS     = kJsonFlags['display'];

// ====================================================================================================================

// == | Static Registry Class | =======================================================================================

// Define a global array to hold the registry
$gRuntime = kEmptyArray;

class gRegistryUtils {
  private static $sInited = false;
  private static $sStore = kEmptyArray;
  private static $sRemap = ['constant', 'superglobal'];

  /********************************************************************************************************************
  * Init the static class
  ********************************************************************************************************************/
  public static function init() {
    if (self::$sInited) {
      return;
    }

    $domain = function($aHost, $aReturnSub = false) {
      $host = gExplodeStr(kDot, $aHost);
      return implode(kDot, $aReturnSub ? array_slice($host, 0, -2) : array_slice($host, -2, 2));
    };

    $path = gExplodePath(self::SuperGlobal('get', 'path', kSlash));

    self::$sStore = array(
      'app' => array(
        'component'   => self::SuperGlobal('get', 'component', 'site'),
        'path'        => $path,
        'depth'       => count($path ?? kEmptyArray),
        'debug'       => kDebugMode,
        'offline'     => file_exists(gBuildPath(ROOT_PATH, '.offline')),
      ),
      'network' => array(
        'scheme'      => self::SuperGlobal('server', 'SCHEME') ?? (self::SuperGlobal('server', 'HTTPS') ? 'https' : 'http'),
        'baseDomain'  => $domain(self::SuperGlobal('server', 'SERVER_NAME', 'localhost')),
        'subDomain'   => $domain(self::SuperGlobal('server', 'SERVER_NAME', 'localhost'), true),
        'remoteAddr'  => self::SuperGlobal('server', 'HTTP_X_FORWARDED_FOR', self::SuperGlobal('server', 'REMOTE_ADDR', '127.0.0.1')),
        'userAgent'   => self::SuperGlobal('server', 'HTTP_USER_AGENT', 'php' . kDash . PHP_SAPI . kSlash . PHP_VERSION),
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
      self::$sStore['app']['debug'] =
        (self::SuperGlobal('server', 'SERVER_NAME', 'localhost') != constant('kDebugDomain') ?? kEmptyString) ?
        file_exists(gBuildPath(ROOT_PATH, '.debugMode')) :
        !kDebugMode;
    }

    self::$sInited = true;
  }

  /********************************************************************************************************************
  * Get the registry property and return it
  ********************************************************************************************************************/
  public static function Component(?string $aCompareComponent = null) {
    $rv = (self::$sInited) ? self::GetRegistryValue('app.component') : self::SuperGlobal('get', 'component', 'site');

    if ($aCompareComponent) {
      $rv = ($rv === $aCompareComponent);
    }

    return $rv;
  }

  /********************************************************************************************************************
  * Get the registry property and return it
  ********************************************************************************************************************/
  public static function debug() {
    return (self::$sInited) ? self::GetRegistryValue('app.debug') : kDebugMode;
  }

  /********************************************************************************************************************
  * Get the registry property and return it
  ********************************************************************************************************************/
  public static function GetStore() {
    return self::$sStore;
  }

  /********************************************************************************************************************
  * Get the value of a dotted key from the registry property except for virtual regnodes
  ********************************************************************************************************************/
  public static function GetRegistryValue(string $aKey, $aDefault = null) {
    if ($aKey == kEmptyString) {
      return null;
    }

    $keys = gExplodeStr(kDot, $aKey);

    if (in_array($keys[0] ?? kEmptyString, self::$sRemap)) {
      switch ($keys[0] ?? kEmptyString) {
        case 'constant':
          if (count($keys) < 2) {
            return null;
          }

          $ucConst = strtoupper($keys[1]);
          $prefixConst = 'k' . ucfirst($keys[1]);

          switch (true) {
            case defined($prefixConst):
              $rv = constant($prefixConst);
              break;
            case defined($ucConst):
              $rv = constant($ucConst);
              break;
            case defined($keys[1]):
              $rv = constant($keys[1]);
              break;
            default:
              return null;
          }

          if (!\Illuminate\Support\Arr::accessible($rv)) {
            return $rv ?? $aDefault;
          }

          unset($keys[0], $keys[1]);
          $rv = \Illuminate\Support\Arr::get($rv, gCheckValue(implode(kDot, $keys)), $aDefault);
          break;
        case 'superglobal':
          if (count($keys) < 3) {
            return null;
          }

          $rv = self::SuperGlobal($keys[1], $keys[2]);

          if (!Illuminate\Support\Arr::accessible($rv)) {
            return $rv ?? $aDefault;
          }

          unset($keys[0], $keys[1]);
          $rv = \Illuminate\Support\Arr::get($rv, gCheckValue(implode(kDot, $keys)), $aDefault);
          break;
        default:
          if (count($keys) < 2 || str_starts_with($keys[1], kUnderbar)) {
            return null;
          }

          unset($keys[0]);
          $rv = \Illuminate\Support\Arr::get($GLOBALS, gCheckValue(implode(kDot, $keys)), $aDefault);
      }
    }
    else {
      $rv = \Illuminate\Support\Arr::get(self::$sStore, $aKey, $aDefault);
    }
      
    return $rv;
  }

  /********************************************************************************************************************
  * Set the value of a dotted key from the registry property
  ********************************************************************************************************************/
  public static function SetRegistryValue(string $aKey, string|int|bool|array|null $aValue) {
    if (in_array(gExplodeStr(kDot, $aKey)[0] ?? kEmptyString, self::$sRemap)) {
      gError('Setting values on virtual nodes is not supported.');
    }

    if (gContains($aKey, '[]', -1)) {
      $aKey = substr($aKey, 0, -2);
      $value = gRegistry($aKey, kEmptyArray);

      if (!is_array($value)) {
        $value = [$value];
      }

      $value[] = $aValue;
      $aValue = $value;
    }

    return \Illuminate\Support\Arr::set(self::$sStore, $aKey, $aValue);
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
        $rv = gCheckValue($aKey);
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
            $val = gCheckValue(str_replace('"', kEmptyString, $arg[1]));

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
        gError('Unknown system node.');
    }
    
    // We always pass $_GET values through a general regular expression
    // This allows only a-z A-Z 0-9 - / { } @ % whitespace and ,
    if ($rv && $aNode == "_GET") {
      $rv = preg_replace(REGEX_GET_FILTER, kEmptyString, $rv);
    }

    // Files need special handling.. In principle we hard fail if it is anything other than
    // OK or NO FILE
    if ($rv && $aNode == "_FILES") {
      if (!in_array($rv['error'], [UPLOAD_ERR_OK, UPLOAD_ERR_NO_FILE])) {
        gError('Upload of ' . $aKey . ' failed with error code: ' . $rv['error']);
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
}

// ====================================================================================================================

// == | Static Error Class | ==========================================================================================

class gErrorUtils {
  const kPhpErrorCodes = array(
    E_ERROR                   => 'PHP Error',
    E_WARNING                 => 'PHP Warning',
    E_PARSE                   => 'PHP Error (Parser)',
    E_NOTICE                  => 'PHP Notice',
    E_CORE_ERROR              => 'PHP Error (Core)',
    E_CORE_WARNING            => 'PHP Warning (Core)',
    E_COMPILE_ERROR           => 'PHP Error (Compiler)',
    E_COMPILE_WARNING         => 'PHP Warning (Compiler)',
    E_USER_ERROR              => 'PHP Error (Application)',
    E_USER_WARNING            => 'PHP Warning (Application)',
    E_USER_NOTICE             => 'PHP Notice (Application)',
    E_STRICT                  => 'PHP Error (Strict)',
    E_RECOVERABLE_ERROR       => 'PHP Error (Recoverable)',
    E_DEPRECATED              => 'PHP Deprecation',
    E_USER_DEPRECATED         => 'PHP Deprecation (Application)',
    E_ALL                     => 'Unable to Comply',
    E_EXCEPTION               => 'PHP Exception',
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
    $traceline = fn($eFile, $eLine) => str_replace(ROOT_PATH, kEmptyString, $eFile) . kColon . $eLine;
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
      $content = is_string($content) ?
                 '<h2 style="display: block; border-bottom: 1px solid #d6e5f5; font-weight: bold;">Issue Details</h2>' .
                 '<p>' . $content . '</p>':
                 kEmptyString;

      $content .= '<h3>Traceback:</h3><ul>';

      foreach ($trace as $_value) {
        $content .= '<li>' . $_value . '</li>';
      }

      $commandBar = ['onclick="history.back()"' => 'Go Back'];

      if (gRegistryUtils::Component(kSpecialComponent) || !gRegistry('constant.components.site')) {
        gRegSet('console.content.commandbar', array_merge($commandBar, ['/special/' => kSpecialComponentName]));
      }
      else {
        gRegSet('console.content.commandbar', array_merge($commandBar, kDefaultMenu));
      }

      gRegSet('console.content.sectionName', kEmptyString);
      gContent($content, ['title' => $title, 'statustext' => 'Please contact a system administrator.']);
    }

    gOutput(['title'=> $title, 'content' => ['errorMessage' => $content, 'traceback' => $trace]]);
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

class gConsoleUtils {
  const HTTP_HEADERS = array(
    404                       => 'HTTP/1.1 404 Not Found',
    501                       => 'HTTP/1.1 501 Not Implemented',
  );

  const kStatusCodes = array(
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

  const kMimeTypes = array(
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
  * @dep gError()
  * @param $aHeader    Short name of header
  **********************************************************************************************************************/
  public static function Header(string|int $aHeader, bool $aSendAllOnContentType = false) { 
    if (is_int($aHeader) && gContains(self::kStatusCodes, $aHeader, 1)) {
      self::HttpStatusCode($aHeader);
      
      if ($aHeader < 400) {
        return true;
      }
      else {
        self::SendHeaders();
        exit();
      }
    }

    if (gContains(self::kMimeTypes, $aHeader, 1)) {
      self::ContentType($aHeader);

      if ($aSendAllOnContentType) {
        return self::SendHeaders();
      }
      else {
        return true;
      }
    }

    return gRegSet('console.output.httpHeaders[]', $aHeader);
  }

  /********************************************************************************************************************
  * Gets or sets the "default" content type so we don't have to output the header ourselves in most cases.
  ********************************************************************************************************************/
  public static function SendHeaders() {
    $responseCode = gRegistry('console.output.responseCode', 200);
    gRegSet('console.output.httpHeaders[]', 'Content-type'. kColon . kSpace . gRegistry('console.output.contentType'));
    gRegSet('console.output.httpHeaders[]', 'HTTP/1.1' . kSpace . $responseCode . kSpace . self::kStatusCodes[$responseCode]);

    $headers = gRegistry('console.output.httpHeaders', kEmptyArray);

    foreach ($headers as $_value) {
      header(trim($_value), true);
    }
  }

  /********************************************************************************************************************
  * Gets or sets the "default" content type so we don't have to output the header ourselves in most cases.
  ********************************************************************************************************************/
  public static function HttpStatusCode(?string $aStatusCode = null) {
    if (!$aStatusCode) {
      gRegistry('console.output.responseCode', 200);
    }

    if (gContains(self::kStatusCodes, $aStatusCode, 1)) {
      gRegSet('console.output.responseCode', $aStatusCode);
    }
  }

  /********************************************************************************************************************
  * Gets or sets the "default" content type so we don't have to output the header ourselves in most cases.
  ********************************************************************************************************************/
  public static function ContentType(?string $aContentType = null) {
     if ($aContentType === null) {
      return gRegistry('console.output.contentType');
    }

    if (gContains(self::kMimeTypes, $aContentType, 1)) {
      return gRegSet('console.output.contentType', self::kMimeTypes[$aContentType]);
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
  * @dep gExplodeStr()
  * @param $aHost       Hostname
  * @param $aReturnSub  Should return subdmain
  * @returns            domain or subdomain
  ***********************************************************************************************************************/
  public static function GetDomain(string $aHost, ?bool $aReturnSub = null) {
    $host = gExplodeStr(kDot, $aHost);
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
    $x1 = (gRegistryUtils::SuperGlobal('get', 'runtime'));
    $content = null;

    if ($x1) {
      self::Header('text', true);
      print(json_encode(gRegistryUtils::GetStore(), kJsonFlags['display']));
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

    $content = (is_string($content) || is_int($content)) ? $content : json_encode($content, kJsonFlags['display']);

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
    $template = SAPI_IS_CLI ? false : gReadFile(gBuildPath(ROOT_PATH, 'base', 'skin', 'template.xhtml'));
    $stylesheet = SAPI_IS_CLI ? false : gReadFile(gBuildPath(ROOT_PATH, 'base', 'skin', 'stylesheet.css'));

    if (!$template) {
      gOutput(['content' => $aContent, 'title' => $aMetadata['title'] ?? 'Output']);
    }

    $content = $aContent;

    $metadata = function($val) use(&$aMetadata) {
      return $aMetadata[$val] ?? null;
    };

    $menuize = function($aMenu) {
      $rv = kEmptyString;

      foreach ($aMenu as $_key => $_value) {
        if (gContains($_key, 'onclick=', 1)) {
          $rv .= '<li><a href="#"' . kSpace . $_key . '>' . $_value . '</a></li>';
        }
        else {
          $rv .= '<li><a href="' . $_key . '">' . $_value . '</a></li>';
        }
      }

      return $rv;
    };

    if ((is_string($content) || is_int($content)) && !$metadata('textbox') && !$metadata('iframe')) {
      if (!gContains($content, ['<p', '<ul', '<ol', '<h1', '<h2', '<h3', '<table'])) {
        $content = '<p>' . $content . '</p>';
      }
    }
    else {
      $aMetadata['textbox'] = true;
    }

    if ($metadata('textbox')) {
      $content = (is_string($content) || is_int($content)) ? $content : json_encode($content, kJsonFlags['display']);
      $content = '<form><textarea class="special-textbox" name="content" rows="30" readonly>' . $content . '</textarea></form>';
    }

    $siteName = gRegistry('console.content.siteName', kAppName);
    $sectionName = gRegistry('console.content.sectionName', kEmptyString);

    if ($sectionName) {
      $siteName = $sectionName . kSpaceDashSpace . $siteName;
    }

    $isTestCase = (!$metadata('title') && gRegistry('special.testCase') && gRegistryUtils::Component(kSpecialComponent));

    $substs = array(
      '{$SITE_STYLESHEET}'  => $stylesheet ?? kEmptyString,
      '{$PAGE_CONTENT}'     => $content,
      '{$SITE_DOMAIN}'      => gRegistryUtils::SuperGlobal('server', 'SERVER_NAME'),
      '{$SITE_NAME}'        => $siteName,
      '{$SITE_MENU}'        => $menuize(gRegistry('console.content.commandbar')),
      '{$SITE_SECTION}'     => $sectionName ?? kEmptyString,
      '{$PAGE_TITLE}'       => $isTestCase ? '[Test]' . kSpace . gRegistry('special.testCase') : ($metadata('title') ?? 'Output'),
      '{$PAGE_STATUS}'      => $metadata('statustext') ?? gRegistry('console.content.statustext'),
      '{$SKIN_PATH}'        => gBuildPath(kSlash, 'base', 'skin'),
      '{$SOFTWARE_VENDOR}'  => kAppVendor,
      '{$SOFTWARE_NAME}'    => kAppName,
      '{$SOFTWARE_VERSION}' => kAppVersion,
    );

    $content = gSubst($template, $substs);

    @ob_end_clean();
    gOutput($content, 'html');
  }

  /**********************************************************************************************************************
  * Special Component!
  ***********************************************************************************************************************/
  public static function SpecialComponent() {
    $spCurrentPath = gRegistry('app.path');
    $spPathCount = gRegistry('app.depth');

    if ($spCurrentPath[0] != kSpecialComponent) {
      gRedirect(kSlash . kSpecialComponent . kSlash);
    }

    gRegSet('app.component', kSpecialComponent);

    if (gRegistry('constant.disableSpecialComponent')) {
      gNotFound('The special component has been disabled.');
    }

    gRegSet('console.content.sectionName', kSpecialComponentName);

    // The Special Component never has more than one level below it
    // We still have to determine the root of the component though...
    if ($spPathCount == 1) {
      // URL /special/
      $spSpecialFunction = 'root';
    }
    else {
      // URL /special/xxx/
      if ($spPathCount > 2) {
        gNotFound('The special component only has one path level.');
      }
      $spSpecialFunction = $spCurrentPath[1];
    }

    $spCommandBar = array(
      '/special/'                 => kSpecialComponentName,
      '/special/test/'            => 'Test Cases',
      '/special/vc/'              => 'Version Compare',
      '/special/guid/'            => 'GUID',
      '/special/hex/'             => 'Hex String',
    );

    gRegSet('console.content.commandbar', gRegistry('constant.components.site') ?
                                               array_merge(kDefaultMenu, $spCommandBar) :
                                               $spCommandBar);

    unset($spCurrentPath, $spPathCount, $spCommandBar);

    switch ($spSpecialFunction) {
      case 'root':
        $spContent = '<h2>Welcome</h2>' .
                     '<p>Please select a special function from the command bar above.';
        gContent($spContent, ['title' => 'Overview']);
        break;
      case 'test':
        if (!gRegistryUtils::Debug()) {
          gNotFound('This special function is not available when not in debug mode.');
        }
        $spCase = gRegistry('superglobal.get.case');
        $spTestsPath = gBuildPath(ROOT_PATH, 'base', 'tests');
        $spGlobTests = glob(gBuildPath($spTestsPath, kAsterisk . PHP_EXTENSION));
        $spTests = kEmptyArray;

        foreach ($spGlobTests as $_value) {
          $spTests[] = gSubst($_value, [PHP_EXTENSION => kEmptyString, $spTestsPath . kSlash => kEmptyString]);
        }

        if ($spCase) {
          if (!gContains($spCase, $spTests)) {
            gError('Unknown test case.');
          }

          gRegSet('special.testCase', $spCase);
          require_once(gBuildPath($spTestsPath, $spCase . PHP_EXTENSION));
          headers_sent() ? exit() : gError('The operation completed successfully.');
        }

        $spContent = kEmptyString;

        foreach ($spTests as $_value) {
          $spContent .= '<li><a href="/special/test/?case=' . $_value . '">' . $_value . '</a></li>';
        }

        $spContent = ($spContent == kEmptyString) ?
                     '<p>There are no test cases.</p>' :
                     '<h2>Please select a test case&hellip;</h2><ul>' . $spContent . '</ul>' . str_repeat('<br />', 3);

        gContent($spContent, ['title' => 'Test Cases']);
        break;
      case 'vc':
        $spCurrVer = gRegistry('superglobal.post.currVer');
        $spCompVer = gRegistry('superglobal.post.compVer');

        if ($spCurrVer && $spCompVer) {
          gContent(gVersionCompare($spCurrVer, $spCompVer));
        }

        $spForm = '<form action="/special/vc/" method="post">Current Version:<br/><input type="text" name="currVer"><br/><br/>' .
                  'Compare to Version:<br/><input type="text" name="compVer"><br/><br/><input type="submit"></form>';

        gContent('<h2>nsIVersionComparator</h2>' . $spForm, ['title' => 'Runtime Status']);
        break;
      case 'guid':
        gContent(gGlobalIdentifer(gRegistry('superglobal.get.vendor'), true),
                 ['title' => 'Globally Unique Identifier (In XPIDL Notation)', 'textbox' => true]);
        break;
      case 'hex':
        gContent(gHexString(gRegistry('superglobal.get.length', 40)),
                 ['title' => 'Pseudo-Random Hex String', 'textbox' => true]);
        break;
      case 'system':
        if (!gRegistryUtils::Debug()) {
          gNotFound('This special function is not available when not in debug mode.');
        }
        self::Header('html', true);
        phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_ENVIRONMENT | INFO_VARIABLES);
        break;
      default:
        gNotFound('There is no matching case in the special component main switch.');
    }

    // We're done here
    exit();
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
          if (gContains($aData['@content'], ["<", ">", "?", "&", "'", "\""])) {
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
        gError('Could not generate extensible markup.');
      }
    }

    if ($aDirectOutput) {
      gOutput($xml, 'xml');
    }

    return $xml;
  }
}

// ====================================================================================================================

// == | Static Class Init and Global Wrappers | =======================================================================

gRegistryUtils::init();
gErrorUtils::init();

// --------------------------------------------------------------------------------------------------------------------

function gRegistry(...$args) { return gRegistryUtils::GetRegistryValue(...$args); }
function gRegSet(...$args) { return gRegistryUtils::SetRegistryValue(...$args); }

// --------------------------------------------------------------------------------------------------------------------

function gHeader(...$args) { return gConsoleUtils::Header(...$args); }
function gContentType(...$args) { return gConsoleUtils::ContentType(...$args); }
function gRedirect(...$args) { return gConsoleUtils::Redirect(...$args); }
function gContent(...$args) { return gConsoleUtils::Content(...$args); }
function gOutput(...$args) { return gConsoleUtils::Output(...$args); }

// --------------------------------------------------------------------------------------------------------------------

function gVersionCompare(...$args) { return mozilla\vc\ToolkitVersionComparator::compare(...$args); }

// --------------------------------------------------------------------------------------------------------------------

function gGetArrVal(...$args) { return Illuminate\Support\Arr::get(...$args); }
function gSetArrVal(...$args) { return Illuminate\Support\Arr::set(...$args); }
function gDelArrVal(...$args) { return Illuminate\Support\Arr::forget(...$args); }
function gDotArray(...$args) { return Illuminate\Support\Arr::dot(...$args); }
function gUndotArray(...$args) { return Illuminate\Support\Arr::undot(...$args); }

// ====================================================================================================================

// == | Global Functions | ============================================================================================

/**********************************************************************************************************************
* General Error Function
*
* @param $aMessage   Error message
**********************************************************************************************************************/
function gError(?string $aMessage = null) {
  if (!$aMessage) {
    $aMessage = 'No further details were provided.';
  }

  gErrorUtils::report(['code' => E_ALL, 'message' => $aMessage,
                       'file' => null, 'line' => null,
                       'trace' => debug_backtrace(2)]);
}

/**********************************************************************************************************************
* Sends 404 or prints error message if debug mode
**********************************************************************************************************************/
function gNotFound(?string $aMessage = null) {
  if (!$aMessage) {
    $aMessage = 'HTTP/1.1 404 Not Found';
  }

  if (gRegistryUtils::Debug()) {
    gErrorUtils::report(['code' => E_ALL, 'message' => $aMessage,
                         'file' => null, 'line' => null,
                         'trace' => debug_backtrace(2)]);
  }
  gHeader(404);
}

/**********************************************************************************************************************
* Check if a value should be null according to Phoebus-legacy behavior.
**********************************************************************************************************************/
function gCheckValue($aValue, $aFallback = null) {
  return (empty($aValue) || $aValue === 'none') ? $aFallback : $aValue;
}

/**********************************************************************************************************************
* Registers Files to be included such as components and modules
***********************************************************************************************************************/
function gRegisterIncludes($aConst, $aIncludes) {
  $aConst = strtoupper($aConst);

  if (defined($aConst)) {
    gError($aConst . kSpace . 'files are already registered and may not be updated.');
  }

  $includes = kEmptyArray;

  foreach($aIncludes as $_key => $_value) { 
    switch ($aConst) {
      case 'COMPONENTS':
        $includes[$_value] = gBuildPath(ROOT_PATH, 'components', $_value, 'src', $_value . kFileExt['php']);
        break;
      case 'MODULES':
        $includes[$_value] = gBuildPath(ROOT_PATH, 'modules', $_value . kFileExt['php']);
        break;
      case 'LIBRARIES':
        if (str_contains($_value, kDot . kDot)) {
          return;
        }

        $includes[$_key] = gBuildPath(ROOT_PATH, 'third_party', $_value);
        break;
      default:
        gfError('Unknown include type');
    }
  }

  define($aConst, $includes);
}

/**********************************************************************************************************************
* Loads a component.
***********************************************************************************************************************/
function gLoadComponent(string $aComponent) {
  if ($aComponent == kSpecialComponent) {
    gConsoleUtils::SpecialComponent();
  }

  $componentPath = gRegistry('constant.components' . kDot . $aComponent);

  if (!$componentPath) {
    gNotFound('Unknown component.');
  }

  if (!file_exists($componentPath)) {
    gNotFound('Failed to load the' . kSpace . $aComponent . kSpace .'component.');
  }

  require_once($componentPath);
}

/**********************************************************************************************************************
* Basic Filter Substitution of a string
*
* @dep kEmptyString
* @dep kSlash
* @dep kSpace
* @dep gError()
* @param $aSubsts               multi-dimensional array of keys and values to be replaced
* @param $aString               string to operate on
* @param $aRegEx                set to true if pcre
* @returns                      bitwise int value representing applications
***********************************************************************************************************************/
function gSubst(string $aString, array $aSubsts, bool $aRegEx = false) {
  $rv = $aString;
  $replaceFunction = $aRegEx ? 'preg_replace' : 'str_replace';

  foreach ($aSubsts as $_key => $_value) {
    $rv = call_user_func($replaceFunction, ($aRegEx ? kSlash . $_key . kSlash . 'iU' : $_key), $_value, $rv);
  }

  return !$rv ? gError('Something has gone wrong...') : $rv;
}

/**********************************************************************************************************************
* Placeholder substitution similar to the simplest useage of python string format.. Does not support {} or aligment.
***********************************************************************************************************************/
function gFormatStr(string $aString, ...$aSubsts) {
  $substs = kEmptyArray;
  foreach ($aSubsts as $_key => $_value) { $substs['{' . $_key . '}'] = $_value; }
  return gSubst($aString, $substs);
}

/**********************************************************************************************************************
* Determines if needle is in haystack and optionally where
***********************************************************************************************************************/
function gContains(string|array $aHaystack, string|array $aNeedle, int $aPos = 0) {
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
        case 1:
          $rv = str_starts_with($aHaystack, $_value);
          break;
        case -1:
          $rv = str_ends_with($aHaystack, $_value);
          break;
        case 0:
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
* Explodes a string to an array without empty elements if it starts or ends with the separator
*
* @dep kSpaceDashSpace
* @dep gError()
* @param $aSeparator   Separator used to split the string
* @param $aString      String to be exploded
* @returns             Array of string parts
***********************************************************************************************************************/
function gExplodeStr(string $aSeparator, string $aString) {
  return (!str_contains($aString, $aSeparator)) ? [$aString] :
          array_values(array_filter(explode($aSeparator, $aString), 'strlen'));
}

/**********************************************************************************************************************
* Splits a path into an indexed array of parts
*
* @dep kSlash
* @dep gExplodeStr()
* @param $aPath   URI Path
* @returns        array of uri parts in order
***********************************************************************************************************************/
function gExplodePath(string $aPath) {
  return ($aPath == kSlash) ? ['root'] : gExplodeStr(kSlash, $aPath);
}

/**********************************************************************************************************************
* Builds and Normalizes Paths
***********************************************************************************************************************/
function gBuildPath(...$aParts) {
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
* Strips a string from another string
***********************************************************************************************************************/
function gStripStr (string $aStr, string $aStrip = kEmptyString) {
  return str_replace($aStrip, kEmptyString, $aStr);
}

/**********************************************************************************************************************
* Read a file
***********************************************************************************************************************/
function gReadFile(string $aFile) {
  $rv = @file_get_contents($aFile);
  return gCheckValue($rv);
}

/**********************************************************************************************************************
* Hash a password
***********************************************************************************************************************/
function gPasswordHash(string $aPassword, mixed $aCrypt = PASSWORD_BCRYPT, ?string $aSalt = null) {
  switch ($aCrypt) {
    case PASSWORD_CLEARTEXT:
      // We can "hash" a cleartext password by prefixing it with the fake algo prefix $clear$
      if (str_contains($aPassword, kDollar)) {
        // Since the dollar sign is used as an identifier and/or separator for hashes we can't use passwords
        // that contain said dollar sign.
        gError('Cannot "hash" this Clear Text password because it contains a dollar sign.');
      }

      return kDollar . PASSWORD_CLEARTEXT . kDollar . time() . kDollar . $aPassword;
    case PASSWORD_HTACCESS:
      // We want to be able to generate Apache APR1-MD5 hashes for use in .htpasswd situations.
      $salt = $aSalt;

      if (!$salt) {
        $salt = kEmptyString;

        for ($i = 0; $i < 8; $i++) {
          $offset = hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % 64;
          $salt .= APRMD5_ALPHABET[$offset];
        }
      }

      $salt = substr($salt, 0, 8);
      $max = strlen($aPassword);
      $context = $aPassword . kDollar . PASSWORD_HTACCESS . kDollar . $salt;
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
      $hash = strtr(strrev(substr(base64_encode($hash), 2)), BASE64_ALPHABET, APRMD5_ALPHABET);

      return kDollar . PASSWORD_HTACCESS . kDollar . $salt . kDollar . $hash;
    default:
      // Else, our standard (and secure) default is PASSWORD_BCRYPT hashing.
      // We do not allow custom salts for anything using password_hash as PHP generates secure salts.
      // PHP Generated passwords are also self-verifiable via password_verify.
      return password_hash($aPassword, $aCrypt);
  }
}

/**********************************************************************************************************************
* Check a password
***********************************************************************************************************************/
function gPasswordVerify(string $aPassword, string $aHash) {
  // We can accept a pseudo-hash for clear text passwords in the format of $clrtxt$unix-epoch$clear-text-password
  if (str_starts_with($aHash, kDollar . PASSWORD_CLEARTEXT)) {
    $password = gExplodeStr(kDollar, $aHash) ?? null;

    if ($password == null || count($password) > 3) {
      gError('Unable to "verify" this Clear Text "hashed" password.');
    }

    return $aPassword === $password[2];
  }

  // We can also accept an Apache APR1-MD5 password that is commonly used in .htpasswd
  if (str_starts_with($aHash, kDollar . PASSWORD_HTACCESS)) {
    $salt = gExplodeStr(kDollar, $aHash)[1] ?? null;

    if(!$salt) {
      gError('Unable to verify this Apache APR1-MD5 hashed password.');
    }

    return gPasswordHash($aPassword, PASSWORD_HTACCESS, $salt) === $aHash;
  }

  // For everything else send to the native password_verify function.
  // It is almost certain to be a BCRYPT2 hash but hashed passwords generated BY PHP are self-verifiable.
  return password_verify($aPassword, $aHash);
}

/**********************************************************************************************************************
* Generate a random hexadecimal string
*
* @param $aLength   Desired number of final chars
* @returns          Random hexadecimal string of desired length
**********************************************************************************************************************/
function gHexString(int $aLength = 40) {
  return bin2hex(random_bytes(($aLength <= 1) ? 1 : (int)($aLength / 2)));
}

/**********************************************************************************************************************
* Generates a v4 random guid or a "v4bis" guid with static vendor node
***********************************************************************************************************************/
function gGlobalIdentifer(?string $aVendor = null, ?bool $aXPCOM = null) {
  if ($aVendor) {
    if (strlen($aVendor) < 3) {
      gError('v4bis GUIDs require a defined vendor of more than three characters long.');
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
    $explode = gExplodeStr(kDash, $guid);
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
} // ==================================================================================================================

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

namespace Illuminate\Support { // == | ArrayUtils | ===================================================================

/* The Arr class and its methods are under the following license:

  The MIT License (MIT)

  Copyright (c) Taylor Otwell

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
  documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
  rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
  persons to whom the Software is furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in all copies or substantial portions
  of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
  WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
  COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
  OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
class Arr {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return static::value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? static::value($default);
        }

        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return static::value($default);
            }
        }

        return $array;
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * Return the default value of the given value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array  $array
     * @param  string|int|null  $key
     * @param  mixed  $value
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
      }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array  $array
     * @param  array|string|int|float  $keys
     * @return void
     */
    public static function forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && static::accessible($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  iterable  $array
     * @param  string  $prepend
     * @return array
     */
    public static function dot($array, $prepend = '')
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     *
     * @param  iterable  $array
     * @return array
     */
    public static function undot($array)
    {
        $results = [];

        foreach ($array as $key => $value) {
            static::set($results, $key, $value);
        }

        return $results;
    }
    
}

} // ==================================================================================================================

namespace { // == | Bootstrap | =======================================================================================

// We want the ability for the entry point to specify that the application effectively IS the special component
if (gRegistry('constant.appIsSpecialComponent')) {
  gLoadComponent(kSpecialComponent);
}

// If this is going to be apart of a larger application then why not detect and load it up and also provide the component
// loading. Execution from app.php will not eventually return to the entry point. It will end here one way or another.
// Otherwise, we will continue back to the script that included us where we will need to handle
// some form of output if there is any.

if (file_exists(gBuildPath(ROOT_PATH, 'base', 'src', 'app.php'))) {
  require_once(gBuildPath(ROOT_PATH, 'base', 'src', 'app.php'));

  if (gRegistry('app.path.0') == kSpecialComponent) {
    gRegSet('app.component', kSpecialComponent);
  }

  gLoadComponent(gRegistry('app.component'));

  gNotFound();
}

} // ==================================================================================================================
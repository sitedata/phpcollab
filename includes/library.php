<?php
/*
** Application name: phpCollab
** Last Edit page: 2004-08-23 
** Path by root: ../includes/library.php
** Authors: Ceam / Fullo 
**
** =============================================================================
**
**               phpCollab - Project Managment 
**
** -----------------------------------------------------------------------------
** Please refer to license, copyright, and credits in README.TXT
**
** -----------------------------------------------------------------------------
** FILE: library.php
**
** DESC: Screen: library file 
**
** -----------------------------------------------------------------------------
** TO-DO:
** move to a better login system and authentication (try to db session)
**
** =============================================================================
**
** New Edit Blocks
** Last Modified: $Date: 2009/02/01 13:52:37 $
** RCS: $Id: library.php,v 1.23 2009/02/01 13:52:37 norman77 Exp $
** -- Edit Log: --
** 2008-11-18   -   Updated the library.php to reflect the new settings object. (dab-norman77)
**
*/
use DebugBar\StandardDebugBar;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;


$debug = false;

define('APP_ROOT', dirname(dirname(__FILE__)));

require APP_ROOT . '/vendor/autoload.php';

if (ini_get('session.auto_start') == 0) {
    $profilSession = "";
}

// Setup debugging
if ($debug) {
    ini_set('xdebug.var_display_max_depth', 5);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 3000);

    // Set error reporting
    error_reporting(E_ALL | E_STRICT);

    // Display errors
    ini_set('display_errors', 1);

    // Log errors
    ini_set('log_errors', 1);

    // No error lof message max
    ini_set('log_errors_max_len', 0);

    // pecify log file
    ini_set('error_log', APP_ROOT . '/logs/php_errors.log');

    include_once APP_ROOT . '/classes/Vendor/FirePHPCore/FirePHP.class.php';


    $debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer();

}

error_reporting(2039);
ini_set("session.use_trans_sid", 0);

//disable session on export
if ($export != "true") {
    session_start();
}
// register_globals cheat code
if (ini_get(register_globals) != "1") {
    //GET and POST VARS
    foreach ($_REQUEST as $key => $val) {
        $GLOBALS[$key] = phpCollab\Util::replaceSpecialCharacters($val);
    }
    //$HTTP_SESSION_VARS
    foreach ($_SESSION as $key => $val) {
        $GLOBALS[$key] = phpCollab\Util::replaceSpecialCharacters($val);
    }
    //$HTTP_SERVER_VARS
    foreach ($_SERVER as $key => $val) {
        $GLOBALS[$key] = phpCollab\Util::replaceSpecialCharacters($val);
    }
}
$request = Request::createFromGlobals();
$msg = phpCollab\Util::returnGlobal('msg', 'GET');
$session = phpCollab\Util::returnGlobal('session', 'GET');
$logout = phpCollab\Util::returnGlobal('logout', 'GET');
$idSession = phpCollab\Util::returnGlobal('idSession', 'SESSION');
$dateunixSession = phpCollab\Util::returnGlobal('dateunixSession', 'SESSION');
$loginSession = phpCollab\Util::returnGlobal('loginSession', 'SESSION');
$profilSession = phpCollab\Util::returnGlobal('profilSession', 'SESSION');
$logouttimeSession = phpCollab\Util::returnGlobal('logouttimeSession', 'SESSION');

$parse_start = phpCollab\Util::getMicroTime();

$twigLoader = new Twig_Loader_Filesystem( APP_ROOT . '/views');

try {
    $twigLoader->addPath(APP_ROOT . '/views/reports', 'reports');
    $twigLoader->addPath(APP_ROOT . '/views/general', 'general');
} catch (Twig_Error_Loader $e) {
    echo $e->getMessage();
}
$twig = new Twig_Environment($twigLoader, [
    'cache' => false,
    'debug' => true,
]);


//database update array
$updateDatabase = array(
    0 => "1.0",
    1 => "1.1",
    2 => "1.3",
    3 => "1.4",
    4 => "1.6",
    5 => "1.8",
    6 => "1.9",
    7 => "2.0",
    8 => "2.1",
    9 => "2.5"
);



//languages array
//$langValue = array(
//    "ar" => "Arabic",
//    "az" => "Azerbaijani",
//    "bg" => "Bulgarian",
//    "ca" => "Catalan",
//    "cs-iso" => "Czech (iso)",
//    "cs-win1250" => "Czech (win1250)",
//    "da" => "Danish",
//    "de" => "German",
//    "en" => "English",
//    "es" => "Spanish",
//    "et" => "Estonian",
//    "fr" => "French",
//    "hu" => "Hungarian",
//    "in" => "Indonesian",
//    "is" => "Icelandic",
//    "it" => "Italian",
//    "ja" => "Japanese",
//    "ko" => "Korean",
//    "lv" => "Latvian",
//    "nl" => "Dutch",
//    "no" => "Norwegian",
//    "pl" => "Polish",
//    "pt" => "Portuguese",
//    "pt-br" => "Brazilian Portuguese",
//    "ro" => "Romanian",
//    "ru" => "Russian",
//    "sk-win1250" => "Slovak (win1250)",
//    "tr" => "Turkish",
//    "uk" => "Ukrainian",
//    "zh" => "Chinese simplified",
//    "zh-tw" => "Chinese traditional",
//);

$lang = new \phpCollab\Languages\Languages();


if ( $request->cookies->get('lang') ) {
    $langDefault = $request->cookies->get('lang');
}

xdebug_var_dump($HTTP_ACCEPT_LANGUAGE);
//language browser detection
if ($langDefault == "") {
    if (isset($HTTP_ACCEPT_LANGUAGE)) {
        $plng = explode(",", $HTTP_ACCEPT_LANGUAGE);
//        xdebug_var_dump($plng);

        if (count($plng) > 0) {
            foreach ($plng as $value) {
                $k = explode(";", $value, 2)[0];

//                xdebug_var_dump($lang->checkLanguageFiles($k));
                if ( $lang->checkLanguageFiles($k) ) {
                    $langDefault = $k;
                    xdebug_var_dump($langDefault);
                    break;
                }

                xdebug_var_dump("post foreach");
//                } else {
//                    $langDefault = "en";
//                }
            }
        } else {
            $langDefault = "en";
        }

//die();
        // Check to see if there is a language file matching the "preferred/top" choice
//        xdebug_var_dump(file_exists("../languages/lang_" . $plng[0] . ".php"));

//        if (file_exists(APP_ROOT . "/languages/lang_" . $plng[0] . ".php")) {
//            $langDefault = $plng[0];
//        } else {
//            // The preferred file didn't match, so continue down the list by spliting on the "-"
//            if (count($plng) > 0) {
//                while (list($k, $v) = each($plng)) {
//
//                    $k = explode(";", $v, 1);
//                    xdebug_var_dump($k);
//                    $k = explode("-", $k[0]);
//
//                    // First search for an exact match, then search for a wildcard match
////                    xdebug_var_dump(file_exists("../languages/lang_" . $k[0] . ".php"));
//
//
//
//                    if (file_exists("../languages/lang_" . $k[0] . ".php")) {
//                        $langDefault = $k[0];
//                        break;
//                    }
//
//                    $list = glob("../languages/lang_" . $k[0] . "*.php");
////                    echo "glob:<br>";
//                    var_dump($list);
//
//                    $langDefault = "en";
//                }
//            } else {
//                $langDefault = "en";
//            }
//        }
    } else {
        $langDefault = "en";
    }
}

//xdebug_var_dump($lang->getLanguage());
$langValue = $lang->setLanguage($langDefault);

//$langValue = $lang->getLanguages();
//$translator = new Translator($langDefault);

//die();

// Translation functionality
//$translator = new Translator('fr_FR');
//$translator = new Translator('en_US', new \Symfony\Component\Translation\MessageSelector());
//$translator = new Translator('fr');
//$translator->setFallbackLocale('en_US');

//$translator->addLoader('po', new \Symfony\Component\Translation\Loader\PoFileLoader());
//$translator->addResource('po', APP_ROOT . '/translations/messages.en.po', 'en');
//if (isset($langDefault)) {
/**
    $translator = new Translator($langDefault);
*/
    //} else {
//    $translator = new Translator('en');
//}

////$translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());

//$lang->loadLanguageFiles();

//$translator->addResource('php', APP_ROOT . '/translations/messages.en.php', 'en');
//$translator->addResource('php', APP_ROOT . '/translations/messages.pt_br.php', 'pt-BR');
//$translator->addResource('php', APP_ROOT . '/translations/messages.fr.php', 'fr');
/**
$twig->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator));
 */

$twig->addExtension(new \Symfony\Bridge\Twig\Extension\TranslationExtension( $lang->getTranslator() ));

//$translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
//$translator->addResource('php', APP_ROOT . '/translations/messages.en.php', 'en_US');
//$translator->addResource('php', APP_ROOT . '/translations/messages.fr.php', 'fr_FR');
$twig->addGlobal('languages', $langValue);
$twig->addGlobal('defaultLanguage', $langDefault);



//set language session
if ($langDefault != "") {
    $langSelected[$langDefault] = "selected";
} else {
    $langSelected = "";
}

if ($languageSession == "") {
    $lang = $langDefault;
} else {
    $lang = $languageSession;
}

//$res = new Response();
//$res->headers->setCookie(new Cookie('lang', $langDefault));
////$res->headers->setCookie( $cookie );
//$res->send();

//xdebug_var_dump($langDefault);
//$cookie = new \Symfony\Component\HttpFoundation\Cookie(
//    'lang',    // Cookie name.
//    $langDefault,    // Cookie value.
//    time() + ( 2 * 365 * 24 * 60 * 60)  // Expires 2 years.
//);

$settings = null;
//settings and date selector includes
if ($indexRedirect == "true") {
    include APP_ROOT . '/includes/settings.php';

    if (defined('CONVERTED') && CONVERTED) {
        include_once APP_ROOT . '/includes/classes/settings.class.php';
        $settings = new Settings(true);
        $settings->makeGlobal();
    }

    include APP_ROOT . '/includes/initrequests.php';

    include APP_ROOT . '/languages/lang_en.php';
//    include APP_ROOT . '/languages/lang_' . $lang . '.php';
//    include APP_ROOT . '/languages/help_' . $lang . '.php';
} else {
    include APP_ROOT . '/includes/settings.php';

    if (defined('CONVERTED') && CONVERTED) {
        include_once APP_ROOT . '/includes/classes/settings.class.php';
        $settings = new Settings(true);
        $settings->makeGlobal();
    }

    include APP_ROOT . '/includes/initrequests.php';

//    include APP_ROOT . '/languages/lang_en.php';
//    include APP_ROOT . '/languages/lang_' . $lang . '.php';
//    include APP_ROOT . '/languages/help_' . $lang . '.php';
}

$logs = new \phpCollab\Logs\Logs();

//fix if update from old version
if ($theme == "") {
    $theme = "default";
}
if (!is_resource(THEME)) {
    define('THEME', $theme);
}
if (!is_resource(FTPSERVER)) {
    define('FTPSERVER', '');
}
if (!is_resource(FTPLOGIN)) {
    define('FTPLOGIN', '');
}
if (!is_resource(FTPPASSWORD)) {
    define('FTPPASSWORD', '');
}
if ($uploadMethod == "") {
    $uploadMethod = "PHP";
}
if ($peerReview == "") {
    $peerReview = "true";
}

if ($loginMethod == "") {
    $loginMethod = "PLAIN";
}
if ($databaseType == "") {
    $databaseType = "mysql";
}
if ($installationType == "") {
    $installationType = "online";
}

if ($checkSession != "false" && $demoSession != "true") {
    if ($profilSession == "3" && !strstr($PHP_SELF, "projects_site")) {
        phpCollab\Util::headerFunction("../projects_site/home.php");
    }

    if ($lastvisitedpage && $profilSession != "0") { // If the user has admin permissions, do not log the last page visited.
        if (!strstr($_SERVER['PHP_SELF'], "graph")) {
            $sidCode = session_name();
            $page = $_SERVER['PHP_SELF'] . "?" . $QUERY_STRING;
            $page = preg_replace('/(&' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = preg_replace('/(' . $sidCode . '=)([A-Za-z0-9.]*)($|.)/', '', $page);
            $page = strrev($page);
            $pieces = explode("/", $page);
            $pieces[0] = strrev($pieces[0]);
            $pieces[1] = strrev($pieces[1]);
            $page = $pieces[1] . "/" . $pieces[0];
            $tmpquery = "UPDATE {$tableCollab["members"]} SET last_page=:page WHERE id = :session_id";

            $dbParams = [];
            $dbParams['page'] = $page;
            $dbParams['session_id'] = $idSession;

            phpCollab\Util::newConnectSql($tmpquery, $dbParams);

            unset($dbParams);



        }
    }
    //if auto logout feature used, store last required page before deconnection
    if ($profilSession != "3") {
        if ($logouttimeSession != "0" && $logouttimeSession != "") {
            $dateunix = date("U");
            $diff = $dateunix - $dateunixSession;

            if ($diff > $logouttimeSession) {
                phpCollab\Util::headerFunction("../general/login.php?logout=true");
            } else {
                $dateunixSession = $dateunix;
                $_SESSION['dateunixSession'] = $dateunixSession;
            }
        }
    }
    $checkLog = $logs->getLogByLogin($loginSession);

    if ($checkLog !== false) {
        if (session_id() != $checkLog["session"]) {
            phpCollab\Util::headerFunction("../index.php?session=false");
        }
    } else {
        phpCollab\Util::headerFunction("../index.php?session=false");
    }
}


//count connected users
if ($checkConnected != "false") {
    $dateunix = date("U");
    $tmpquery1 = "UPDATE {$tableCollab["logs"]} SET connected=:date_unix WHERE login = :login_session";

    $dbParams = [];
    $dbParams['date_unix'] = $dateunix;
    $dbParams['login_session'] = $loginSession;

    phpCollab\Util::newConnectSql($tmpquery1, $dbParams);

    unset($dbParams);

    $tmpsql = "SELECT * FROM {$tableCollab["logs"]} WHERE connected > :date_unix";

    $dbParams = [];
    $dbParams['date_unix'] = $dateunix-5*60;

    $connectedUsers = phpCollab\Util::newComputeTotal($tmpsql, $dbParams);

    unset($dbParams);
}


//disable actions if demo user logged in demo mode
if ($action != "") {
    if ($demoSession == "true") {
        $closeTopic = "";
        $addToSiteTask = "";
        $removeToSiteTask = "";
        $addToSiteTopic = "";
        $removeToSiteTopic = "";
        $addToSiteTeam = "";
        $removeToSiteTeam = "";
        $action = "";
        $msg = "demo";
    }
}

//time variables
if ($gmtTimezone == "true") {
    $date = gmdate("Y-m-d");
    $dateheure = gmdate("Y-m-d H:i");
} else {
    $date = date("Y-m-d");
    $dateheure = date("Y-m-d H:i");
}

//update sorting table if query sort column
if (!empty($sor_cible) && $sor_cible != "" && $sor_champs != "none") {
    $sor_champs = phpCollab\Util::convertData($sor_champs);
    $sor_cible = phpCollab\Util::convertData($sor_cible);

    $tmpquery = "UPDATE {$tableCollab["sorting"]} SET ".$sor_cible." = :sort_value WHERE member = :session_id;";

    $dbParams = [];
    $dbParams['sort_value'] = $sor_champs . ' ' . $sor_ordre;
    $dbParams['session_id'] = $idSession;

    phpCollab\Util::newConnectSql($tmpquery, $dbParams);

    unset($dbParams);

}

//set all sorting values for logged user
$tmpquery = "WHERE sor.member = '" . phpCollab\Util::fixInt($idSession) . "'";
$sortingUser = new phpCollab\Request();
$sortingUser->openSorting($tmpquery);

// :-)
$setCopyright = "<!-- Powered by PhpCollab v$version //-->";


/*
 * Twig Test
 */
//Twig_Autoloader::register();

//$loader = new Twig_Loader_Filesystem('./templates');
//$twig = new Twig_Environment($loader, array(
//    //'cache' => './cache',
//    'cache' => false
//));





/* i18n */
//$twig->addExtension(new Twig_Extensions_Extension_I18n());
//
//$availableLanguages = array(
//    'en' => 'en_US',
//    'default' => 'en_US'
//);

$twig->addExtension(new Twig_Extension_Debug());

// Set language to French
//putenv('LC_ALL=en_US');
//setlocale(LC_ALL, 'en_US');
//
//// Specify the location of the translation tables
//bindtextdomain('myAppPhp', 'tran/locale');
//bind_textdomain_codeset('myAppPhp', 'UTF-8');
//
//// Choose domain
//textdomain('myAppPhp');

//$block1->heading($translator->trans('Globular Clusters')); // .po example



// Todo: Refactor to NOT use globals
$twig->addGlobal('globals', $GLOBALS);

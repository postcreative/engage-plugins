<?php 
/*
Plugin Name: Module Manager
Plugin URI: http://wp-types.com/home/module-manager/
Description: Create reusable modules comprising of Types, Views and CRED parts that represent complete functionality
Version: 0.9.3
Author: OnTheGoSystems	 
Author URI: http://www.onthegosystems.com/
*/

// current version
define('MODMAN_VERSION','0.9.3');
define('MODMAN_NAME','MODMAN');
define('MODMAN_CAPABILITY','manage_options');
if ( function_exists('realpath') )
    define('MODMAN_PLUGIN_PATH', realpath(dirname(__FILE__)));
else
    define('MODMAN_PLUGIN_PATH', dirname(__FILE__));
define('MODMAN_PLUGIN', plugin_basename(__FILE__));
define('MODMAN_PLUGIN_FOLDER', basename(MODMAN_PLUGIN_PATH));
define('MODMAN_PLUGIN_NAME',MODMAN_PLUGIN_FOLDER.'/'.basename(__FILE__));
define('MODMAN_PLUGIN_BASENAME',MODMAN_PLUGIN);
define('MODMAN_PLUGIN_URL',plugins_url().'/'.MODMAN_PLUGIN_FOLDER);
define('MODMAN_ASSETS_URL',MODMAN_PLUGIN_URL.'/assets');
define('MODMAN_ASSETS_PATH',MODMAN_PLUGIN_PATH.'/assets');
define('MODMAN_VIEWS_PATH',MODMAN_PLUGIN_PATH.'/views');
define('MODMAN_TEMPLATES_PATH',MODMAN_PLUGIN_PATH.'/views/templates');
define('MODMAN_CLASSES_PATH',MODMAN_PLUGIN_PATH.'/classes');
define('MODMAN_COMMON_PATH',MODMAN_PLUGIN_PATH.'/common');
define('MODMAN_TABLES_PATH',MODMAN_PLUGIN_PATH.'/views/tables');
define('MODMAN_CONTROLLERS_PATH',MODMAN_PLUGIN_PATH.'/controllers');
define('MODMAN_MODELS_PATH',MODMAN_PLUGIN_PATH.'/models');
define('MODMAN_LOGS_PATH',MODMAN_PLUGIN_PATH.'/logs');
define('MODMAN_LOCALE_PATH',MODMAN_PLUGIN_FOLDER.'/locale');
// save temp module zips
define('MODMAN_TMP_PATH',WP_CONTENT_DIR.'/_modulemanager_tmp_');
define('MODMAN_TMP_LOCK',WP_CONTENT_DIR.'/______lock_____');
// clear all tmps after this time
define('MODMAN_PURGE_TIME', 86400); // 24 hours
define('MODMAN_MODULE_INFO','__module_info__');
define('MODMAN_MODULE_TMP_FILE','__module_tmp_file__');

//define('MODMAN_DEBUG',true);
//define('MODMAN_DEV',true);

// logging function
if (!function_exists('modman_log'))
{
if (defined('MODMAN_DEBUG')&&MODMAN_DEBUG)
{
    function modman_log($message, $file=null, $type=null, $level=1)
    {
        // debug levels
        $dlevels=array(
            'default' => defined('MODMAN_DEBUG') && MODMAN_DEBUG
        );

        // check if we need to log..
        if (!$dlevels['default']) return false;
        if ($type==null) $type='default';
        if (!isset($dlevels[$type]) || !$dlevels[$type]) return false;
        
        // full path to log file
        if ($file==null)
        {
            $file='debug.log';
        }
        $file=MODMAN_LOGS_PATH.DIRECTORY_SEPARATOR.$file;

        /* backtrace */
        $bTrace = debug_backtrace(); // assoc array

        /* Build the string containing the complete log line. */
        $line = PHP_EOL.sprintf('[%s, <%s>, (%d)]==> %s', 
                                date("Y/m/d h:i:s", mktime()),
                                basename($bTrace[0]['file']), 
                                $bTrace[0]['line'], 
                                print_r($message,true) );
        
        if ($level>1)
        {
            $i=0;
            $line.=PHP_EOL.sprintf('Call Stack : ');
            while (++$i<$level && isset($bTrace[$i]))
            {
                $line.=PHP_EOL.sprintf("\tfile: %s, function: %s, line: %d".PHP_EOL."\targs : %s", 
                                    isset($bTrace[$i]['file'])?basename($bTrace[$i]['file']):'(same as previous)', 
                                    isset($bTrace[$i]['function'])?$bTrace[$i]['function']:'(anonymous)', 
                                    isset($bTrace[$i]['line'])?$bTrace[$i]['line']:'UNKNOWN',
                                    print_r($bTrace[$i]['args'],true));
            }
            $line.=PHP_EOL.sprintf('End Call Stack').PHP_EOL;
        }
        // log to file
        file_put_contents($file,$line,FILE_APPEND);
        
        return true;
    }
}
else
{
    function modman_log()  { }
}
}

// <<<<<<<<<<<< includes --------------------------------------------------
include(MODMAN_PLUGIN_PATH.'/loader.php');
// include basic classes
ModMan_Loader::load('CLASS/ModuleManager');
// init
ModuleManager::init();

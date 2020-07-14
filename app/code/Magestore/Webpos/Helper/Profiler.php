<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

/**
 * Simple profiler for API request
 */
class Profiler
{
    /**
     * Enable/disable profiler
     * 
     * @var bool
     */
    public static $enable = false;
    
    /**
     * Profiler result
     * 
     * @var array
     */
    public static $profiles = [];
    
    /**
     * Current profile path
     * 
     * @var array
     */
    private static $paths = [];
    
    /**
     * Start timer
     * 
     * @param string $name
     */
    public static function start($name) {
        if (self::$enable) {
            $profiles = &self::$profiles;
            foreach (self::$paths as $path) {
                $profiles = &$profiles[$path];
            }
            self::$paths[] = $name;
            $profiles[$name]['run_time'] = microtime(true);
        }
    }
    
    /**
     * Stop timer
     * 
     * @param string $name
     */
    public static function stop($name) {
        if (self::$enable) {
            $profiles = &self::$profiles;
            
            array_pop(self::$paths);
            foreach (self::$paths as $path) {
                $profiles = &$profiles[$path];
            }
            $profiles[$name]['run_time'] = microtime(true) - $profiles[$name]['run_time'];
        }
    }
}

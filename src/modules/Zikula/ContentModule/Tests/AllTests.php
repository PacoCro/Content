<?php

declare(strict_types=1);

/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

require_once __DIR__ . '/bootstrap.php';

class AllTests
{
    public static function main(): void
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite(): PHPUnit_Framework_TestSuite
    {
        return new PHPUnit_Framework_TestSuite('ZikulaContentModule - All Tests');
    }
}

if (PHPUnit_MAIN_METHOD === 'AllTests::main') {
    AllTests::main();
}

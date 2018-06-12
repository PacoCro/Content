<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule;

use Zikula\ContentModule\Base\AbstractContentModuleInstaller;

/**
 * Installer implementation class.
 */
class ContentModuleInstaller extends AbstractContentModuleInstaller
{
    /**
     * @inheritDoc
     */
    public function install()
    {
        $result = parent::install();
        if (!$result) {
            return $result;
        }

        $this->setVar('stylingClasses', "greybox|Grey box\nredbox|Red box\nyellowbox|Yellow box\ngreenbox|Green box\norangeannouncementbox|Orange announcement box\ngreenimportantbox|Green important box");

        return $result;
    }
}

<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <info@ziku.la>.
 * @link https://ziku.la
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

        $this->setVar('pageStyles', "product|Product page\nlegal|Legal page");
        $this->setVar('sectionStyles', "header|Header\nreferences|References\nfooter|Footer");
        $this->setVar('contentStyles', "grey-box|Grey box\nred-box|Red box\nyellow-box|Yellow box\ngreen-box|Green box\norange-announcement-box|Orange announcement box\ngreen-important-box|Green important box");

        return $result;
    }
}

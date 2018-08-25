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

namespace Zikula\ContentModule\Menu;

use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Menu\Base\AbstractMenuBuilder;

/**
 * Menu builder implementation class.
 */
class MenuBuilder extends AbstractMenuBuilder
{
    /**
     * @var boolean
     */
    protected $multilingual;

    /**
     * @inheritDoc
     */
    public function createItemActionsMenu(array $options = [])
    {
        $menu = parent::createItemActionsMenu($options);
        if (!isset($options['entity']) || !isset($options['area']) || !isset($options['context'])) {
            return $menu;
        }

        $entity = $options['entity'];
        if (!($entity instanceof PageEntity)) {
            return $menu;
        }

        $hasEditPermissions = $this->permissionHelper->mayEdit($entity);
        $hasContentPermissions = $this->permissionHelper->mayManagePageContent($entity);
        if (!$hasEditPermissions && !$hasContentPermissions) {
            return $menu;
        }

        $searchTitle = $this->__('Details', 'zikulacontentmodule');
        $reuseTitle = $this->__('Reuse', 'zikulacontentmodule');
        if ($hasEditPermissions) {
            $searchTitle = $reuseTitle;
        }
        $searchFound = false;
        $reappendChildren = [];
        foreach ($menu->getChildren() as $item) {
            if (!$searchFound) {
                if ($searchTitle == $item->getName()) {
                    $searchFound = true;
                    if ($searchTitle == $reuseTitle) {
                        $menu->removeChild($item);
                    }
                }
                continue;
            } else {
                $reappendChildren[] = $item;
                $menu->removeChild($item);
            }
        }

        $routePrefix = 'zikulacontentmodule_page_';
        $routeArea = $options['area'];
        $context = $options['context'];

        if ($hasContentPermissions) {
            $title = $this->__('Manage content', 'zikulacontentmodule');
            $menu->addChild($title, [
                'route' => $routePrefix . $routeArea . 'managecontent',
                'routeParameters' => $entity->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute('title', $this->__('Manage content elements of page', 'zikulacontentmodule'));
            if ($context == 'display') {
                $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
            }
            $menu[$title]->setAttribute('icon', 'fa fa-cubes');
        }
        if ($hasEditPermissions) {
            $title = $this->__('Duplicate', 'zikulacontentmodule');
            $menu->addChild($title, [
                'route' => $routePrefix . $routeArea . 'duplicate',
                'routeParameters' => $entity->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute('title', $this->__('Duplicate this page', 'zikulacontentmodule'));
            if ($context == 'display') {
                $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
            }
            $menu[$title]->setAttribute('icon', 'fa fa-files-o');
        }
        if ($this->multilingual && $hasEditPermissions && $hasContentPermissions) {
            $title = $this->__('Translate', 'zikulacontentmodule');
            $menu->addChild($title, [
                'route' => $routePrefix . $routeArea . 'translate',
                'routeParameters' => $entity->createUrlArgs()
            ]);
            $menu[$title]->setLinkAttribute('title', $this->__('Translate this page', 'zikulacontentmodule'));
            if ($context == 'display') {
                $menu[$title]->setLinkAttribute('class', 'btn btn-sm btn-default');
            }
            $menu[$title]->setAttribute('icon', 'fa fa-language');
        }

        foreach ($reappendChildren as $item) {
            $menu->addChild($item);
        }

        return $menu;
    }

    /**
     * @param boolean $multilingual
     */
    public function setMultilingual($multilingual = true)
    {
        $this->multilingual = $multilingual;
    }
}

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

namespace Zikula\ContentModule\Entity\Factory;

use Zikula\ContentModule\Entity\Factory\Base\AbstractEntityInitialiser;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Entity initialiser class used to dynamically apply default values to newly created entities.
 */
class EntityInitialiser extends AbstractEntityInitialiser
{
    /**
     * @var string State of new pages setting
     */
    protected $stateOfNewPages;

    public function __construct(
        PermissionHelper $permissionHelper,
        ListEntriesHelper $listEntriesHelper,
        VariableApiInterface $variableApi
    ) {
        parent::__construct($permissionHelper, $listEntriesHelper);
        $this->stateOfNewPages = (int)$variableApi->get('ZikulaContentModule', 'stateOfNewPages', '1');
    }

    public function initPage(PageEntity $entity): PageEntity
    {
        $entity = parent::initPage($entity);

        if (1 > $this->stateOfNewPages || 4 < $this->stateOfNewPages) {
            $this->stateOfNewPages = 1;
        }

        switch ($this->stateOfNewPages) {
            case 1:
                $entity->setActive(true);
                $entity->setInMenu(true);
                break;
            case 2:
                $entity->setActive(false);
                $entity->setInMenu(true);
                break;
            case 3:
                $entity->setActive(true);
                $entity->setInMenu(false);
                break;
            case 4:
                $entity->setActive(false);
                $entity->setInMenu(false);
                break;
        }

        return $entity;
    }
}

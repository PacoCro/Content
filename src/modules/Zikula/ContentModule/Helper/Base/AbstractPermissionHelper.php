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

namespace Zikula\ContentModule\Helper\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Core\Doctrine\EntityAccess;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\GroupsModule\Entity\GroupEntity;
use Zikula\PermissionsModule\Api\ApiInterface\PermissionApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\UsersModule\Entity\UserEntity;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;

/**
 * Permission helper base class.
 */
abstract class AbstractPermissionHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionApiInterface
     */
    protected $permissionApi;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionApiInterface $permissionApi,
        VariableApiInterface $variableApi,
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository,
        FeatureActivationHelper $featureActivationHelper,
        CategoryHelper $categoryHelper
    ) {
        $this->requestStack = $requestStack;
        $this->permissionApi = $permissionApi;
        $this->variableApi = $variableApi;
        $this->currentUserApi = $currentUserApi;
        $this->userRepository = $userRepository;
        $this->featureActivationHelper = $featureActivationHelper;
        $this->categoryHelper = $categoryHelper;
    }
    
    /**
     * Checks if the given entity instance may be read.
     */
    public function mayRead(EntityAccess $entity, int $userId = null): bool
    {
        return $this->hasEntityPermission($entity, ACCESS_READ, $userId);
    }
    
    /**
     * Checks if the given entity instance may be edited.
     */
    public function mayEdit(EntityAccess $entity, int $userId = null): bool
    {
        return $this->hasEntityPermission($entity, ACCESS_EDIT, $userId);
    }
    
    /**
     * Checks if the given entity instance may be deleted.
     */
    public function mayAccessHistory(EntityAccess $entity, int $userId = null): bool
    {
        $objectType = $entity->get_objectType();
    
        return $this->mayEdit($entity, $userId) && $this->variableApi->get('ZikulaContentModule', 'show' . ucfirst($objectType) . 'History', true);
    }
    
    /**
     * Checks if the given entity instance may be deleted.
     */
    public function mayDelete(EntityAccess $entity, int $userId = null): bool
    {
        return $this->hasEntityPermission($entity, ACCESS_DELETE, $userId);
    }
    
    /**
     * Checks if a certain permission level is granted for the given entity instance.
     */
    public function hasEntityPermission(EntityAccess $entity, int $permissionLevel, int $userId = null): bool
    {
        $objectType = $entity->get_objectType();
        $instance = $entity->getKey() . '::';
    
        // check category permissions
        if (in_array($objectType, ['page'], true)) {
            if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
                if (!$this->categoryHelper->hasPermission($entity)) {
                    return false;
                }
            }
        }
    
        return $this->permissionApi->hasPermission('ZikulaContentModule:' . ucfirst($objectType) . ':', $instance, $permissionLevel, $userId);
    }
    
    /**
     * Filters a given collection of entities based on different permission checks.
     *
     * @param array|ArrayCollection $entities The given list of entities
     */
    public function filterCollection($objectType, $entities, int $permissionLevel, int $userId = null): array
    {
        $filteredEntities = [];
        foreach ($entities as $content) {
            if (!$this->hasEntityPermission($content, $permissionLevel, $userId)) {
                continue;
            }
            $filteredEntities[] = $content;
        }
    
        return $filteredEntities;
    }
    
    /**
     * Checks if a certain permission level is granted for the given object type.
     */
    public function hasComponentPermission(string $objectType, int $permissionLevel, int $userId = null): bool
    {
        return $this->permissionApi->hasPermission('ZikulaContentModule:' . ucfirst($objectType) . ':', '::', $permissionLevel, $userId);
    }
    
    /**
     * Checks if the quick navigation form for the given object type may be used or not.
     */
    public function mayUseQuickNav(string $objectType, int $userId = null): bool
    {
        return $this->hasComponentPermission($objectType, ACCESS_READ, $userId);
    }
    
    /**
     * Checks if a certain permission level is granted for the application in general.
     */
    public function hasPermission(int $permissionLevel, int $userId = null): bool
    {
        return $this->permissionApi->hasPermission('ZikulaContentModule::', '::', $permissionLevel, $userId);
    }
    
    /**
     * Returns the list of user group ids of the current user.
     *
     * @return int[] List of group ids
     */
    public function getUserGroupIds(): array
    {
        $isLoggedIn = $this->currentUserApi->isLoggedIn();
        if (!$isLoggedIn) {
            return [];
        }
    
        $groupIds = [];
        $groups = $this->currentUserApi->get('groups');
        /** @var GroupEntity $group */
        foreach ($groups as $group) {
            $groupIds[] = $group->getGid();
        }
    
        return $groupIds;
    }
    
    /**
     * Returns the the current user's id.
     */
    public function getUserId(): int
    {
        return (int)$this->currentUserApi->get('uid');
    }
    
    /**
     * Returns the the current user's entity.
     */
    public function getUser(): UserEntity
    {
        return $this->userRepository->find($this->getUserId());
    }
}

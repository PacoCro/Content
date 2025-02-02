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

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Entity collection filter helper base class.
 */
abstract class AbstractCollectionFilterHelper
{
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var bool Fallback value to determine whether only own entries should be selected or not
     */
    protected $showOnlyOwnEntries = false;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi,
        CategoryHelper $categoryHelper,
        VariableApiInterface $variableApi
    ) {
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
        $this->categoryHelper = $categoryHelper;
        $this->variableApi = $variableApi;
        $this->showOnlyOwnEntries = (bool)$variableApi->get('ZikulaContentModule', 'showOnlyOwnEntries');
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     */
    public function getViewQuickNavParameters(string $objectType = '', string $context = '', array $args = []): array
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'], true)) {
            $context = 'controllerAction';
        }
    
        if ('page' === $objectType) {
            return $this->getViewQuickNavParametersForPage($context, $args);
        }
        if ('contentItem' === $objectType) {
            return $this->getViewQuickNavParametersForContentItem($context, $args);
        }
    
        return [];
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     */
    public function addCommonViewFilters(string $objectType, QueryBuilder $qb): QueryBuilder
    {
        if ('page' === $objectType) {
            return $this->addCommonViewFiltersForPage($qb);
        }
        if ('contentItem' === $objectType) {
            return $this->addCommonViewFiltersForContentItem($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     */
    public function applyDefaultFilters(string $objectType, QueryBuilder $qb, array $parameters = []): QueryBuilder
    {
        if ('page' === $objectType) {
            return $this->applyDefaultFiltersForPage($qb, $parameters);
        }
        if ('contentItem' === $objectType) {
            return $this->applyDefaultFiltersForContentItem($qb, $parameters);
        }
    
        return $qb;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     */
    protected function getViewQuickNavParametersForPage(string $context = '', array $args = []): array
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['catId'] = $request->query->get('catId', '');
        $parameters['catIdList'] = $this->categoryHelper->retrieveCategoriesFromRequest('page', 'GET');
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['scope'] = $request->query->get('scope', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['showTitle'] = $request->query->get('showTitle', '');
        $parameters['skipHookSubscribers'] = $request->query->get('skipHookSubscribers', '');
        $parameters['active'] = $request->query->get('active', '');
        $parameters['inMenu'] = $request->query->get('inMenu', '');
    
        return $parameters;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     */
    protected function getViewQuickNavParametersForContentItem(string $context = '', array $args = []): array
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['page'] = $request->query->get('page', 0);
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['scope'] = $request->query->get('scope', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['active'] = $request->query->get('active', '');
    
        return $parameters;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     */
    protected function addCommonViewFiltersForPage(QueryBuilder $qb): QueryBuilder
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForPage();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if ('catId' === $k) {
                if (0 < (int)$v) {
                    // single category filter
                    $qb->andWhere('tblCategories.category = :category')
                       ->setParameter('category', $v);
                }
                continue;
            }
            if ('catIdList' === $k) {
                // multi category filter
                $qb = $this->categoryHelper->buildFilterClauses($qb, 'page', $v);
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('page', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['showTitle', 'skipHookSubscribers', 'active', 'inMenu'], true)) {
                // boolean filter
                if ('no' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ('yes' === $v || '1' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } elseif (in_array($k, ['scope'], true)) {
                    // multi list filter
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . $v . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForPage($qb, $parameters);
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     */
    protected function addCommonViewFiltersForContentItem(QueryBuilder $qb): QueryBuilder
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForContentItem();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('contentItem', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['active'], true)) {
                // boolean filter
                if ('no' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ('yes' === $v || '1' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && 0 === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } elseif (in_array($k, ['scope'], true)) {
                    // multi list filter
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . $v . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForContentItem($qb, $parameters);
    }
    
    /**
     * Adds default filters as where clauses.
     */
    protected function applyDefaultFiltersForPage(QueryBuilder $qb, array $parameters = []): QueryBuilder
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$this->variableApi->get('ZikulaContentModule', 'pagePrivateMode');
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulacontentmodule_page_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved pages only
            $onlineStates = ['approved'];
            if ($showOnlyOwnEntries) {
                // allow the owner to see his pages
                $onlineStates[] = 'deferred';
                $onlineStates[] = 'trashed';
            }
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        $qb = $this->applyDateRangeFilterForPage($qb);
        if (in_array('tblContentItems', $qb->getAllAliases(), true)) {
            $qb = $this->applyDateRangeFilterForContentItem($qb, 'tblContentItems');
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     */
    protected function applyDefaultFiltersForContentItem(QueryBuilder $qb, array $parameters = []): QueryBuilder
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulacontentmodule_contentitem_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved content items only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        $qb = $this->applyDateRangeFilterForContentItem($qb);
        if (in_array('tblPage', $qb->getAllAliases(), true)) {
            $qb = $this->applyDateRangeFilterForPage($qb, 'tblPage');
        }
    
        return $qb;
    }
    
    /**
     * Applies start and end date filters for selecting pages.
     */
    protected function applyDateRangeFilterForPage(QueryBuilder $qb, string $alias = 'tbl'): QueryBuilder
    {
        $request = $this->requestStack->getCurrentRequest();
        $startDate = $request->query->get('activeFrom', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeFrom <= :startDate OR ' . $alias . '.activeFrom IS NULL)')
           ->setParameter('startDate', $startDate);
    
        $endDate = $request->query->get('activeTo', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeTo >= :endDate OR ' . $alias . '.activeTo IS NULL)')
           ->setParameter('endDate', $endDate);
    
        return $qb;
    }
    
    /**
     * Applies start and end date filters for selecting content items.
     */
    protected function applyDateRangeFilterForContentItem(QueryBuilder $qb, string $alias = 'tbl'): QueryBuilder
    {
        $request = $this->requestStack->getCurrentRequest();
        $startDate = $request->query->get('activeFrom', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeFrom <= :startDate OR ' . $alias . '.activeFrom IS NULL)')
           ->setParameter('startDate', $startDate);
    
        $endDate = $request->query->get('activeTo', date('Y-m-d H:i:s'));
        $qb->andWhere('(' . $alias . '.activeTo >= :endDate OR ' . $alias . '.activeTo IS NULL)')
           ->setParameter('endDate', $endDate);
    
        return $qb;
    }
    
    /**
     * Adds a where clause for search query.
     */
    public function addSearchFilter(string $objectType, QueryBuilder $qb, string $fragment = ''): QueryBuilder
    {
        if ('' === $fragment) {
            return $qb;
        }
    
        $filters = [];
        $parameters = [];
    
        if ('page' === $objectType) {
            $filters[] = 'tbl.workflowState = :searchWorkflowState';
            $parameters['searchWorkflowState'] = $fragment;
            $filters[] = 'tbl.title LIKE :searchTitle';
            $parameters['searchTitle'] = '%' . $fragment . '%';
            $filters[] = 'tbl.metaDescription LIKE :searchMetaDescription';
            $parameters['searchMetaDescription'] = '%' . $fragment . '%';
            if (is_numeric($fragment)) {
                $filters[] = 'tbl.views = :searchViews';
                $parameters['searchViews'] = $fragment;
            }
            $filters[] = 'tbl.activeFrom = :searchActiveFrom';
            $parameters['searchActiveFrom'] = $fragment;
            $filters[] = 'tbl.activeTo = :searchActiveTo';
            $parameters['searchActiveTo'] = $fragment;
            $filters[] = 'tbl.scope = :searchScope';
            $parameters['searchScope'] = $fragment;
            $filters[] = 'tbl.optionalString1 LIKE :searchOptionalString1';
            $parameters['searchOptionalString1'] = '%' . $fragment . '%';
            $filters[] = 'tbl.optionalString2 LIKE :searchOptionalString2';
            $parameters['searchOptionalString2'] = '%' . $fragment . '%';
            $filters[] = 'tbl.optionalText LIKE :searchOptionalText';
            $parameters['searchOptionalText'] = '%' . $fragment . '%';
            if (is_numeric($fragment)) {
                $filters[] = 'tbl.currentVersion = :searchCurrentVersion';
                $parameters['searchCurrentVersion'] = $fragment;
            }
        }
        if ('contentItem' === $objectType) {
            $filters[] = 'tbl.owningType LIKE :searchOwningType';
            $parameters['searchOwningType'] = '%' . $fragment . '%';
            $filters[] = 'tbl.activeFrom = :searchActiveFrom';
            $parameters['searchActiveFrom'] = $fragment;
            $filters[] = 'tbl.activeTo = :searchActiveTo';
            $parameters['searchActiveTo'] = $fragment;
            $filters[] = 'tbl.scope = :searchScope';
            $parameters['searchScope'] = $fragment;
            $filters[] = 'tbl.searchText LIKE :searchSearchText';
            $parameters['searchSearchText'] = '%' . $fragment . '%';
            $filters[] = 'tbl.additionalSearchText LIKE :searchAdditionalSearchText';
            $parameters['searchAdditionalSearchText'] = '%' . $fragment . '%';
        }
    
        $qb->andWhere('(' . implode(' OR ', $filters) . ')');
    
        foreach ($parameters as $parameterName => $parameterValue) {
            $qb->setParameter($parameterName, $parameterValue);
        }
    
        return $qb;
    }
    
    /**
     * Adds a filter for the createdBy field.
     */
    public function addCreatorFilter(QueryBuilder $qb, int $userId = null): QueryBuilder
    {
        if (null === $userId) {
            $userId = $this->currentUserApi->isLoggedIn() ? (int)$this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        }
    
        $qb->andWhere('tbl.createdBy = :userId')
           ->setParameter('userId', $userId);
    
        return $qb;
    }
}

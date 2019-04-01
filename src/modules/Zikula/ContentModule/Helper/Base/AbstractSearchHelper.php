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
use Doctrine\ORM\Query\Expr\Composite;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\RouteUrl;
use Zikula\SearchModule\Entity\SearchResultEntity;
use Zikula\SearchModule\SearchableInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Helper\CategoryHelper;
use Zikula\ContentModule\Helper\ControllerHelper;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;
use Zikula\ContentModule\Helper\PermissionHelper;

/**
 * Search helper base class.
 */
abstract class AbstractSearchHelper implements SearchableInterface
{
    use TranslatorTrait;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;
    
    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    /**
     * @var CategoryHelper
     */
    protected $categoryHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        EntityFactory $entityFactory,
        ControllerHelper $controllerHelper,
        EntityDisplayHelper $entityDisplayHelper,
        PermissionHelper $permissionHelper,
        FeatureActivationHelper $featureActivationHelper,
        CategoryHelper $categoryHelper
    ) {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
        $this->entityFactory = $entityFactory;
        $this->controllerHelper = $controllerHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->permissionHelper = $permissionHelper;
        $this->featureActivationHelper = $featureActivationHelper;
        $this->categoryHelper = $categoryHelper;
    }
    
    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }
    
    public function amendForm(FormBuilderInterface $builder): void
    {
        if (!$this->permissionHelper->hasPermission(ACCESS_READ)) {
            return;
        }
    
        $builder->add('active', HiddenType::class, [
            'data' => true
        ]);
    
        $searchTypes = $this->getSearchTypes();
    
        foreach ($searchTypes as $searchType => $typeInfo) {
            $builder->add('active_' . $searchType, CheckboxType::class, [
                'data' => true,
                'value' => $typeInfo['value'],
                'label' => $typeInfo['label'],
                'label_attr' => ['class' => 'checkbox-inline'],
                'required' => false
            ]);
        }
    }
    
    public function getResults(array $words, string $searchType = 'AND', array $modVars = null): array
    {
        if (!$this->permissionHelper->hasPermission(ACCESS_READ)) {
            return [];
        }
    
        // initialise array for results
        $results = [];
    
        // retrieve list of activated object types
        $searchTypes = $this->getSearchTypes();
        $entitiesWithDisplayAction = ['page'];
        $request = $this->requestStack->getCurrentRequest();
    
        foreach ($searchTypes as $searchTypeCode => $typeInfo) {
            $isActivated = false;
            $searchSettings = $request->query->get('zikulasearchmodule_search', []);
            $moduleActivationInfo = $searchSettings['modules'];
            if (isset($moduleActivationInfo['ZikulaContentModule'])) {
                $moduleActivationInfo = $moduleActivationInfo['ZikulaContentModule'];
                $isActivated = isset($moduleActivationInfo['active_' . $searchTypeCode]);
            }
            if (!$isActivated) {
                continue;
            }
    
            $objectType = $typeInfo['value'];
            $whereArray = [];
            $languageField = null;
            switch ($objectType) {
                case 'page':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.title';
                    $whereArray[] = 'tbl.metaDescription';
                    $whereArray[] = 'tbl.scope';
                    $whereArray[] = 'tbl.optionalString1';
                    $whereArray[] = 'tbl.optionalString2';
                    $whereArray[] = 'tbl.optionalText';
                    break;
                case 'contentItem':
                    $whereArray[] = 'tbl.workflowState';
                    $whereArray[] = 'tbl.owningType';
                    $whereArray[] = 'tbl.scope';
                    $whereArray[] = 'tbl.searchText';
                    $whereArray[] = 'tbl.additionalSearchText';
                    break;
            }
    
            $repository = $this->entityFactory->getRepository($objectType);
    
            // build the search query without any joins
            $qb = $repository->getListQueryBuilder('', '', false);
    
            // build where expression for given search type
            $whereExpr = $this->formatWhere($qb, $words, $whereArray, $searchType);
            $qb->andWhere($whereExpr);
    
            $query = $repository->getQueryFromBuilder($qb);
    
            // set a sensitive limit
            $query->setFirstResult(0)
                  ->setMaxResults(250);
    
            // fetch the results
            $entities = $query->getResult();
    
            if (0 === count($entities)) {
                continue;
            }
    
            $descriptionFieldName = $this->entityDisplayHelper->getDescriptionFieldName($objectType);
            $hasDisplayAction = in_array($objectType, $entitiesWithDisplayAction, true);
    
            $session = $request->getSession();
            foreach ($entities as $entity) {
                if (!$this->permissionHelper->mayRead($entity)) {
                    continue;
                }
    
                if (in_array($objectType, ['page'], true)) {
                    if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, $objectType)) {
                        if (!$this->categoryHelper->hasPermission($entity)) {
                            continue;
                        }
                    }
                }
    
                $description = !empty($descriptionFieldName) ? strip_tags($entity[$descriptionFieldName]) : '';
                $created = $entity['createdDate'] ?? null;
    
                $formattedTitle = $this->entityDisplayHelper->getFormattedTitle($entity);
                $displayUrl = null;
                if ($hasDisplayAction) {
                    $urlArgs = $entity->createUrlArgs();
                    $urlArgs['_locale'] = null !== $languageField && !empty($entity[$languageField]) ? $entity[$languageField] : $request->getLocale();
                    $displayUrl = new RouteUrl('zikulacontentmodule_' . strtolower($objectType) . '_display', $urlArgs);
                }
    
                $result = new SearchResultEntity();
                $result->setTitle($formattedTitle)
                    ->setText($description)
                    ->setModule($this->getBundleName())
                    ->setCreated($created)
                    ->setSesid($session->getId())
                ;
                if (null !== $displayUrl) {
                    $result->setUrl($displayUrl);
                }
                $results[] = $result;
            }
        }
    
        return $results;
    }
    
    /**
     * Returns list of supported search types.
     */
    protected function getSearchTypes(): array
    {
        $searchTypes = [
            'zikulaContentModulePages' => [
                'value' => 'page',
                'label' => $this->__('Pages', 'zikulacontentmodule')
            ],
            'zikulaContentModuleContentItems' => [
                'value' => 'contentItem',
                'label' => $this->__('Content items', 'zikulacontentmodule')
            ]
        ];
    
        $allowedTypes = $this->controllerHelper->getObjectTypes('helper', ['helper' => 'search', 'action' => 'getSearchTypes']);
        $allowedSearchTypes = [];
        foreach ($searchTypes as $searchType => $typeInfo) {
            if (!in_array($typeInfo['value'], $allowedTypes, true)) {
                continue;
            }
            if (!$this->permissionHelper->hasComponentPermission($typeInfo['value'], ACCESS_READ)) {
                continue;
            }
            $allowedSearchTypes[$searchType] = $typeInfo;
        }
    
        return $allowedSearchTypes;
    }
    
    public function getErrors(): array
    {
        return [];
    }
    
    /**
     * Construct a QueryBuilder Where orX|andX Expr instance.
     */
    protected function formatWhere(QueryBuilder $qb, array $words = [], array $fields = [], string $searchtype = 'AND'): ?Composite
    {
        if (empty($words) || empty($fields)) {
            return null;
        }
    
        $method = 'OR' === $searchtype ? 'orX' : 'andX';
        /** @var $where Composite */
        $where = $qb->expr()->$method();
        $i = 1;
        foreach ($words as $word) {
            $subWhere = $qb->expr()->orX();
            foreach ($fields as $field) {
                $expr = $qb->expr()->like($field, "?$i");
                $subWhere->add($expr);
                $qb->setParameter($i, '%' . $word . '%');
                $i++;
            }
            $where->add($subWhere);
        }
    
        return $where;
    }
    
    public function getBundleName(): string
    {
        return 'ZikulaContentModule';
    }
}

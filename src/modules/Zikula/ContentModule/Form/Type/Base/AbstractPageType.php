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

namespace Zikula\ContentModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\CategoriesModule\Form\Type\CategoriesType;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Entity\Factory\EntityFactory;
use Zikula\ContentModule\Form\Type\Field\ArrayType;
use Zikula\ContentModule\Form\Type\Field\MultiListType;
use Zikula\ContentModule\Form\Type\Field\TranslationType;
use Zikula\ContentModule\Entity\PageEntity;
use Zikula\ContentModule\Entity\PageCategoryEntity;
use Zikula\ContentModule\Form\Type\Field\EntityTreeType;
use Zikula\ContentModule\Helper\CollectionFilterHelper;
use Zikula\ContentModule\Helper\EntityDisplayHelper;
use Zikula\ContentModule\Helper\FeatureActivationHelper;
use Zikula\ContentModule\Helper\ListEntriesHelper;
use Zikula\ContentModule\Helper\TranslatableHelper;
use Zikula\ContentModule\Traits\ModerationFormFieldsTrait;

/**
 * Page editing form type base class.
 */
abstract class AbstractPageType extends AbstractType
{
    use TranslatorTrait;
    use ModerationFormFieldsTrait;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var EntityDisplayHelper
     */
    protected $entityDisplayHelper;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;

    public function __construct(
        TranslatorInterface $translator,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        EntityDisplayHelper $entityDisplayHelper,
        VariableApiInterface $variableApi,
        TranslatableHelper $translatableHelper,
        ListEntriesHelper $listHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->entityDisplayHelper = $entityDisplayHelper;
        $this->variableApi = $variableApi;
        $this->translatableHelper = $translatableHelper;
        $this->listHelper = $listHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('create' === $options['mode']) {
            $builder->add('parent', EntityTreeType::class, [
                'class' => PageEntity::class,
                'multiple' => false,
                'expanded' => false,
                'use_joins' => false,
                'label' => $this->__('Parent page'),
                'attr' => [
                    'title' => $this->__('Choose the parent page.')
                ]
            ]);
        }
        $this->addEntityFields($builder, $options);
        if ($this->featureActivationHelper->isEnabled(FeatureActivationHelper::CATEGORIES, 'page')) {
            $this->addCategoriesField($builder, $options);
        }
        $this->addModerationFields($builder, $options);
        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds basic entity fields.
     */
    public function addEntityFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('title', TextType::class, [
            'label' => $this->__('Title') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the title of the page.')
            ],
            'required' => true,
        ]);
        
        $builder->add('metaDescription', TextType::class, [
            'label' => $this->__('Meta description') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the meta description of the page.')
            ],
            'required' => false,
        ]);
        
        $builder->add('optionalString1', TextType::class, [
            'label' => $this->__('Optional string 1') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the optional string 1 of the page.')
            ],
            'required' => false,
        ]);
        
        $builder->add('optionalString2', TextType::class, [
            'label' => $this->__('Optional string 2') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the optional string 2 of the page.')
            ],
            'required' => false,
        ]);
        
        $builder->add('optionalText', TextareaType::class, [
            'label' => $this->__('Optional text') . ':',
            'help' => $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 2000]),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 2000,
                'class' => '',
                'title' => $this->__('Enter the optional text of the page.')
            ],
            'required' => false,
        ]);
        $helpText = $this->__('You can input a custom permalink for the page or let this field free to create one automatically.');
        if ('create' !== $options['mode']) {
            $helpText = '';
        }
        $builder->add('slug', TextType::class, [
            'label' => $this->__('Permalink') . ':',
            'required' => 'create' !== $options['mode'],
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => 'validate-unique',
                'title' => $helpText
            ],
            'help' => $helpText
        ]);
        
        if ($this->variableApi->getSystemVar('multilingual') && $this->featureActivationHelper->isEnabled(FeatureActivationHelper::TRANSLATIONS, 'page')) {
            $supportedLanguages = $this->translatableHelper->getSupportedLanguages('page');
            if (is_array($supportedLanguages) && count($supportedLanguages) > 1) {
                $currentLanguage = $this->translatableHelper->getCurrentLanguage();
                $translatableFields = $this->translatableHelper->getTranslatableFields('page');
                $mandatoryFields = $this->translatableHelper->getMandatoryFields('page');
                foreach ($supportedLanguages as $language) {
                    if ($language === $currentLanguage) {
                        continue;
                    }
                    $builder->add('translations' . $language, TranslationType::class, [
                        'fields' => $translatableFields,
                        'mandatory_fields' => $mandatoryFields[$language],
                        'values' => $options['translations'][$language] ?? []
                    ]);
                }
            }
        }
        
        $builder->add('showTitle', CheckboxType::class, [
            'label' => $this->__('Show title') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('show title ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('skipHookSubscribers', CheckboxType::class, [
            'label' => $this->__('Skip hook subscribers') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('skip hook subscribers ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('layout', ArrayType::class, [
            'label' => $this->__('Layout') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the layout of the page.')
            ],
            'required' => false,
        ]);
        
        $builder->add('views', IntegerType::class, [
            'label' => $this->__('Views') . ':',
            'empty_data' => 0,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the views of the page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => false,
            'scale' => 0
        ]);
        
        $builder->add('active', CheckboxType::class, [
            'label' => $this->__('Active') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('active ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('activeFrom', DateTimeType::class, [
            'label' => $this->__('Active from') . ':',
            'attr' => [
                'class' => ' validate-daterange-page',
                'title' => $this->__('Enter the active from of the page.')
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);
        
        $builder->add('activeTo', DateTimeType::class, [
            'label' => $this->__('Active to') . ':',
            'attr' => [
                'class' => ' validate-daterange-page',
                'title' => $this->__('Enter the active to of the page.')
            ],
            'required' => false,
            'empty_data' => '',
            'with_seconds' => true,
            'date_widget' => 'single_text',
            'time_widget' => 'single_text'
        ]);
        
        $listEntries = $this->listHelper->getEntries('page', 'scope');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('scope', MultiListType::class, [
            'label' => $this->__('Scope') . ':',
            'label_attr' => [
                'class' => 'tooltips checkbox-inline',
                'title' => $this->__('As soon as at least one selected entry applies for the current user the page becomes visible.')
            ],
            'help' => $this->__('As soon as at least one selected entry applies for the current user the page becomes visible.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the scope.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => true,
            'expanded' => true
        ]);
        
        $builder->add('inMenu', CheckboxType::class, [
            'label' => $this->__('In menu') . ':',
            'attr' => [
                'class' => '',
                'title' => $this->__('in menu ?')
            ],
            'required' => false,
        ]);
        
        $builder->add('stylingClasses', ArrayType::class, [
            'label' => $this->__('Styling classes') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the styling classes of the page.')
            ],
            'required' => false,
        ]);
        
        $builder->add('contentData', ArrayType::class, [
            'label' => $this->__('Content data') . ':',
            'help' => $this->__('Enter one entry per line.'),
            'empty_data' => [],
            'attr' => [
                'class' => '',
                'title' => $this->__('Enter the content data of the page.')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds a categories field.
     */
    public function addCategoriesField(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('categories', CategoriesType::class, [
            'label' => $this->__('Category') . ':',
            'empty_data' => null,
            'attr' => [
                'class' => 'category-selector'
            ],
            'required' => false,
            'multiple' => false,
            'module' => 'ZikulaContentModule',
            'entity' => 'PageEntity',
            'entityCategoryClass' => PageCategoryEntity::class,
            'showRegistryLabels' => true
        ]);
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = []): void
    {
        foreach ($options['actions'] as $action) {
            $builder->add($action['id'], SubmitType::class, [
                'label' => $action['title'],
                'icon' => 'delete' === $action['id'] ? 'fa-trash-o' : '',
                'attr' => [
                    'class' => $action['buttonClass']
                ]
            ]);
            if ('create' === $options['mode'] && 'submit' === $action['id'] && !$options['inline_usage']) {
                // add additional button to submit item and return to create form
                $builder->add('submitrepeat', SubmitType::class, [
                    'label' => $this->__('Submit and repeat'),
                    'icon' => 'fa-repeat',
                    'attr' => [
                        'class' => $action['buttonClass']
                    ]
                ]);
            }
        }
        $builder->add('reset', ResetType::class, [
            'label' => $this->__('Reset'),
            'icon' => 'fa-refresh',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
        $builder->add('cancel', SubmitType::class, [
            'label' => $this->__('Cancel'),
            'icon' => 'fa-times',
            'attr' => [
                'class' => 'btn btn-default',
                'formnovalidate' => 'formnovalidate'
            ]
        ]);
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_page';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data (required for embedding forms)
                'data_class' => PageEntity::class,
                'empty_data' => function (FormInterface $form) {
                    return $this->entityFactory->createPage();
                },
                'error_mapping' => [
                    'isScopeValueAllowed' => 'scope',
                    'isActiveFromBeforeActiveTo' => 'activeFrom',
                ],
                'mode' => 'create',
                'actions' => [],
                'has_moderate_permission' => false,
                'allow_moderation_specific_creator' => false,
                'allow_moderation_specific_creation_date' => false,
                'translations' => [],
                'filter_by_ownership' => true,
                'inline_usage' => false
            ])
            ->setRequired(['mode', 'actions'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('actions', 'array')
            ->setAllowedTypes('has_moderate_permission', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creator', 'bool')
            ->setAllowedTypes('allow_moderation_specific_creation_date', 'bool')
            ->setAllowedTypes('translations', 'array')
            ->setAllowedTypes('filter_by_ownership', 'bool')
            ->setAllowedTypes('inline_usage', 'bool')
            ->setAllowedValues('mode', ['create', 'edit'])
        ;
    }
}

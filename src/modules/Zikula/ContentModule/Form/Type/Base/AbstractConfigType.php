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
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\Form\Type\Field\MultiListType;
use Zikula\ContentModule\AppSettings;
use Zikula\ContentModule\Helper\ListEntriesHelper;

/**
 * Configuration form type base class.
 */
abstract class AbstractConfigType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var ListEntriesHelper
     */
    protected $listHelper;

    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper
    ) {
        $this->setTranslator($translator);
        $this->listHelper = $listHelper;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addGeneralSettingsFields($builder, $options);
        $this->addCustomStylesFields($builder, $options);
        $this->addAdditionalFieldsFields($builder, $options);
        $this->addPermalinksFields($builder, $options);
        $this->addListViewsFields($builder, $options);
        $this->addModerationFields($builder, $options);
        $this->addIntegrationFields($builder, $options);
        $this->addVersioningFields($builder, $options);

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds fields for general settings fields.
     */
    public function addGeneralSettingsFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'stateOfNewPages');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('stateOfNewPages', ChoiceType::class, [
            'label' => $this->__('State of new pages') . ':',
            'empty_data' => '1',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the state of new pages.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $builder->add('countPageViews', CheckboxType::class, [
            'label' => $this->__('Count page views') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Page views are only counted when the user has no edit access. Enable if you want to use the block showing most viewed pages.')
            ],
            'help' => $this->__('Page views are only counted when the user has no edit access. Enable if you want to use the block showing most viewed pages.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The count page views option')
            ],
            'required' => false,
        ]);
        
        $builder->add('googleMapsApiKey', TextType::class, [
            'label' => $this->__('Google maps api key') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If you want to use Google maps you need an API key for it. You should enable both "Maps JavaScript API" and "Maps Static API".')
            ],
            'help' => $this->__('If you want to use Google maps you need an API key for it. You should enable both "Maps JavaScript API" and "Maps Static API".'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the google maps api key.')
            ],
            'required' => false,
        ]);
        
        $builder->add('yandexTranslateApiKey', TextType::class, [
            'label' => $this->__('Yandex translate api key') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If you want to get translation support by Yandex which can provide suggestions you need an API key for it.')
            ],
            'help' => $this->__('If you want to get translation support by Yandex which can provide suggestions you need an API key for it.'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the yandex translate api key.')
            ],
            'required' => false,
        ]);
        
        $builder->add('enableRawPlugin', CheckboxType::class, [
            'label' => $this->__('Enable raw plugin') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to enable the unfiltered raw text plugin. Use this plugin with caution and if you can trust your editors, since no filtering is being done on the content. To be used for iframes, JavaScript blocks, etc.')
            ],
            'help' => $this->__('Whether to enable the unfiltered raw text plugin. Use this plugin with caution and if you can trust your editors, since no filtering is being done on the content. To be used for iframes, JavaScript blocks, etc.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The enable raw plugin option')
            ],
            'required' => false,
        ]);
        
        $builder->add('inheritPermissions', CheckboxType::class, [
            'label' => $this->__('Inherit permissions') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to inherit permissions from parent to child pages or not.')
            ],
            'help' => $this->__('Whether to inherit permissions from parent to child pages or not.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The inherit permissions option')
            ],
            'required' => false,
        ]);
        
        $builder->add('enableAutomaticPageLinks', CheckboxType::class, [
            'label' => $this->__('Enable automatic page links') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether page titles should automatically be linked using MultiHook.')
            ],
            'help' => $this->__('Whether page titles should automatically be linked using MultiHook.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The enable automatic page links option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for custom styles fields.
     */
    public function addCustomStylesFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('pageStyles', TextareaType::class, [
            'label' => $this->__('Page styles') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('A list of CSS class names available for styling pages - for example "product" or "legal".')
            ],
            'help' => [$this->__('A list of CSS class names available for styling pages - for example "product" or "legal".'), $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 5000])],
            'empty_data' => 'dummy|Dummy',
            'attr' => [
                'maxlength' => 5000,
                'class' => '',
                'title' => $this->__('Enter the page styles.')
            ],
            'required' => true,
        ]);
        
        $builder->add('sectionStyles', TextareaType::class, [
            'label' => $this->__('Section styles') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('A list of CSS class names available for styling page sections - for example "header" or "reference-row".')
            ],
            'help' => [$this->__('A list of CSS class names available for styling page sections - for example "header" or "reference-row".'), $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 5000])],
            'empty_data' => 'dummy|Dummy',
            'attr' => [
                'maxlength' => 5000,
                'class' => '',
                'title' => $this->__('Enter the section styles.')
            ],
            'required' => true,
        ]);
        
        $builder->add('contentStyles', TextareaType::class, [
            'label' => $this->__('Content styles') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('A list of CSS class names available for styling single content elements - for instance "note" or "shadow".')
            ],
            'help' => [$this->__('A list of CSS class names available for styling single content elements - for instance "note" or "shadow".'), $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 5000])],
            'empty_data' => 'dummy|Dummy',
            'attr' => [
                'maxlength' => 5000,
                'class' => '',
                'title' => $this->__('Enter the content styles.')
            ],
            'required' => true,
        ]);
    }

    /**
     * Adds fields for additional fields fields.
     */
    public function addAdditionalFieldsFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('enableOptionalString1', CheckboxType::class, [
            'label' => $this->__('Enable optional string 1') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If you need an additional string for each page you can enable an optional field.')
            ],
            'help' => $this->__('If you need an additional string for each page you can enable an optional field.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The enable optional string 1 option')
            ],
            'required' => false,
        ]);
        
        $builder->add('enableOptionalString2', CheckboxType::class, [
            'label' => $this->__('Enable optional string 2') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If you need an additional string for each page you can enable an optional field.')
            ],
            'help' => $this->__('If you need an additional string for each page you can enable an optional field.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The enable optional string 2 option')
            ],
            'required' => false,
        ]);
        
        $builder->add('enableOptionalText', CheckboxType::class, [
            'label' => $this->__('Enable optional text') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('If you need an additional text for each page you can enable an optional field.')
            ],
            'help' => $this->__('If you need an additional text for each page you can enable an optional field.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The enable optional text option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for permalinks fields.
     */
    public function addPermalinksFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('ignoreBundleNameInRoutes', CheckboxType::class, [
            'label' => $this->__('Ignore bundle name in routes') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This removes the module name (defaults to "content") from permalinks.')
            ],
            'help' => $this->__('This removes the module name (defaults to "content") from permalinks.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The ignore bundle name in routes option')
            ],
            'required' => false,
        ]);
        
        $builder->add('ignoreEntityNameInRoutes', CheckboxType::class, [
            'label' => $this->__('Ignore entity name in routes') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This removes the primary entity name ("page") from permalinks.')
            ],
            'help' => $this->__('This removes the primary entity name ("page") from permalinks.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The ignore entity name in routes option')
            ],
            'required' => false,
        ]);
        
        $builder->add('ignoreFirstTreeLevelInRoutes', CheckboxType::class, [
            'label' => $this->__('Ignore first tree level in routes') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('This removes the first tree level of pages from permalinks of pages in greater levels. If enabled first level pages act only as dummys while second level pages are the actual main pages. Recommended because it allows working with only one single tree of pages.')
            ],
            'help' => $this->__('This removes the first tree level of pages from permalinks of pages in greater levels. If enabled first level pages act only as dummys while second level pages are the actual main pages. Recommended because it allows working with only one single tree of pages.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The ignore first tree level in routes option')
            ],
            'required' => false,
        ]);
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'permalinkSuffix');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('permalinkSuffix', ChoiceType::class, [
            'label' => $this->__('Permalink suffix') . ':',
            'empty_data' => 'none',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the permalink suffix.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
    }

    /**
     * Adds fields for list views fields.
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('pageEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Page entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of pages shown per page')
            ],
            'help' => $this->__('The amount of pages shown per page'),
            'empty_data' => 10,
            'attr' => [
                'maxlength' => 11,
                'class' => '',
                'title' => $this->__('Enter the page entries per page.') . ' ' . $this->__('Only digits are allowed.')
            ],
            'required' => true,
            'scale' => 0
        ]);
        
        $builder->add('linkOwnPagesOnAccountPage', CheckboxType::class, [
            'label' => $this->__('Link own pages on account page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to add a link to pages of the current user on his account page')
            ],
            'help' => $this->__('Whether to add a link to pages of the current user on his account page'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The link own pages on account page option')
            ],
            'required' => false,
        ]);
        
        $builder->add('pagePrivateMode', CheckboxType::class, [
            'label' => $this->__('Page private mode') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether users may only see own pages')
            ],
            'help' => $this->__('Whether users may only see own pages'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The page private mode option')
            ],
            'required' => false,
        ]);
        
        $builder->add('showOnlyOwnEntries', CheckboxType::class, [
            'label' => $this->__('Show only own entries') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether only own entries should be shown on view pages by default or not')
            ],
            'help' => $this->__('Whether only own entries should be shown on view pages by default or not'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The show only own entries option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for moderation fields.
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $builder->add('allowModerationSpecificCreatorForPage', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creator for page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a user which will be set as creator.')
            ],
            'help' => $this->__('Whether to allow moderators choosing a user which will be set as creator.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creator for page option')
            ],
            'required' => false,
        ]);
        
        $builder->add('allowModerationSpecificCreationDateForPage', CheckboxType::class, [
            'label' => $this->__('Allow moderation specific creation date for page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to allow moderators choosing a custom creation date.')
            ],
            'help' => $this->__('Whether to allow moderators choosing a custom creation date.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The allow moderation specific creation date for page option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for integration fields.
     */
    public function addIntegrationFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'enabledFinderTypes');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('enabledFinderTypes', MultiListType::class, [
            'label' => $this->__('Enabled finder types') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Which sections are supported in the Finder component (used by Scribite plug-ins).')
            ],
            'help' => $this->__('Which sections are supported in the Finder component (used by Scribite plug-ins).'),
            'empty_data' => 'page',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the enabled finder types.')
            ],
            'required' => false,
            'placeholder' => $this->__('Choose an option'),
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => true,
            'expanded' => false
        ]);
    }

    /**
     * Adds fields for versioning fields.
     */
    public function addVersioningFields(FormBuilderInterface $builder, array $options = []): void
    {
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'revisionHandlingForPage');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('revisionHandlingForPage', ChoiceType::class, [
            'label' => $this->__('Revision handling for page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Adding a limitation to the revisioning will still keep the possibility to revert pages to an older version. You will loose the possibility to inspect changes done earlier than the oldest stored revision though.')
            ],
            'help' => $this->__('Adding a limitation to the revisioning will still keep the possibility to revert pages to an older version. You will loose the possibility to inspect changes done earlier than the oldest stored revision though.'),
            'empty_data' => 'unlimited',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the revision handling for page.')
            ],
            'required' => true,
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $listEntries = $this->listHelper->getEntries('appSettings', 'maximumAmountOfPageRevisions');
        $choices = [];
        $choiceAttributes = [];
        foreach ($listEntries as $entry) {
            $choices[$entry['text']] = $entry['value'];
            $choiceAttributes[$entry['text']] = ['title' => $entry['title']];
        }
        $builder->add('maximumAmountOfPageRevisions', ChoiceType::class, [
            'label' => $this->__('Maximum amount of page revisions') . ':',
            'empty_data' => '25',
            'attr' => [
                'class' => '',
                'title' => $this->__('Choose the maximum amount of page revisions.')
            ],
            'required' => false,
            'placeholder' => $this->__('Choose an option'),
            'choices' => $choices,
            'choice_attr' => $choiceAttributes,
            'multiple' => false,
            'expanded' => false
        ]);
        
        $builder->add('periodForPageRevisions', DateIntervalType::class, [
            'label' => $this->__('Period for page revisions') . ':',
            'empty_data' => 'P1Y0M0DT0H0M0S',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Choose the period for page revisions.')
            ],
            'required' => false,
            'labels' => [
                'years' => $this->__('Years'),
                'months' => $this->__('Months'),
                'days' => $this->__('Days'),
                'hours' => $this->__('Hours'),
                'minutes' => $this->__('Minutes'),
                'seconds' => $this->__('Seconds')
            ],
            'placeholder' => [
                'years' => $this->__('Years'),
                'months' => $this->__('Months'),
                'days' => $this->__('Days'),
                'hours' => $this->__('Hours'),
                'minutes' => $this->__('Minutes'),
                'seconds' => $this->__('Seconds')
            ],
            'input' => 'string',
            'widget' => 'choice',
            'with_years' => true,
            'with_months' => true,
            'with_weeks' => false,
            'with_days' => true,
            'with_hours' => true,
            'with_minutes' => true,
            'with_seconds' => true
        ]);
        
        $builder->add('showPageHistory', CheckboxType::class, [
            'label' => $this->__('Show page history') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether to show the version history to editors or not.')
            ],
            'help' => $this->__('Whether to show the version history to editors or not.'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The show page history option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('save', SubmitType::class, [
            'label' => $this->__('Update configuration'),
            'icon' => 'fa-check',
            'attr' => [
                'class' => 'btn btn-success'
            ]
        ]);
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
        return 'zikulacontentmodule_config';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data
                'data_class' => AppSettings::class,
            ]);
    }
}

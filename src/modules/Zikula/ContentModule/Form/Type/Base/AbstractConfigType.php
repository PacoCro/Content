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

namespace Zikula\ContentModule\Form\Type\Base;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

    /**
     * ConfigType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     * @param ListEntriesHelper $listHelper ListEntriesHelper service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        ListEntriesHelper $listHelper
    ) {
        $this->setTranslator($translator);
        $this->listHelper = $listHelper;
    }

    /**
     * Sets the translator.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addGeneralSettingsFields($builder, $options);
        $this->addListViewsFields($builder, $options);
        $this->addIntegrationFields($builder, $options);

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds fields for general settings fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addGeneralSettingsFields(FormBuilderInterface $builder, array $options = [])
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
            'empty_data' => '',
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
                'title' => $this->__('Page views are only counted when not in preview or edit mode and only when the user has no edit access.')
            ],
            'help' => $this->__('Page views are only counted when not in preview or edit mode and only when the user has no edit access.'),
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
                'title' => $this->__('If you want to use Google maps you need an API key for it.')
            ],
            'help' => $this->__('If you want to use Google maps you need an API key for it.'),
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => '',
                'title' => $this->__('Enter the google maps api key.')
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
        
        $builder->add('stylingClasses', TextareaType::class, [
            'label' => $this->__('Styling classes') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('A list of CSS class names available for styling of content elements. The end user can select these classes for each element on a page - for instance "note" for an element styled as a note. Write one class name on each line. Please separate the CSS classes and displaynames with | - eg. "note | Memo".')
            ],
            'help' => [$this->__('A list of CSS class names available for styling of content elements. The end user can select these classes for each element on a page - for instance "note" for an element styled as a note. Write one class name on each line. Please separate the CSS classes and displaynames with | - eg. "note | Memo".'), $this->__f('Note: this value must not exceed %amount% characters.', ['%amount%' => 5000])],
            'empty_data' => 'greybox|Grey box',
            'attr' => [
                'maxlength' => 5000,
                'class' => '',
                'title' => $this->__('Enter the styling classes.')
            ],
            'required' => true,
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
     * Adds fields for list views fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addListViewsFields(FormBuilderInterface $builder, array $options = [])
    {
        
        $builder->add('pageEntriesPerPage', IntegerType::class, [
            'label' => $this->__('Page entries per page') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('The amount of pages shown per page')
            ],
            'help' => $this->__('The amount of pages shown per page'),
            'empty_data' => '10',
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
        
        $builder->add('filterDataByLocale', CheckboxType::class, [
            'label' => $this->__('Filter data by locale') . ':',
            'label_attr' => [
                'class' => 'tooltips',
                'title' => $this->__('Whether automatically filter data in the frontend based on the current locale or not')
            ],
            'help' => $this->__('Whether automatically filter data in the frontend based on the current locale or not'),
            'attr' => [
                'class' => '',
                'title' => $this->__('The filter data by locale option')
            ],
            'required' => false,
        ]);
    }

    /**
     * Adds fields for integration fields.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addIntegrationFields(FormBuilderInterface $builder, array $options = [])
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
            'empty_data' => '',
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
     * Adds submit buttons.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = [])
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

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_config';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // define class for underlying data
                'data_class' => AppSettings::class,
            ]);
    }
}

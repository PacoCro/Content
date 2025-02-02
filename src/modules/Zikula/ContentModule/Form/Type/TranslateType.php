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

namespace Zikula\ContentModule\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\ContentModule\Form\Type\Field\TranslationType;
use Zikula\ContentModule\Helper\TranslatableHelper;

/**
 * Translation form type implementation class.
 */
class TranslateType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var VariableApiInterface
     */
    protected $variableApi;

    /**
     * @var TranslatableHelper
     */
    protected $translatableHelper;

    public function __construct(
        TranslatorInterface $translator,
        VariableApiInterface $variableApi,
        TranslatableHelper $translatableHelper
    ) {
        $this->setTranslator($translator);
        $this->variableApi = $variableApi;
        $this->translatableHelper = $translatableHelper;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $hasContentData = false;
        if ('page' === $options['mode']) {
            $this->addPageFields($builder, $options);
        } elseif ('item' === $options['mode']) {
            $this->addItemFields($builder, $options);
            $hasContentData = null !== $options['content_type'] && 0 < count($options['content_type']->getTranslatableDataFields());
            if ($hasContentData) {
                $editFormClass = $options['content_type']->getEditFormClass();
                if (null !== $editFormClass && '' !== $editFormClass && class_exists($editFormClass)) {
                    $builder->add('contentData', $editFormClass, $options['content_type']->getEditFormOptions(ContentTypeInterface::CONTEXT_TRANSLATION));
                }
            }
        }

        $translatableFields = [];
        $supportedLanguages = $this->translatableHelper->getSupportedLanguages('page');
        if (is_array($supportedLanguages) && 1 < count($supportedLanguages)) {
            $currentLanguage = $this->translatableHelper->getCurrentLanguage();
            if ('page' === $options['mode']) {
                $translatableFields = $this->translatableHelper->getTranslatableFields('page');
            } elseif ('item' === $options['mode']) {
                if ($hasContentData) {
                    $translatableFields = ['contentData', 'additionalSearchText'];
                } else {
                    $translatableFields = ['additionalSearchText'];
                }
            }
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

        $this->addSubmitButtons($builder, $options);
    }

    /**
     * Adds translatable page fields.
     */
    public function addPageFields(FormBuilderInterface $builder, array $options = []): void
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
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalString1')) {
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
        }
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalString2')) {
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
        }
        if ($this->variableApi->get('ZikulaContentModule', 'enableOptionalText')) {
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
        }
        $builder->add('slug', TextType::class, [
            'label' => $this->__('Permalink') . ':',
            'required' => true,
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'class' => 'validate-unique'
            ]
        ]);
    }

    /**
     * Adds translatable content item fields.
     */
    public function addItemFields(FormBuilderInterface $builder, array $options = []): void
    {
        $builder->add('additionalSearchText', TextType::class, [
            'label' => $this->__('Additional search text') . ':',
            'empty_data' => '',
            'attr' => [
                'maxlength' => 255,
                'title' => $this->__('You may enter any text which will be used during the site search to find this element.')
            ],
            'required' => false,
            'help' => $this->__('You may enter any text which will be used during the site search to find this element.')
        ]);
    }

    /**
     * Adds submit buttons.
     */
    public function addSubmitButtons(FormBuilderInterface $builder, array $options = []): void
    {
        if ('page' !== $options['mode']) {
            $builder->add('prev', SubmitType::class, [
                'label' => $this->__('Previous'),
                'icon' => 'fa-arrow-left',
                'attr' => [
                    'class' => 'btn btn-default'
                ]
            ]);
        }
        $builder->add('next', SubmitType::class, [
            'label' => $this->__('Next'),
            'icon' => 'fa-arrow-right',
            'attr' => [
                'class' => 'btn btn-primary'
            ]
        ]);
        $builder->add('skip', SubmitType::class, [
            'label' => $this->__('Skip'),
            'icon' => 'fa-exchange',
            'attr' => [
                'class' => 'btn btn-default'
            ]
        ]);
        $builder->add('saveandquit', SubmitType::class, [
            'label' => $this->__('Save and quit'),
            'icon' => 'fa-floppy-o',
            'attr' => [
                'class' => 'btn btn-default'
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
        return 'zikulacontentmodule_translate';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'mode' => 'page',
                'content_type' => null,
                'translations' => []
            ])
            ->setRequired(['mode'])
            ->setAllowedTypes('mode', 'string')
            ->setAllowedTypes('translations', 'array')
            ->setAllowedValues('mode', ['page', 'item'])
        ;
    }
}

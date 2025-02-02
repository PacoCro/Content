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

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Heading form type class.
 */
class HeadingType extends AbstractContentFormType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $context = $options['context'] ?? ContentTypeInterface::CONTEXT_EDIT;
        $builder
            ->add('text', TextType::class, [
                'label' => $this->__('Heading') . ':'
            ])
        ;
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder->add('headingType', ChoiceType::class, [
                'label' => $this->__('Heading type') . ':',
                'label_attr' => [
                    'class' => 'radio-inline'
                ],
                'choices' => [
                    'h2' => 'h2',
                    'h3' => 'h3',
                    'h4' => 'h4'
                ],
                'expanded' => true
            ]);
        }
        $builder->add('anchorName', TextType::class, [
            'label' => $this->__('Internal anchor link name') . ':',
            'help' => $this->__('Leave empty for no internal anchor link.'),
            'required' => false,
            'attr' => [
                'title' => $this->__('Leave empty for no internal anchor link.')
            ]
        ]);
        if (ContentTypeInterface::CONTEXT_EDIT === $context) {
            $builder->add('displayPageTitle', CheckboxType::class, [
                'label' => $this->__('Display the page title') . ':',
                'help' => $this->__('If this setting is enabled the text field above will be ignored and the page title will be displayed instead.'),
                'required' => false
            ]);
        }
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_heading';
    }
}

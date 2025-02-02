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

namespace Zikula\ContentModule\Form\Type\Field\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zikula\ContentModule\Form\EventListener\TranslationListener;

/**
 * Translations field type base class.
 */
abstract class AbstractTranslationType extends AbstractType
{
    /**
     * @var TranslationListener
     */
    protected $translationListener;

    public function __construct()
    {
        $this->translationListener = new TranslationListener();
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber($this->translationListener);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'by_reference' => false,
                'mapped' => false,
                'empty_data' => static function (FormInterface $form) {
                    return new ArrayCollection();
                },
                'fields' => [],
                'mandatory_fields' => [],
                'values' => []
            ])
            ->setRequired(['fields'])
            ->setDefined(['mandatory_fields', 'values'])
            ->setAllowedTypes('fields', 'array')
            ->setAllowedTypes('mandatory_fields', 'array')
            ->setAllowedTypes('values', 'array')
        ;
    }

    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_field_translation';
    }
}

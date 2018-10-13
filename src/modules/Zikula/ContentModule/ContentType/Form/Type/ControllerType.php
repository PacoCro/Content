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

namespace Zikula\ContentModule\ContentType\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Zikula\Common\Content\AbstractContentFormType;
use Zikula\Common\Translator\TranslatorInterface;

/**
 * Controller form type class.
 */
class ControllerType extends AbstractContentFormType
{
    /**
     * ControllerType constructor.
     *
     * @param TranslatorInterface $translator Translator service instance
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->setTranslator($translator);
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('controller', TextType::class, [
                'label' => $this->__('Controller'),
                'help' => $this->__('MyModuleName:Controller:method'),
                'constraints' => [
                    new Regex('/\w+:\w+:\w+/')
                ]
            ])
            ->add('query', TextType::class, [
                'label' => $this->__('GET parameters'),
                'help' => $this->__('Separate with &, for example:') . ' foo=2&bar=5',
                'required' => false
            ])
            ->add('request', TextType::class, [
                'label' => $this->__('POST parameters'),
                'help' => $this->__('Separate with &, for example:') . ' foo=2&bar=5',
                'required' => false
            ])
            ->add('attributes', TextType::class, [
                'label' => $this->__('Request attributes'),
                'help' => $this->__('Separate with &, for example:') . ' foo=2&bar=5',
                'required' => false
            ])
        ;
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_controller';
    }
}

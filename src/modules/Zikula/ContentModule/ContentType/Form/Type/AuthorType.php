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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\ContentModule\ContentType\Form\DataTransformer\AuthorTransformer;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;
use Zikula\UsersModule\Form\Type\UserLiveSearchType;

/**
 * Author form type class.
 */
class AuthorType extends AbstractType
{
    use TranslatorTrait;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * AuthorType constructor.
     *
     * @param TranslatorInterface     $translator     Translator service instance
     * @param UserRepositoryInterface $userRepository UserRepository service instance
     */
    public function __construct(TranslatorInterface $translator, UserRepositoryInterface $userRepository)
    {
        $this->setTranslator($translator);
        $this->userRepository = $userRepository;
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
        $builder
            ->add('author', UserLiveSearchType::class, [
                'label' => $this->__('Author') . ':',
                'attr' => [
                    'maxlength' => 11,
                    'title' => $this->__('Here you can choose a user which will be used as author.')
                ],
                'help' => $this->__('Here you can choose a user which will be used as author.')
            ])
        ;
        $transformer = new AuthorTransformer($this->userRepository);
        $builder->get('author')->addModelTransformer($transformer);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'zikulacontentmodule_contenttype_author';
    }
}

<?php
/**
 * Content.
 *
 * @copyright Axel Guckelsberger (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Axel Guckelsberger <vorstand@zikula.de>.
 * @link https://zikula.de
 * @link http://zikula.org
 * @version Generated by ModuleStudio 1.3.2 (https://modulestudio.de).
 */

namespace Zikula\ContentModule\ContentType;

use \Twig_Environment;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\AbstractContentType;
use Zikula\ContentModule\ContentType\Form\Type\AuthorType as FormType;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Entity\RepositoryInterface\UserRepositoryInterface;

/**
 * Author content type.
 */
class AuthorType extends AbstractContentType
{
    /**
     * @var CurrentUserApiInterface
     */
    private $currentUserApi;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * AuthorType constructor.
     *
     * @param TranslatorInterface     $translator     Translator service instance
     * @param Twig_Environment        $twig           Twig service instance
     * @param FilesystemLoader        $twigLoader     Twig loader service instance
     * @param CurrentUserApiInterface $currentUserApi CurrentUserApi service instance
     * @param UserRepositoryInterface $userRepository UserRepository service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        Twig_Environment $twig,
        FilesystemLoader $twigLoader,
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository
    ) {
        $this->currentUserApi = $currentUserApi;
        $this->userRepository = $userRepository;
        parent::__construct($translator, $twig, $twigLoader);
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'id-card';
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->__('Author information');
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->__('Various information about the author of the page.');
    }

    /**
     * @inheritDoc
     */
    public function getDefaultData()
    {
        return [
            'authorId' => $this->currentUserApi->get('uid')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSearchableText()
    {
        $user = $this->userRepository->find($this->data['authorId']);
        $authorName = null !== $user ? $user->getUname() : $this->__('Unknown author');

        return html_entity_decode(strip_tags($authorName));
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return FormType::class;
    }
}

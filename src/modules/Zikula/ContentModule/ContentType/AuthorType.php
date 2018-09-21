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

namespace Zikula\ContentModule\ContentType;

use \Twig_Environment;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;
use Zikula\Common\Content\AbstractContentType;
use Zikula\Common\Content\ContentTypeInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ContentModule\ContentType\Form\Type\AuthorType as FormType;
use Zikula\ContentModule\Helper\PermissionHelper;
use Zikula\ThemeModule\Engine\Asset;
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
    protected $currentUserApi;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * AuthorType constructor.
     *
     * @param TranslatorInterface     $translator       Translator service instance
     * @param Twig_Environment        $twig             Twig service instance
     * @param FilesystemLoader        $twigLoader       Twig loader service instance
     * @param PermissionHelper        $permissionHelper PermissionHelper service instance
     * @param Asset                   $assetHelper      Asset service instance
     * @param CurrentUserApiInterface $currentUserApi   CurrentUserApi service instance
     * @param UserRepositoryInterface $userRepository   UserRepository service instance
     */
    public function __construct(
        TranslatorInterface $translator,
        Twig_Environment $twig,
        FilesystemLoader $twigLoader,
        PermissionHelper $permissionHelper,
        Asset $assetHelper,
        CurrentUserApiInterface $currentUserApi,
        UserRepositoryInterface $userRepository
    ) {
        $this->currentUserApi = $currentUserApi;
        $this->userRepository = $userRepository;
        parent::__construct($translator, $twig, $twigLoader, $permissionHelper, $assetHelper);
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
        $data = [
            'author' => $this->currentUserApi->get('uid')
        ];

        $user = $this->userRepository->find($data['author']);
        $data['authorName'] = $user->getUname();

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = parent::getData();

        $user = $this->userRepository->find($data['author']);
        $data['author'] = $user;
        $data['authorName'] = null !== $user ? $user->getUname() : $this->__('Unknown author');

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getSearchableText()
    {
        return html_entity_decode(strip_tags($this->data['authorName']));
    }

    /**
     * @inheritDoc
     */
    public function getEditFormClass()
    {
        return FormType::class;
    }

    /**
     * @inheritDoc
     */
    public function getAssets($context)
    {
        $assets = parent::getAssets($context);
        if (ContentTypeInterface::CONTEXT_EDIT != $context) {
            return $assets;
        }

        $assets['css'][] = $this->assetHelper->resolve('@ZikulaUsersModule:css/livesearch.css');
        $assets['js'][] = $this->assetHelper->resolve('@ZikulaUsersModule:js/Zikula.Users.LiveSearch.js');
        $assets['js'][] = $this->assetHelper->resolve('@ZikulaContentModule:js/ZikulaContentModule.ContentType.Author.js');

        return $assets;
    }

    /**
     * @inheritDoc
     */
    public function getJsEntrypoint($context)
    {
        if (ContentTypeInterface::CONTEXT_EDIT != $context) {
            return null;
        }

        return 'contentInitAuthorEdit';
    }
}

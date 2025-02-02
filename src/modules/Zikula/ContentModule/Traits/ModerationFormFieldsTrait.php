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

namespace Zikula\ContentModule\Traits;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Zikula\UsersModule\Form\Type\UserLiveSearchType;

/**
 * Moderation form fields trait implementation class.
 */
trait ModerationFormFieldsTrait
{
    /**
     * Adds special fields for moderators.
     */
    public function addModerationFields(FormBuilderInterface $builder, array $options = []): void
    {
        if (!$options['has_moderate_permission']) {
            return;
        }
        if (isset($options['inline_usage']) && $options['inline_usage']) {
            return;
        }
    
        if (isset($options['allow_moderation_specific_creator']) && $options['allow_moderation_specific_creator']) {
            $builder->add('moderationSpecificCreator', UserLiveSearchType::class, [
                'mapped' => false,
                'label' => $this->__('Creator') . ':',
                'attr' => [
                    'maxlength' => 11,
                    'title' => $this->__('Here you can choose a user which will be set as creator.')
                ],
                'empty_data' => 0,
                'required' => false,
                'help' => $this->__('Here you can choose a user which will be set as creator.')
            ]);
        }
        if (isset($options['allow_moderation_specific_creation_date']) && $options['allow_moderation_specific_creation_date']) {
            $builder->add('moderationSpecificCreationDate', DateTimeType::class, [
                'mapped' => false,
                'label' => $this->__('Creation date') . ':',
                'attr' => [
                    'class' => '',
                    'title' => $this->__('Here you can choose a custom creation date.')
                ],
                'empty_data' => '',
                'required' => false,
                'with_seconds' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'help' => $this->__('Here you can choose a custom creation date.')
            ]);
        }
    }
}

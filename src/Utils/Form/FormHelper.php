<?php

namespace App\Utils\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class FormHelper
{
    /**
     * Retourne un tableau associatif d'erreurs avec forms imbriqués
     * et path (chemin vers champ <input name="{path}" )
     *
     * [
     *  field1 = [
     *    errors = [......]
     *    path = "mon_form[field1]"
     *  ],
     *  field2 = [
     *     subfield1 = [
     *        errors = [....]
     *        path = "mon_form[field2][subfield1]"
     *      ],...
     *  ]
     * ]
     *
     * @param Form $form
     * @param null $parentPath
     * @return array
     */
    public static function getFormErrors(FormInterface $form, $parentPath = null): array
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            /** @var FormError $error */

            if ($error instanceof FormError) {
                if ($parentPath) {
                    $errors['path'] = $parentPath;
                    $errors['errors'][] = $error->getMessage();
                } else {
                    $errors['global_errors']['errors'][] = $error->getMessage();
                }

            }
        }

        foreach ($form->all() as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                $path = $child->getName();
                $errors[$child->getName()] = self::getFormErrors($child, $parentPath . '[' . $path . ']');
            }
        }

        return $errors;
    }

    /**
     * Merge field options.
     * @param FormInterface $form
     * @param string $field
     * @param array $options
     * @return void
     */
    public static function mergeField(FormInterface $form, string $field, array $options): void {
        $field = $form->get($field);
        $name = $field->getName();
        $type = get_class($field->getConfig()->getType()->getInnerType());
        $fOptions = $field->getConfig()->getOptions();

        // Nested keys.
        $fOptions['constraints'] = array_merge($fOptions['constraints'] ?? [], $options['constraints'] ?? []);
        $fOptions['attr'] = array_merge($fOptions['attr'] ?? [], $options['attr'] ?? []);

        unset($options['constraints'], $options['attr']);

        $fOptions = array_merge($fOptions, $options);

        $form->add($name, $type, array_merge($fOptions, $options));
    }

    /**
     * Replace field options.
     * @param FormInterface $form
     * @param string|array $field
     * @param array $options
     * @return void
     */
    public static function replaceField(FormInterface $form, string|array $field, array $options): void {
        if (is_array($field)) {
            foreach ($field as $f) {
                static::replaceField($form, $f, $options);
            }

            return;
        }

        $field = $form->get($field);
        $name = $field->getName();
        $type = get_class($field->getConfig()->getType()->getInnerType());
        $fOptions = $field->getConfig()->getOptions();

        $fOptions = array_merge($fOptions, $options);

        $form->add($name, $type, array_merge($fOptions, $options));
    }

    /**
     * Replace constraints of a field.
     * @param FormInterface $form
     * @param string|array $field
     * @param array $constraints
     * @return void
     */
    public static function replaceConstraints(FormInterface $form, string|array $field, array $constraints): void {
        static::replaceField($form, $field, [
            'constraints' => $constraints
        ]);
    }

    /**
     * @param FormInterface $form
     * @param string|array $field
     * @param array $constraints
     * @return void
     */
    public static function addConstraints(FormInterface $form, string|array $field, array $constraints): void {
        $currentConstraints = $form->get($field)->getConfig()->getOption('constraints') ?? [];

        static::replaceConstraints($form, $field, array_merge($currentConstraints, $constraints));
    }

    /**
     * @param FormInterface $form
     * @param string|array $field
     * @return void
     */
    public static function removeConstraints(FormInterface $form, string|array $field): void {
        static::replaceConstraints($form, $field, []);
    }

    /**
     * @param FormInterface $form
     * @param string|array $field
     * @param array $attributes
     * @return void
     */
    public static function replaceAttributes(FormInterface $form, string|array $field, array $attributes): void {
        static::replaceField($form, $field, [
            'attr' => $attributes
        ]);
    }

    /**
     * Add needed Vuex attributes to each field.
     * @param FormBuilderInterface $builder
     * @param string|array|null $fields
     * @param array|null $options
     * @return void
     */
    public static function addVuexAttributes(FormBuilderInterface|FormInterface $builder, string|array|null $fields = null, ?array $options = []): void {
        $eventFn = $options['eventFn'] ?? 'updateInput';
        $updateEventType = 'change';

        if (is_string($fields)) {
            $fields = [$fields];
        } else if (is_null($fields)) {
            $fields = $builder->all();
        }

        foreach ($fields as $field) {
            if (is_string($field)) {
                $field = $builder->get($field);
            }

            /** @var FormBuilder $field */
            $name = $field->getName();
            $type = get_class($field->getType()->getInnerType());
            $options = $field->getOptions();
            $attrOpt = $field->getOption('attr');

            $isTextType = ($type === TextType::class);
            $isChoiceType = ($type === ChoiceType::class);
            $isRepeatedType = ($type === RepeatedType::class);

            // Update event type.
            if ($isTextType) {
                $updateEventType = 'input';
            }

            // If the current field is a choice type (= radios or checkboxes) or a repeated type
            // then we need to loop through all its choices/children.
            if (($isChoiceType && $field->hasOption('choices'))
                || $isRepeatedType) {
                $form = $builder->get($name);
                $children = $builder->get($name)->all();

                foreach ($children as $child) {
                    $cName = $child->getName();
                    $cType = get_class($child->getType()->getInnerType());
                    $cOptions = $child->getOptions();
                    $cAttrOpt = $child->getOption('attr');
                    $cValueOpt = $child->getOption('value');

                    if ($isChoiceType) {
                        $form->add($cName, $cType, array_merge($cOptions, [
                            'attr' => array_merge($cAttrOpt, [
                                ':checked' => $name . ' == "' . $cValueOpt . '"',
                                ('@' . $updateEventType) => $eventFn . '("' . $name . '", $event)'
                            ])
                        ]));
                    } else if ($isRepeatedType) {
                        $form->add($cName, $cType, array_merge($cOptions, [
                            'attr' => array_merge($cAttrOpt, [
                                ':value' => $name,
                                ('@' . $updateEventType) => $eventFn . '("' . $name . '", $event)'
                            ])
                        ]));
                    }
                }
            } else {
                $builder->add($name, $type, array_merge($options, [
                    'attr' => array_merge($attrOpt, [
                        ':value' => $name,
                        ('@' . $updateEventType) => $eventFn . '("' . $name . '", $event)'
                    ])
                ]));
            }
        }
    }
}

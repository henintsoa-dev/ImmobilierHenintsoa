<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Property;
use App\Validator\Image as ValidatorImage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
            'label' => false,
            'attr' => ['class' => 'file-upload'],
            'row_attr' => ['class' => 'd-none'],

            // make it optional so you don't have to re-upload the PDF file
            // every time you edit the Product details
            'required' => false,
            // 'constraints' => $options['is_new'] == true ? [new NotBlank()] : []
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'is_new' => true
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Pin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class PinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Si on veut seulement rendre obligatoire l'upload d'image que lors de la modification
        $isEdit = $options['method'] === "PUT";
        $imageFileConstraints = [];
        if ($isEdit) {
            $imageFileConstraints[] = new NotNull();
        }
        $imageFileConstraints[] = new Image(['maxSize' => '8M', 'maxSizeMessage' => 'fuck you']);
        $builder
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image (JPG or PNG file)',
                'allow_delete' => true,
                'delete_label' => 'Delete?',
                'download_uri' => false,
                'imagine_pattern' => 'squared_thumbnail_small',
                'constraints' => $imageFileConstraints
            ])
            ->add('title', TextType::class)
            ->add('description', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Pin::class,
        ]);
    }
}

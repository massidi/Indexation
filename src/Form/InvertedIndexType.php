<?php

namespace App\Form;

use App\Entity\Document;
use App\Entity\InvertedIndex;
use App\Entity\Word;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvertedIndexType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('wordCount')
            ->add('word', EntityType::class, [
                'class' => Word::class,
                'choice_label' => 'id',
            ])
            ->add('document', EntityType::class, [
                'class' => Document::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvertedIndex::class,
        ]);
    }
}

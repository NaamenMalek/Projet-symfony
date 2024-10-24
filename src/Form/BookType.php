<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ref')
            ->add('title')
            ->add('publicationDate', null, [
                'widget' => 'single_text'
            ])
       
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Science Fiction' => 'Science-Fiction',
                    'Mystery' => 'Mystery',
                    'Autobiography ' => 'Autobiography ',
                                  ],
                'placeholder' => 'Choisissez une catégorie',  // Option vide par défaut
            ])
            ->add('published', CheckboxType::class, [
                'required' => false, // Le cas échéant, cela pourrait ne pas être requis
            ])
            ->add('author', EntityType::class, [
                'class' => Author::class,
                'choice_label' => 'username', // Assuming Author entity has a method getFullName() that returns the author's full name
                'placeholder' => 'Select an author', // Optional, adds an empty option at the top
                'required' => true, // Set to true if the author selection is mandatory
            ])
            ->add('Save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}

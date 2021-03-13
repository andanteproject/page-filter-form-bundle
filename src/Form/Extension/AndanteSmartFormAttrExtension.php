<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AndanteSmartFormAttrExtension extends AbstractTypeExtension
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('andante_smart_form_attr', false);
        $resolver->setAllowedTypes('andante_smart_form_attr', 'bool');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($form->isRoot()) {
            if ($options['andante_smart_form_attr'] && (!isset($view->vars['attr']['id']) || empty($view->vars['attr']['id']))) {
                $view->vars['attr']['id'] = $this->getFormId($view);
            }
        } elseif ($form->getRoot()->getConfig()->getOption('andante_smart_form_attr', false)) {
            $view->vars['attr'] = \array_merge($view->vars['attr'], [
                'form' => $this->getRootView($view->parent)->vars['attr']['id'],
            ]);
        }
    }

    protected function getFormId(FormView $formView): string
    {
        $formId = $formView->vars['id'];
        if (empty($formId)) {
            $formId = \sprintf('form-%s', \hash('crc32', \spl_object_hash($formView)));
        }

        return $formId;
    }

    protected function getRootView(FormView $formView): FormView
    {
        return null !== $formView->parent ? $this->getRootView($formView->parent) : $formView;
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }
}

![Andante Project Logo](https://github.com/andanteproject/page-filter-form-bundle/blob/main/andanteproject-logo.png?raw=true)

# Page Filter Form Bundle

#### Symfony Bundle - [AndanteProject](https://github.com/andanteproject)

[![Latest Version](https://img.shields.io/github/release/andanteproject/page-filter-form-bundle.svg)](https://github.com/andanteproject/page-filter-form-bundle/releases)
![Github actions](https://github.com/andanteproject/page-filter-form-bundle/actions/workflows/ci.yml/badge.svg?branch=main)
![Framework](https://img.shields.io/badge/Symfony-4.x|5.x|6.x|7.x|8.x-informational?Style=flat&logo=symfony)
![Php7](https://img.shields.io/badge/PHP-%207.4|8.x-informational?style=flat&logo=php)
![PhpStan](https://img.shields.io/badge/PHPStan-Level%208-syccess?style=flat&logo=php)

A Symfony Bundle to simplify the handling of page filters for lists/tables in admin panels. 🧪

## Requirements

Symfony 4.x-7.x and PHP 7.4-8.0.

## Features

- Use [Symfony Form](https://symfony.com/doc/current/forms.html);
- Keep your URL parameters clean as `?search=value&otherFilterName=anotherValue` by default;
- Form will work even if you render form elements **outside the form tag**, around the web page, exactly where you need,
  **avoiding nested form conflicts**.
- Super easy to implement and maintain;
- Works like magic ✨.

## How to install

After [installation](#how-to-install), make sure you have the bundle registered in your Symfony bundles list (`config/bundles.php`):

```php
return [
    /// bundles...
    Andante\PageFilterFormBundle\AndantePageFilterFormBundle::class => ['all' => true],
    /// bundles...
];
```

This should have been done automatically if you are using [Symfony Flex](https://symfony.com/components/Symfony%20Flex). Otherwise, register it yourself.

## The problem

Let's suppose you have this common admin panel controller with a page listing some `Employee` entities.

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EmployeeRepository;
use Knp\Component\Pager\PaginatorInterface;

class EmployeeController extends AbstractController{
    
    public function index(Request $request, EmployeeRepository $employeeRepository, PaginatorInterface $paginator){
        /** @var Doctrine\ORM\QueryBuilder $qb */
        $qb = $employeeRepository->getFancyQueryBuilderLogic('employee');
        
        $employees = $paginator->paginate($qb, $request);
        return $this->render('admin/employee/index.html.twig', [
            'employees' => $employees,
        ]);
    }
}
```

To add filters to this page, let's create a Symfony form.

```php
<?php

namespace App\Form\Admin;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EmployeeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('search', Type\SearchType::class);
        $builder->add('senior', Type\CheckboxType::class);
        $builder->add('orderBy', Type\ChoiceType::class, [
            'choices' => [
                'name' => 'name',
                'age' => 'birthday'     
            ],
        ]);
    }
}
```

Let's add this Form to our controller page:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EmployeeRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Admin\EmployeeFilterType;
 
class EmployeeController extends AbstractController{
    
    public function index(Request $request, EmployeeRepository $employeeRepository, PaginatorInterface $paginator){
        /** @var Doctrine\ORM\QueryBuilder $qb */
        $qb = $employeeRepository->getFancyQueryBuilderLogic('employee');
        
        $form = $this->createForm(EmployeeFilterType::class);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $qb->expr()->like('employee.name',':name');
            $qb->setParameter('name', $form->get('search')->getData());
            
            $qb->expr()->like('employee.senior',':senior');
            $qb->setParameter('senior', $form->get('senior')->getData());
            
            $qb->orderBy('employee.'. $form->get('orderBy')->getData(), 'asc');
            
            // Don't you see the problem here?
        }
        
        $employees = $paginator->paginate($qb, $request);
        return $this->render('admin/employee/index.html.twig', [
            'employees' => $employees,
            'form' => $form->createView()
        ]);
    }
}
```

The code above has some huge problems:

- 👎 Handling all this filter logic inside the controller is not a good idea. Sure, you can move it into a dedicated
  service, but that means creating another class file alongside `EmployeeFilterType` to handle filters, and that
  still does not solve the second point in this list;
- 👎 You need to carry around and match form element names. `search`, `senior` and `orderBy` are keys you could store
  in constants to avoid repeating yourself, but this will drive you crazy as the filter logic grows.

## The solution with Page Filter Form

Use `Andante\PageFilterFormBundle\Form\PageFilterType` as parent of your filter
form ([why?](#why-use-pagefiltertype-as-form-parent)) and implement `target_callback` option on your form elements like
this:

```php
<?php

namespace App\Form\Admin;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Andante\PageFilterFormBundle\Form\PageFilterType;
use Doctrine\ORM\QueryBuilder;

class EmployeeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('search', Type\SearchType::class, [
            'target_callback' => function(QueryBuilder $qb, ?string $searchValue):void {
                $qb->expr()->like('employee.name',':name'); // Don't want to guess for entity alias "employee"?
                $qb->setParameter('name', $searchValue);    // Check andanteproject/shared-query-builder
            }
        ]);
        $builder->add('senior', Type\CheckboxType::class, [
            'target_callback' => function(QueryBuilder $qb, bool $seniorValue):void {
                $qb->expr()->like('employee.senior',':senior');
                $qb->setParameter('senior', $seniorValue);
            }
        ]);
        $builder->add('orderBy', Type\ChoiceType::class, [
            'choices' => [
                'name' => 'name',
                'age' => 'birthday'     
            ],
            'target_callback' => function(QueryBuilder $qb, string $orderByValue):void {
                $qb->orderBy('employee.'. $orderByValue, 'asc');
            }
        ]);
    }
    public function getParent() : string
    {
        return PageFilterType::class;
    }
}
```

Implement `Andante\PageFilterFormBundle\PageFilterFormTrait` in your controller (or inject
`Andante\PageFilterFormBundle\PageFilterManagerInterface` as an argument) and use the form like this:

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EmployeeRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\Admin\EmployeeFilterType;
use Andante\PageFilterFormBundle\PageFilterFormTrait;

class EmployeeController extends AbstractController{

    use PageFilterFormTrait;
    
    public function index(Request $request, EmployeeRepository $employeeRepository, PaginatorInterface $paginator){
        /** @var Doctrine\ORM\QueryBuilder $qb */
        $qb = $employeeRepository->getFancyQueryBuilderLogic('employee');
        
        $form = $this->createAndHandleFilter(EmployeeFilterType::class, $qb, $request);
        
        $employees = $paginator->paginate($qb, $request);
        return $this->render('admin/employee/index.html.twig', [
            'employees' => $employees,
            'form' => $form->createView()
        ]);
    }
}
```

✅ Done!

- 👍 Controller is clean and easy to read;
- 👍 We have just one class taking care of filters;
- 👍 The option `target_callback` lets you avoid repeating yourself and carrying around form element names;
- 👍 You can type-hint your callable 🥰 ([check callback arguments](#target_callback-option));
- 👍 We got you covered solving possible nested form problems ([how?](#render-the-form-in-twig));

### "target_callback" option

#### target_callback

**type**: `null` or `callable` **default**: `null`

The `callable` is going to have 3 parameters (third is optional):

| Parameter | What | Mandatory | Description |
| --------- | ---- | --------- | ----------- |
| 1 | Filter `$target` | `yes` | The second argument of `createAndHandleFilter`. It can be whatever you want: a query builder, an array, a collection, an object. It does not matter as long as you match its type with this argument signature. |
| 2 | form data | `yes` | Equivalent to calling `$form->getData()` on the current form field. It will be a `?string` for a `TextType` or a `?\DateTime` for a `DateTimeType`. |
| 3 | form itself | `no` | The current `$form` instance. | 

### Why use PageFilterType as form parent

You could avoid using `Andante\PageFilterFormBundle\Form\PageFilterType` as the parent of your form, but be aware it sets
some useful defaults you may want to replicate:

| Option | Value | Description |
| --- | --- | --- |
| `method` | `GET` | You probably want filters to be part of the page URL, right? |
| `csrf_protection` | `false` | So users can share the page URL with others without running into problems. |
| `allow_extra_fields` | `true` | Allows other URL parameters outside your form values. |
| `andante_smart_form_attr` | `true` | Enables form elements to be rendered wherever you want on your page, even outside the form tag, while keeping them working properly ([discover more](https://www.w3schools.com/html/html_form_attributes_form.asp)). |

### Render the form in twig

As long as `andante_smart_form_attr` is `true`, you can render your form like this:

```twig
<div class="header-filters">
    {{ form_start(form) }} {# id="list_filter" #}
        {{ form_errors(form) }}
        {{ form_row(form.search) }}
        {{ form_row(form.orderBy) }}
    {{ form_end(form, {'render_rest': false}) }}
</div>

<!-- -->
<!-- Some other HTML content, like a table or even another Symfony form -->
<!-- -->

<div class="footer-filters">
    {{ form_row(form.orderBy) }} {# has attribute form="list_filter" #}
</div>
```
✅ The `form.perPage` element works properly even outside the form tag ([how?!](https://www.w3schools.com/html/html_form_attributes_form.asp)).

Give us a ⭐!

Built with love ❤️ by [AndanteProject](https://github.com/andanteproject) team.

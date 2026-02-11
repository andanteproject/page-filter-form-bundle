<?php

declare(strict_types=1);

namespace Andante\PageFilterFormBundle\Tests\App;

use Andante\PageFilterFormBundle\AndantePageFilterFormBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PageFilterFormAppKernel extends AppKernel
{
    /**
     * @return iterable<int, Bundle>
     */
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new AndantePageFilterFormBundle(),
        ];
    }
}

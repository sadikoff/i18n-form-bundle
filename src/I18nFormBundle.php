<?php

namespace Koff\Bundle\I18nFormBundle;

use Koff\Bundle\I18nFormBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class I18nFormBundle.
 */
class I18nFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        //$container->addCompilerPass(new CompilerPass\LocaleProviderPass());
    }
}

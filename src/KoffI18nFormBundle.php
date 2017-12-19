<?php

namespace Koff\Bundle\I18nFormBundle;

use Koff\Bundle\I18nFormBundle\DependencyInjection\CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class KoffI18nFormBundle.
 */
class KoffI18nFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompilerPass\TemplatingPass());
        $container->addCompilerPass(new CompilerPass\LocaleProviderPass());
    }
}

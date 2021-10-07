<?php

namespace Koff\Bundle\I18nFormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Vladimir Sadicov <sadikoff@gmail.com>
 */
class KoffI18nFormBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}

<?php

namespace Xanweb\PhpCsFixer;

use Xanweb\Common\Service\Provider;
use Concrete\Core\Support\CodingStyle\PhpFixer as CorePhpFixer;
use Concrete\Core\Support\CodingStyle\PhpFixerRuleResolver as CorePhpFixerRuleResolver;

class ServiceProvider extends Provider
{
    public function _register(): void
    {
        $this->app->bind(CorePhpFixerRuleResolver::class, PhpFixerRuleResolver::class);
        $this->app->bind(CorePhpFixer::class, PhpFixer::class);
    }
}

<?php

namespace Orchestra\GeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OrchestraGeneratorBundle extends Bundle
{
    public function getParent()
    {
        return 'SensioGeneratorBundle';
    }
}

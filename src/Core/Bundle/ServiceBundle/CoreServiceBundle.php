<?php

namespace Core\Bundle\ServiceBundle;

use Core\Bundle\ServiceBundle\DependencyInjection\CoreServiceExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CoreServiceBundle extends Bundle
{

    public function getContainerExtension() {
        return new CoreServiceExtension();
    }
}

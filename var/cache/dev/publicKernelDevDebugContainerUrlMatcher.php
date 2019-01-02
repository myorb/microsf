<?php

use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherTrait;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class publicKernelDevDebugContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    use PhpMatcherTrait;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
        $this->regexpList = array(
            0 => '{^(?'
                    .'|/random/([^/]++)(*:23)'
                .')(?:/?)$}sD',
        );
        $this->dynamicRoutes = array(
            23 => array(array(array('_route' => '_random_limit', '_controller' => 'Kernel::randomNumber'), array('limit'), null, null, false, null)),
        );
    }
}

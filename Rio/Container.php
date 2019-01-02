<?php
namespace Rio;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Pimple\Container as PimpleContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Rio\Exception\NotFoundException;
use Rio\Handlers\Error;
use Rio\Handlers\NotFound;
use Rio\Handlers\NotAllowed;
use Rio\Handlers\Strategies\RequestResponse;
use Rio\Http\Environment;
use Rio\Http\Headers;
use Rio\Http\Request;
use Rio\Http\Response;
use Rio\Interfaces\CallableResolverInterface;
use Rio\Interfaces\Http\EnvironmentInterface;
use Rio\Interfaces\InvocationStrategyInterface;
use Rio\Interfaces\RouterInterface;

final class Container extends PimpleContainer implements ContainerInterface
{
    /**
     * Default settings
     *
     * @var array
     */
    private $defaultSettings = [
        'cookieLifetime' => '20 minutes',
        'cookiePath' => '/',
        'cookieDomain' => null,
        'cookieSecure' => false,
        'cookieHttpOnly' => false,
        'httpVersion' => '1.1',
        'responseChunkSize' => 4096,
        'outputBuffering' => 'append',
        'determineRouteBeforeAppMiddleware' => false,
    ];

    /**
     * Create new container
     *
     * @param array $userSettings Associative array of application settings
     */
    public function __construct(array $userSettings = [])
    {
        parent::__construct();

        $this->registerDefaultServices($userSettings);
    }

    /**
     * This function registers the default services that Slim needs to work.
     *
     * All services are shared - that is, they are registered such that the
     * same instance is returned on subsequent calls.
     *
     * @param array $userSettings Associative array of application settings
     *
     * @return void
     */
    private function registerDefaultServices($userSettings)
    {
        $defaultSettings = $this->defaultSettings;

        /**
         * This service MUST return an array or an
         * instance of \ArrayAccess.
         *
         * @param Container $c
         *
         * @return array|\ArrayAccess
         */
        $this['settings'] = function ($c) use ($userSettings, $defaultSettings) {
            return array_merge($defaultSettings, $userSettings);
        };

        /**
         * This service MUST return a shared instance
         * of \Rio\Interfaces\Http\EnvironmentInterface.
         *
         * @param Container $c
         *
         * @return EnvironmentInterface
         */
        $this['environment'] = function ($c) {
            return new Environment($_SERVER);
        };

        /**
         * PSR-7 Request object
         *
         * @param Container $c
         *
         * @return ServerRequestInterface
         */
        $this['request'] = function ($c) {
            return Request::createFromEnvironment($c['environment']);
        };

        /**
         * PSR-7 Response object
         *
         * @param Container $c
         *
         * @return ResponseInterface
         */
        $this['response'] = function ($c) {
            $headers = new Headers(['Content-Type' => 'text/html']);
            $response = new Response(200, $headers);

            return $response->withProtocolVersion($c['settings']['httpVersion']);
        };

        /**
         * This service MUST return a SHARED instance
         * of \Rio\Interfaces\RouterInterface.
         *
         * @param Container $c
         *
         * @return RouterInterface
         */
        $this['router'] = function ($c) {
            return new Router();
        };

        /**
         * This service MUST return a SHARED instance
         * of \Rio\Interfaces\InvocationStrategyInterface.
         *
         * @param Container $c
         *
         * @return InvocationStrategyInterface
         */
        $this['foundHandler'] = function ($c) {
            return new RequestResponse();
        };

        /**
         * This service MUST return a callable
         * that accepts three arguments:
         *
         * 1. Instance of \Psr\Http\Message\ServerRequestInterface
         * 2. Instance of \Psr\Http\Message\ResponseInterface
         * 3. Instance of \Exception
         *
         * The callable MUST return an instance of
         * \Psr\Http\Message\ResponseInterface.
         *
         * @param Container $c
         *
         * @return callable
         */
        $this['errorHandler'] = function ($c) {
            return new Error();
        };

        /**
         * This service MUST return a callable
         * that accepts two arguments:
         *
         * 1. Instance of \Psr\Http\Message\ServerRequestInterface
         * 2. Instance of \Psr\Http\Message\ResponseInterface
         *
         * The callable MUST return an instance of
         * \Psr\Http\Message\ResponseInterface.
         *
         * @param Container $c
         *
         * @return callable
         */
        $this['notFoundHandler'] = function ($c) {
            return new NotFound();
        };

        /**
         * This service MUST return a callable
         * that accepts three arguments:
         *
         * 1. Instance of \Psr\Http\Message\ServerRequestInterface
         * 2. Instance of \Psr\Http\Message\ResponseInterface
         * 3. Array of allowed HTTP methods
         *
         * The callable MUST return an instance of
         * \Psr\Http\Message\ResponseInterface.
         *
         * @param Container $c
         *
         * @return callable
         */
        $this['notAllowedHandler'] = function ($c) {
            return new NotAllowed;
        };

        /**
         * Instance of \Slim\Interfaces\CallableResolverInterface
         *
         * @param Container $c
         *
         * @return CallableResolverInterface
         */
        $this['callableResolver'] = function ($c) {
            return new CallableResolver($c);
        };
    }

    /********************************************************************************
     * Methods to satisfy Interop\Container\ContainerInterface
     *******************************************************************************/

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->offsetExists($id)) {
            throw new NotFoundException(sprintf('Identifier "%s" is not defined.', $id));
        }
        return $this->offsetGet($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return boolean
     */
    public function has($id)
    {
        return $this->offsetExists($id);
    }
}

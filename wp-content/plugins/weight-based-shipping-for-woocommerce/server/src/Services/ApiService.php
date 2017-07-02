<?php
namespace Wbs\Services;

use InvalidArgumentException;
use Wbs\Plugin;
use Wbs\Services\ApiService\ApiEndpoint;
use Wbs\Services\ApiService\Apis\ConfigApi;
use Wbs\Services\ApiService\Apis\LegacyConfigApi;
use Wbs\Services\ApiService\IApi;
use Wbs\Services\Interfaces\IService;


class ApiService implements IService
{
    /**
     * @param callable $legacyConfigServiceFactory  function(): LegacyConfigService
     */
    public function __construct($legacyConfigServiceFactory)
    {
        $this->legacyConfigServiceFactory = $legacyConfigServiceFactory;
    }

    /**
     * @return void
     */
    public function install()
    {
        $legacyConfigServiceFactory = $this->legacyConfigServiceFactory;

        foreach (self::$endpoints as $class => $endpoint) {
            add_action("wp_ajax_{$endpoint->action}", function() use($class, $legacyConfigServiceFactory) {

                /** @var IApi $api */
                $api =
                    $class === LegacyConfigApi::className()
                        ? new $class($legacyConfigServiceFactory())
                        : new $class;


                $api->handleRequest();
            });
        }
    }

    /**
     * @param string $apiClass
     * @return ApiEndpoint
     * @throws InvalidArgumentException If an unknown $apiClass provided
     */
    static public function endpoint($apiClass)
    {
        $endpoint = @self::$endpoints[$apiClass];
        if (!isset($endpoint)) {
            throw new InvalidArgumentException("No endpoints found for api class '{$apiClass}'.");
        }

        return $endpoint;
    }

    /**
     * @internal
     */
    static public function _staticInit()
    {
        self::$endpoints = array(
            ConfigApi::className() => new ApiEndpoint(Plugin::ID . '_config'),
            LegacyConfigApi::className() => new ApiEndpoint(Plugin::ID . '_legacy_config'),
        );
    }

    /** @var ApiEndpoint[] */
    static private $endpoints;

    /** @var callable */
    private $legacyConfigServiceFactory;
}

ApiService::_staticInit();
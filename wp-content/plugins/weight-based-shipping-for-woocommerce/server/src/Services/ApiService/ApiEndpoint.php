<?php
namespace Wbs\Services\ApiService;

use WbsVendors\Dgm\SimpleProperties\SimpleProperties;

/**
 * @property-read string $action
 */
class ApiEndpoint extends SimpleProperties
{
    public function __construct($action)
    {
        $this->action = $action;
    }

    public function url(array $parameters = array())
    {
        $parameters['action'] = $this->action;

        $parameters = array_filter(
            $parameters,
            function($v) { return isset($v); }
        );

        $query = http_build_query($parameters);

        $url = admin_url("admin-ajax.php?{$query}");

        return $url;
    }

    protected $action;
}
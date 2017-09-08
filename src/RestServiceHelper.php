<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 21.08.17
 * Time: 15:44
 */

namespace LpRest;


class RestServiceHelper
{
    /**
     * @var array
     */
    private $routeGroupOptions = [];

    /**
     * @param array $routeGroupOptions
     */
    public function setRouteGroupOptions(array $routeGroupOptions = []) {
        $this->routeGroupOptions = $routeGroupOptions;
    }

    /**
     * @return array
     */
    public function getRouteGroupOptions() {
        return $this->routeGroupOptions;
    }

}
<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

use Ponticlaro\Bebop\Common\Collection;

class MetaboxData {

    /**
     * List of environments
     * 
     * @var Ponticlaro\Bebop\Common\Collection;
     */
    private $__data;

    /**
     * Instantiated a single metadata container
     * 
     * @param array $data Array of data
     */
    public function __construct(array $data = array()) 
    {
        $this->__data = new Collection($data);
    }

    /**
     * Replace data container
     * 
     * @param Ponticlaro\Bebop\Common\Patterns\CollectionAbstract $container Data container
     */
    public function setDataContainer(\Ponticlaro\Bebop\Common\Patterns\CollectionAbstract $container)
    {
        // Get currently stored data
        $current_data = $this->__data->getAll();

        // Set container and pass current data
        $this->__data = new $container($current_data);
    }

    /**
     * Gets data with target key
     * 
     * @param  string  $key       Key to get data from
     * @param  boolean $is_single False if we assume there is an array of values, true if only a single value
     * @return mixed              Data contained in target key
     */
    public function get($key, $is_single = false) 
    {
        // Get data from container
        $data = $this->__data->get($key);

        // If array and single item is requested, try to unserialize it
        if ($data && is_array($data) && $is_single) {
            
            // Get first item
            $data = isset($data[0]) ? maybe_unserialize($data[0]) : '';
        }

        // Handle arrays that only contain empty values
        elseif (is_array($data) && !array_filter($data) && $is_single) {

            $data = $is_single ? '' : array();
        }

        // Return data
        return $data;
    }

    /**
     * Sends all undefined method calls to the data collection object
     * 
     * @param  string $name Method name
     * @param  array  $args Method arguments
     * @return mixed        Method returned value
     */
    public function __call($name, $args)
    {
        if (!method_exists($this->__data, $name))
            throw new \Exception("MetaboxData->$name method do not exist", 1);

        return call_user_func_array(array($this->__data, $name), $args);
    }
}
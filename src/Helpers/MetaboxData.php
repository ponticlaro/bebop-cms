<?php

namespace Ponticlaro\Bebop\Cms\Helpers;

use Ponticlaro\Bebop\Common\Collection;

class MetaboxData extends Collection {

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
    $data = $this->__get($key);

    // If array and single item is requested, try to unserialize it
    if ($data && is_array($data) && $is_single) {
        
      // Get first item
      $data = isset($data[0]) ? maybe_unserialize($data[0]) : '';
    }

    // Handle arrays that only contain empty values
    elseif (is_array($data) && !array_filter($data) && $is_single) {

      $data = '';
    }

    // Return data
    return $data;
  }
}
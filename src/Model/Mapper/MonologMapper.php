<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 10:05 AM
 */

namespace Model\Mapper;


use Component\DataMapper;

class MonologMapper extends DataMapper
{

    /**
     * Get configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
<?php

namespace App\Actions;

use App\Cloud;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UpdateCloud
 * @package App\Actions
 */
class UpdateCloud
{
    /**
     * Run the action.
     *
     * @param Cloud|Model $cloud
     * @param array $parameters
     * @return Cloud|Model
     */
    public function execute($cloud, array $parameters)
    {
        $cloud->update($parameters);

        return $cloud;
    }
}

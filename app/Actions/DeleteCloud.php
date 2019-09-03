<?php

namespace App\Actions;

use App\Cloud;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DeleteCloud
 * @package App\Actions
 */
class DeleteCloud
{
    /**
     * Run the action.
     *
     * @param Cloud|Model $cloud
     * @return void
     */
    public function execute($cloud)
    {
        $cloud->delete();
    }
}

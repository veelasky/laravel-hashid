<?php

namespace Tests\Models;

use Veelasky\LaravelHashId\Eloquent\HashableId;

class HashModelWithFallback extends BasicModel
{
    use HashableId;

    /**
     * Enable fallback to numeric ID resolution in route model binding.
     *
     * @var bool
     */
    protected $bindingFallback = true;
}

<?php

namespace Veelasky\LaravelHashId\Rules;

use Illuminate\Contracts\Validation\Rule;

class ExistsByHash implements Rule
{
    /**
     * @var string
     */
    private $model;

    /**
     * Create a new rule instance.
     *
     * @param string $model
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->model::query()->where('id', $this->model::HashtoId($value))->count() > 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists');
    }
}

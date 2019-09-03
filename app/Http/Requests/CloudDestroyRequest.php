<?php

namespace App\Http\Requests;

use App\Cloud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CloudDestroyRequest extends FormRequest
{
    protected $model;

    /**
     * Get the requested model
     *
     * @throws ModelNotFoundException
     * @return Cloud|Model
     */
    public function model()
    {
        if ($this->model) {
            return $this->model;
        }

        return $this->model = Cloud::query()->findOrFail($this->route('cloud'));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::check('delete', $this->model());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}

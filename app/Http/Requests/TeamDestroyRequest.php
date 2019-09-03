<?php

namespace App\Http\Requests;

use App\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class TeamDestroyRequest extends FormRequest
{
    protected $model;

    /**
     * Get the requested model
     *
     * @throws ModelNotFoundException
     * @return Team|Model
     */
    public function model()
    {
        if ($this->model) {
            return $this->model;
        }

        return $this->model = Team::query()->findOrFail($this->route('team'));
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

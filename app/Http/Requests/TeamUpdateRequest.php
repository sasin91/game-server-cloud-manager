<?php

namespace App\Http\Requests;

use App\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class TeamUpdateRequest extends FormRequest
{
    /**
     * The requested model
     *
     * @var Team|Model|null
     */
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
        return Gate::check('update', $this->model());
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'min:1', 'max:255', Rule::unique('teams', 'name')->ignoreModel($this->model())]
        ];
    }
}

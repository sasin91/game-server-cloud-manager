<?php

namespace App\Http\Requests;

use App\Team;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TeamRestoreRequest extends FormRequest
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

        $this->model = Team::onlyTrashed()->findOrFail($this->route('team'));

        return $this->model;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::check('restore', $this->model());
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

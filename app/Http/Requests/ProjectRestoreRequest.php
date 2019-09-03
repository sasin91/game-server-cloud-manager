<?php

namespace App\Http\Requests;

use App\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectRestoreRequest extends FormRequest
{
    /**
     * The requested model
     *
     * @var Project|Model|null
     */
    protected $model;

    /**
     * The requested model
     *
     * @throws ModelNotFoundException
     * @return Project|Model
     */
    public function model()
    {
        if ($this->model) {
            return $this->model;
        }

        return $this->model = Project::onlyTrashed()->findOrFail(
            $this->route('project')
        );
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

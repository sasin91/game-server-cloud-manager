<?php

namespace App\Http\Requests;

use App\Project;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectDestroyRequest extends FormRequest
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

        return $this->model = Project::query()->findOrFail(
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

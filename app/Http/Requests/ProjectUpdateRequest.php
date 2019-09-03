<?php

namespace App\Http\Requests;

use App\Project;
use App\VersionControl;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectUpdateRequest extends FormRequest
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
            'environment_id' => ['exists:environments,id'],
            'environment' => ['array'],
            'environment.*' => ['required_with:environment', 'string'],
            'repository_url' => ['url'],
            'version_control' => ['nullable', 'string', Rule::in(VersionControl::$registered->pluck('name'))],
        ];
    }
}

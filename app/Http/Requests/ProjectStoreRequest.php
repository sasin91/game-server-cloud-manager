<?php

namespace App\Http\Requests;

use App\Project;
use App\VersionControl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::check('create', new Project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'environment_id' => ['required_without:environment', 'exists:environments,id'],
            'environment' => ['required_without:environment_id', 'array'],
            'environment.*' => ['required_with:environment', 'string'],
            'repository_url' => ['required', 'url'],
            'version_control' => ['nullable', 'string', Rule::in(VersionControl::$registered->pluck('name'))],
        ];
    }
}

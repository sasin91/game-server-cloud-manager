<?php

namespace App\Http\Requests;

use App\Deployment;
use App\Filesystem\FileIndex;
use App\Rules\ValidScript;
use App\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreServerDeploymentRequest extends FormRequest
{
    /**
     * The requested model
     *
     * @var Server|Model|null
     */
    protected $model;

    /**
     * The requested model
     *
     * @throws ModelNotFoundException
     * @return Server|Model
     */
    public function model()
    {
        if ($this->model) {
            return $this->model;
        }

        return $this->model = $this
            ->user()
            ->currentTeam
            ->servers()
            ->findOrFail(
                $this->route('server')
            );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::check('create', [new Deployment, $this->model()]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
        'project_id' => ['required', 'exists:projects,id'],
        'script' => ['required', 'string', new ValidScript],
        ];
    }
}

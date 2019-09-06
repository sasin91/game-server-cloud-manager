<?php

namespace App\Http\Requests;

use App\Deployment;
use App\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;

class ServerDeploymentIndexRequest extends FormRequest
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
        return Gate::check('viewAny', new Deployment);
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

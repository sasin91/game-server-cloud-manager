<?php

namespace App\Http\Requests;

use App\Server;
use App\Rules\ValidURI;
use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class ServerUpdateRequest extends FormRequest
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

        return $this->model = Server::query()->findOrFail(
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
        return Gate::check('update', $this->model());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $currentTeam = $this->user()->currentTeam;

        return [
            'environment_id' => ['exists:environments,id'],
            'environment' => ['array'],
            'environment.*' => ['string'],
            'cloud_id' => [Rule::exists('clouds', 'id')->whereIn('cloud_id', collect(optional($currentTeam)->clouds)->map->getKey()->toArray())],
            'status' => ['nullable', 'string', Rule::in(ServerStatus::getValues())],
            'image' => ['nullable', 'string'],
            'private_address' => ['nullable', 'ip'],
            'public_address' => [new ValidURI],
            'provider_id' => ['nullable', 'string']
        ];
    }
}

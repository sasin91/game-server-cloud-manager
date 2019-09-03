<?php

namespace App\Http\Requests;

use App\Cloud;
use App\Server;
use App\Rules\ValidURI;
use App\Enums\ServerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class ServerStoreRequest extends FormRequest
{
    /**
     * The requested cloud
     *
     * @var Cloud|Model|null
     */
    protected $cloud;

    /**
     * The requested cloud
     *
     * @throws ModelNotFoundException
     * @return Cloud|Model
     */
    public function cloud()
    {
        if ($this->cloud) {
            return $this->cloud;
        }

        return $this->cloud = Cloud::query()->findOrFail($this->input('cloud_id'));
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::check('create', new Server);
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
            'environment.*' => ['string'],
            'cloud_id' => ['required', Rule::exists('clouds', 'id')->whereIn('id', $this->user()->currentTeam->clouds->map->getKey()->toArray())],
            'status' => ['nullable', 'string', Rule::in(ServerStatus::getValues())],
            'image' => ['nullable', 'string'],
            'private_address' => ['nullable', 'ip'],
            'public_address' => ['required', new ValidURI],
            'provider_id' => ['nullable', 'string']
        ];
    }
}

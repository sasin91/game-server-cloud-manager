<?php

namespace App\Http\Requests;

use App\Cloud;
use App\CloudProvider;
use App\Rules\ValidURI;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class CloudStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::check('create', new Cloud);
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
            'provider' => ['required', 'string', Rule::in(CloudProvider::$registered->pluck('name'))],
            'private_network' => ['required', 'string'],
            'address' => ['required', new ValidURI]
        ];
    }
}

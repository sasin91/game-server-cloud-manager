<?php

namespace App\Http\Requests;

use App\Server;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ServerShowRequest extends FormRequest
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
        return Gate::check('view', $this->model());
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

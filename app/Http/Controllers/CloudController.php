<?php

namespace App\Http\Controllers;

use App\Actions\CreateCloud;
use App\Actions\DeleteCloud;
use App\Actions\UpdateCloud;
use App\Cloud;
use App\Http\Requests\CloudDestroyRequest;
use Illuminate\Http\Request;
use App\Http\Resources\CloudResource;
use App\Http\Requests\CloudShowRequest;
use App\Http\Requests\CloudIndexRequest;
use App\Http\Requests\CloudStoreRequest;
use App\Http\Requests\CloudUpdateRequest;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CloudController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return CloudResource|AnonymousResourceCollection
     */
    public function index(CloudIndexRequest $request)
    {
        return CloudResource::collection(
            $request->user()->clouds()->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CloudStoreRequest $request
     * @param CreateCloud $createCloud
     * @return CloudResource
     */
    public function store(CloudStoreRequest $request, CreateCloud $createCloud)
    {
        return new CloudResource(
            $createCloud->execute(
                $request->user()->currentTeam,
                $request->validated()
            )
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  CloudShowRequest $request
     * @return CloudResource
     */
    public function show(CloudShowRequest $request)
    {
        return new CloudResource(
            $request->model()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CloudUpdateRequest  $request
     * @param  UpdateCloud $updateCloud
     * @return CloudResource
     */
    public function update(CloudUpdateRequest $request, UpdateCloud $updateCloud)
    {
        $updateCloud->execute(
            $request->model(),
            $request->validated()
        );

        return new CloudResource(
            $request->model()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  CloudDestroyRequest $request
     * @return void
     */
    public function destroy(CloudDestroyRequest $request, DeleteCloud $deleteCloud)
    {
        $deleteCloud->execute(
            $request->model()
        );
    }
}

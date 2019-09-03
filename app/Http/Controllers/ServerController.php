<?php

namespace App\Http\Controllers;

use App\Actions\CreateServer;
use App\Actions\DeleteServer;
use App\Actions\UpdateServer;
use App\Http\Requests\ServerDestroyRequest;
use App\Http\Requests\ServerIndexRequest;
use App\Http\Requests\ServerShowRequest;
use App\Http\Requests\ServerStoreRequest;
use App\Http\Requests\ServerUpdateRequest;
use App\Http\Resources\ServerResource;
use App\Server;
use Illuminate\Http\Request;

class ServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ServerIndexRequest $request
     * @return ServerResource
     */
    public function index(ServerIndexRequest $request)
    {
        return new ServerResource(
            Server::query()
                ->whereHas('cloud.team', function ($model) use ($request) {
                    $model->whereKey($request->user()->team_id);
                })
                ->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ServerStoreRequest  $request
     * @param CreateServer $createServer
     * @return ServerResource
     */
    public function store(ServerStoreRequest $request, CreateServer $createServer)
    {
        $model = $createServer->execute(
            $request->cloud(),
            $request->validated()
        );

        return new ServerResource($model);
    }

    /**
     * Display the specified resource.
     *
     * @param  ServerShowRequest $request
     * @return ServerResource
     */
    public function show(ServerShowRequest $request)
    {
        return new ServerResource(
            $request->model()
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ServerUpdateRequest $request
     * @param UpdateServer $updateServer
     * @return ServerResource
     */
    public function update(ServerUpdateRequest $request, UpdateServer $updateServer)
    {
        return new ServerResource(
            $updateServer->execute(
                $request->model(),
                $request->validated()
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ServerDestroyRequest $request
     * @param DeleteServer $deleteServer
     * @return void
     */
    public function destroy(ServerDestroyRequest $request, DeleteServer $deleteServer)
    {
        $deleteServer->execute(
            $request->model()
        );
    }
}

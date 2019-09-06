<?php

namespace App\Http\Controllers;

use App\Actions\CreateServerDeployment;
use App\Deployment;
use App\Http\Requests\ServerDeploymentIndexRequest;
use App\Http\Requests\StoreServerDeploymentRequest;
use App\Server;
use Illuminate\Http\Request;

class ServerDeploymentController extends Controller
{
    public function index(ServerDeploymentIndexRequest $request)
    {
        return $request
            ->model()
            ->deployments()
            ->paginate();
    }

    public function store(StoreServerDeploymentRequest $request, CreateServerDeployment $createServerDeployment)
    {
        return $createServerDeployment->execute(
            $request->model(),
            $request->validated()
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Deployment  $deployment
     * @return \Illuminate\Http\Response
     */
    public function show(Deployment $deployment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Deployment  $deployment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Deployment $deployment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Deployment  $deployment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deployment $deployment)
    {
        //
    }
}

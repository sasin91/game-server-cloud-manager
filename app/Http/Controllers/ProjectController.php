<?php

namespace App\Http\Controllers;

use App\Project;
use App\Actions\CreateProject;
use App\Actions\DeleteProject;
use App\Actions\UpdateProject;
use App\Actions\RestoreProject;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\ProjectShowRequest;
use App\Http\Requests\ProjectIndexRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Requests\ProjectUpdateRequest;
use App\Http\Requests\ProjectDestroyRequest;
use App\Http\Requests\ProjectRestoreRequest;

class ProjectController extends Controller
{
    public function index(ProjectIndexRequest $request)
    {
        return ProjectResource::collection(
            $request
                ->user()
                ->currentTeam
                ->projects()
                ->paginate()
        );
    }

    public function store(ProjectStoreRequest $request, CreateProject $createProject)
    {
        return new ProjectResource(
            $createProject->execute(
                $request->user()->currentTeam,
                $request->validated()
            )
        );
    }

    public function show(ProjectShowRequest $request)
    {
        return new ProjectResource(
            $request->model()
        );
    }

    public function update(ProjectUpdateRequest $request, UpdateProject $updateProject)
    {
        return new ProjectResource(
            $updateProject->execute(
                $request->model(),
                $request->validated()
            )
        );
    }

    public function destroy(ProjectDestroyRequest $request, DeleteProject $deleteProject)
    {
        $deleteProject->execute(
            $request->model()
        );
    }

    public function restore(ProjectRestoreRequest $request, RestoreProject $restoreProject)
    {
        $restoreProject->execute(
            $request->model()
        );
    }
}

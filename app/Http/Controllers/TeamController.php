<?php

namespace App\Http\Controllers;

use App\Actions\CreateTeam;
use App\Http\Resources\TeamResource;
use App\Http\Requests\TeamStoreRequest;
use App\Http\Requests\TeamUpdateRequest;
use App\Team;
use App\Actions\UpdateTeam;
use App\Actions\DeleteTeam;
use App\Http\Requests\TeamDestroyRequest;
use App\Http\Requests\TeamRestoreRequest;
use App\Actions\RestoreTeam;

class TeamController extends Controller
{
    public function store(TeamStoreRequest $request, CreateTeam $createTeam)
    {
        return new TeamResource(
            $createTeam->execute(
                $request->input('name'),
                $request->user()
            )
        );
    }

    public function update(TeamUpdateRequest $request, UpdateTeam $updateTeam)
    {
        return new TeamResource(
            $updateTeam->execute(
                $request->model(),
                $request->validated()
            )
        );
    }

    public function destroy(TeamDestroyRequest $request, DeleteTeam $deleteTeam)
    {
        $deleteTeam->execute(
            $request->model()
        );
    }

    public function restore(TeamRestoreRequest $request, RestoreTeam $restoreTeam)
    {
        return new TeamResource(
            $restoreTeam->execute(
                $request->model()
            )
        );
    }
}

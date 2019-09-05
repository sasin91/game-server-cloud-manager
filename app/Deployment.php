<?php

namespace App;

use App\Scripts\Script;
use Illuminate\Support\Str;
use App\Events\DeploymentCreated;
use App\Events\DeploymentDeleted;
use App\Events\DeploymentUpdated;
use App\Events\DeploymentExecuted;
use App\Events\DeploymentExecuting;
use Symfony\Component\Process\Process;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deployment extends Model
{
    protected $fillable = [
        'server_id',
        'project_id',
        'script',
        'exitcode',
        'output'
    ];

    protected $dispatchesEvents = [
        'created' => DeploymentCreated::class,
        'updated' => DeploymentUpdated::class,
        'deleted' => DeploymentDeleted::class,
        'executing' => DeploymentExecuting::class,
        'executed' => DeploymentExecuted::class
    ];

    /**
     * The server the deployment happen on
     *
     * @return BelongsTo
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * The deployed Project
     *
     * @return BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Run the commandline script
     *
     * @return $this
     */
    public function run()
    {
        return $this->execute(
            Process::fromShellCommandline($this->script)
        );
    }

    /**
     * Execute the given process instance
     *
     * @param Process $process
     * @return $this
     */
    protected function execute($process)
    {
        $this->fireModelEvent('executing');

        $output = '';

        $process->run(static function ($type, $shellOutput) use (&$output) {
            // Skip lines like SSH "Warning: Permanently added 'IP' ..."
            if (Str::startsWith($shellOutput, 'Warning:')) {
                $output .= \trim(
                    Str::after($shellOutput, PHP_EOL)
                );
            } else {
                $output .= \trim($shellOutput);
            }
        });

        $this->recordProcessStatus(
            $output,
            $process->getExitCode()
        );

        $this->fireModelEvent('executed');

        return $this;
    }

    /**
     * Record the result of running a script process.
     *
     * @param string $output
     * @param integer $exitcode
     * @return void
     */
    protected function recordProcessStatus(string $output, int $exitcode)
    {
        $this->update([
            'exitcode' => $exitcode,
            'output' => $output
        ]);
    }
}

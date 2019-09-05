<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ScriptMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:script';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Script class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Script';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/script.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Scripts';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $this->replaceView($stub, $this->option('view'));

        return $stub;
    }

    /**
     * Replace the Script for the given stub.
     *
     * @param  string  $stub
     * @param  string  $view
     * @return $this
     */
    protected function replaceView(&$stub, $view)
    {
        $stub = str_replace('DummyView', $view, $stub);

        return $this;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['view', null, InputOption::VALUE_OPTIONAL, 'The View path for the script'],
        ];
    }
}

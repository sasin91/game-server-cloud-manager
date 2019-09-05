<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class VersionControlMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:version-control';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new version control class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'VersionControl';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/versionControl.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\VersionControl';
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

        $this->replaceURL($stub, ($this->option('url') || 'https://api.example.com/'));

        return $stub;
    }

    /**
     * Replace the client URL for the given stub.
     *
     * @param  string  $stub
     * @param  string  $url
     * @return $this
     */
    protected function replaceURL(&$stub, $url)
    {
        $stub = str_replace('DummyURL', $url, $stub);

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
            ['url', null, InputOption::VALUE_OPTIONAL, 'The URL to the provider API'],
        ];
    }
}

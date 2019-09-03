<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CloudProviderMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:cloud-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new cloud provider class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'CloudProvider';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/cloudProvider.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\CloudProviders';
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

        return $this->replaceURL($stub, ($this->option('url') || 'https://api.example.com/'));
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

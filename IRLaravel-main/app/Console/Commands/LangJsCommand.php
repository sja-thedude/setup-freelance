<?php

namespace App\Console\Commands;

use Mariuzzo\LaravelJsLocalization\Commands\LangJsCommand as ParentCommand;

/**
 * The LangJsCommand class.
 *
 * @author  Rubens Mariuzzo <rubens@mariuzzo.com>
 */
class LangJsCommand extends ParentCommand
{
    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'lang:js-generate';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Generate JS lang files.';

    /**
     * The generator instance.
     *
     * @var LangJsGenerator
     */
    protected $generator;

    /**
     * Construct a new LangJsCommand.
     *
     * @param LangJsGenerator $generator The generator.
     */
    public function __construct(\App\Console\Commands\LangJsGenerator $generator)
    {
        parent::__construct($generator);
    }
}

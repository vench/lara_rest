<?php

namespace LpRest\Commands;

use Illuminate\Console\Command;
use LpRest\Repositories\CommonRepositoryModelProvider;

/**
 * Class AccessFillCommand
 * @package LpRest\Commands
 */
class AccessFillCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rest:fill-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var CommonRepositoryModelProvider
     */
    private $modelProvider;


    /**
     * AccessFillCommand constructor.
     * @param CommonRepositoryModelProvider $modelProvider
     * @param null $name
     */
    public function __construct(CommonRepositoryModelProvider $modelProvider, $name = null)    {
        parent::__construct($name);
        $this->modelProvider = $modelProvider;
    }

    /**
     *
     */
    public function handle() {

        foreach ($this->modelProvider->getRegisteredAliases() as $alias) {
            echo $alias, PHP_EOL;
        }
    }
}
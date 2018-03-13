<?php

namespace LpRest\Commands;

use Illuminate\Console\Command;
use LpRest\Models\RestAccess;
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

            $model = $this->modelProvider->getModelByName($alias);
            $permissions = array_values(
                $model->getRestAccessPermissionAliases());


            $accesses = RestAccess::query()
                ->where('type', RestAccess::TYPE_PERMISSION)
                ->whereIn('name',  $permissions)
                ->get();

            foreach ($accesses as $item) {
                //check isset
                if(($key = array_search($item->name, $permissions)) !== false) {
                    unset($permissions[$key]);
                    continue;
                }

                //delete
                $item->delete();
            }

            //insert
            foreach ($permissions as $permission) {
                RestAccess::query()->insert([
                    'name'          => $permission,
                    'type'          => RestAccess::TYPE_PERMISSION,
                    'description'   => $permission,
                ]);
            }
        }
    }
}
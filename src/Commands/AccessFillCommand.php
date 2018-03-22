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
    protected $signature = 'rest:fill-access {--safe}';

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

        $accesses = RestAccess::query()
            ->where('type', RestAccess::TYPE_PERMISSION)
            ->get();

        $permissions = [];
        $groups = [];
        $safe = $this->option('safe');

        foreach ($this->modelProvider->getRegisteredAliases() as $alias) {
            $model = $this->modelProvider->getModelByName($alias);
            $perms = array_values( $model->getRestAccessPermissionAliases());

            $groups[$alias] = $perms;
            $permissions = array_merge($permissions, $perms);
        }

        foreach ($accesses as $item) {
            //check isset
            if(($key = array_search($item->name, $permissions)) !== false) {
                unset($permissions[$key]);
                continue;
            }

            //delete
            if(!$safe) {
                $item->delete();
            }
        }

        //insert
        foreach ($permissions as $permission) {
            RestAccess::query()->insert([
                'name'          => $permission,
                'type'          => RestAccess::TYPE_PERMISSION,
                'description'   => $permission,
            ]);
        }


        //create group
        foreach ($groups as $alias => $perms) {
            $access = RestAccess::query()
                ->where('type', RestAccess::TYPE_GROUP)
                ->where('name', $alias)
                ->first();

            if(!is_null($access)) {
                continue;
            }

            RestAccess::query()->insert([
                'name'          => $alias,
                'type'          => RestAccess::TYPE_GROUP,
                'description'   => $alias,
            ]);


            $models = RestAccess::query()->whereIn('name', $perms)->get();

            RestAccess::groupAddChilds($alias, $models->map(function($model){
                return $model->id;
            })->toArray());
        }
    }
}
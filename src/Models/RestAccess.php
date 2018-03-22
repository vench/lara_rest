<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 12.03.18
 * Time: 16:33
 */

namespace LpRest\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Group
 * @package LpRest\Models
 *
 * @property string $type
 * @property string $name
 * @property string $description
 *
 * @property RestAccess[] $childAccesses
 */
class RestAccess extends Model
{

    const TYPE_GROUP = 'group';

    const TYPE_PERMISSION = 'permission';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rest_access';

    /**
     * @var string
     */
    protected $primaryKey = 'id';



    /**
     * @return mixed
     */
    public function childAccesses() {
        return $this->belongsToMany(
            static::class,
            'rest_group_permission', 'group', 'permission');
    }

    /**
     * @return bool
     */
    public function isGroup() {
        return $this->type == self::TYPE_GROUP;
    }

    /**
     * @param string $accessName
     * @return bool
     */
    public function checkAccess( $accessName) {
        if ($this->name === $accessName) {
            return true;
        }

        if($this->isGroup()) {
            foreach ($this->childAccesses as $access) {
                if ($access->checkAccess($accessName)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $group
     * @param array $chlds
     */
    public static function groupAddChilds($group, array $chlds) {
        foreach ($chlds as $permission) {
            DB::table('rest_group_permission')->insert([
                'permission'    => $permission,
                'group'         => $group,
            ]);
        }
    }
}
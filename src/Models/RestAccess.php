<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 12.03.18
 * Time: 16:33
 */

namespace LpRest\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Group
 * @package LpRest\Models
 *
 * @property string $type
 * @property string $name
 * @property string $description
 *
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
    protected $primaryKey = 'name';

    /**
     * @var string
     */
    protected $keyType = 'string';


    /**
     * @return mixed
     */
    public function getPermissions() {
        return $this->belongsToMany(static::class, 'rest_group_permission', 'group', 'permission');
    }
}
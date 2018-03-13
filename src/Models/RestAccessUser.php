<?php
/**
 * Created by PhpStorm.
 * User: vench
 * Date: 12.03.18
 * Time: 16:42
 */

namespace LpRest\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RestAccessUser
 * @package LpRest\Models
 *
 * @property string $access_name
 * @property string $user_outer
 */
class RestAccessUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rest_access_user';

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * @return mixed
     */
    public function getAccesses() {
        return $this->belongsToMany(RestAccess::class, 'name', 'access_name');
    }

}
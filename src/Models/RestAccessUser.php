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
 * @property string $access_id
 * @property string $user_outer
 *
 * @property RestAccess $restAccess
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
     * @return mixed
     */
    public function restAccess() {
        return $this->hasOne(
            RestAccess::class,
            'id',
            'access_id'
        );
    }


    /**
     * @param string $accessName
     * @return bool
     */
    public function checkAccess( $accessName) {
        if ($this->access_id === $accessName) {
            return true;
        }

        return (!is_null($this->restAccess)  && $this->restAccess->checkAccess($accessName));
    }

}
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
            'name',
            'access_name'
        );
    }


    /**
     * @param string $accessName
     * @return bool
     */
    public function checkAccess( $accessName) {
        if ($this->access_name === $accessName) {
            return true;
        }

        return (!is_null($this->restAccess)  && $this->restAccess->checkAccess($accessName));
    }

}
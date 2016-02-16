<?php

// app/Models/BaseModel.php

namespace Models;

use Silex\Application;


/**
 * Class - BaseModel
 *
 * @category Model
 * @package  app\Models
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 */
class BaseModel {

    use \Models\Helper\OrmTrait;
    use \Models\Helper\DbalTrait;
    use \Models\Helper\EntityTrait;
    use \Controllers\Helper\SecurityTrait;

}

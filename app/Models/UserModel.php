<?php

// app/Models/UserModel.php

namespace Models;


/**
 * Class - UsersModel
 * Model for Users
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class UserModel extends BaseModel {
    
    /**
     * Constructor
     */
//    public function __construct() {    
//        parent::__construct();
//    }

    /**
     * Action - newUser
     * 
     * @param array $data
     * @return void
     */
    public function newUser($data) {
        $em = $this->app['em'];
//        $repo = $em->getRepository('Models\ORM\User');
        //--------------------
        
        $new_user = $data['new_user'];
        $new_user->setRoles('ROLE_USER');
        $new_user->setPassword('5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg==');

        $em->persist($new_user);
        $em->flush();
        
        return array(
            'username' => $new_user->getUsername(), 
            'first_name'=> $new_user->getFirstName(), 
            'password' => 'foo'
            );
    }

    
}

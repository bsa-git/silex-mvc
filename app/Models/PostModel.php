<?php

// app/Models/PostModel.php

namespace Models;


/**
 * Class - PostModel
 * Model for Posts
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class PostModel extends BaseModel {

    //------------------------
    
     /**
     * newPost
     * Add post for current user
     * 
     * @param array $data
     * @return void
     */
    public function newPost($data) {
        $em = $this->app['em'];
        $repo = $em->getRepository('Models\ORM\User');
        //--------------------
        
        $username = $data['username'];
        $new_post = $data['new_post'];
        
        $user = $repo->findOneByUsername($username);

        if ($user === NULL) {
            $this->app->abort(404, "Not find user for this username \"{$userName}\"");
        }
        
        $new_post->setUser($user);
        $em->persist($new_post);
        $em->flush();
    }
    
    /**
     * editPost
     * Edit this post
     * 
     * @param array $data
     * @return void
     */
    public function editPost($data) {
        $em = $this->app['em'];
        //--------------------
        $edit_post = $data['edit_post'];
        $em->persist($edit_post);
        $em->flush();
    }
    
    /**
     * getPostsForUser
     * Get posts for user
     * 
     * @param string $userName
     * @return array
     */
    public function getPostsForUser($userName) {
        $em = $this->app['em'];
        $repo = $em->getRepository('Models\ORM\User');
        //--------------------
        $user = $repo->findOneByUsername($userName);

        if ($user === NULL) {
            $this->app->abort(404, "Not find user for this username \"{$userName}\"");
        }
        $posts = $user->getPosts();

        // Data for user's posts
        $data = array('username' => $user->getUsername(), 'posts' => $posts->toArray());

        return $data;
    }

    /**
     * getPosts
     * Get posts for user
     * 
     * @param string $userName
     * @return array
     */
    public function getPosts($userName) {
        $arBox = $this->app['my']->get('array');
        $db = $this->app['db'];
        //--------------------
        if ($userName) {
            // Get user's id
            $sql = "SELECT * FROM user WHERE username = ?";
            $user = $db->fetchAssoc($sql, array($userName));
            if ($user === FALSE) {
                $this->app->abort(404, "Not find user for this username \"{$userName}\"");
            }
            $user_id = $user['id'];

            // Get user's posts
            $sql = "SELECT * FROM post WHERE user_id = ?";
            $posts = $db->fetchAll($sql, array((int) $user_id));
            if (!count($posts)) {
                $posts = array();
            }

            // Data for user's posts
            $data = array('username' => $userName, 'posts' => $posts);
        } else {

            $posts = array();
            $sql = "SELECT *  FROM user";
            $users = $db->fetchAll($sql);
            $arUsers = $arBox->set($users)->slice(array('id', 'username'))->get();
            // Add debug info
            $this->app['my.debug']->add($users);
            foreach ($arUsers as $id => $user_name) {
                $sql = "SELECT * FROM post WHERE user_id = ?";
                $userPosts = $db->fetchAll($sql, array($id));
                $posts[$user_name] = $userPosts;
            }

            // Add debug info
            $this->app['my.debug']->add($posts);
            
            // Data for user's posts
            $data = array('username' => $userName, 'posts' => $posts);
        }
        return $data;
    }

}

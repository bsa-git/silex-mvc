<?php

// app/Models/PostModel.php

namespace Models;

/**
 * Class - PostModel
 * Model for Posts
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergii Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
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
        $db = $this->app['db'];
        //--------------------

        $username = $data['username'];
        $ormPost = $data['new_post'];
        // Transform the date to format 'Y-m-d'
        $date = new \DateTime($ormPost->getCreated());
        $ormPost->setCreated($date->format('Y-m-d'));
        $arrPost = $ormPost->getValues();

        // Get user's id
        $sql = "SELECT * FROM user WHERE username = ?";
        $user = $db->fetchAssoc($sql, array($username));
        if ($user === FALSE) {
            $this->app->abort(404, "Not find user for this username \"{$username}\"");
        }
        $arrPost['user_id'] = $user['id'];
        $db->insert('post', $arrPost);
    }

    /**
     * editPost
     * Edit this post
     * 
     * @param array $data
     * @return void
     */
    public function editPost($data) {
        $db = $this->app['db'];
        //--------------------
        $ormPost = $data['edit_post'];
        $id = $ormPost->getId();
        // Transform the date to format 'Y-m-d'
        $date = new \DateTime($ormPost->getCreated());
        $ormPost->setCreated($date->format('Y-m-d'));
        $arrPost = $ormPost->getValues();
        unset($arrPost['id']);
        
        $db->update('post', $arrPost, array('id' => $id));
        
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
        $arrBox = $this->app['my']->get('array');
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
            $arrUsers = $arrBox->set($users)->slice(array('id', 'username'))->get();
            // Add debug info
            $this->app['my.debug']->add($users);
            foreach ($arrUsers as $id => $user_name) {
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

    /**
     * getPost
     * Read post for id
     * 
     * @param int $id
     * @return array
     */
    public function getPost($id) {
        $db = $this->app['db'];
        //--------------------
        // Get user's posts
        $sql = "SELECT * FROM post WHERE id = ?";
        $post = $db->fetchAssoc($sql, array($id));
        if ($post === FALSE) {
            $this->app->abort(404, "Not find post for this id \"{$id}\"");
        }
        return $post;
    }

}

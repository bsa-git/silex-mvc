<?php

// app/Models/Entity/User.php

namespace Models\ORM;

use Forms\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class - User
 * Model for Users
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 * 
 * @Entity
 * @Table(name="user")
 */
class User {
    
    use \Models\Helper\EntityTrait;


    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=32)
     */
    protected $username;
    
    /**
     * @Column(type="string", length=32)
     */
    protected $first_name;
    
    /**
     * @Column(type="string", length=32)
     */
    protected $second_name;

    /**
     * @Column(type="string", length=127)
     */
    protected $email;
    
    /**
     * @Column(type="string", length=32)
     */
    protected $personal_mobile;

    /**
     * @Column(type="string", length=255)
     */
    protected $password;

    /**
     * @Column(type="string", length=255)
     */
    protected $roles;

    /**
     * @OneToMany(targetEntity="Post", mappedBy="user")
     */
    protected $posts;

    //--------------------------------------------

    /**
     * Load validator metadata
     * 
     * @param Symfony\Component\Validator\Mapping\ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('password', new Assert\NotBlank(
                array(
            'groups' => array()
        )));
        $metadata->addPropertyConstraint('password', new Assert\Length(
                array(
            'min' => 3,
            'groups' => array()
        )));
        $metadata->addPropertyConstraint('first_name', new Assert\NotBlank(
                array(
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('second_name', new Assert\NotBlank(
                array(
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('username', new Assert\NotBlank(
                array(
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('username', new Assert\Length(
                array(
            'min' => 3,
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('username', new Assert\Customize\UniqueEntity(array(
            'groups' => array('registration'),
            'app' => self::getAppStatic(),
            'entity' => 'Models\ORM\User',
            'field' => 'username',
        )));
        $metadata->addPropertyConstraint('username', new Assert\NotBlank(
                array(
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('email', new Assert\NotBlank(
                array(
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('email', new Assert\Email(array(
            'groups' => array('registration')
        )));
        $metadata->addPropertyConstraint('email', new Assert\Customize\UniqueEntity(array(
            'groups' => array('registration'),
            'app' => self::getAppStatic(),
            'entity' => 'Models\ORM\User',
            'field' => 'email',
        )));
    }
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->first_name = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Set secondName
     *
     * @param string $secondName
     *
     * @return User
     */
    public function setSecondName($secondName)
    {
        $this->second_name = $secondName;

        return $this;
    }

    /**
     * Get secondName
     *
     * @return string
     */
    public function getSecondName()
    {
        return $this->second_name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set personalMobile
     *
     * @param string $personalMobile
     *
     * @return User
     */
    public function setPersonalMobile($personalMobile)
    {
        $this->personal_mobile = $personalMobile;

        return $this;
    }

    /**
     * Get personalMobile
     *
     * @return string
     */
    public function getPersonalMobile()
    {
        return $this->personal_mobile;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param string $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add post
     *
     * @param \Models\ORM\Post $post
     *
     * @return User
     */
    public function addPost(\Models\ORM\Post $post)
    {
        $this->posts[] = $post;

        return $this;
    }

    /**
     * Remove post
     *
     * @param \Models\ORM\Post $post
     */
    public function removePost(\Models\ORM\Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }
}

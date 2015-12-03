<?php

// app/Models/Entity/Post.php

namespace Models\ORM;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Forms\Constraints as Assert;

/**
 * Class - Post
 * Model for Post
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 * 
 * @Entity
 * @Table(name="post")
 */
class Post {
    
    use \Models\Helper\EntityTrait;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="date")
     */
    protected $created;

    /**
     * @Column(type="string", length=255)
     */
    protected $title;

    /**
     * @Column(type="text")
     */
    protected $body;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="posts")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     * 
     */
    protected $user;

    //------------------------

    /**
     * Load validator metadata
     * 
     * @param Symfony\Component\Validator\Mapping\ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('created', new Assert\NotBlank());
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
        $metadata->addPropertyConstraint('title', new Assert\Length(array('min' => 5)));
        $metadata->addPropertyConstraint('body', new Assert\NotBlank());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Post
     */
    public function setBody($body) {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * Set user
     *
     * @param \Models\ORM\User $user
     *
     * @return Post
     */
    public function setUser(\Models\ORM\User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Models\ORM\User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Post
     */
    public function setCreated($created) {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

}

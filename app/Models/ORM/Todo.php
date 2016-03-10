<?php

// app/Models/Entity/Todo.php

namespace Models\ORM;

use Silex\Application;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Forms\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;

/**
 * Class - Todo
 * Model for user tasks
 * 
 * @category Model
 * @package  app\Models
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     https://github.com/bsa-git/silex-mvc/
 * 
 * @Entity
 * @Table(name="todo")
 */
class Todo {

    use \Models\Helper\EntityTrait;

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(name="`title`", type="string", length=255)
     */
    protected $title;

    /**
     * @Column(name="`task_order`", type="integer")
     */
    protected $task_order;

    /**
     * @Column(name="`done`", type="boolean")
     */
    protected $done;

    //------------------------

    /**
     * Load validator metadata
     * 
     * @param Symfony\Component\Validator\Mapping\ClassMetadata $metadata
     */
    static public function loadValidatorMetadata(ClassMetadata $metadata) {
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
        $metadata->addPropertyConstraint('task_order', new Assert\Type('int'));
        $metadata->addPropertyConstraint('done', new Assert\Type('boolean'));
    }

    /**
     * Configure options for resolver
     * 
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        //----------------------
        $resolver->setDefaults(array(
            'id' => 0,
            'title' => "Empty task...",
            'task_order' => 1,
            'done' => false
        ));

        $resolver->setRequired(array('id', 'title', 'task_order', 'done'));
        // Set options types
        $resolver->setAllowedTypes('id', 'int');
        $resolver->setAllowedTypes('title', 'string');
        $resolver->setAllowedTypes('task_order', 'int');
        $resolver->setAllowedTypes('done', 'boolean');

        // Normalizer 'title'
        $resolver->setNormalizer('title', function (Options $options, $value) {
            $nValue = $this->app['zf2']
                    ->get('filter')
                    ->attach($this->app['zf2.filter.string_trim']())
                    ->attach($this->app['zf2.filter.strip_tags']())
                    ->filter($value);
            return $nValue;
        });
    }

    /**
     * Normalize values
     * 
     * @param array $aValues
     * @param Application $aApp
     * @return array Normalized values
     */
    public static function normalizeValues($aValues = array(), Application $aApp = NULL) {
        $nValues = array();
        $app = $aApp ? $aApp : self::getAppStatic();
        //------------------------
        if (isset($aValues['id'])) {
            $nValues['id'] = (int) $aValues['id'];
        }
        if (isset($aValues['task_order'])) {
            $nValues['task_order'] = (int) $aValues['task_order'];
        }
        if (isset($aValues['done'])) {
            $nValues['done'] = (boolean) $aValues['done'];
        }
        if (isset($aValues['title'])) {
            $value = $aValues['title'];
            $nValue = $app['zf2']
                    ->get('filter')
                    ->attach($app['zf2.filter.string_trim']())
                    ->attach($app['zf2.filter.strip_tags']())
                    ->filter($value);
            $nValues['title'] = $nValue;
        }

        return $nValues;
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
     * @return Todo
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
     * Set taskOrder
     *
     * @param integer $taskOrder
     *
     * @return Todo
     */
    public function setTaskOrder($taskOrder) {
        $this->task_order = $taskOrder;

        return $this;
    }

    /**
     * Get taskOrder
     *
     * @return integer
     */
    public function getTaskOrder() {
        return $this->task_order;
    }

    /**
     * Set done
     *
     * @param boolean $done
     *
     * @return Todo
     */
    public function setDone($done) {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean
     */
    public function getDone() {
        return $this->done;
    }

}

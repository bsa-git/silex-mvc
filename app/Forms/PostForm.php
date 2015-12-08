<?php

// app/Forms/PostForm.php

namespace Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class - PostForm
 * Add new post
 * 
 * @category Form
 * @package  app\Forms
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class PostForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if(isset($options['action'])){
            $builder->setAction($options['action']);
        }
        //$builder->add('created', 'date', array('widget' => 'single_text'));
        $builder->add('created', 'text', array());
        $builder->add('title', 'text', array());
        $builder->add('body', 'textarea');
        $builder->add('save', 'submit');
    }

    public function getName() {
        return 'post';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array(),
            'data_class' => 'Models\ORM\Post',//Models\ORM\Post Models\AR\Post
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // unique key to generate a secret token
            'intention'       => 'post_secret',
        ));
    }

}

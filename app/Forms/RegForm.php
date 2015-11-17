<?php

// app/Forms/RegForm.php

namespace Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class - RegForm
 * Login user
 * 
 * @category Form
 * @package  app\Forms
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
class RegForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->setAction('/registration');
        $builder->add('username', 'text');
        $builder->add('first_name', 'text');
        $builder->add('second_name', 'text');
        $builder->add('email', 'text');
        $builder->add('personal_mobile', 'text');
        $builder->add('save', 'submit');
    }

    public function getName() {
        return 'reg';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'validation_groups' => array('registration'),
            'data_class' => 'Models\ORM\User',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // unique key to generate a secret token
            'intention' => 'reg_secret',
        ));
    }

}

<?php

namespace AppBundle\Twig;

class GetConstantExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('constant', array($this, 'getConstant'), array()),
            new \Twig_SimpleFunction('static_value', array($this, 'getStaticValue'), array()),
            new \Twig_SimpleFunction('static_function', array($this, 'getStaticFunctionCall'), array()),
        );
    }

    /**
     * Get global constant from twig
     *
     * @param $constant
     *
     * @return mixed
     */
    public function getConstant($constant)
    {
        return constant($constant);
    }

    /**
     * Gets static constant on class from twig
     *
     * @param $class
     * @param $value
     *
     * @return mixed
     */
    public function getStaticValue($class, $value)
    {
        $reflection = new \ReflectionClass($class);
        return $reflection->getConstant($value);
    }

    /**
     * Calls static function from twig
     *
     * @param $class
     * @param $function
     * @param array $args
     *
     * @return mixed|null
     */
    public function getStaticFunctionCall($class, $function, $args = array())
    {
        if (class_exists($class) && method_exists($class, $function)) {
            return call_user_func_array(array($class, $function), $args);
        }
        return null;
    }

    public function getName()
    {
        return 'constant_extension';
    }
}

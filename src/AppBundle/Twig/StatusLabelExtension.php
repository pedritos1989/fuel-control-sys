<?php

namespace AppBundle\Twig;

/**
 * Class StatusLabelExtension
 *
 * @package AppBundle\Twig
 */
class StatusLabelExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('status_label', array($this, 'getStatus'), array(
                'is_safe' => array('html'),
                'needs_environment' => true,
            )),
        );
    }

    /**
     * @param \Twig_Environment $twig
     * @param                   $status
     * @param string            $template
     * @param string            $translationDomain
     *
     * @return mixed
     * @internal param $constant
     */
    public function getStatus(
        \Twig_Environment $twig,
        $status,
        $template='_labels.html.twig',
        $translationDomain='messages'
    ) {
        $twigTemplate = $twig->resolveTemplate($template);

        return $twigTemplate->render(array(
            'status' => $status,
            'title' => $status,
            'translationDomain' => $translationDomain
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'status_label_extension';
    }
}

<?php
/**
 * Class yii\helpers\Html
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace app\components;

/**
 * Description of Html
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Html extends \yii\helpers\BaseHtml
{

    /**
     * Renders Bootstrap glyph span of given sub-class
     *
     * @param string $class
     * @return string
     */
    public static function glyph($class)
    {
        return self::tag('span', '', ['class' => "glyphicon glyphicon-{$class}"]);
    }


    /**
     * Renders Bootstrap label span
     *
     * @param string $content
     * @param string|string[] $class
     * @return string
     */
    public static function bslabel($content, $class = 'primary')
    {
        $options = is_array($class)
            ? $class
            : ['class' => "label label-{$class}"] ;

        return self::tag('span', $content, $options);
    }

    /**
     * Renders Bootstrap alert div
     *
     * @param string $content
     * @param string|string[] $class
     * @return string
     */
    public static function bsalert($content, $class = 'danger')
    {
        if ($content)
        {
            $options = is_array($class) ? $class : ['class' => "alert alert-{$class} tight"] ;

            return self::tag('div', $content, $options);
        }
        return '';
    }
}

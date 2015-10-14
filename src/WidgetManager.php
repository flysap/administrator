<?php

namespace Flysap\Application;

use Flysap\Application\Exceptions\WidgetException;

class WidgetManager {

    /**
     * @var array
     */
    protected $widgets = array();

    public function __construct(array $widgets = array()) {
        $this->addWidgets($widgets);
    }

    /**
     * Register class widget .
     *
     * @param $class
     * @return $this
     * @throws WidgetException
     */
    public function subscribe($class) {
        if( class_exists($class) ) {
            $instance = (new $class);

            if( $instance instanceof WidgetAble )
                $this->addWidget($class, (new $class));
            else
                throw new WidgetException('Invalid class');

            return $this;
        }

        throw new WidgetException('Invalid class');
    }


    /**
     * Add widgets .
     *
     * @param array $widgets
     * @return $this
     */
    public function addWidgets(array $widgets) {
        array_walk($widgets, function ($widget, $alias) {
            $this->addWidget($alias, $widget);
        });

        return $this;
    }

    /**
     * Add widget .
     *
     * @param $alias
     * @param WidgetAble $widget
     * @return $this
     */
    public function addWidget($alias, $widget) {
        $this->widgets[$alias] = $widget;

        return $this;
    }


    /**
     * Remove widgets by key .
     *
     * @param $alias
     * @return $this
     */
    public function removeWidget($alias) {
        if (isset($this->widgets[$alias]))
            unset($this->widgets[$alias]);

        return $this;
    }


    /**
     * Get all widgets .
     *
     * @return array
     */
    public function getWidgets() {
        return $this->widgets;
    }

    /**
     * Get widget by key .
     *
     * @param $alias
     * @return mixed
     */
    public function getWidget($alias) {
        if (isset($this->widgets[$alias]))
            return $this->widgets[$alias];
    }


    /**
     * Render widgets .
     *
     * @param array $only
     * @return string
     * @internal param array $widgets
     */
    public function render(array $only = array()) {
        $widgets = $this->getWidgets();

        $html = '';
        array_walk($widgets, function ($widget, $alias) use (& $html, $only) {
            if( !empty($only) && !in_array($alias, $only))
                return false;

            if( $widget instanceof \Closure ) {
                $html .= $widget();
            } else {
                $instance = (new $widget);
                if ($instance instanceof Widget) {
                    if ($instance->isAllowed())
                        $html .= $instance->render();
                } else {
                    $html .= $instance->render();
                }
            }
        });

        return $html;
    }

}
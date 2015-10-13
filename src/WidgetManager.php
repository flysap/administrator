<?php

namespace Flysap\Application;

class WidgetManager {

    /**
     * @var array
     */
    protected $widgets = array();

    public function __construct(array $widgets = array()) {
        $this->addWidgets($widgets);
    }


    /**
     * Add widgets .
     *
     * @param array $widgets
     * @return $this
     */
    public function addWidgets(array $widgets) {
        array_walk($widgets, function($widget) {
            $this->addWidget($widget);
        });

        return $this;
    }

    /**
     * Add widget .
     *
     * @param WidgetAble $widget
     * @return $this
     */
    public function addWidget(WidgetAble $widget) {
        $this->widgets[get_class($widget)] = $widget;

        return $this;
    }


    /**
     * Remove widgets by key .
     *
     * @param $class
     * @return $this
     */
    public function removeWidget($class) {
        if( isset($this->widgets[$class]) )
            unset($this->widgets[$class]);

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
     * @param $class
     * @return mixed
     */
    public function getWidget($class) {
        if( isset($this->widgets[$class]) )
            return $this->widgets[$class];
    }


    /**
     * Render widgets .
     *
     * @return string
     */
    public function render() {
        $widgets = $this->getWidgets();

        $html = '';
        array_walk($widgets, function($widget) use(& $html) {
            if( $widget instanceof Widget ) {
                if( $widget->isAllowed() )
                    $html .= $widget->render();
            } else {
                $html .= $widget->render();
            }
        });

        return $html;
    }

}
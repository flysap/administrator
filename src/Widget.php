<?php

namespace Flysap\Application;

use Flysap\Support\Traits\ElementAttributes;
use Flysap\Support\Traits\ElementPermissions;

abstract class Widget {

    use ElementPermissions, ElementAttributes;

    /**
     * @var
     */
    protected $before;

    /**
     * @var
     */
    protected $after;

    /**
     * @var
     */
    protected $label;

    public function __construct(array $attributes = array()) {
        $this->setAttributes($attributes);
    }

    /**
     * Render element .
     *
     * @return string
     */
    public function render() {
        $result = $this->before;

        if( $this->hasLabel() )
            $result .= '<label>' . $this->getLabel();

        $result .= $this->getAttribute('value');

        if( $this->hasLabel() )
            $result .= '</label>';

        $result .= $this->after;

        return $result;
    }

    /**
     * Render widget .
     *
     * @return mixed
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Set label .
     *
     * @param $name
     * @return $this
     */
    public function label($name) {
        $this->label = $name;

        return $this;
    }

    /**
     * Check if has label .
     *
     * @return bool
     */
    public function hasLabel() {
        return !empty($this->label);
    }

    /**
     * Get label .
     *
     * @return mixed
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Adding before html
     *
     * @param $value
     * @return $this
     */
    public function before($value) {
        $this->before = $value;

        return $this;
    }

    /**
     * Adding after html .
     *
     * @param $value
     * @return $this
     */
    public function after($value) {
        $this->after = $value;

        return $this;
    }
}
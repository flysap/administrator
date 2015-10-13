<?php

namespace Flysap\Application\Widgets;

use Flysap\Application\Widget;
use Flysap\Application\WidgetAble;

class UsersWidget extends Widget implements WidgetAble {

    /**
     * @var string
     */
    protected $template = 'themes::widgets.users';

    public function value() {
        return ;
    }

}

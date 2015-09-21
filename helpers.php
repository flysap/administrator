<?php

namespace Flysap\Application;


/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Menu helpers                                                       *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

/**
 * Get menu sections .
 *
 * @return mixed
 */
function get_menu_sections() {
    $manager = app('menu-manager');

    return $manager
        ->buildMenu()
        ->getGroups();
}

/**
 * Render section .
 *
 * @param $section
 * @param array $attributes
 * @return
 */
function render_menu_section($section, $attributes = array()) {
    $manager = app('menu-manager');

    return $manager->render($section, $attributes);
}

/**
 * Render all the menu .
 *
 * @param $manager
 * @return mixed
 */
function render_menu($manager) {
    return $manager->render();
}


/**
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * User helpers                                                       *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

/**
 * Get current user .
 *
 * @return mixed
 */
function current_user() {
    return \Auth::user();
}

/**
 * Get current Username .
 *
 */
function current_username() {
    $user = current_user();

    if( isset($user->id) )
        return $user->name;
}



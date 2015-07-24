<?php

namespace Flysal\Administrator;

/**
 * Get menu sections .
 *
 * @return mixed
 */
function get_menu_sections() {
    $manager = app('menu-manager');

    return $manager->getGroups();
}

/**
 * Render section .
 *
 * @param $section
 * @param $manager
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
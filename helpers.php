<?php

namespace Flysal\Administrator;

/**
 * Render section .
 *
 * @param $section
 * @param $manager
 */
function render_section($section, $manager) {
    return $manager->render($section);
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
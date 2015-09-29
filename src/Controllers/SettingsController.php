<?php

namespace Flysap\Application\Controllers;

use App\Http\Controllers\Controller;
use Flysap\TableManager;

class SettingsController extends Controller {

    /**
     * @var
     */
    protected $settingsRepository;

    public function __construct() {
        $this->setRepository(
            app('settings-manager')
        );
    }

    /**
     * Set repository .
     *
     * @param $repository
     */
    protected function setRepository($repository) {
        $this->settingsRepository = $repository;
    }

    /**
     * Get repository .
     *
     * @return mixed
     */
    protected function getRepository() {
        return $this->settingsRepository;
    }

    /**
     * Lists all settings .
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $settings = $this->getRepository()->all();

        $array = [];
        $count = 0;

        array_walk($settings, function($value, $key) use(& $array, & $count) {
            $array[$count]['section'] = is_array($value) ? $key : null;
            $array[$count]['value']   = is_array($value) ? json_encode($value) : $value;
            $array[$count]['key']   = $key;

            $count++;
        });

        $table = TableManager\table([
            'columns' => ['key', 'value', 'section'],
            'rows' => $array
        ], 'collection', ['class' => 'table table-hover']);


        $table->addColumn(['closure' => function($value, $attributes) {
            $elements = $attributes['elements'];
            $section  = isset($elements['section']) && !is_null($elements['section']) ? $elements['section'] : $elements['key'];

            $edit_route = route('edit_setting', ['section' => $section]);
            $delete_route = route('delete_setting', ['section' => $section]);

            return <<<DOC
<a href="$edit_route">Edit</a><br />
<a href="$delete_route">Delete</a><br />
DOC;
            ;
        }], 'action');

        return view('themes::pages.table', [
            'title' => trans('Settings'),
            'table' => $table
        ]);
    }


    public function edit($section) {

    }

    public function update($section) {

    }

    public function delete($section) {

    }
}
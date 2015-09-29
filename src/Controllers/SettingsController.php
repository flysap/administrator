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
        array_walk($settings, function($value, $key) use(& $array) {
            $section = is_array($value) ? $key : 'default';

            if( isset($array[$section])) {
                $array[$section]['value'] = json_encode(array_merge([$key => $value], json_decode($array[$section]['value'], true)));
            } else {
                $attributes = is_array($value) ? $value : [$key => $value];
                $array[$section] = [
                    'section'   => $section,
                    'value' => json_encode($attributes),
                ];
            }

        });

        $table = TableManager\table([
            'columns' => ['section', 'value'],
            'rows' => $array
        ], 'collection', ['class' => 'table table-hover']);


        $table->addColumn(['closure' => function($value, $attributes) {
            $elements = $attributes['elements'];
            $section  = $elements['section'];

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
<?php

namespace Flysap\Application\Controllers;

use App\Http\Controllers\Controller;
use Flysap\TableManager;
use Flysap\FormBuilder;

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
                $array[$section]['values'] = json_encode(array_merge([$key => $value], json_decode($array[$section]['values'], true)));
            } else {
                $attributes = is_array($value) ? $value : [$key => $value];
                $array[$section] = [
                    'section'   => $section,
                    'values' => json_encode($attributes),
                ];
            }
        });

        $table = TableManager\table([
            'columns' => ['section', 'values'],
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

    /**
     * @param $section
     * @return \Illuminate\View\View
     */
    public function edit($section) {
        $settings = $this->getRepository()->all($section != 'default' ? $section : null);

        $form = FormBuilder\create_form([
            'action' => route('update_setting', ['section' => $section]),
            'method' => FormBuilder\Form::METHOD_POST
        ]);

        array_walk($settings, function($value, $key) use(& $form) {
            if(is_array($value))
                return false;

            $form->addElements([
                $key => FormBuilder\element_text($key, ['value' => $value, 'name' => $key])
            ]);
        });

        return view('scaffold::scaffold.edit', compact('form'));
    }

    /**
     * Update settings .
     *
     * @param $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($section) {
        foreach(request()->all() as $key => $value) {
            $this->getRepository()->update(
                $key, $value, $section != 'default' ? $section : null
            );
        }

        return back();
    }

    /**
     * Delete all sections settings .
     *
     * @param $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($section) {
        $settings = $this->getRepository()->all($section != 'default' ? $section : null);

        foreach ($settings as $key => $value)
            $this->getRepository()->delete($key, $section != 'default' ? $section : null);

        return back();
    }
}
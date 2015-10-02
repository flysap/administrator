<?php

namespace Flysap\Application\Controllers;

use App\Http\Controllers\Controller;
use Flysap\Support\Traits\ElementPermissions;
use Flysap\TableManager;
use Flysap\FormBuilder;

class SettingsController extends Controller {

    use ElementPermissions;

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
        $default  = config('administrator-settings.default');

        $array = [];
        array_walk($default, function($value, $section) use(& $array) {

            if( ! $this->isAllowed(
                isset($value['roles']) ? $value['roles'] : [],
                isset($value['permissions']) ? $value['permissions'] : []
            ) )
                return false;

            $json = [];
            foreach ($value['items'] as $k => $v)
                $json[$v['key']] = isset($v['value']) ? $v['value'] : '';

            $settings = $this->getRepository()->all($section);
            $values   = ($section == 'general') ? (isset($settings['general']) ? $settings['general'] : []) : $settings;

            $array[] = [
                'section' => $section,
                'values'  => json_encode(
                    array_merge($json, $values)
                )
            ];
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
<a href="$delete_route">Reset</a><br />
DOC;
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
        $default = config('administrator-settings.default')[$section];

        $form = FormBuilder\create_form([
            'action' => route('update_setting', ['section' => $section]),
            'method' => FormBuilder\Form::METHOD_POST
        ]);

        $settings = $this->getRepository()->all($section);
        $settings = ($section == 'general') ? (isset($settings['general']) ? $settings['general'] : []) : $settings;

        array_walk($default['items'], function($value, $key) use(& $form, $settings) {

            if( ! $this->isAllowed(
                isset($value['roles']) ? $value['roles'] : [],
                isset($value['permissions']) ? $value['permissions'] : []
            ) )
                return false;

            $attributes = [
                'name' => $value['key'],
                'value' => isset($settings[$value['key']]) ? $settings[$value['key']] : (isset($value['value']) ? $value['value'] : '')
            ];

            $type = isset($value['type']) ? $value['type'] : 'text';

            if( $type == 'checkbox' )
                $form->addElement($key.'1', FormBuilder\element_hidden('', ['value' => 0, 'group' => $value['group']] + $attributes), true);

            $form->addElement($key, FormBuilder\get_element($type, $attributes + $value), true);
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
        $default = config('administrator-settings.default')[$section];

        if( ! $this->isAllowed(
            isset($default['roles']) ? $default['roles'] : [],
            isset($default['permissions']) ? $default['permissions'] : []
        ) )
            return back();

        foreach(request()->all() as $key => $value) {
            $this->getRepository()
                ->update(
                $key, $value, $section
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
        $default = config('administrator-settings.default')[$section];

        if( ! $this->isAllowed(
            isset($default['roles']) ? $default['roles'] : [],
            isset($default['permissions']) ? $default['permissions'] : []
        ) )
            return back();

      $this->getRepository()
          ->clear($section);

        return back();
    }
}
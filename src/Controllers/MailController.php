<?php

namespace Flysap\Application\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Eloquent\Translatable\Translatable;
use Flysap\Application\MailAssets\MailTemplate;
use Parfumix\TableManager;
use Parfumix\FormBuilder;
use Localization as Locale;

class MailController extends Controller {

    protected $repository;

    public function __construct() {
        $this->repository = (new MailTemplate);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $table = TableManager\table($this->repository, 'eloquent', ['class' => 'table table-hover']);

        $table->addColumn(['closure' => function() {
            $edit_route = route('admin.mail.edit', ['mail' => 1]);
            $delete_route = route('admin.mail.delete', ['mail' => 1]);

            return <<<DOC
<a href="$edit_route">Edit</a><br />
<a href="$delete_route">Delete</a><br />
DOC;
        }], 'action');

        return view('themes::pages.table', [
            'title' => trans('Mail Templates'),
            'table' => $table
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $mailRow = $this->repository
            ->find($id);

        if(! $_POST) {
            $form = FormBuilder\create_form([
                'action' => route('admin.mail.edit', ['id' => $id]),
                'method' => FormBuilder\Form::METHOD_POST
            ]);

            $elements[] = FormBuilder\element_text('slug', [
                'name'  => 'slug',
                'group' => 'default',
                'value' => $mailRow->slug
            ]);

            if( $mailRow instanceof Translatable ) {
                $locales = Locale\get_locales();

                foreach($locales as $locale => $attributes) {
                    $translation = $mailRow->translate($locale);

                    foreach($mailRow->translatedAttributes() as $attribute => $type) {
                        $elements[]  = FormBuilder\get_element($type, [
                            'group' => 'translations',
                            'label' => ucfirst($attribute) . ' ' . $locale,
                            'value' => $translation[$attribute],
                            'name'  => $locale . '['.$attribute.']',
                        ]);
                    }
                }
            }

            $form->addElements($elements, true);

            return view('scaffold::scaffold.edit', compact('form'));
        }

        $mailRow->fill($_POST)
            ->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id) {
        $mailRow = $this->repository
            ->find($id);

        if( $mailRow )
            $mailRow->delete();

        return back();
    }
}

<?php

namespace Flysap\Application\MailAssets;

use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Eloquent\Translatable\Translatable;
use Eloquent\Translatable\TranslatableTrait;
use Flysap\Scaffold\ScaffoldAble;
use Flysap\Scaffold\Traits\ScaffoldTrait;
use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model implements Translatable, ScaffoldAble, SluggableInterface {

    use TranslatableTrait;

    use ScaffoldTrait;

    use SluggableTrait;

    protected $translationClass = MailTemplateTranslation::class;

    public $table = 'mail_templates';

    public $fillable = ['slug'];

    public $translatedAttributes = [
        'title' => 'text',
        'description' => 'wysiwyg',
    ];

    protected $sluggable = [
        'unique' => true,
        'separator' => '-',
        'on_update' => true,
        'build_from' => 'slug',
        'save_to'    => 'slug',
    ];

    public $timestamps = false;

}
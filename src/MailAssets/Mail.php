<?php

namespace Flysap\Application\MailAssets;

use Eloquent\Translatable\Translatable;
use Eloquent\Translatable\TranslatableTrait;
use Illuminate\Database\Eloquent\Model;

class Mail extends Model implements Translatable {

    use TranslatableTrait;

    protected $translationClass = MailTranslations::class;

    public $table = 'mail_templates';

    public $timestamps = false;
}
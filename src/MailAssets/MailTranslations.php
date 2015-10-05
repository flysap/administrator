<?php

namespace Flysap\Application\MailAssets;

use Illuminate\Database\Eloquent\Model;

class MailTranslations extends Model  {

    public $table = 'mail_template_translations';

    public $timestamps = false;

    protected $fillable = ['mail_id', 'language_id', 'title', 'description'];
}
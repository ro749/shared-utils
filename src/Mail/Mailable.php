<?php

namespace Ro749\SharedUtils\Mail;

use Illuminate\Mail\Mailable as MailableBase;
class Mailable extends MailableBase
{
    public static function instance(): Mailable
    {
        $basename = class_basename(static::class);
        return new (config('contact_mail.'.$basename));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $table = 'notification_template';

    protected $fillable = ['subject','title','notification_content','spanish_notification_content','spanish_mail_content','mail_content'];
}

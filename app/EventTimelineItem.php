<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DateTime;

class EventTimelineItem extends Model
{
    //
    public function event(){
        $this->belongsTo("App\Evento","event_id","id");
    }

    public function getDateTime()
    {
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $this->datetime);
        if ($datetime) {
            return $datetime->format("d/m/Y H:i");
        } else {
            return false;
        }

    }
}

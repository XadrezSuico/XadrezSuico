<?php

namespace App\Classification;

use Illuminate\Database\Eloquent\Model;

class EventClassificate extends Model
{
    public function event()
    {
        return $this->belongsTo("App\Evento", "event_id", "id");
    }

    public function event_classificator()
    {
        return $this->belongsTo("App\Evento", "event_classificator_id", "id");
    }

    public function categories()
    {
        return $this->belongsTo("App\Classification\EventClassificateCategory", "event_classificates_id", "id");
    }

    public function rules()
    {
        return $this->belongsTo("App\Classification\EventClassificateRule", "event_classificates_id", "id");
    }
}

<?php

namespace App\Classification;

use Illuminate\Database\Eloquent\Model;

class EventClassificateCategory extends Model
{
    public function event_classificate()
    {
        return $this->belongsTo("App\Classification\EventClassificate", "event_classificates_id", "id");
    }
    public function category()
    {
        return $this->belongsTo("App\Categoria", "category_id", "id");
    }
    public function category_classificator()
    {
        return $this->belongsTo("App\Categoria", "category_classificator_id", "id");
    }
}

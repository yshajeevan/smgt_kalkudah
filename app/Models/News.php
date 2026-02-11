<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NewsCategory;
use App\Models\NewsPhoto;


class News extends Model
{
    use HasFactory;
    
    protected $table = 'news';  

    protected $primaryKey='id';  

    protected $fillable = ['id','name','content','cover_photo','summary'];

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id', 'id');
    }
    public function photo()
    {
        return $this->hasMany(NewsPhoto::class,'news_id');
    }
    protected static function booted () {
        static::deleting(function(News $news) { // before delete() method call this
             $news->photo()->delete();
             // do the rest of the cleanup...
        });
    }
}
<?php
  
namespace App\Models;
  
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
  
class Product extends Model
{
    use HasFactory;
  
    /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'name', 'detail', 'price', 'picture'
    ];

    public function picture()
    {
        if ($this->picture) {
            return asset('storage/products/' . $this->picture);
        } else {
            return asset('storage/default/product.png');
        }
    }
}

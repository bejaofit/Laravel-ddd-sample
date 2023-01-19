<?php

namespace Bejao\Core\Table\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\Model;

final class TableAR extends Model
{
    protected $table = 'tables';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'guests',
        'bookId',
        'status',
        'created_at',
        'updated_at',
    ];

}

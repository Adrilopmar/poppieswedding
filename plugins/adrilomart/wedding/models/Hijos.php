<?php namespace Adrilomart\Wedding\Models;

use Model;

/**
 * Model
 */
class Hijos extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    /**
     * @var array dates to cast from the database.
     */
    protected $dates = ['deleted_at'];

    /**
     * @var string table in the database used by the model.
     */
    public $table = 'adrilomart_wedding_hijos';

    /**
     * @var array rules for validation.
     */
    public $rules = [
    ];

    public $belongsTo = [
        'Invitado' => [
            \Adrilomart\Wedding\Models\Invitado::class,
            'delete' => true
        ]
    ];
}

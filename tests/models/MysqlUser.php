<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Eloquent\HybridRelations;

class MysqlUser extends Eloquent
{
    use HybridRelations;

    protected $connection = 'mysql';
    protected $table = 'users';
    protected static $unguarded = true;

    public function books(): HasMany
    {
        return $this->hasMany('Book', 'author_id');
    }

    public function role(): HasOne
    {
        return $this->hasOne('Role');
    }

    public function mysqlBooks(): HasMany
    {
        return $this->hasMany(MysqlBook::class);
    }

    public function clients()
    {
        return $this->belongsToMany('Client', null, 'mysql_users_id', 'clients');
    }

    /**
     * Check if we need to run the schema.
     */
    public static function executeSchema(): void
    {
        /** @var \Illuminate\Database\Schema\MySqlBuilder $schema */
        $schema = Schema::connection('mysql');

        if (! $schema->hasTable('users')) {
            Schema::connection('mysql')->create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }
        if (! $schema->hasTable('client_mysql_user')) {
            Schema::connection('mysql')->create('client_mysql_user', function (Blueprint $table) {
                $table->integer('mysql_user_id')->unsigned();
                $table->string('client_id');
                $table->primary(['mysql_user_id', 'client_id']);
            });
        }
    }
}

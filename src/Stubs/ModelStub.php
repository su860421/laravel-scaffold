<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class ModelStub
{
    public static function generate(string $name, string $model): string
    {
        $tableName = \Illuminate\Support\Str::plural(\Illuminate\Support\Str::snake($name));

        return "<?php

declare(strict_types=1);

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

class {$model} extends Model
{
    use HasFactory;

    protected \$fillable = [
        // Add fillable fields here
        // Examples:
        // 'name',
        // 'email',
        // 'status',
    ];

    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Add other type casts here
        // Examples:
        // 'is_active' => 'boolean',
        // 'settings' => 'array',
    ];

    // Define relationship methods here
    // Examples:
    // public function posts(): HasMany
    // {
    //     return \$this->hasMany(Post::class);
    // }
}
";
    }
}

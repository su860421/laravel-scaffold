<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class RequestStub
{
    public static function generate(string $name, string $type): string
    {
        $requestName = "{$type}{$name}Request";
        $rules = self::getRulesByType($type);

        return "<?php

declare(strict_types=1);

namespace App\\Http\\Requests;

use Illuminate\\Foundation\\Http\\FormRequest;

class {$requestName} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
{$rules}
    }
}
";
    }

    private static function getRulesByType(string $type): string
    {
        return match ($type) {
            'Index' => "        return [
            'per_page' => ['integer', 'min:1', 'max:100'],
            'order_by' => ['string'],
            'order_direction' => ['in:asc,desc'],
            'with' => ['array'],
            'columns' => ['array'],
            'filters' => ['array'],
        ];",
            'Show' => "        return [
            'columns' => ['array'],
            'with' => ['array'],
        ];",
            default => "        return [];",
        };
    }
}

<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class RequestStub
{
    public static function generate(string $name, string $type): string
    {
        $requestName = "{$type}{$name}Request";
        $rulesExample = self::getRulesExampleByType($type);

        return "<?php

declare(strict_types=1);

namespace App\\Http\\Requests\\{$name};

use JoeSu\\LaravelScaffold\\BaseRequest;
use App\\Models\\{$name};

class {$requestName} extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
{$rulesExample}
    }

    /**
     * Get the error messages for the validation rules.
     */
    public function messages(): array
    {
        return \$this->getAllMessages();
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return \$this->getAllAttributes();
    }

    /**
     * Get available fields for validation.
     */
    protected function getAvailableFields(): array
    {
        return \$this->getModelFields({$name}::class);
    }

    /**
     * Get the model instance.
     */
    protected function model(): {$name}
    {
        return new {$name}();
    }
}
";
    }

    private static function getRulesExampleByType(string $type): string
    {
        return match ($type) {
            'Index' => "        // 使用預設的 index 規則（包含分頁、排序、篩選等）
        return \$this->getDefaultIndexRules();

        // 或者自訂要使用的規則：
        // return \$this->onlyRules(['per_page', 'order_by', 'order_direction', 'columns']);
        
        // 或者使用分組規則：
        // return array_merge(
        //     \$this->getPaginationRules(),
        //     \$this->getSortingRules(),
        //     \$this->getSelectionRules()
        // );
        
        // 或者排除某些規則：
        // return \$this->exceptRules(['filters', 'search']);",

            'Show' => "        // 使用預設的 show 規則（欄位選擇和關聯載入）
        return \$this->getDefaultShowRules();

        // 或者自訂要使用的規則：
        // return \$this->onlyRules(['columns', 'with']);
        
        // 或者只要欄位選擇：
        // return \$this->getSelectionRules();",

            default => "        return [];",
        };
    }
}

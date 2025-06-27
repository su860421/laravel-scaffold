<?php

return [
    // Record not found
    'record_not_found' => '找不到 ID 為 :id 的記錄',

    // Find operations
    'find_failed' => '查找記錄失敗: :error',

    // Filter operations
    'invalid_filter_format' => '過濾格式無效',
    'filter_must_be_array' => '過濾條件必須是陣列格式',
    'invalid_relation_field_format' => '關聯欄位格式無效。請使用: relation.column',

    // Sorting operations
    'invalid_order_direction' => '排序方向必須是 asc 或 desc',
    'invalid_sort_column' => '無效的排序欄位: :column',

    // CRUD operations
    'create_failed' => '建立模型失敗: :error',
    'update_failed' => '更新模型失敗: :error',
    'delete_failed' => '刪除模型失敗: :error',

    // Batch operations
    'batch_create_failed' => '批量建立記錄失敗: :error',
    'batch_update_failed' => '批量更新記錄失敗: :error',
    'batch_delete_failed' => '批量刪除記錄失敗: :error',

    // Soft delete operations
    'force_delete_failed' => '強制刪除模型失敗: :error',
    'restore_failed' => '恢復模型失敗: :error',

    // Utility operations
    'update_or_create_failed' => '更新或建立模型失敗: :error',
    'exists_check_failed' => '檢查存在性失敗: :error',
    'count_failed' => '計算記錄數量失敗: :error',
];

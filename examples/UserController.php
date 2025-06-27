<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * 顯示所有使用者（支援分頁、排序、關係載入、過濾）
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 0);
        $orderBy = $request->get('order_by');
        $orderDirection = $request->get('order_direction', 'asc');
        $relationships = $request->get('with', []);
        $columns = $request->get('columns', ['*']);
        $filters = $request->get('filters', []);

        $users = $this->userService->index(
            $perPage,
            $orderBy,
            $orderDirection,
            $relationships,
            $columns,
            $filters
        );

        return response()->json($users);
    }

    /**
     * 顯示特定使用者（支援關聯載入）
     */
    public function show($id, Request $request)
    {
        try {
            $columns = $request->get('columns', ['*']);
            $relationships = $request->get('with', []);

            $user = $this->userService->find($id, $columns, $relationships);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(["message" => "User not found"], 404);
        }
    }

    /**
     * 建立新使用者
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = $this->userService->create($validated);
        return response()->json($user, 201);
    }

    /**
     * 更新或建立使用者
     */
    public function updateOrCreate(Request $request)
    {
        $attributes = $request->validate([
            'email' => 'required|email',
        ]);

        $values = $request->validate([
            'name' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:8',
        ]);

        $user = $this->userService->updateOrCreate($attributes, $values);
        return response()->json($user);
    }

    /**
     * 更新使用者
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
        ]);

        $user = $this->userService->update($id, $validated);
        return response()->json($user);
    }

    /**
     * 刪除使用者
     */
    public function destroy($id)
    {
        $this->userService->delete($id);
        return response()->json(["message" => "User deleted successfully"]);
    }

    /**
     * 強制刪除使用者
     */
    public function forceDelete($id)
    {
        $this->userService->forceDelete($id);
        return response()->json(["message" => "User permanently deleted"]);
    }

    /**
     * 恢復已刪除的使用者
     */
    public function restore($id)
    {
        $user = $this->userService->restore($id);
        return response()->json($user);
    }

    /**
     * 批量建立使用者
     */
    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'users' => 'required|array|min:1',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|email|unique:users',
            'users.*.password' => 'required|string|min:8',
        ]);

        $result = $this->userService->batchCreate($validated['users']);
        return response()->json($result, 201);
    }

    /**
     * 批量更新使用者
     */
    public function batchUpdate(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
            'attributes' => 'required|array',
        ]);

        $result = $this->userService->batchUpdate($validated['ids'], $validated['attributes']);
        return response()->json($result);
    }

    /**
     * 批量刪除使用者
     */
    public function batchDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:users,id',
        ]);

        $result = $this->userService->batchDelete($validated['ids']);
        return response()->json($result);
    }

    /**
     * 檢查使用者是否存在
     */
    public function exists(Request $request)
    {
        $conditions = $request->get('conditions', []);

        if (empty($conditions)) {
            return response()->json(["message" => "Conditions are required"], 400);
        }

        $exists = $this->userService->exists($conditions);
        return response()->json(['exists' => $exists]);
    }

    /**
     * 計算使用者數量
     */
    public function count(Request $request)
    {
        $conditions = $request->get('conditions', []);
        $count = $this->userService->count($conditions);
        return response()->json(['count' => $count]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * @return list<string>
     */
    public static function adminUserFields(): array
    {
        return [
            'id',
            'username',
            'email',
            'name',
            'avatar',
            'is_admin',
            'is_blocked',
            'created_at',
        ];
    }

    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        $users = User::query()
            ->when($search !== '', function ($query) use ($search): void {
                $like = '%'.$search.'%';
                $query->where(function ($inner) use ($like): void {
                    $inner->where('username', 'ilike', $like)
                        ->orWhere('email', 'ilike', $like)
                        ->orWhere('name', 'ilike', $like);
                });
            })
            ->orderBy('username')
            ->paginate(20)
            ->withQueryString()
            ->through(function (User $user): array {
                return collect($user->only(self::adminUserFields()))
                    ->merge([
                        'created_at' => $user->created_at?->toIso8601String(),
                    ])
                    ->all();
            });

        return Inertia::render('admin/ui/UsersPage', [
            'users' => $users,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function toggleAdmin(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        if ($admin === null || $admin->id === $user->id) {
            abort(403);
        }

        $user->forceFill([
            'is_admin' => ! $user->is_admin,
        ])->save();

        return back()->with(
            'success',
            $user->is_admin ? 'Пользователь назначен администратором.' : 'Права администратора сняты.',
        );
    }

    public function toggleBlock(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();

        if ($admin === null || $admin->id === $user->id) {
            abort(403, 'Нельзя блокировать самого себя.');
        }

        if ($user->is_admin) {
            abort(403, 'Нельзя блокировать администратора.');
        }

        $user->forceFill([
            'is_blocked' => ! $user->is_blocked,
        ])->save();

        return back()->with(
            'success',
            $user->is_blocked ? 'Пользователь заблокирован.' : 'Пользователь разблокирован.',
        );
    }
}
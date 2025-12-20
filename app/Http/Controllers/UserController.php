<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserPreferencesService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:user.read', only: ['index', 'show']),
            new Middleware('can:user.create', only: ['create', 'store']),
            new Middleware('can:user.update', only: ['edit', 'update']),
            new Middleware('can:user.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserPreferencesService $userPreferencesService): Response
    {
        // Get user's page preferences
        $userPreferences = $userPreferencesService->getAndUpdatePagePreferences('users');

        // Join roles table for searching and sorting
        $query = User::query()
            ->leftJoin('model_has_roles', function ($join) {
                $join->on('users.id', '=', 'model_has_roles.model_id')
                    ->where('model_has_roles.model_type', '=', User::class);
            })
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->with(['roles'])
            ->select('users.*');

        // Apply sorting
        if ($userPreferences['sortColumn'] === 'role') {
            $query->orderBy('roles.name', $userPreferences['sortDirection']);
        } else {
            $query->orderBy($userPreferences['sortColumn'], $userPreferences['sortDirection']);
        }

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('roles.name', 'like', "%{$search}%");
            });
        }

        // Paginate and add query parameters
        $users = $query->paginate($userPreferences['perPage'])
            ->onEachSide(1)
            ->withQueryString();

        // Return view
        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => $request->only(['search']),
            'userPreferences' => $userPreferences,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('users/CreateEdit', [
            'roles' => Role::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        if ($request->user()->can('user.assign.role')) {
            $user->syncRoles([$validated['role'] ?? 'Medewerker']);
        }

        return redirect()->route('users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('users/CreateEdit', [
            'roles' => Role::all(['id', 'name']),
            'user' => $user->load('roles:id,name'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->user()->can('user.assign.role')) {
            $user->syncRoles([$validated['role']]);
        }

        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index');
    }
}

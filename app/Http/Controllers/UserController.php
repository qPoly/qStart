<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserPreferencesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:manage users'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserPreferencesService $userPreferencesService): Response
    {
        // Get user's page preferences
        $userPreferences = $userPreferencesService->getAndUpdatePagePreferences('users');

        // Get ordered users
        $query = User::orderBy($userPreferences['sortColumn'], $userPreferences['sortDirection']);

        // Apply organisation filter
        if ($request->user()->organisation_id) {
            $query->where('users.organisation_id', $request->user()->organisation_id);
        } else {
            $query->whereNull('users.organisation_id');
        }

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%");
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
    public function create(): Response
    {
        return Inertia::render('users/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        $authUser = $request->user();

        if ($authUser->organisation_id) {
            // Auth user is in a workspace of an organisation, so assign user role and set organisation
            $user->organisation_id = $authUser->organisation_id;
            $user->save();
            $user->assignRole('user');
        }

        if (!$authUser->organisation_id) {
            // Auth user is in admin workspace, so assign admin role
            $user->assignRole('admin');
        }

        return redirect()->route('users.index', $request->query());
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
    public function edit(User $user): Response
    {
        Gate::authorize('mutate', $user);

        return Inertia::render('users/CreateEdit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        Gate::authorize('mutate', $user);

        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index', $request->query());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Request $request): RedirectResponse
    {
        Gate::authorize('mutate', $user);

        $user->roles()->detach();
        $user->delete();

        return redirect()->route('users.index', $request->query());
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\ResizeImage;
use App\Http\Requests\OrganisationRequest;
use App\Models\Organisation;
use App\Services\UserPreferencesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class OrganisationController extends Controller
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('can:manage organisations'),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserPreferencesService $userPreferencesService): Response
    {
        // Get user's page preferences
        $userPreferences = $userPreferencesService->getAndUpdatePagePreferences('organisations');

        // Get ordered organisations
        $query = Organisation::orderBy($userPreferences['sortColumn'], $userPreferences['sortDirection']);

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Paginate and add query parameters
        $organisations = $query->paginate($userPreferences['perPage'])
            ->onEachSide(1)
            ->withQueryString();

        // Return view
        return Inertia::render('organisations/Index', [
            'organisations' => $organisations,
            'filters' => $request->only(['search']),
            'userPreferences' => $userPreferences,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('organisations/CreateEdit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrganisationRequest $request): RedirectResponse
    {
        $organisation = Organisation::create($request->validated());

        if ($request->hasFile('logo_path')) {
            $this->handleLogo($organisation, $request->file('logo_path'));
        }

        return redirect()->route('organisations.index', $request->query());
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation): Response
    {
        return Inertia::render('organisations/CreateEdit', [
            'organisation' => $organisation,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(OrganisationRequest $request, Organisation $organisation): RedirectResponse
    {
        $organisation->update($request->validated());

        if ($request->hasFile('logo_path')) {
            $this->handleLogo($organisation, $request->file('logo_path'));
        }

        return redirect()->route('organisations.index', $request->query());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, Request $request): RedirectResponse
    {
        $organisation->delete();

        return redirect()->route('organisations.index', $request->query());
    }

    /**
     * Switch to the workspace of given organisation.
     */
    public function switch(Request $request, $organisationId): RedirectResponse
    {
        $request->user()->organisation_id = $organisationId ?: null;
        $request->user()->save();

        return redirect()->back();
    }

    /**
     * Upload given logo and resize it.
     */
    private function handleLogo(Organisation $organisation, UploadedFile $file): void
    {
        $organisation->logo_path = $file->store('images/logos', 'public');
        $organisation->save();

        ResizeImage::scaleDown('public', $organisation->logo_path, 800);
    }
}

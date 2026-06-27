<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use App\Models\SavedListing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    // ── Dashboard ─────────────────────────────────────────────────────────────

    public function index()
    {
        $stats = [
            'users'      => User::count(),
            'orgs'       => Organization::count(),
            'listings'   => Listing::count(),
            'saves'      => SavedListing::count(),
            'pro_users'  => User::where('plan', 'pro')->count(),
        ];

        $recentUsers = User::latest()->limit(8)->get();
        $recentSaves = SavedListing::with('user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentSaves'));
    }

    // ── Organizations ─────────────────────────────────────────────────────────

    public function orgs()
    {
        $orgs = Organization::withCount('users')->latest()->get();
        return view('admin.organizations', compact('orgs'));
    }

    public function orgShow(Organization $org)
    {
        $org->load(['users.savedListings', 'users.organization']);
        $saves = SavedListing::with('user')
            ->where('organization_id', $org->id)
            ->latest()->get();

        return view('admin.organization-show', compact('org', 'saves'));
    }

    public function createOrg(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'user_limit' => 'required|integer|min:1|max:500',
            'logo'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('org-logos', 'public');
        }

        $org    = Organization::create($data);
        $invite = OrganizationInvite::create([
            'organization_id' => $org->id,
            'token'           => Str::random(48),
            'role'            => 'ceo',
            'expires_at'      => now()->addDays(7),
        ]);

        return redirect()->route('admin.orgs')
            ->with('ceo_link', route('invite.show', $invite->token));
    }

    public function updateOrg(Request $request, Organization $org)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'user_limit'       => 'required|integer|min:1|max:500',
            'show_team_saves'  => 'boolean',
            'show_team_prices' => 'boolean',
        ]);

        $org->update([
            'name'             => $data['name'],
            'user_limit'       => $data['user_limit'],
            'show_team_saves'  => $request->boolean('show_team_saves'),
            'show_team_prices' => $request->boolean('show_team_prices'),
        ]);

        return back()->with('success', 'Organization updated.');
    }

    public function deleteOrg(Organization $org)
    {
        // Remove org reference from users
        $org->users()->update(['organization_id' => null, 'role' => 'free']);
        $org->delete();

        return redirect()->route('admin.orgs')->with('success', 'Organization deleted.');
    }

    public function generateCeoInvite(Organization $org)
    {
        $invite = OrganizationInvite::create([
            'organization_id' => $org->id,
            'token'           => Str::random(48),
            'role'            => 'ceo',
            'expires_at'      => now()->addDays(7),
        ]);

        return back()->with('ceo_link', route('invite.show', $invite->token));
    }

    // ── Users ─────────────────────────────────────────────────────────────────

    public function users(Request $request)
    {
        $q = User::with('organization')->withCount('savedListings')->latest();

        if ($request->filled('role')) $q->where('role', $request->role);
        if ($request->filled('plan')) $q->where('plan', $request->plan);

        $users = $q->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function editUser(User $user)
    {
        $orgs = Organization::orderBy('name')->get();
        $user->load('savedListings');
        return view('admin.user-edit', compact('user', 'orgs'));
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'role'            => 'required|in:super_admin,ceo,employee,free',
            'plan'            => 'required|in:free,pro',
            'organization_id' => 'nullable|exists:organizations,id',
            'password'        => 'nullable|min:8',
        ]);

        $update = [
            'name'            => $data['name'],
            'email'           => $data['email'],
            'role'            => $data['role'],
            'plan'            => $data['plan'],
            'organization_id' => $data['organization_id'] ?: null,
        ];

        if (! empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        $user->update($update);

        return back()->with('success', 'User updated.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted.');
    }

    // ── Activity ──────────────────────────────────────────────────────────────

    public function activity()
    {
        $saves = SavedListing::with(['user', 'organization'])
            ->latest()
            ->paginate(30);

        return view('admin.activity', compact('saves'));
    }

    public function exportActivity()
    {
        $saves = SavedListing::with(['user', 'organization'])->latest()->get();

        return response()->stream(function () use ($saves) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Employee', 'Email', 'Organization', 'Saved At', 'ID', 'Title', 'Address', 'Owner', 'Phone', 'Original Price', 'My Price', 'URL']);

            foreach ($saves as $s) {
                $l = $s->listing_snapshot;
                fputcsv($out, [
                    $s->user->name,
                    $s->user->email,
                    $s->organization?->name ?? '',
                    $s->created_at->format('Y-m-d H:i'),
                    $s->listing_id,
                    $l['title'] ?? '',
                    $l['address'] ?? '',
                    $l['owner_name'] ?? '',
                    $l['phone'] ?? '',
                    $l['price'] ?? '',
                    $s->my_price ?? '',
                    $l['url'] ?? '',
                ]);
            }

            fclose($out);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="activity-' . now()->format('Y-m-d') . '.csv"',
            'Cache-Control'       => 'no-store',
        ]);
    }
}

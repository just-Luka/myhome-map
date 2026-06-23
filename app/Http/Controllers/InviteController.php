<?php

namespace App\Http\Controllers;

use App\Models\OrganizationInvite;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InviteController extends Controller
{
    public function show(string $token)
    {
        $invite = OrganizationInvite::where('token', $token)->firstOrFail();

        if (! $invite->isValid()) {
            abort(410, 'This invite link has expired or already been used.');
        }

        return view('invite.show', compact('invite'));
    }

    public function accept(Request $request, string $token)
    {
        $invite = OrganizationInvite::where('token', $token)->firstOrFail();

        if (! $invite->isValid()) {
            abort(410, 'This invite link has expired or already been used.');
        }

        $org = $invite->organization;

        if ($invite->role === 'employee' && ! $org->canAddMember()) {
            abort(422, "This organization has reached its member limit.");
        }

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'password'        => $data['password'],
            'plan'            => 'pro',
            'organization_id' => $org->id,
            'role'            => $invite->role,
        ]);

        $invite->update(['used_at' => now()]);

        Auth::login($user);
        $request->session()->regenerate();

        if ($invite->role === 'employee') {
            $lang = in_array($request->input('lang'), ['en', 'ka']) ? $request->input('lang') : 'en';
            $request->session()->flash('welcome_splash', [
                'name' => $user->name,
                'org'  => $org->name,
                'logo' => $org->logo,
                'lang' => $lang,
            ]);
        }

        return redirect($invite->role === 'ceo' ? '/dashboard' : '/');
    }
}

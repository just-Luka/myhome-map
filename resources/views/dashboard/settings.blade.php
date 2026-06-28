@extends('dashboard.layout')

@section('title', 'Settings')

@section('styles')
.settings-card { max-width: 560px; }
.field-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid var(--toggle-border); }
.field-row:last-child { border-bottom: none; }
.field-row .label span { font-size: 13px; display: block; }
.field-row .label small { font-size: 11px; color: var(--toggle-small); display: block; }
.field-row input[type=number] {
    width: 80px; padding: 7px 10px; border-radius: 8px; text-align: center;
    border: 1px solid var(--toggle-border); background: var(--body-bg);
    color: var(--body-text); font-size: 14px; outline: none; transition: border-color 0.2s;
}
.field-row input[type=number]:focus { border-color: #4f6ef7; }
.btn-save {
    display: block; width: 100%; margin-top: 20px; padding: 11px;
    background: #4f6ef7; color: #fff; border: none; border-radius: 10px;
    font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.15s;
}
.btn-save:hover { background: #3b5be0; }
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card settings-card">
    <div class="card-title" style="margin-bottom:18px" data-i18n="settings_team">Team</div>
    <form method="POST" action="{{ route('owner.settings.update') }}">
        @csrf @method('PATCH')

        <div class="toggle-row">
            <div>
                <span data-i18n="show_team_saves">Show team saves</span>
                <small data-i18n="show_team_saves_hint">Employees see teammates' pins on the map</small>
            </div>
            <input type="checkbox" name="show_team_saves" value="1"
                {{ $org->show_team_saves ? 'checked' : '' }}>
        </div>

        <div class="toggle-row">
            <div>
                <span data-i18n="show_team_prices">Show teammate prices on hover</span>
                <small data-i18n="show_team_prices_hint">Price each teammate saved is visible on avatar hover</small>
            </div>
            <input type="checkbox" name="show_team_prices" value="1"
                {{ $org->show_team_prices ? 'checked' : '' }}>
        </div>

        <div class="field-row">
            <div class="label">
                <span data-i18n="save_limit_label">Daily save limit per agent</span>
                <small data-i18n="save_limit_hint">Max listings an agent can save per day</small>
            </div>
            <input type="number" name="save_limit" value="{{ $org->save_limit ?? 20 }}" min="1" max="200">
        </div>

        <button class="btn-save" type="submit" data-i18n="save_changes">Save changes</button>
    </form>
</div>

@endsection

@section('scripts')
<script>
Object.assign(translations.en, {
    settings_team:       'Team',
    show_team_saves:     'Show team saves',
    show_team_saves_hint:'Employees see teammates\' pins on the map',
    show_team_prices:    'Show teammate prices on hover',
    show_team_prices_hint:'Price each teammate saved is visible on avatar hover',
    save_limit_label:    'Daily save limit per agent',
    save_limit_hint:     'Max listings an agent can save per day',
    save_changes:        'Save changes',
});
Object.assign(translations.ka, {
    settings_team:       'გუნდი',
    show_team_saves:     'გუნდის შენახვების ჩვენება',
    show_team_saves_hint:'თანამშრომლები ხედავენ გუნდის ნიშნებს რუკაზე',
    show_team_prices:    'თანამშრომლების ფასების ჩვენება',
    show_team_prices_hint:'შენახული ფასი ჩანს პროფილის სურათზე გადაჭერისას',
    save_limit_label:    'დღიური ლიმიტი აგენტზე',
    save_limit_hint:     'მაქსიმუმ განცხადებები, რომლის შენახვაც შეუძლია აგენტს დღეში',
    save_changes:        'ცვლილებების შენახვა',
});
</script>
@endsection

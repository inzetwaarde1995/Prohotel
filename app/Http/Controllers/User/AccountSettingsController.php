<?php

namespace App\Http\Controllers\User;

use App\Actions\User\RemoveAllFriends;
use App\Actions\User\UpdateAccountSettings;
use App\Actions\User\UpdatePlayerPreferences;
use App\Emulator\Contracts\PlayerSettingsRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\AccountSettingsFormRequest;
use App\Http\Requests\PlayerPreferencesFormRequest;
use App\Services\User\SessionService;
use App\Support\AuthenticatedUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountSettingsController extends Controller
{
    public function edit(PlayerSettingsRepository $settings): View
    {
        $user = AuthenticatedUser::current();

        return view('user.settings.account', [
            'user' => $user,
            'canChangeName' => $settings->canChangeName($user),
            'preferences' => $settings->preferences($user),
        ]);
    }

    public function sessionLogs(Request $request, SessionService $sessionService): View
    {
        $sessions = $sessionService->fetchSessionLogs($request);

        return view('user.settings.session-logs', [
            'logs' => $sessions,
        ]);
    }

    public function update(AccountSettingsFormRequest $request, UpdateAccountSettings $updateAccountSettings): RedirectResponse
    {
        $updateAccountSettings->execute(AuthenticatedUser::from($request), $request->validated());

        return redirect()->route('settings.account.show')->with('success', __('Your account settings has been updated'));
    }

    public function updatePreferences(PlayerPreferencesFormRequest $request, UpdatePlayerPreferences $updatePreferences): RedirectResponse
    {
        $updatePreferences->execute(AuthenticatedUser::from($request), $request->preferences());

        return redirect()->route('settings.account.show')->with('success', __('Your preferences have been saved'));
    }

    public function removeFriends(Request $request, RemoveAllFriends $removeAllFriends): RedirectResponse
    {
        $removed = $removeAllFriends->execute(AuthenticatedUser::from($request));

        return redirect()->route('settings.account.show')->with('success', trans_choice('{0} You had no friends to remove|{1} :count friend has been removed|[2,*] :count friends have been removed', $removed, ['count' => $removed]));
    }
}

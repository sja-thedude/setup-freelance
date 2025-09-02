<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;

class Domain
{
    private $except = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();
        $primaryDomain = parse_url(config('app.url'), PHP_URL_HOST);
        $workspaceSlug = str_replace($primaryDomain, '', $host);

        if(empty($workspaceSlug)) {
            $request->request->add(['main_system' => true]);
            return $next($request);
        }

        $workspaceSlug = substr($workspaceSlug, 0, -1);
        $isHook = !empty($request->get('hook'));

        if(!empty(session('workspace_'.$workspaceSlug))) {
            $workspaceSession = $isHook ? session('workspace_' . $workspaceSlug)
                : session('workspace_' . $workspaceSlug)->refresh();

            if($workspaceSession->slug == $workspaceSlug) {
                $request->request->add(['workspaceId' => $workspaceSession->id]);

                //Redirect when offline
                if (!$workspaceSession->active && (!isset(request()->segments()[1]) || request()->segments()[1] != 'error')) {
                    return redirect(route('web.error'));
                }

                $checkLang = $this->checkLanguage($request, $workspaceSession, session('workspace_locale_'.$workspaceSession->slug));

                if(!empty($checkLang['flag'])) {
                    session(['workspace_locale_'.$workspaceSession->slug => true]);
                    return new RedirectResponse($checkLang['redirection'], 302, ['Vary' => 'Accept-Language']);
                }

                return $next($request);
            }

            session(['workspace_'.$workspaceSlug => null]);
        }

        $workspaceWith = $isHook ? [] : [
            'workspaceExtras',
            'settingGeneral',
            'settingOpenHours',
            'settingOpenHours.openTimeSlots',
            'settingOpenHours.openTimeSlotsOrderStartTime',
            'settingPreference',
            'settingDeliveryConditions',
            'settingExceptHours'
        ];

        $workspace = Workspace::where('slug', $workspaceSlug)
            ->with($workspaceWith)
            ->first();

        if(empty($workspace)) {
            return abort(403, json_encode([
                'link' => '#',
                'user' => null
            ]));
        }

        if(empty(session('workspace_'.$workspaceSlug))) {
            session(['workspace_'.$workspaceSlug => $workspace]);
        }

        $request->request->add(['workspaceId' => $workspace->id]);

        //Redirect when offline
        if (!$workspace->active && (!isset(request()->segments()[1]) || request()->segments()[1] != 'error')) {
            return redirect(route('web.error'));
        }

        $checkLang = $this->checkLanguage($request, $workspace, session('workspace_locale_'.$workspace->slug));

        if(!empty($checkLang['flag'])) {
            session(['workspace_locale_'.$workspace->slug => true]);
            return new RedirectResponse($checkLang['redirection'], 302, ['Vary' => 'Accept-Language']);
        }

        return $next($request);
    }

    public function checkLanguage($request, $workspace, $sessionWorkspace) {
        $flag = false;
        $redirection = null;

        if(empty($sessionWorkspace)) {
            $locale = $workspace->language;
            $referer = $request->fullUrl();
            $path = trim(str_replace(url('/'), '', $referer), '/');
            $params = explode('/', $path);

            if (count($params) > 0 && app('laravellocalization')->checkLocaleInSupportedLocales($params[0])) {
                if ($locale && app('laravellocalization')->checkLocaleInSupportedLocales($locale)
                    && !(app('laravellocalization')->getDefaultLocale() === $locale && app('laravellocalization')->hideDefaultLocaleInURL())) {
                    $redirection = app('laravellocalization')->getLocalizedURL($locale, $referer);
                    $flag = true;
                }
            }
        }

        return compact('flag', 'redirection');
    }
}

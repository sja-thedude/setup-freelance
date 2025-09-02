<?php

namespace App\Modules\ContentManager\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\Theme;
use App\Modules\ContentManager\Models\Themes;
use App\Modules\ContentManager\Models\ThemeMeta;
use App\Facades\Admin;

class ThemeController extends Controller
{
    public function index()
    {
        $model = Themes::orderBy('status', 'desc')->get();
        return view("ContentManager::theme.index", ['models' => $model]);
    }

    public function view($id)
    {
        $model = Themes::find($id);
        return view("ContentManager::theme.view", ['model' => $model]);
    }

    public function update(Request $request)
    {
        $reqMeta = $request->meta;
        $id = $request->idtheme;
        $beforeMeta = ThemeMeta::where('theme_id', $id)->where('meta_group', 'options')->get();
        foreach ($beforeMeta as $value) {
            $meta = unserialize($value->meta_value);
            $newMeta = [];
            foreach ($meta as $val) {
                $val['value'] = $reqMeta[$value->meta_key][$val['name']]['value'];
                $newMeta[] = $val;
            }
            ThemeMeta::where('theme_id', $id)->where('meta_key', $value->meta_key)->update(['meta_value' => serialize($newMeta)]);
        }
        return redirect(Admin::StrURL('contentManager/theme/' . $id))->with('success', 'Theme Option update success');;
    }

    public function active($id, Request $request)
    {
        Themes::where('status', 1)->update(['status' => 0]);
        $activeTheme = Themes::find($id);
        $activeTheme->status = 1;
        $activeTheme->save();
        Theme::setActive($activeTheme);
        $request->session()->flash('response', [
            'success' => true,
            'message' => array("Theme {$activeTheme->name} has been active.")
        ]);

        return redirect(Admin::StrURL('contentManager/theme'));
    }

    /**
     * Form install theme
     *
     * @return view
     */
    public function install()
    {
        $model = Themes::orderBy('status', 'desc')
            ->get();

        return view('ContentManager::theme.install', ['models' => $model]);
    }

    /**
     * Process install theme
     *
     * @param Request $request
     * @return Illuminate\Response redirect
     */
    public function installed(Request $request)
    {
        try {
            if (!$request->hasFile('theme_zip')) {
                throw new \Exception('Theme not exists.');
            }

            $themeZip = $request->file('theme_zip');
            if (!$themeZip->isValid()) {
                throw new \Exception('Theme is valid.');
            }

            $clientMimeType = $themeZip->getClientMimeType();
            $extension = $themeZip->extension();
            if (("application/x-zip-compressed" != $clientMimeType)
                || ('zip' != $extension)
            ) {
                throw new \Exception('You must install theme in a .zip format');
            }

            $clientOriginalName = $themeZip->getClientOriginalName();
            $themeName = explode(".{$extension}", $clientOriginalName)[0];
            $countTheme = Themes::where('name', $themeName)->count();
            if ($countTheme > 0) {
                throw new \Exception("Theme {$themeName} is exists. Please choose other theme.");
            }
            $themeZip->move(app_path('Themes/upload'), $clientOriginalName);

            Theme::install($themeName);
            if (Theme::error()) {
                $errors = implode(Theme::getErrors(), ", ");
                throw new \Exception($errors);
            }

            $request->session()->flash('response', [
                'success' => true,
                'message' => array("Theme {$themeName} is install successfully.")
            ]);
        } catch (\Exception $exception) {
            $messages = $exception->getMessage();
            $request->session()->flash('response', [
                'success' => false,
                'message' => is_array($messages) ? $messages : array($messages)
            ]);
        }

        return redirect(Admin::route('contentManager.theme'));
    }

    /**
     * Uninstall theme
     *
     * @param Request $request
     * @param string $themeName
     * @response mixed
     */
    public function uninstall(Request $request, $themeName)
    {
        try {
            Theme::uninstall($themeName);

            if (Theme::error()) {
                $errors = implode(Theme::getErrors(), ", ");
                throw new \Exception($errors);
            }

            $request->session()->flash('response', [
                'success' => true,
                'message' => array("Theme {$themeName} is uninstall successfully.")
            ]);
        } catch (\Exception $exception) {
            $messages = $exception->getMessage();
            $request->session()->flash('response', [
                'success' => false,
                'message' => is_array($messages) ? $messages : array($messages)
            ]);

        }

        redirect(Admin::route('contentManager.theme'));
    }

}

<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\Helper;
use Flash;
use Illuminate\Http\Request;

class LangController extends BaseController
{
    /**
     * LangController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $datas = array();

        $currentLangApp = app()->getLocale();
        $files = \Storage::disk('lang')->files('');

        foreach ($files as $file) {
            $nameFile = explode('.php', $file)[0];

            $arrDirLang = \Storage::disk('lang_json')->allDirectories();

            $arrDirLang = $arrDirLang ?: config('languages');

            $arrContent = array();
            $contentLang = array();

            $result = trans($nameFile);
            Helper::getChildNode("", is_array($result) ? $result : [], $arrContent);

            $totalRecord = count($arrContent);
            $numberValTrans = count(array_filter($arrContent));

            foreach ($arrDirLang as $lang) {
                $path = $lang . '/' . $nameFile . ".json";

                // For case NL, because exist file lang of NL
                if ($lang === "nl") {
                    app()->setLocale("nl");
                    $arrContent = array();
                    $result = trans($nameFile);
                    Helper::getChildNode("", is_array($result) ? $result : [], $arrContent);
                    $numberValTrans = count(array_filter($arrContent));
                    app()->setLocale($currentLangApp);
                }

                if (\Storage::disk('lang_json')->exists($path)) {
                    $arrContent = array();

                    $resultJson2Arr = \GuzzleHttp\json_decode(\Storage::disk('lang_json')->get($path), true);

                    Helper::getChildNode("", $resultJson2Arr, $arrContent);

                    $numberValTrans = count(array_filter($arrContent));
                }

                $contentLang[$lang] = $numberValTrans === $totalRecord
                                        ? "$numberValTrans/$totalRecord"
                                        : "<b style='color:red'>$numberValTrans/$totalRecord</b>";
            }

            $datas[$nameFile] = $contentLang;
        }

        return view('admin.lang.index', compact('datas', 'arrDirLang'));
    }

    /**
     * @param Request $request
     * @param         $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $content = array();
        $nameFile = $request->name;

        $result = trans($nameFile);

        Helper::getChildNode("", $result, $content);

        $arrDirLang = \Storage::disk('lang_json')->allDirectories();

        $contentLang = array();

        foreach ($arrDirLang as $lang) {
            $arrContent = array();
            $path = $lang . '/' . $nameFile . ".json";

            if (\Storage::disk('lang_json')->exists($path)) {
                $resultJson2Arr = \GuzzleHttp\json_decode(\Storage::disk('lang_json')->get($path), true);

                Helper::getChildNode("", $resultJson2Arr, $arrContent);

                $contentLang[$lang] = $arrContent;
            }
        }

        return view('admin.lang.edit', compact('content', 'nameFile', 'contentLang'));
    }

    /**
     * @param Request $request
     * @param         $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $nameFile = $id;
        $datas = $request->get('data');

        foreach ($datas as $keyLang => $contents) {
            $path = $keyLang . "/" . $nameFile . ".json";
            \Storage::disk('lang_json')->put($path, \GuzzleHttp\json_encode($contents, JSON_PRETTY_PRINT));
        }

        Flash::success(trans('messages.lang'));

        return redirect()->back()->withInput();
    }
}

<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use Illuminate\Pagination\LengthAwarePaginator;
class LanguageController extends Controller
{
    public function langManage($lang = false)
    {
        $pageTitle = __('Manage Languages');
        $languages = Language::where('code', '!=', 'es')->orderBy('is_default','desc')->get();
        return view('admin.language.lang', compact('pageTitle', 'languages'));
    }
    public function langStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:40',
            'code' => 'required|string|max:40|unique:languages|not_in:es',
            'text_align' => 'required|in:0,1'
        ]);
        function find_lang_strings($directory, &$strings) {
            $pattern = "/@lang\\('(.*?)'\\)/"; // define the regular expression pattern
            if (is_dir($directory)) {
                $files = scandir($directory); // get a list of files in the directory
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $path = $directory . '/' . $file;
                        if (is_dir($path)) {
                            find_lang_strings($path, $strings); // recursively search subdirectories
                        } else {
                            $content = file_get_contents($path); // read the file contents
                            preg_match_all($pattern, $content, $matches); // search for the pattern in the content
                            foreach ($matches[1] as $match) {
                                $strings[] = $match; // add the matched string to the array
                            }
                        }
                    }
                }
            }
        }
        $directory = base_path('resources/views'); // get the path to the views directory
        $strings = array(); // initialize an array to store the strings
        find_lang_strings($directory, $strings); // call the recursive function to search the directory
        $lang = array();
        foreach ($strings as $string) {
            $lang[$string] = $string; // use the string as both the key and the value
        }
        $json = json_encode($lang, JSON_PRETTY_PRINT); // convert the array to a JSON string
        $json_file = strtolower($request->code) . '.json';
        file_put_contents(resource_path('lang/') . $json_file, $json); // write the JSON string to a file
        $language = new  Language();
        if ($request->is_default) {
            $lang = $language->where('is_default', 1)->first();
            if ($lang) {
                $lang->is_default = 0;
                $lang->save();
            }
        }
        $language->name = $request->name;
        $language->code = strtolower($request->code);
        $language->text_align = $request->text_align;
        $language->is_default = $request->is_default ? 1 : 0;
        if ($request->hasFile('icon')) {
            try {
                $language->icon = fileUploader($request->icon, getFilePath('language'), getFileSize('language'));
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Couldn\'t upload language image')];
                return back()->withNotify($notify);
            }
        }
        $language->save();
        $notify[] = ['success', __('Language has been added successfully')];
        return back()->withNotify($notify);
    }
    public function langUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'text_align' => 'required|in:0,1',
        ]);
        $language = Language::findOrFail($id);
        if (!$request->is_default) {
            $defaultLang = Language::where('is_default', 1)->where('id','!=',$id)->exists();
            if (!$defaultLang) {
                $notify[] = ['error', __('You\'ve to set another language as default before unset this')];
                return back()->withNotify($notify);
            }
        }
        $language->name = $request->name;
        $language->text_align = $request->text_align;
        $language->is_default = $request->is_default ? 1 : 0;
        if ($request->hasFile('icon')) {
            try {
                $old = $language->icon;
                $language->icon = fileUploader($request->icon, getFilePath('language'), getFileSize('language'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Couldn\'t upload language image')];
                return back()->withNotify($notify);
            }
        }
        $language->save();
        if ($request->is_default) {
            $lang = Language::where('is_default', 1)->where('id','!=',$language->id)->first();
            if ($lang) {
                $lang->is_default = 0;
                $lang->save();
            }
        }
        $notify[] = ['success', __('Update successfully')];
        return back()->withNotify($notify);
    }
    public function langDelete($id)
    {
        $lang = Language::find($id);
        fileManager()->removeFile(resource_path('lang/') . $lang->code . '.json');
        fileManager()->removeFile(getFilePath('language') . '/' . $lang->icon);
        $lang->delete();
        $notify[] = ['success', __('Language has been deleted successfully')];
        return back()->withNotify($notify);
    }
    public function langEdit($id)
    {
        $lang = Language::find($id);
        $pageTitle = __('Update :lang Keywords', ['lang' => $lang->name]);
        $jsonPath = resource_path('lang/') . $lang->code . '.json';
        if (!file_exists($jsonPath)) {
            $notify[] = ['error', __('File not found')];
            return back()->withNotify($notify);
        }
        $jsonData = file_get_contents($jsonPath);
        $data = json_decode($jsonData, true);
        $perPage = 20; // Number of items per page
        $page = request('page', 1); // Get the current page from the query string
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($data, $offset, $perPage, true);
        $paginatedData = new LengthAwarePaginator($paginatedData, count($data), $perPage, $page);
        $paginatedData->setPath(url()->current());
   
        $list_lang = Language::where('code', '!=', 'es')->get();
        return view('admin.language.edit_lang', compact('pageTitle', 'paginatedData', 'lang', 'list_lang'));
    }
    public function langSearch(Request $request)
    {
        $lang = Language::findOrFail($request->id);
        $pageTitle = __('Search Value of :lang Keywords', ['lang' => $lang->name]);
        $this->validate($request, [
            'search_key' => 'required'
        ]);
        $key = trim($request->search_key);
        $data = file_get_contents(resource_path('lang/') . $lang->code . '.json');
        $json_arr = json_decode($data, true);
        $searchKey = trim($request->search_key);
        $list_lang = [];
        foreach ($json_arr as $key => $value) {
            if (stristr($key, $searchKey)) {
                $list_lang[] = [
                    'key' => $key,
                    'value' => $value,
                    'id' => $lang->id,
                ];
            }
        }
        $perPage = 20;
        $page = request('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedData = array_slice($list_lang, $offset, $perPage, true);
        $paginatedData = new LengthAwarePaginator($paginatedData, count($list_lang), $perPage, $page);
        $paginatedData->setPath(url()->current());
        return view('admin.language.lang_search', compact('pageTitle', 'paginatedData', 'lang', 'list_lang'));
    }
    public function langSearchReplace(Request $request)
    {
        $language = Language::findOrFail($request->id);
        $this->validate($request, [
            'key' => 'required',
            'value' => 'required'
        ]);
        $key = trim($request->key);
        $reqValue = $request->value;
        $data = file_get_contents(resource_path('lang/') . $language->code . '.json');
        $json_arr = json_decode($data, true);
        if (array_key_exists($key, $json_arr)) {
            $json_arr[$key] = $reqValue;
        } else {
            $notify[] = ['error', __('Your search keyword not found.')];
            return back()->withNotify($notify);
        }
        file_put_contents(resource_path('lang/'). $language->code . '.json', json_encode($json_arr));
        $notify[] = ['success', __('Language value has been replaced successfully')];
        return back()->withNotify($notify);
    }
    public function langImport(Request $request)
    {
        $tolang = Language::find($request->toLangid);
        $fromLang = Language::find($request->id);
        $json = file_get_contents(resource_path('lang/') . $fromLang->code . '.json');
        $json_arr = json_decode($json, true);
        file_put_contents(resource_path('lang/') . $tolang->code . '.json', json_encode($json_arr));
        return 'success';
    }
    public function storeLanguageJson(Request $request, $id)
    {
        $lang = Language::find($id);
        $this->validate($request, [
            'key' => 'required',
            'value' => 'required'
        ]);
        $items = file_get_contents(resource_path('lang/') . $lang->code . '.json');
        $reqKey = trim($request->key);
        if (array_key_exists($reqKey, json_decode($items, true))) {
            $notify[] = ['error', __('Key already exist')];
            return back()->withNotify($notify);
        } else {
            $newArr[$reqKey] = trim($request->value);
            $itemData = json_decode($items, true);
            $result = array_merge($itemData, $newArr);
            file_put_contents(resource_path('lang/') . $lang->code . '.json', json_encode($result));
            $notify[] = ['success', __('Language key has been added successfully')];
            return back()->withNotify($notify);
        }
    }
    public function deleteLanguageJson(Request $request, $id)
    {
        $this->validate($request, [
            'key' => 'required',
            'value' => 'required'
        ]);
        $key = $request->key;
        $lang = Language::find($id);
        $data = file_get_contents(resource_path('lang/') . $lang->code . '.json');
        $json_arr = json_decode($data, true);
        unset($json_arr[$key]);
        file_put_contents(resource_path('lang/'). $lang->code . '.json', json_encode($json_arr));
        $notify[] = ['success', __('Language key has been deleted successfully')];
        return redirect()->route('admin.language.key', ['id' => $id])->withNotify($notify);
    }
    public function updateLanguageJson(Request $request, $id)
    {
        $this->validate($request, [
            'key' => 'required',
            'value' => 'required'
        ]);
        $key = trim($request->key);
        $reqValue = $request->value;
        $lang = Language::find($id);
        $data = file_get_contents(resource_path('lang/') . $lang->code . '.json');
        $json_arr = json_decode($data, true);
        $json_arr[$key] = $reqValue;
        file_put_contents(resource_path('lang/'). $lang->code . '.json', json_encode($json_arr));
        $notify[] = ['success', __('Language key has been updated successfully')];
        return back()->withNotify($notify);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Http\Controllers\Controller;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class FrontendController extends Controller
{

    public function templates()
    {
        $pageTitle = __('Templates');
        $temPaths = array_filter(glob('core/resources/views/templates/*'), 'is_dir');
        foreach ($temPaths as $key => $temp) {
            $arr = explode('/', $temp);
            $tempname = end($arr);
            $templates[$key]['name'] = $tempname;
            $templates[$key]['image'] = asset($temp) . '/preview.jpg';
        }
        $extra_templates = json_decode(getTemplates(), true);
        return view('admin.frontend.templates', compact('pageTitle', 'templates', 'extra_templates'));
    }

    public function templatesActive(Request $request)
    {
        $general = GeneralSetting::first();

        $general->active_template = $request->name;
        $general->save();

        $notify[] = ['success', __(':name template activated successfully', ['name' => strtoupper($request->name)])];
        return back()->withNotify($notify);
    }

    public function seoEdit()
    {
        $pageTitle = __('SEO Configuration');
        $seo = Frontend::where('data_keys', 'seo.data')->first();
        if (!$seo) {
            $data_values = '{"keywords":[],"description":"","social_title":"","social_description":"","image":null}';
            $data_values = json_decode($data_values, true);
            $frontend = new Frontend();
            $frontend->data_keys = 'seo.data';
            $frontend->data_values = $data_values;
            $frontend->save();
        }
        return view('admin.frontend.seo', compact('pageTitle', 'seo'));
    }



    public function frontendSections($key)
    {
        $section = getPageSections()->$key;
        if (!$section) {
            return abort(404);
        }
        $content = Frontend::where('data_keys', $key . '.content')->orderBy('id', 'desc')->first();
        $elements = Frontend::where('data_keys', $key . '.element')->orderBy('id')->orderBy('id', 'desc')->get();
        $pageTitle = __($section->name);
        return view('admin.frontend.index', compact('section', 'content', 'elements', 'key', 'pageTitle'));
    }




    public function frontendContent(Request $request, $key)
    {
        $purifier = new \HTMLPurifier();
        $valInputs = $request->except('_token', 'image_input', 'key', 'status', 'type', 'id');
        foreach ($valInputs as $keyName => $input) {
            if (gettype($input) == 'array') {
                $inputContentValue[$keyName] = $input;
                continue;
            }
            $inputContentValue[$keyName] = $purifier->purify($input);
        }
        $type = $request->type;
        if (!$type) {
            abort(404);
        }
        $pageSections = getPageSections();
        if (isset($pageSections->$key) && isset($pageSections->$key->$type) && isset($pageSections->$key->$type->images)) {
            $imgJson = $pageSections->$key->$type->images;
        } else {
            $imgJson = null;
        }
        $validation_rule = [];
        $validation_message = [];
        foreach ($request->except('_token', 'video') as $input_field => $val) {
            if ($input_field == 'has_image' && $imgJson) {
                foreach ($imgJson as $imgValKey => $imgJsonVal) {
                    $validation_rule['image_input.' . $imgValKey] = ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])];
                    $validation_message['image_input.' . $imgValKey . '.image'] = keyToTitle($imgValKey) . ' must be an image';
                    $validation_message['image_input.' . $imgValKey . '.mimes'] = keyToTitle($imgValKey) . ' file type not supported';
                }
                continue;
            } elseif ($input_field == 'seo_image') {
                $validation_rule['image_input'] = ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])];
                continue;
            }
            // Language-specific fields (e.g. title_ar, sub_heading_en) are optional
            $languages = \App\Models\Language::pluck('code')->toArray();
            $isLangField = false;
            foreach ($languages as $langCode) {
                if (str_ends_with($input_field, '_' . $langCode)) {
                    $isLangField = true;
                    break;
                }
            }
            $validation_rule[$input_field] = $isLangField ? 'nullable' : 'required';

        }
        $request->validate($validation_rule, $validation_message, ['image_input' => 'image']);
        if ($request->id) {
            $content = Frontend::findOrFail($request->id);
        } else {
            $content = Frontend::where('data_keys', $key . '.' . $request->type)->first();
            if (!$content || $request->type == 'element') {
                $content = new Frontend();
                $content->data_keys = $key . '.' . $request->type;
                $content->save();
            }
        }
        if ($type == 'data') {
            $inputContentValue['image'] = $content->data_values->image;
            if ($request->hasFile('image_input')) {
                try {
                    $inputContentValue['image'] = fileUploader($request->image_input, getFilePath('seo'), getFileSize('seo'), $content->data_values->image);
                } catch (\Exception $exp) {
                    $notify[] = ['error', __('Couldn\'t upload the image')];
                    return back()->withNotify($notify);
                }
            }
        } else {
            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    if (!empty($request->image_input) && isset($request->image_input[$imgKey])) {
                        $imgData = $request->image_input[$imgKey];
                        if (is_file($imgData)) {
                            try {
                                $inputContentValue[$imgKey] = $this->storeImage(
                                    $imgJson,
                                    $type,
                                    $key,
                                    $imgData,
                                    $imgKey,
                                    $content->data_values->$imgKey ?? null
                                );
                            } catch (\Exception $exp) {
                                $notify[] = ['error', __('Couldn\'t upload the image')];
                                return back()->withNotify($notify);
                            }
                        }
                    } elseif (isset($content->data_values->$imgKey)) {
                        $inputContentValue[$imgKey] = $content->data_values->$imgKey;
                    }
                }
            }
        }
        $content->data_values = $inputContentValue;
        $content->save();
        $notify[] = ['success', __('Content has been updated successfully')];
        return back()->withNotify($notify);
    }



    public function frontendElement($key, $id = null)
    {
        $section = getPageSections()->$key;
        if (!$section) {
            return abort(404);
        }

        unset($section->element->modal);
        $pageTitle = __(':section Items', ['section' => $section->name]);
        if ($id) {
            $data = Frontend::findOrFail($id);
            return view('admin.frontend.element', compact('section', 'key', 'pageTitle', 'data'));
        }
        return view('admin.frontend.element', compact('section', 'key', 'pageTitle'));
    }




    protected function storeImage($imgJson, $type, $key, $image, $imgKey, $old_image = null)
    {
        $path = 'assets/images/frontend/' . $key;
        if ($type == 'element' || $type == 'content') {
            $size = $imgJson
                ->$imgKey->size;
            $thumb = $imgJson->$imgKey->thumb ?? null;
        } else {
            $path = getFilePath($key);
            $size = getFileSize($key);
            $thumb = fileManager()->$key()->thumb;
        }
        return fileUploader($image, $path, $size, $old_image, $thumb);
    }

    public function remove($id)
    {
        $frontend = Frontend::findOrFail($id);
        $key = explode('.', $frontend->data_keys)[0];
        $type = explode('.', $frontend->data_keys)[1];
        if ($type == 'element' || $type == 'content') {
            $path = 'assets/images/frontend/' . $key;

            $pageSections = getPageSections();
            if (isset($pageSections->$key) && isset($pageSections->$key->$type) && isset($pageSections->$key->$type->images)) {
                $imgJson = $pageSections->$key->$type->images;
            } else {
                $imgJson = null;
            }

            if ($imgJson) {
                foreach ($imgJson as $imgKey => $imgValue) {
                    fileManager()->removeFile($path . '/' . $frontend->data_values->$imgKey);
                    fileManager()->removeFile($path . '/thumb_' . $frontend->data_values->$imgKey);
                }
            }
        }
        $frontend->delete();
        $notify[] = ['success', __('Content removed successfully')];
        return back()->withNotify($notify);
    }
}

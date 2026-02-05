<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\LookUp;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Log;

class LookupsApiController extends Controller
{

    public function __construct(Request $request)
    {
        $locale = $request->input('lang');
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'en';
        }
        App::setLocale($locale);
    }

     /**
     * @function: to fetch Lookup details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function index(Request $request)
    {
        try {

            $lookup_en = LookUp::orderBy('created_at', 'desc')->where('parent_id', 0)->where('lang', 'en')->get();
            $lookup_ar = LookUp::orderBy('created_at', 'desc')->where('parent_id', 0)->where('lang', 'ar')->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'lookup_en' => $lookup_en, 'lookup_ar' => $lookup_ar]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

        /**
     * @function: to store lookups details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function store(Request $request)
    {
        try {

            if ($request->slug) {
                $lookups = LookUp::where('slug', $request->slug)->first();
                $request['parent_id'] = $lookups->id;
            }
            $lookup = LookUp::create($request->all());

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.saved_success'), 'lookup' => $lookup]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to edit lookups details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function edit($slug)
    {
        try {
            $header_id = LookUp::where('slug', $slug)->value('header_id');
            $lookup = LookUp::where('header_id', $header_id)->orderBy('id')->get(['id', 'header_id', 'lang', 'shortname', 'longname', 'description', 'icon']);

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.data_return'), 'lookup' => $lookup]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to update lookups details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function update(Request $request, $id)
    {
        try {
            $lookup = LookUp::findOrFail($id);
            $lookup->update($request->all());

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.updatedsuccessfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to delete lookup details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function destroy($id)
    {
        try {
            LookUp::where('header_id', $id)->delete();
            if(LookUp::where('parent_id', $id)->exists()){
                LookUp::where('parent_id', $id)->delete();

            }
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.deletedsuccessfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }
    /**
     * @function: to fetch parent lookups details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function parentlookups()
    {
        try {
            $lookups = LookUp::where('parent_id', 0)->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'lookups' => $lookups]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to fetch lookups details.
     *
     * @author: Santhosha G
     *
     * @created-on: 04 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function lookupdata($type)
    {
        try {

            $parent_header_id = LookUp::where('slug', $type)->value('header_id');
            $parent_lookup = LookUp::where('header_id', $parent_header_id)->get(['id', 'longname']);
            $lookups_en = LookUp::where('parent_id', $parent_header_id)->where('lang', 'en')->orderBy('seq', 'asc')->get();
            $lookups_ar = LookUp::where('parent_id', $parent_header_id)->where('lang', 'ar')->orderBy('seq', 'asc')->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'lookups_en' => $lookups_en, 'lookups_ar' => $lookups_ar, 'parent_lookup' => $parent_lookup]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    public function fetchLookup(Request $request)
    {
        $lookup_type = $request->lookup_type;
        $validator = Validator::make($request->all(), [
            'lookup_type' => 'required',
        ]);
        try {
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            } else {
                $parent = LookUp::where('shortname', $lookup_type)->where('status', 1)->first();
                if ($parent) {
                    $childs = LookUp::where('parent_id', $parent->id)->where('status', 1)->orderBy('seq', 'asc')->get();
                    if (count($childs) == 0) {
                        return response()->json(['status' => 'E', 'message' => trans('returnmessage.details_not_found')]);
                    }
                    return response()->json(['status' => 'S', 'message' => trans('returnmessage.lookup_values'), 'lookup_details' => $childs]);
                } else {
                    return response()->json(['status' => 'E', 'message' => trans('returnmessage.details_not_found')]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    public function updateLookupStatus(request $request)
    {
        try {
            $LookUp = LookUp::where('header_id', $request->id)->first();
            if ($LookUp['status'] == 1) {
                $status = LookUp::where('header_id', $request->id)->update(['status' => 0]);
            } else {
                $status = LookUp::where('header_id', $request->id)->update(['status' => 1]);
            }
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.saved_success')]);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    public function store_lookups(Request $request)
    {
        try {
            DB::beginTransaction();
            $datas = $request->all();
            Log::info('datas');
            Log::info($datas);
            $message = "";
            $header_id = 0;

            foreach ($datas as $key => $data) {

                $messages = [];
                if ($data['lang'] === 'en') {
                    $messages['shortname.required'] = trans('returnmessage.required_shortname_en');
                    $messages['longname.required'] = trans('returnmessage.required_longname_en');
                    // $messages['description.required'] = trans('returnmessage.required_description_en');
                } else {
                    $messages['shortname.required'] = trans('returnmessage.required_shortname_ar');
                    $messages['longname.required'] = trans('returnmessage.required_longname_ar');
                    // $messages['description.required'] = trans('returnmessage.required_description_ar');
                }

                $validator = Validator::make($data, [
                    'shortname' => 'required',
                    'longname' => 'required',
                    // 'description' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json(['status' => 'E', 'message' => $validator->errors()->first()]);
                } else {
                    if ($data['id'] > 0) {
                        LookUp::where('header_id', $data['header_id'])->where('lang', $data['lang'])
                            ->update([
                                'shortname' => $data['shortname'],
                                'longname' => $data['longname'],
                                'description' => $data['description'],
                                'icon' => $data['icon'],
                                'updated_by' => Auth::user()->id,
                            ]);
                        $message = trans('returnmessage.updatedsuccessfully');
                    } else {
                        $record = new LookUp();
                        $record->shortname = $data['shortname'];
                        $record->longname = $data['longname'];
                        $record->description = $data['description'];
                        $record->icon = $data['icon'];
                        $record->lang = $data['lang'];
                        $record->created_by = Auth::user()->id;
                        $record->save();
                        $record->header_id = $header_id > 0 ? $header_id : $record->id;
                        $record->save();
                        $header_id = $record->header_id;
                        $message = trans('returnmessage.createdsuccessfully');

                    }
                }

            }

            DB::commit();
            return response()->json(['status' => 'S', 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::info($e);
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    public function store_child_lookups(Request $request)
    {
        try {
            DB::beginTransaction();
            $datas = $request->all();
            Log::info("datas in child");
            Log::info($datas);
            $message = "";
            $header_id = 0;
            $parent_lu_id = LookUp::where('slug', $datas[0]['parentslug'])->value('header_id');

            foreach ($datas as $key => $data) {
                $messages = [];

                if ($data['lang'] === 'en') {
                    $messages['shortname.required'] = trans('returnmessage.required_shortname_en');
                    $messages['longname.required'] = trans('returnmessage.required_longname_en');
                    // $messages['description.required'] = trans('returnmessage.required_description_en');
                } else {
                    $messages['shortname.required'] = trans('returnmessage.required_shortname_ar');
                    $messages['longname.required'] = trans('returnmessage.required_longname_ar');
                    // $messages['description.required'] = trans('returnmessage.required_description_ar');
                }

                $validator = Validator::make($data, [
                    'shortname' => 'required',
                    'longname' => 'required',
                    // 'description' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json(['status' => 'E', 'message' => $validator->errors()->first()]);
                } else {
                    if ($data['id'] > 0) {
                        LookUp::where('header_id', $data['header_id'])->where('lang', $data['lang'])
                            ->update([
                                'shortname' => $data['shortname'],
                                'longname' => $data['longname'],
                                'description' => $data['description'],
                                'icon' => $data['icon'],
                                'updated_by' => Auth::user()->id,
                            ]);
                        $message = trans('returnmessage.updatedsuccessfully');
                    } else {
                        $record = new LookUp();
                        $record->shortname = $data['shortname'];
                        $record->longname = $data['longname'];
                        $record->description = $data['description'];
                        $record->parent_id = $parent_lu_id;
                        $record->lang = $data['lang'];
                        $record->icon = $data['icon'];
                        $record->created_by = Auth::user()->id;
                        $record->save();
                        $record->header_id = $header_id > 0 ? $header_id : $record->id;
                        $record->save();
                        $header_id = $record->header_id;
                        $message = trans('returnmessage.createdsuccessfully');

                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'S', 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    public function fetchParentLookup(Request $request)
    {
        try {
            $header_id = LookUp::where('slug', $request->slug)->value('header_id');
            $parent_en = LookUp::where('header_id', $header_id)->where('lang', 'en')->value('longname');
            $parent_ar = LookUp::where('header_id', $header_id)->where('lang', 'ar')->value('longname');
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'parent_en' => $parent_en, 'parent_ar' => $parent_ar]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }

    }

    public function childLookupEdit(Request $request)
    {
        try {
            $header = LookUp::where('slug', $request->slug)->first();
            $parent = LookUp::where('header_id', $header['parent_id'])->first();
            $lookup = LookUp::where('header_id', $header['header_id'])->orderBy('id')->get(['id', 'header_id', 'lang', 'shortname', 'longname', 'description', 'icon']);

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.data_return'), 'lookup' => $lookup, 'parent_slug' => $parent->slug, 'parent_name' => $parent->shortname]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    public function fetchLangLookup(Request $request)
    {
        $lookup_type = $request->lookup_type;
        $validator = Validator::make($request->all(), [
            'lookup_type' => 'required',
        ]);
        try {
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()]);
            } else {
                $parent = LookUp::where('shortname', $lookup_type)->where('status', 1)->first();
                if ($parent) {
                    $lookup_en = LookUp::where('parent_id', $parent->id)->where('lang','en')->where('status', 1)->orderBy('seq', 'asc')->get();
                    $lookup_ar = LookUp::where('parent_id', $parent->id)->where('lang','ar')->where('status', 1)->orderBy('seq', 'asc')->get();
                    if (count($lookup_en) == 0 || count($lookup_ar) == 0) {
                        return response()->json(['status' => 'E', 'message' => trans('returnmessage.details_not_found')]);
                    }
                    return response()->json(['status' => 'S', 'message' => trans('returnmessage.lookup_values'), 'lookup_en' => $lookup_en, 'lookup_ar' => $lookup_ar]);
                } else {
                    return response()->json(['status' => 'E', 'message' => trans('returnmessage.details_not_found')]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }
}

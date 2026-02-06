<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institutions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class InstitutionApiController extends Controller
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
     * @function: to fetch institution details.
     *
     * @author: Raghavendra kumar
     *
     * @created-on: 6 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function index(Request $request)
    {
        try {
            $institutions = Institutions::orderBy('updated_at', 'desc')->where('status', 1)->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'institutions' => $institutions]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    /**
     * @function: to store institution details.
     *
     * @author: Raghavendra kumar
     *
     * @created-on: 6 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function store(Request $request)
    {
        try {
            if (Institutions::where('name', $request->name)->count() > 0) {
                return response()->json(['status' => 'E', 'message' => trans('returnmessage.menu') . ' ' . $request->name . ', ' . trans('returnmessage.already_exists')]);
            }

            $institution = Institutions::create($request->all());

            // CustomFunctions::updateSlug($request->name, 'institutions');

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.createdsuccessfully'), 'institution' => $institution]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    /**
     * @function: to edit institution details.
     *
     * @author: Raghavendra kumar
     *
     * @created-on: 6 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function edit($slug)
    {
        try {
            $institution = Institutions::where('slug', $slug)->firstOrFail();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'institution' => $institution]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    /**
     * @function: to update institution details.
     *
     * @author: Raghavendra kumar
     *
     * @created-on: 6 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function update(Request $request, $id)
    {
        try {

            if (Institutions::where('name', $request->name)->where('id', '!=', $id)->count() > 0) {
                return response()->json(['status' => 'E', 'message' => trans('returnmessage.menu') . $request->title . trans('returnmessage.already_exists')]);
            }
            $institution = Institutions::findOrFail($id);
            $institution->update($request->all());

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.updatedsuccessfully'), 'institution' => $institution]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    /**
     * @function: to delete institution details.
     *
     * @author: Raghavendra kumar
     *
     * @created-on: 6 Feb, 2026
     *
     * @updated-on: N/A
     */
    public function destroy($id)
    {
        try
        {
            Institutions::destroy($id);
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.deletedsuccessfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_delete')]);
        }
    }

}

<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cities;
use App\Models\Countries;
use App\Models\States;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
// use League\Csv\Reader;
use Log;

// use Maatwebsite\Excel\Facades\Excel;

class CountriesApiController extends Controller
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
     * @function: to fetch countries data.
     *
     * @author: Stalvin
     *
     * @created-on: 6 Dec, 2022
     *
     * @updated-on: 7 Dec, 2022
     */
    public function index()
    {
        try {
            $countries_en = Countries::with('states', 'states.cities')->orderBy("updated_at", "desc")->where('lang', 'en')->get();
            $countries_ar = Countries::with('states', 'states.cities')->orderBy("updated_at", "desc")->where('lang', 'ar')->get();

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'countries_en' => $countries_en, 'countries_ar' => $countries_ar]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to fetch countries data using slug.
     *
     * @author: Stalvin
     *
     * @created-on: 6 Dec, 2022
     *
     * @updated-on: 13 Dec, 2022
     */
    public function getCountriesBySlug($slug)
    {
        try {
            $header_id = Countries::where('slug', $slug)->value('header_id');
            $countries = Countries::where('header_id', $header_id)->orderBy('id')->get(['id', 'name', 'mobile_code', 'lang', 'header_id']);
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'countries' => $countries]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to fetch countries data using Id.
     *
     * @author: Stalvin
     *
     * @created-on: 6 Dec, 2022
     *
     * @updated-on: 13 Dec, 2022
     */
    public function getCountriesById($id)
    {
        try {
            $countries = Countries::where('id', $id)->first();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'countries' => $countries]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to save countries details.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: 12 Dec, 2022
     */
    public function saveCountries(Request $request)
    {

        try {
            DB::beginTransaction();
            $currenttime = date('Y-m-d h:i:s');
            $datas = $request->all();
            $message = "";
            $header_id = 0;
            foreach ($datas as $key => $data) {

                $messages = [];
                if ($data['lang'] === 'en') {
                    $messages['name.required'] = trans('returnmessage.required_countryname_en');
                } else {
                    $messages['name.required'] = trans('returnmessage.required_countryname_ar');
                }

                $validator = Validator::make($data, [
                    'name' => 'required',
                    'mobile_code' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json(['status' => 'E', 'message' => $validator->errors()->first()]);
                } else {
                    Log::info($data['name']);
                    if ($data['id'] > 0) {
                        Log::info('inside if');
                        Countries::where('header_id', $data['header_id'])->where('lang', $data['lang'])
                            ->update([
                                'name' => $data['name'],
                                'mobile_code' => $data['mobile_code'],
                                'updated_by' => Auth::user()->id,
                            ]);
                        $message = trans('returnmessage.updatedsuccessfully');
                    } else {
                        $record = new Countries();
                        $record->name = $data['name'];
                        $record->mobile_code = $data['mobile_code'];
                        $record->lang = $data['lang'];
                        $record->created_by = Auth::user()->id;
                        $record->save();
                        $record->header_id = $header_id > 0 ? $header_id : $record->id;
                        $record->save();
                        $header_id = $record->header_id;
                        // CustomFunctions::updateSlug($record->id, $record->name, 'countries');
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

    /**
     * @function: to delet countries details.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function deleteCountries($id)
    {
        try {
            $countries = Countries::where('header_id', $id)->delete();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.deletedsuccessfully')]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to update countries status.
     *
     * @author: Stalvin
     *
     * @created-on: 12 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function saveStates(Request $request)
    {
        try {
            DB::beginTransaction();
            $currenttime = date('Y-m-d h:i:s');
            $datas = $request->all();

            $message = "";
            $header_id = 0;

            foreach ($datas as $key => $data) {
                $messages = [];

                if ($data['lang'] === 'en') {
                    $messages['name.required'] = trans('returnmessage.required_statename_en');
                } else {
                    $messages['name.required'] = trans('returnmessage.required_statename_ar');
                }

                $validator = Validator::make($data, [
                    'name' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json(['status' => 'E', 'message' => $validator->errors()->first()]);
                } else {
                    if ($data['id'] > 0) {
                        $states = States::where('header_id', $data['header_id'])->where('lang', $data['lang'])
                            ->update([
                                'name' => $data['name'],
                                'updated_by' => Auth::user()->id,
                            ]);
                        $message = trans('returnmessage.updatedsuccessfully');
                    } else {
                        $states = new States();
                        $states->name = $data['name'];
                        $states->country_id = $data['country_id'];
                        $states->lang = $data['lang'];
                        $states->created_by = Auth::user()->id;
                        $states->save();
                        $states->header_id = $header_id > 0 ? $header_id : $states->id;
                        $states->save();
                        $header_id = $states->header_id;

                        // CustomFunctions::updateSlug($states->id, $states->name, 'states');
                        $message = trans('returnmessage.createdsuccessfully');
                    }
                }
            }

            DB::commit();
            return response()->json(['status' => 'S', 'message' => $message]);

        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    /**
     * @function: to setch states details.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: 8 Dec, 2022
     */
    public function fetchStates(Request $request)
    {
        try {

            $country = Countries::where('slug', $request->countryslug)->first();
            $headerid = $country['header_id'];

            $states_en = States::where('country_id', $headerid)->orderBy("updated_at", "desc")->where('lang', 'en')->get();
            $states_ar = States::where('country_id', $headerid)->orderBy("updated_at", "desc")->where('lang', 'ar')->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'states_en' => $states_en, 'states_ar' => $states_ar, 'countries' => $country]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to setch states details using slug.
     *
     * @author: Stalvin
     *
     * @created-on: 12 Dec, 2022
     *
     * @updated-on: 13 Dec, 2022
     */
    public function getStatesBySlug($slug)
    {
        try {
            $header_id = States::where('slug', $slug)->value('header_id');

            $state = States::where('header_id', $header_id)->orderby('id')->get();
            $country_id = $state[0]['country_id'];

            $country = Countries::where('header_id', $country_id)->orderBy('id')->get();

            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'state' => $state, 'country' => $country]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to setch states details using Id.
     *
     * @author: Stalvin
     *
     * @created-on: 12 Dec, 2022
     *
     * @updated-on: 13 Dec, 2022
     */
    public function getStatesById($id)
    {
        try {
            $states = States::where('id', $id)->first();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'states' => $states]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to setch states name.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function fetchStatesName($id)
    {
        try {
            $states_en = States::where('country_id', $id)->where('lang', 'en')->get();
            $states_ar = States::where('country_id', $id)->where('lang', 'ar')->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'states_en' => $states_en, 'states_ar' => $states_ar]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to setch cities name.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function fetchCitiesName($id)
    {
        try {
            $cities_en = Cities::where('state_id', $id)->where('lang', 'en')->get();
            $cities_ar = Cities::where('state_id', $id)->where('lang', 'ar')->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'cities_en' => $cities_en, 'cities_ar' => $cities_ar]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to delete states.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function deleteStates($id)
    {
        try {
            $states = States::where('header_id', $id)->delete();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.deletedsuccessfully')]);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to save cities details.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function saveCities(Request $request)
    {
        try {
            DB::beginTransaction();
            $currenttime = date('Y-m-d h:i:s');
            $datas = $request->all();
            $message = "";
            $header_id = 0;

            foreach ($datas as $key => $data) {

                $messages = [];
                if ($data['lang'] === 'en') {
                    $messages['name.required'] = trans('returnmessage.required_cityname_en');
                } else {
                    $messages['name.required'] = trans('returnmessage.required_cityname_ar');
                }

                $validator = Validator::make($data, [
                    'name' => 'required',
                ], $messages);

                if ($validator->fails()) {
                    return response()->json(['status' => 'E', 'message' => $validator->errors()->first()]);
                } else {
                    if ($data['id'] > 0) {
                        $cities = Cities::where('header_id', $data['header_id'])->where('lang', $data['lang'])
                            ->update([
                                'name' => $data['name'],
                                'updated_by' => Auth::user()->id,
                            ]);
                        $message = trans('returnmessage.updatedsuccessfully');
                    } else {
                        $cities = new Cities();
                        $cities->name = $data['name'];
                        $cities->lang = $data['lang'];
                        $cities->country_id = $data['country_id'];
                        $cities->state_id = $data['state_id'];
                        $cities->created_by = Auth::user()->id;
                        $cities->save();
                        $cities->header_id = $header_id > 0 ? $header_id : $cities->id;
                        $cities->save();
                        $header_id = $cities->header_id;
                        // CustomFunctions::updateSlug($cities->id, $cities->name, 'cities');
                        $message = trans('returnmessage.createdsuccessfully');
                    }
                }

            }
            DB::commit();
            return response()->json(['status' => 'S', 'message' => $message]);

        } catch (\Exception $e) {
            Log::info($e);
            DB::rollback();
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'errordata' => $e->getmessage()]);
        }
    }

    /**
     * @function: to fetch cities details.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function fetchCities(Request $request)
    {

        try {
            $state = States::where('slug', $request->statename)->first();
            $header_id = $state->header_id;
            $countries = Countries::where('slug', $request->countryname)->first();

            $cities_en = Cities::where('state_id', $header_id)->where('lang', 'en')->orderBy("updated_at", "desc")->get();
            $cities_ar = Cities::where('state_id', $header_id)->where('lang', 'ar')->orderBy("updated_at", "desc")->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'cities_en' => $cities_en, 'cities_ar' => $cities_ar, 'state' => $state]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to fetch cities details using slug.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: 13 Dec, 2022
     */
    public function getCitiesBySlug($slug)
    {
        try {
            $header_id = Cities::where('slug', $slug)->value('header_id');

            $city = Cities::where('header_id', $header_id)->orderby('id')->get();
            $state = States::where('header_id', $city[0]['state_id'])->orderBy('id')->get();
            $country = Countries::where('header_id', $city[0]['country_id'])->get();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'city' => $city, 'state' => $state, 'country' => $country]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    /**
     * @function: to delete cities details.
     *
     * @author: Stalvin
     *
     * @created-on: 7 Dec, 2022
     *
     * @updated-on: N/A
     */
    public function deleteCities($id)
    {
        try {
            $cities = Cities::where('header_id', $id)->delete();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.deletedsuccessfully')]);
        } catch (\Exception $e) {
            Log::info($e);
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }

    public function InsertCountryTemplate(Request $request)
    {
        try {
            DB::beginTransaction();
            Log::info($request);
            foreach ($request->file as $key => $data) {
                if ($key > 0) {
                    $mobileCodeEn = '+' . ltrim($data[1], '+');
                    $mobileCodeAr = '+' . ltrim($data[5], '+');
                    $country_en = Countries::firstOrCreate([
                        'name' => $data[0],
                        'lang' => 'en',
                    ], [
                        'mobile_code' => $mobileCodeEn,
                    ]);
    
                    $country_ar = Countries::firstOrCreate([
                        'name' => $data[4],
                        'lang' => 'ar',
                    ], 
                    [
                        'header_id' => $country_en->id,
                        'mobile_code' => $mobileCodeAr,
                    ]);
    
                    Countries::where('id', $country_en->id)->update([
                        'header_id' => $country_en->id,
                    ]);
    
                    $state_en = States::firstOrCreate([
                        'name' => $data[2],
                        'lang' => 'en',
                        'country_id' => $country_en->id,
                    ]);
                    
                    $state_ar = States::firstOrCreate([
                        'name' => $data[6],
                        'lang' => 'ar',
                        'country_id' => $country_en->id,
                    ], [
                        'header_id' => $state_en->id,
                    ]);
    
                    if ($state_en->wasRecentlyCreated) {
                        $state_en->update(['header_id' => $state_en->id]);
                    }
    
                    $city_en = Cities::firstOrCreate([
                        'name' => $data[3],
                        'lang' => 'en',
                        'country_id' => $country_en->id,
                        'state_id' => $state_en->id,
                    ]);
                    
                    $city_ar = Cities::firstOrCreate([
                        'name' => $data[7],
                        'lang' => 'ar',
                        'country_id' => $country_en->id,
                        'state_id' => $state_en->id,
                    ], [
                        'header_id' => $city_en->id, 
                    ]);
    
                    if ($city_en->wasRecentlyCreated) {
                        $city_en->update(['header_id' => $city_en->id]);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.createdsuccessfully')]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }
    
    public function fetchCountryCodes()
    {
        try {
            $country_codes = Countries::orderBy("updated_at", "desc")->where('lang', 'en')->get(['id','header_id','name','mobile_code']);
            return response()->json(['status' => 'S', 'message' => trans('returnmessage.dataretreived'), 'country_codes' => $country_codes]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'E', 'message' => trans('returnmessage.error_processing'), 'error_data' => $e->getmessage()]);
        }
    }
}

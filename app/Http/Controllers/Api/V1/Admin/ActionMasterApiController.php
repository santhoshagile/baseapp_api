<?php
namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionMasterApiController extends Controller
{
    public function index()
    {
        try {
            $actions = ActionMaster::orderBy('id', 'desc')->get();

            return response()->json([
                'status' => 'S',
                'data'   => $actions,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'E',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'action_name' => 'required|string|max:250|unique:action_master,action_name',
                'category'    => 'nullable|string|max:250',
                'description' => 'nullable|string|max:500',
                'status'      => 'required|integer',
            ], [
                'action_name.unique' => 'The action name is already taken. Please choose another name.',
            ]);

            $action = ActionMaster::create([
                 ...$validated,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'status'  => 'S',
                'message' => 'Action created successfully',
                'data'    => $action,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'E',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $action = ActionMaster::where('slug', $slug)->first();

            if (! $action) {
                return response()->json([
                    'status'  => 'E',
                    'message' => 'Action not found',
                ], 404);
            }

            return response()->json([
                'status' => 'S',
                'data'   => $action,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'E',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'action_name' => 'required|string|max:250|unique:action_master,action_name,' . $id,
                'category'    => 'nullable|string|max:250',
                'description' => 'nullable|string|max:500',
                'status'      => 'required|integer',
            ], [
                'action_name.unique' => 'The action name is already taken. Please choose another name.',
            ]);

            $action = ActionMaster::findOrFail($id);

            $action->update([
                 ...$validated,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'status'  => 'S',
                'message' => 'Action updated successfully',
                'data'    => $action,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'E',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $action = ActionMaster::findOrFail($id);
            $action->delete();

            return response()->json([
                'status'  => 'S',
                'message' => 'Action deleted successfully',
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status'  => 'E',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateActionMasterStatus(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer|exists:action_master,id',
            ]);

            $action = ActionMaster::findOrFail($request->id);

            $action->status = $action->status == 1 ? 0 : 1;
            $action->save();

            return response()->json([
                'status'  => 'S',
                'message' => 'Action status updated successfully.',
                'data'    => [
                    'id'     => $action->id,
                    'status' => $action->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'E',
                'message' => 'Error updating action status: ' . $e->getMessage(),
            ]);
        }
    }

}

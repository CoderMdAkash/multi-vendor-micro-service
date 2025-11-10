<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index() 
    {
        $vendor = Vendor::all();

        return response()->json([
            'success' => true,
            'message' => 'Vendors retrieved successfully',
            'data' => VendorResource::collection($vendor)
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|unique:vendors|max:255',
            'email' => 'required|max:255',
            'note' => 'required|max:255',
        ]);

        $validate['user_id'] = @$request->user['id'];

        try {
            $vendor = Vendor::create($validate);
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'Vendor created successfully',
            'data' => new VendorResource($vendor)
        ];
    }

    public function update(Request $request, $id) 
    {
        $vendor = Vendor::find($id);
        
        if (!$vendor) {
            return [
                'success' => false,
                'message' => 'Vendor not found'
            ];
        }

        $validate = $request->validate([
            'name' => 'required|max:255|unique:vendors,name,' . $vendor->id,
            'email' => 'required|max:255',
            'note' => 'required|max:255',
        ]);

        $validate['user_id'] = @$request->user['id'];


        try {
            $vendor->update($validate);
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'vendor updated successfully',
            'data' => new VendorResource($vendor)
        ];
    }

    public function destroy($id) 
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return [
                'success' => false,
                'message' => 'vendor not found'
            ];
        }

        try {
            $vendor->delete();
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => $th->getMessage()
            ];
        }

        return [
            'success' => true,
            'message' => 'vendor deleted successfully'
        ];
    }
}

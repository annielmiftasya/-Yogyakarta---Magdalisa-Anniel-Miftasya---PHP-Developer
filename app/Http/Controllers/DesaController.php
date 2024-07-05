<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\Village;
use Laravolt\Indonesia\Models\District;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DesaController extends Controller
{
    public function index()
    {
        $desa = Village::paginate(20);
        return response()->json(['data' => $desa]);
    }

    public function district()
    {
        $district = District::paginate(20);
        return response()->json(['data' => $district]);
    }

    public function show($id)
    {
        $desa = Village::find($id);
        if (!$desa) {
            return response()->json(['message' => 'Desa not found'], 404);
        }
        return response()->json(['data' => $desa]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'district_code' => 'required',
            'name' => 'required',
            'lat' => 'nullable',
            'long' => 'nullable',
            'pos' => 'nullable',
        ]);

            $code = $this->generateUniqueCode($data['district_code']);
            $data['code'] = $code;

            $data['created_at'] = now();
            $data['updated_at'] = now();


            $desa = Village::create($data);

            DB::commit();

            return response()->json(['message' => 'Desa created successfully', 'data' => $desa], 201);
    }

    public function update(Request $request, $id)
    {
        $desa = Village::find($id);
        if (!$desa) {
            return response()->json(['message' => 'Desa not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'district_code' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'district_code' => $request->district_code,
            'name' => $request->name,
            'updated_at' => now(),
        ];

        if ($request->has('lat')) {
            $data['meta->lat'] = $request->lat;
        }
        if ($request->has('long')) {
            $data['meta->long'] = $request->long;
        }
        if ($request->has('pos')) {
            $data['meta->pos'] = $request->pos;
        }

        if ($desa->district_code !== $request->district_code) {
            $newCode = $this->generateUniqueCode($request->district_code);
            $data['code'] = $newCode;
        }

       
        $desa->update($data);

        return response()->json(['message' => 'Desa updated successfully', 'data' => $desa]);
    }

    public function destroy($id)
    {
        $desa = Village::find($id);
        if (!$desa) {
            return response()->json(['message' => 'Desa not found'], 404);
        }

        $desa->delete();
        return response()->json(['message' => 'Desa deleted']);
    }

    private function generateUniqueCode($districtCode)
    {
            $latestDesa = Village::where('district_code', $districtCode)
                                  ->orderBy('id', 'desc')
                                  ->first();

            if (!$latestDesa) {
                $code = $districtCode . '001';
            } else {
                $lastCode = substr($latestDesa->code, -3);
                $newCodeNumber = intval($lastCode) + 1;
                $newCode = str_pad($newCodeNumber, 3, '0', STR_PAD_LEFT);
                $code = $districtCode . $newCode;
            }

        return $code;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function show($id)
    {
        return User::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $validator = Validator::make($request->all(), [
            "name" => "sometimes|required|string|max:255",
            "email" => "sometimes|required|string|email|max:255|unique:users,email,".$id,
            "password" => "sometimes|required|string|min:8|max:255"
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update($request->only(['name', 'email', 'password']));

        return response()->json([
            "message" => "User updated successfully",
            "data" => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }
    public function statistics()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $dailyRegistrations = User::whereDate('created_at', $today)->count();
        $weeklyRegistrations = User::whereBetween('created_at', [$startOfWeek, $today])->count();
        $monthlyRegistrations = User::whereBetween('created_at', [$startOfMonth, $today])->count();

        return response()->json([
            "daily_registrations" => $dailyRegistrations,
            "weekly_registrations" => $weeklyRegistrations,
            "monthly_registrations" => $monthlyRegistrations
        ]);
    }
}


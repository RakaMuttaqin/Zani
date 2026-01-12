<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return Account::all();
        }

        return Account::where('user_id', Auth::user()->id)->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request)
    {
        $request->validated();

        $account = Account::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'balance' => $request->balance,
        ]);

        return response()->json([
            "message" => "Account created successfully",
            "account" => $account
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();

        $query = Account::where('id', $id);

        // kalau bukan admin, kunci ke user_id
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        $account = $query->firstOrFail();

        return response()->json($account);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, $id)
    {
        $user = Auth::user();

        $account = Account::findOrFail($id);

        // kalau bukan admin dan bukan pemilik → block
        if ($user->role !== 'admin' && $account->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $account->update($request->validated());

        return response()->json([
            'message' => 'Account updated successfully',
            'account' => $account
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $account = Account::findOrFail($id);

        // pastikan account milik user login
        if ($account->user_id !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $account->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}

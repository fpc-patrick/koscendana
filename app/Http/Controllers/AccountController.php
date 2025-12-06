<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AccountController extends Controller
{
    public function index()
    {
        $account = User::orderBy('name', 'asc')->get();

        return view('account.account', [
            'account' => $account
        ]);;
    }


    public function create()
    {
        return view('account.account-add');
    }

    public function store(Request $request)
    {
        // Validation logic here if needed

        User::create([
            'name' => $request->input('user'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'updated_at' => $request->input('updated_at'),
            'created_at' => $request->input('created_at')
            // Add other fields as needed
        ]);

        return redirect('/account')->with('success', 'Account added successfully');
    }

    public function edit($id_user)
    {
        try {
            $account = User::findOrFail($id_user);
            return view('account.account-edit', compact('account'));
        } catch (\Exception $ex) {
            abort(404); // Redirect to 404 page
        }
    }



    public function update(Request $request, $id_user)
    {
        // Find the account by ID
        $account = User::findOrFail($id_user);

        // Update the fields based on the form data
        $account->name = $request->input('user');
        $account->email = $request->input('email');
        $account->password = $request->input('password');
        $account->updated_at = $request->input('updated_at');
        $account->created_at = $request->input('created_at');
        $account->save();

        return redirect('/account')->with('success', 'Account updated successfully');
    }




    public function destroy($id_user)
    {
        try {
            $account = User::findOrFail($id_user);
            $account->delete(); // Use delete for soft delete

            return redirect('/account')->with('success', 'Account deleted successfully');
        } catch (\Exception $ex) {
            return redirect('/account')->with('error', 'Failed to delete Account');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\AccountGroup;
use Inertia\Inertia;

class AccountGroupController extends Controller
{
    public function index()
    {
        return Inertia::render('AccountGroups/Index', [
            'data' => AccountGroup::all()
                ->map(function ($accountgroup){
                    return [
                        'id' => $accountgroup->id,
                        'name' => $accountgroup->name,
                        'type_id' => $accountgroup->type_id,
                        'type_name' => $accountgroup->accountType->name,
                    ];
                }), 
        ]);
    }

    public function create()
    {
        $types = \App\Models\AccountType::all()->map->only('id','name');
        $first = \App\Models\AccountType::all('id','name')->first();

        $companies = \App\Models\Company::all()->map->only('id','name');
        $comp_first = \App\Models\Company::all('id','name')->first();

        return Inertia::render('AccountGroups/Create',['types' => $types, 'first' => $first,
        'companies' => $companies, 'comp_first' => $comp_first]);
    }

    public function store()
    {
        Request::validate([
            'type' => ['required'],
            'name' => ['required'],
            'company' => ['required'],
        ]);
        AccountGroup::create([
            'type_id' => Request::input('type'),
            'name' => Request::input('name'),
            'company_id' => Request::input('company'),
        ]);

        return Redirect::route('accountgroups')->with('success', 'Account Group created.');
    }

    public function show(AccountGroup $accountgroup)
    {
    }

    public function edit(AccountGroup $accountgroup)
    {
        $types = \App\Models\AccountType::all()->map->only('id','name');
        $companies = \App\Models\Company::all()->map->only('id','name');
        return Inertia::render('AccountGroups/Edit', [
            'accountgroup' => [
                'id' => $accountgroup->id,
                'type_id' => $accountgroup->type_id,
                'name' => $accountgroup->name,
                'company_id' => $accountgroup->company_id,
            ],
            'types' => $types,
            'companies' => $companies,
        ]);
    }

    public function update(AccountGroup $accountgroup)
    {
        Request::validate([
            'type' => ['required'],
            'name' => ['required'],
            'company' => ['required'],
        ]);
        $accountgroup->type_id = Request::input('type');
        $accountgroup->name = Request::input('name');
        $accountgroup->company_id = Request::input('company');
        $accountgroup->save();

        return Redirect::route('accountgroups')->with('success', 'Account Group updated.');
    }

    public function destroy(AccountGroup $accountgroup)
    {
        $accountgroup->delete();
        return Redirect::back()->with('success', 'Account Group deleted.');
    }
}
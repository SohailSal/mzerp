<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\AccountGroup;
use App\Models\AccountType;
use App\Models\Company;
use Database\Seeders\AccountSeeder;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request as Req;

class AccountGroupController extends Controller
{
    public function index()
    {

        //Validating request
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,email']
        ]);

        //Searching request
        $query = AccountGroup::query();
        if (request('search')) {
            $query->where('name', 'LIKE', '%' . request('search') . '%');
        }

        // // Ordering request
        // if (request()->has(['field', 'direction'])) {
        //     $query->orderBy(
        //         request('field'),
        //         request('direction')
        //     );
        // }

        $balances = $query
            ->where('company_id', session('company_id'))
            ->paginate(15)
            ->through(
                function ($accountgroup) {
                    return
                        [
                            'id' => $accountgroup->id,
                        'name' => $accountgroup->name,
                        'type_id' => $accountgroup->type_id,
                        'type_name' => $accountgroup->accountType->name,
                        'company_id' => $accountgroup->company_id,
                        'company_name' => $accountgroup->company->name,
                        'delete' => Account::where('group_id', $accountgroup->id)->first() ? false : true,
                        ];
                }
            );

        return Inertia::render('AccountGroups/Index', [
            // 'data' => AccountGroup::all()
            //     ->where('company_id', session('company_id'))
            //     ->map(function ($accountgroup) {
            //         return [
            //             'id' => $accountgroup->id,
            //             'name' => $accountgroup->name,
            //             'type_id' => $accountgroup->type_id,
            //             'type_name' => $accountgroup->accountType->name,
            //             'company_id' => $accountgroup->company_id,
            //             'company_name' => $accountgroup->company->name,
            //             'delete' => Account::where('group_id', $accountgroup->id)->first() ? false : true,
            //         ];
            //     }),
            'filters' => request()->all(['search', 'field', 'direction']),
            'balances' => $balances,

            // 'exists' => Account::where('company_id', session('company_id'))->first() ? false : true,
            'exists' => AccountGroup::where('company_id', session('company_id'))->first() ? false : true,

            'company' => Company::where('id', session('company_id'))->first(),
            'companies' => Auth::user()->companies,
        ]);
    }

    // public function generate()
    // {
    //     // $exitCode = Artisan::call('db:seed', [
    //     //     '--class' => 'TypeSeeder'
    //     // ]);
    //     // print_r("hi");
    //     // die();
    //     // $this->call([
    //     //     // UserSeeder::class,
    //     //     // PostSeeder::class,
    //     //     // CommentSeeder::class,
    //     //     // AccountSeeder::class,
    //     //     GroupSeeder::class,
    //     // ]);
    //     return GroupSeeder::class;
    //     return Redirect::back()->with('success', 'Account Group deleted.');
    // }

    public function create()
    {
        $types = \App\Models\AccountType::all()->map->only('id', 'name');
        $first = \App\Models\AccountType::all('id', 'name')->first();

        $account_groups[0] = \App\Models\AccountGroup::where('type_id', $first->id)->get();
        $account_group[0] = \App\Models\AccountGroup::where('type_id', $first->id)->first();
        // dd($account_group);

        return Inertia::render('AccountGroups/Create', [
            'types' => $types,
            'first' => $first,
            'account_groups' => $account_groups,
            'account_group' => $account_group,
        ]);
    }

    public function store(Req $request)
    {
        dd($request);
        Request::validate([
            'type' => ['required'],
            'name' => ['required'],
        ]);
        AccountGroup::create([
            'type_id' => Request::input('type'),
            'name' => Request::input('name'),
            'company_id' => session('company_id'),
        ]);

        return Redirect::route('accountgroups')->with('success', 'Account Group created.');
    }

    // public function show(AccountGroup $accountgroup)
    // {
    // }

    public function edit(AccountGroup $accountgroup)
    {
        $types = \App\Models\AccountType::all()->map->only('id', 'name');
        return Inertia::render('AccountGroups/Edit', [
            'accountgroup' => [
                'id' => $accountgroup->id,
                'type_id' => $accountgroup->type_id,
                'name' => $accountgroup->name,
                'company_id' => session('company_id'),
            ],
            'types' => $types,
        ]);
    }

    public function update(AccountGroup $accountgroup)
    {
        Request::validate([
            'type' => ['required'],
            'name' => ['required'],
        ]);
        $accountgroup->type_id = Request::input('type');
        $accountgroup->name = Request::input('name');
        $accountgroup->company_id = session('company_id');
        $accountgroup->save();

        return Redirect::route('accountgroups')->with('success', 'Account Group updated.');
    }

    public function destroy(AccountGroup $accountgroup)
    {
        $accountgroup->delete();
        return Redirect::back()->with('success', 'Account Group deleted.');
    }


    public function account_type_ch(Req $request)
    {
        // dd($request);
        $accountgroups[0] =  AccountGroup::where('type_id', $request->type)->get();
        $accountgroup[0] =  AccountGroup::where('type_id', $request->type)->first();
    
        $types = AccountType::all()->map->only('id', 'name');
        $first = AccountType::where('id', $request->type)->first();


        return Inertia::render('AccountGroups/Create', [
            'types' => $types, 'first' => $first,
            'name' => $request->name,
            'account_groups' => $accountgroups,
            'account_group' => $accountgroup,
        ]);
    }
    public function account_group_ch(Req $request)
    {
        
        // $accountgroups = null;
        // $accountgroup = null;
        $types = AccountType::all()->map->only('id', 'name');
        $first = AccountType::where('id', $request->type)->first();

        // dd($first);

        $accountgroups[0] = AccountGroup::where('type_id', $request->type)->get();
       
        // $accountgroup[0] = AccountGroup::where('type_id', $request->type)->first();
        $accountgroup[0] = AccountGroup::where('id', $request->acc_groups[0]['acc_group'])->first();
        
        for ($i = 0; $i < count($request->acc_groups); $i++) {
            // dd($i);
            // dd($request->acc_groups[$i]['acc_group']);
            if(AccountGroup::where('parent_id', $request->acc_groups[$i]['acc_group'])->first())
            {
                $accountgroups[$i + 1] =  \App\Models\AccountGroup::where('parent_id', $request->acc_groups[$i]['acc_group'])->get();
                $accountgroup[$i + 1] =  \App\Models\AccountGroup::where('parent_id', $request->acc_groups[$i]['acc_group'])->first();
            }
            // else {
                // array_pop($accountgroups);
                // $accountgroups[] = count($accountgroups) - 1;
            // }
        }
    //    dd(count($request->acc_groups));

        //  dd($accountgroups);
        // dd($accountgroups);

        // dd($accountgroup);
        return Inertia::render('AccountGroups/Create', [
            'types' => $types,
            'first' => $first,
            'account_groups' => $accountgroups,
            'account_group' => $accountgroup,
            'name' => $request->name,
        ]);
    }

}

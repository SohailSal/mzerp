<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\Year;
use App\Models\Setting;

use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $data = Company::all();
        return Inertia::render('Company/Index', ['data' => $data]);
    }

    public function create()
    {
        $fiscals = ['June', 'March', 'September', 'December'];
        $fiscal_first = 'June';

        return Inertia::render('Company/Create', [
            'fiscals' => $fiscals, 'fiscal_first' => $fiscal_first
        ]);
    }

    public function store()
    {
        Request::validate([
            'name' => ['required'],
            'address' => ['nullable'],
            'email' => ['nullable'],
            'website' => ['nullable'],
            'phone' => ['nullable'],
            'fiscal' => ['required'],
            'incorp' => ['nullable'],
        ]);
        $comp = Company::create([
            'name' => Request::input('name'),
            'address' => Request::input('address'),
            'email' => Request::input('email'),
            'website' => Request::input('website'),
            'phone' => Request::input('phone'),
            'fiscal' => Request::input('fiscal'),
            'incorp' => Request::input('incorp'),
        ]);

        Setting::create([
            'key' => 'active_company',
            'value' => $comp->id,
            'user_id' => Auth::user()->id,
        ]);

        session(['company_id' => $comp->id]);
        return Redirect::route('companies')->with('success', 'Company created.');
    }

    // public function show(Company $company)
    // {
    // }

    public function edit(Company $company)
    {
        return Inertia::render('Company/Edit', [
            'company' => [
                'id' => $company->id,
                'name' => $company->name,
                'address' => $company->address,
                'email' => $company->email,
                'website' => $company->website,
                'phone' => $company->phone,
                'fiscal' => $company->fiscal,
                'incorp' => $company->incorp,
            ],
        ]);
    }

    public function update(Company $company)
    {
        Request::validate([
            'name' => ['required'],
            'address' => ['nullable'],
            'email' => ['nullable'],
            'website' => ['nullable'],
            'phone' => ['nullable'],
            'fiscal' => ['required'],
            'incorp' => ['nullable'],
        ]);

        $company->name = Request::input('name');
        $company->address = Request::input('address');
        $company->email = Request::input('email');
        $company->web = Request::input('web');
        $company->phone = Request::input('phone');
        $company->fiscal = Request::input('fiscal');
        $company->incorp = Request::input('incorp');
        $company->save();

        return Redirect::route('companies')->with('success', 'Company updated.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return Redirect::back()->with('success', 'Company deleted.');
    }

    //TO CHANGE THE COMPANY IN SESSION FROM DROPDOWN
    public function coch($id)
    {
        $active_co = Setting::where('user_id', Auth::user()->id)->where('key', 'active_company')->first();

        $active_co->value = $id;
        if (Year::where('company_id', $id)->latest()->first()) {
            $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
            $active_yr->value = Year::where('company_id', $id)->latest()->first()->id;
            $active_yr->save();
            session(['year_id' => $active_yr->value]);
        }
        $active_co->save();
        session(['company_id' => $id]);

        return Redirect::back();
    }
}

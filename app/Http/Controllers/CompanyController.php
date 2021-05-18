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
use Egulias\EmailValidator\Warning\Warning;
use Inertia\Inertia;

use App;

class CompanyController extends Controller
{

    // FOR PDF FROM MZAUDIT --------
    public function pd()
    {
        $a = "hello world";
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('pdf', compact('a'));
        return $pdf->stream('v.pdf');
    }
    // FOR PDF FROM MZAUDIT --------

    public function index()
    {
        return Inertia::render('Company/Index', [
            'data' => Company::all()
                ->map(function ($comp) {
                    return [
                        'id' => $comp->id,
                        'name' => $comp->name,
                        'address' => $comp->address,
                        'email' => $comp->email,
                        'website' => $comp->web,
                        'phone' => $comp->phone,
                        'fiscal' => $comp->fiscal,
                        'incorp' => $comp->incorp,
                        'delete' => Year::where('company_id', $comp->id)->first() ? false : true,
                    ];
                }),
        ]);
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
            'web' => ['nullable'],
            'phone' => ['nullable'],
            'fiscal' => ['required'],
            'incorp' => ['nullable'],
        ]);
        $comp = Company::create([
            'name' => Request::input('name'),
            'address' => Request::input('address'),
            'email' => Request::input('email'),
            'web' => Request::input('website'),
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
        session(['year_id' => null]);

        return Redirect::route('years.create')->with('success', 'Company created. Please create Year for your to Company.');
    }

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
            'web' => ['nullable'],
            'phone' => ['nullable'],
            'fiscal' => ['required'],
            'incorp' => ['nullable'],
        ]);

        $company->name = Request::input('name');
        $company->address = Request::input('address');
        $company->email = Request::input('email');
        $company->web = Request::input('website');
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

        $active_co->save();
        session(['company_id' => $id]);

        if (Year::where('company_id', $id)->latest()->first()) {
            $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();
            $active_yr->value = Year::where('company_id', $id)->latest()->first()->id;
            $active_yr->save();
            session(['year_id' => $active_yr->value]);
            // $active_co->save();
            // session(['company_id' => $id]);
            return Redirect::back();
        } else {
            session(['year_id' => null]);
            return Redirect::route('years.create')->with('success', 'YEAR NOT FOUND. Please create an Year for selected Company.');
        }
    }
}

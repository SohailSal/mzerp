<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\Year;
use App\Models\DocumentType;
use Inertia\Inertia;

class YearController extends Controller
{
    public function index()
    {
        return Inertia::render('Years/Index', [
            'data' => Year::all()
                ->map(function ($year){
                    return [
                        'id' => $year->id,
                        'begin' => $year->begin,
                        'end' => $year->end,
                        'company_name' => $year->company->name,
                        'company_id' => $year->company_id,
                    ];
                }), 
                ]);
    }

    public function create()
    {
        $companies = \App\Models\Company::all()->map->only('id','name');
        $comp_first = \App\Models\Company::all('id','name')->first();

        return Inertia::render('Years/Create',[
        'companies' => $companies, 'comp_first' => $comp_first]);
    }

    public function store()
    {
        Request::validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date'],
            'company_id' => ['required'],
        ]);
        Year::create([
            'begin' => Request::input('begin'),
            'end' => Request::input('end'),
            'company_id' => Request::input('company_id'),
        ]);
        return Redirect::route('years')->with('success', 'Year created.');
    }

    public function show(AccountGroup $accountgroup)
    {
    }

    public function edit(Year $year)
    {
        $companies = \App\Models\Company::all()->map->only('id','name');
        return Inertia::render('Years/Edit', [
            'year' => [
                'id' => $year->id,
                'begin' => $year->begin,
                'end' => $year->end,
                'company_id' => $year->company_id,
            ],
            'companies' => $companies,
        ]);
    }

    public function update(Year $year)
    {
        Request::validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date'],
            'company_id' => ['required'],
        ]);
        $year->begin = Request::input('begin');
        $year->end = Request::input('end');
        $year->company_id = Request::input('company_id');
        $year->save();

        return Redirect::route('years')->with('success', 'Year updated.');
    }

    public function destroy(Year $year)
    {
        $year->delete();
        return Redirect::back()->with('success', 'Year deleted.');
    }
}
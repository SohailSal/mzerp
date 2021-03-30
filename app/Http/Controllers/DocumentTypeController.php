<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\DocumentType;
use Inertia\Inertia;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $data = DocumentType::all();
        return Inertia::render('DocumentTypes/Index', ['data' => $data]);
    }

    public function create()
    {
        $companies = \App\Models\Company::all()->map->only('id','name');
        $comp_first = \App\Models\Company::all('id','name')->first();

        return Inertia::render('DocumentTypes/Create',[
            'companies' => $companies, 'comp_first' => $comp_first]);
        }

    public function store()
    {
        Request::validate([
            'name' => ['required'],
            'prefix' => ['required'],
            'company' => ['required'],
        ]);

        DocumentType::create([
            'name' => Request::input('name'),
            'prefix' => Request::input('prefix'),
            'company_id' => Request::input('company'),
        ]);

        return Redirect::route('documenttypes')->with('success', 'Voucher created.');
    }

    public function show(DocumentType $documenttype)
    {
    }

    public function edit(DocumentType $documenttype)
    {
        $companies = \App\Models\Company::all()->map->only('id','name');
        return Inertia::render('DocumentTypes/Edit', [
            'documenttype' => [
                'id' => $documenttype->id,
                'name' => $documenttype->name,
                'prefix' => $documenttype->prefix,
                'company_id' => $documenttype->company_id,
            ],
            'companies' => $companies,
        ]);
    }

    public function update(DocumentType $documenttype)
    {
        Request::validate([
            'name' => ['required'],
            'prefix' => ['required'],
            'company' => ['required'],
        ]);
        $documenttype->name = Request::input('name');
        $documenttype->prefix = Request::input('prefix');
        $documenttype->company_id = Request::input('company');
        $documenttype->save();

        return Redirect::route('documenttypes')->with('success', 'Voucher updated.');
    }

    public function destroy(DocumentType $documenttype)
    {
        $documenttype->delete();
        return Redirect::back()->with('success', 'Voucher deleted.');
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Models\DocumentType;
use App\Models\Company;
use Inertia\Inertia;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $data = DocumentType::all()
            ->where('company_id', session('company_id'));
        return Inertia::render('DocumentTypes/Index', [
            'data' => $data,

            'companies' => Company::all()
                ->map(
                    function ($com) {
                        return [
                            'id' => $com->id,
                            'name' => $com->name,
                        ];
                    }
                ),

        ]);
    }

    public function create()
    {
        return Inertia::render('DocumentTypes/Create');
    }

    public function store()
    {
        Request::validate([
            'name' => ['required'],
        ]);
        //Generating Prefix from Voucher/Document Type name --START--
        $prefix = Request::input('name');
        $name = str_replace(["(", ")"], "", $prefix);
        $words = preg_split("/[\s,_-]+/", $name);
        $acronym = "";
        $count = 1;
        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        //Generating Prefix from Voucher/Document Type name --END--

        DocumentType::create([
            'name' => Request::input('name'),
            'prefix' => $acronym,
            'company_id' => session('company_id'),
        ]);

        return Redirect::route('documenttypes')->with('success', 'Voucher created.');
    }

    // public function show(DocumentType $documenttype)
    // {
    // }

    public function edit(DocumentType $documenttype)
    {
        return Inertia::render('DocumentTypes/Edit', [
            'documenttype' => [
                'id' => $documenttype->id,
                'name' => $documenttype->name,
                'prefix' => $documenttype->prefix,
            ],
        ]);
    }

    public function update(DocumentType $documenttype)
    {
        Request::validate([
            'name' => ['required'],
        ]);

        //Generating Prefix from Voucher/Document Type name --START--
        $prefix = Request::input('name');
        $name = str_replace(["(", ")"], "", $prefix);
        $words = preg_split("/[\s,_-]+/", $name);
        $acronym = "";
        $count = 1;
        foreach ($words as $w) {
            $acronym .= $w[0];
        }
        //Generating Prefix from Voucher/Document Type name --END--

        $documenttype->name = Request::input('name');
        $documenttype->prefix = $acronym;
        $documenttype->company_id = session('company_id');
        $documenttype->save();

        return Redirect::route('documenttypes')->with('success', 'Voucher updated.');
    }

    public function destroy(DocumentType $documenttype)
    {
        $documenttype->delete();
        return Redirect::back()->with('success', 'Voucher deleted.');
    }
}

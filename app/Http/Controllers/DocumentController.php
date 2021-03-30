<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Entry;
use Inertia\Inertia;
use Carbon\Carbon;

class DocumentController extends Controller
{
    public function index()
    {
        return Inertia::render('Documents/Index', [
            'data' => Document::all()
                ->map(function ($document){
                    return [
                        'id' => $document->id,
                        'ref' => $document->ref,
                        'description' => $document->description,
                        'type_id' => $document->type_id,
                        'company_id' => $document->company_id,
                        'year_id' => $document->year_id,
                    ];
                }), 
        ]);
    }

    public function create()
    {
        $accounts = \App\Models\Account::all()->map->only('id','name');
        $account_first = \App\Models\Account::all('id','name')->first();

        $companies = \App\Models\Company::all()->map->only('id','name');
        $comp_first = \App\Models\Company::all('id','name')->first();

        $years = \App\Models\Year::all()->map->only('id','begin', 'end');
        $year_first = \App\Models\Year::all('id','begin', 'end')->first();

        $doc_type_first = \App\Models\DocumentType::all('id','name')->first();
//FUNCTION FOR REFERENCE GENERATOR       
            // $str = "My name is Muhammad Haris";
            // $stri = explode(" ",$str);
            // print_r($str);
            // dd($stri);
            // $second;
            // foreach($word in $stri){
            //     $second = explode("", $word);
            // }

        return Inertia::render('Documents/Create',[ 
            'accounts' => $accounts, 'account_first' => $account_first,
            'companies' => $companies, 'comp_first' => $comp_first,
            'years' => $years, 'year_first' => $year_first,
            'doc_type_first' => $doc_type_first,
            'doc_types' => DocumentType::all()
                ->map(function ($doc_type){
                    return [
                    'id' => $doc_type->id,
                    'name' => $doc_type->name,
                    'ref' => $doc_type->prefix. "/" .$doc_type->timestamps,
                ];
            }),            
            ]);
        }

    public function store(Req $request)
    {
        Request::validate([
            'type_id' => ['required'],
            'year_id' => ['required'],
            'company_id' => ['required'],
            'ref' => ['required'],
            'description' => ['required'],
            'date' => ['required', 'date'],

            'entries.*.account_id' => ['required'],
            'entries.*.debit' => ['required'],
            'entries.*.credit' => ['required'],
        ]);

        DB::transaction(function() use ($request){
        
            $date = new Carbon($request->date);
            try{
                $doc = Document::create([
                    'type_id' => Request::input('type_id'),
                    'company_id' => Request::input('company_id'),
                    'description' => Request::input('description'),
                    'ref' => Request::input('ref'),
                    'date' => $date->format('Y-m-d'),
                    'year_id' => Request::input('year_id'),
                ]);

                $data = $request->entries;
                foreach($data as $entry){
                    Entry::create([
                        'company_id' => $doc->company_id,
                        'account_id' => $entry['account_id'],
                        'year_id' => $doc->year_id,
                        'document_id' => $doc->id,
                        'debit' => $entry['debit'],
                        'credit' => $entry['credit'],
                    ]);
                }
            }catch(Exception $e){
                return $e;
            }
        });

        return Redirect::route('documents')->with('success', 'Transaction created.');
    }

    public function show(AccountGroup $accountgroup)
    {
    }

    public function edit(AccountGroup $accountgroup)
    {
        // $types = \App\Models\AccountType::all()->map->only('id','name');
        // $companies = \App\Models\Company::all()->map->only('id','name');
        // return Inertia::render('AccountGroups/Edit', [
        //     'accountgroup' => [
        //         'id' => $accountgroup->id,
        //         'type_id' => $accountgroup->type_id,
        //         'name' => $accountgroup->name,
        //         'company_id' => $accountgroup->company_id,
        //     ],
        //     'types' => $types,
        //     'companies' => $companies,
        // ]);
    }

    public function update(AccountGroup $accountgroup)
    {
        // Request::validate([
        //     'type' => ['required'],
        //     'name' => ['required'],
        //     'company' => ['required'],

        // ]);

        // $accountgroup->type_id = Request::input('type');
        // $accountgroup->name = Request::input('name');
        // $accountgroup->company_id = Request::input('company');
        // $accountgroup->save();

        // return Redirect::route('accountgroups')->with('success', 'Account Group updated.');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return Redirect::back()->with('success', 'Transaction deleted.');
    }
}
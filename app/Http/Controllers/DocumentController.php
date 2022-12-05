<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Account;
use App\Models\Company;
use App\Models\Year;
use App\Models\Entry;
use Inertia\Inertia;
use Carbon\Carbon;
use Exception;
use PhpParser\Comment\Doc;

class DocumentController extends Controller
{
    public function index()
    {
        //Validating request
        request()->validate([
            'direction' => ['in:asc,desc'],
            'field' => ['in:name,email']
        ]);

        $acc = Account::where('company_id', session('company_id'))->first();
        $doc_ty = DocumentType::where('company_id', session('company_id'))->first();

        $yearclosed = year::where('id', session('year_id'))->where('closed', 0)->first();

        if ($acc && $doc_ty) {

            $query = Document::query();

            if (request('search')) {
                // $query->where('description', 'LIKE', '%' . request('search') . '%');
                $query->where(function ($query) {
                    $query
                        ->where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('description', 'LIKE', '%' . request('search') . '%');
                })->orWhere(function($query) {
                    $query
                        ->where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('ref', 'LIKE', '%' . request('search') . '%');
                })->orWhere(function($query) {
                    $query
                        ->where('company_id', session('company_id'))
                        ->where('year_id', session('year_id'))
                        ->where('date', 'LIKE', '%' . request('search') . '%');
                });
            }
            //Ordering request
            if (request()->has(['field', 'direction'])) {
                $query->orderBy(
                    request('field'),
                    request('direction')
                );
            }

            $docs = $query
                ->where('company_id', session('company_id'))
                ->where('year_id', session('year_id'))
                ->paginate(10)
                ->withQueryString()
                ->through(
                    fn ($document) =>
                    [
                        'id' => $document->id,
                        'ref' => $document->ref,

                        $date = new Carbon($document->date),
                        'date' => $date->format('M d, Y'),
                        'description' => $document->description,
                        'type_id' => $document->type_id,
                        'company_id' => session('company_id'),
                        'year_id' => session('year_id'),
                        'delete' => Entry::where('document_id', $document->id)->first() ? false : true,
                    ]
                );


            return Inertia::render(
                'Documents/Index',
                [
                    'can' => [
                        'edit' => auth()->user()->can('edit'),
                        'create' => auth()->user()->can('create'),
                        'delete' => auth()->user()->can('delete'),
                        'read' => auth()->user()->can('read'),
                    ],
                    'data' => $docs,
                    'yearclosed' => $yearclosed,
                    'filters' => request()->all(['search', 'field', 'direction']),
                    'company' => Company::where('id', session('company_id'))->first(),
                    'companies' => auth()->user()->companies,
                    'years' => Year::all()
                        ->where('company_id', session('company_id'))
                        ->map(function ($year) {
                            $begin = new Carbon($year->begin);
                            $end = new Carbon($year->end);

                            return [
                                'id' => $year->id,
                                'name' => $begin->format('M d, Y') . ' - ' . $end->format('M d, Y'),
                            ];
                        },
                    ),
                ]
            );
        } elseif ($acc) {
            return Redirect::route('documenttypes')->with('warning', 'VOUCHER NOT FOUND, Please create voucher first.');
        } else {
            return Redirect::route('accounts')->with('warning', 'ACCOUNT NOT FOUND, Please create an account first.');
        }
    }

    public function create()
    {
        $account_first = Account::all()->where('company_id', session('company_id'))->map->only('id', 'name')->first();
        $doc_type_first = DocumentType::all()->where('company_id', session('company_id'))->map->only('id', 'name')->first();
        $accounts = Account::where('company_id', session('company_id'))
            ->get()
            ->map(function ($acc) {
                return [
                    "id" => $acc->id,
                    "number" => $acc->number,
                    "name" => $acc->number . ' - ' . $acc->name . ' - ' . $acc->accountGroup->name,
                    "company_id" => $acc->company_id,
                    "group_id" => $acc->group_id,
                    // "credit" => $acc->credit,
                    // "nameNum" => $acc->number . ' - ' . $acc->name . ' - ' . $acc->accountGroup->name,
                ];
            });

        if ($account_first && $doc_type_first) {
            $date_range = Year::where('id', session('year_id'))->first();

            return Inertia::render('Documents/Create', [
                'min_start' => $date_range->begin,
                'max_end' => $date_range->end,
                'accounts' => $accounts,
                'account_first' => $account_first,
                'doc_type_first' => $doc_type_first,
                'doc_types' => DocumentType::where('company_id', session('company_id'))->get(),
            ]);
        } else {
            if ($doc_type_first) {
                return Redirect::route('accounts.create')->with('success', 'ACCOUNTS NOT FOUND, Please create an account first');
            } else {
                return Redirect::route('documenttypes.create')->with('success', 'VOUCHER NOT FOUND, Please create a voucher first');
            }
        }
    }

    public function store(Req $request)
    {
        Request::validate([
            'type_id' => ['required'],
            'description' => ['required'],
            'date' => ['required', 'date'],

            'entries.*.account_id' => ['required'],
            'entries.*.debit' => ['required'],
            'entries.*.credit' => ['required'],
        ]);


        DB::transaction(function () use ($request) {
            $date = new Carbon($request->date);
            try {

                $prefix = DocumentType::where('company_id', session('company_id'))->where('id', $request->type_id)->first()->prefix;
                $date = $date->format('Y-m-d');
                $ref_date_parts = explode("-", $date);

                //serial number
                $latest_doc = Document::where('ref', 'LIKE', $prefix . '%')->where('year_id', session('year_id'))->latest('id')->first();
                if ($latest_doc) {
                    $pre_refe = $latest_doc->ref;
                    $pre_ref_serial = explode("/", $pre_refe);
                    $serial = (int)$pre_ref_serial[3] + (int)1;
                } else {
                    $serial = 1;
                }
                //serial number

                // $reference = $prefix . "/" . $ref_date_parts[0] . "/" . $ref_date_parts[1] . "/" . $ref_date_parts[2];
                // $reference = $prefix . "/" . $ref_date_parts[0] . "/" . $ref_date_parts[1] . "/" . $ref_date_parts[2] . "/" . $serial;
                $reference = $prefix . "/" . $ref_date_parts[0] . "/" . $ref_date_parts[1] . "/" . $serial;

                $doc = Document::create([
                    'type_id' => Request::input('type_id')['id'],
                    'company_id' => session('company_id'),
                    'description' => Request::input('description'),
                    'ref' => $reference,
                    'date' => $date,
                    'year_id' => session('year_id'),
                ]);
                $data = $request->entries;
                foreach ($data as $entry) {
                    Entry::create([
                        'company_id' => $doc->company_id,
                        'account_id' => $entry['account_id']['id'],
                        'year_id' => $doc->year_id,
                        'document_id' => $doc->id,
                        'debit' => $entry['debit'],
                        'credit' => $entry['credit'],
                    ]);
                }
            } catch (Exception $e) {
                return back()->with('error', $e);
                return $e;
            }
        });

        return Redirect::route('documents')->with('success', 'Transaction created.');
    }

    public function edit(Document $document)
    {
        $accounts = Account::where('company_id', session('company_id'))->get()->map(function ($acc) {
            return [
                "id" => $acc->id,
                "number" => $acc->number,
                "name" => $acc->number . ' - ' . $acc->name . ' - ' . $acc->accountGroup->name,
                "company_id" => $acc->company_id,
                "group_id" => $acc->group_id,
                // "nameNum" => $acc->name,
                // "credit" => $acc->credit,
            ];
        });

        $doc_types = DocumentType::all()->map->only('id', 'name');
        $doc = Document::all()->where('id', $document->id)->map->only('id', 'ref')->first();

        $ref = Entry::all()->where('document_id', $document->id);
        $entrie = Entry::all()->where('document_id', $document->id)
            ->map(function ($entry) {
                return [
                    "id" => $entry->id,
                    "company_id" => $entry->company_id,
                    "document_id" => $entry->document_id,
                    "account_id" => $entry->account,
                    "year_id" => $entry->year_id,
                    "debit" => $entry->debit,
                    "credit" => $entry->credit,
                ];
            });

        // $ref = Document::all()->where('document_id', $document->id);
        // // $entrie = Document::all()->where('document_id', $document->id);
        // $entrie = Document::all()->where('document_id', 1);
        // // ->toArray();

        $i = 0;
        foreach ($entrie as $entry) {
            $entries[$i] = $entry;
            $i++;
        }

        $date_range = Year::where('id', session('year_id'))->first();

        return Inertia::render(
            'Documents/Edit',
            [
                'document' =>
                // $document,
                [
                    'id' => $document->id,
                    'ref' => $document->ref,
                    'date' => $document->date,
                    'description' => $document->description,
                    'type_id' => $document->type_id,
                    'type_name' => $document->documentType->name,
                    'entries' => $document->entries,
                ],
                'accounts' => $accounts,
                'doc_types' => $doc_types,
                'entriess' => $entries,
                'min_start' => $date_range->begin,
                'max_end' => $date_range->end,
                'can' => [
                        'edit' => auth()->user()->can('edit'),
                        'create' => auth()->user()->can('create'),
                        'delete' => auth()->user()->can('delete'),
                        'read' => auth()->user()->can('read'),
                    ],
            ]
        );
    }

    // public function update(Document $document)
    public function update(Req $request, Document $document
    // Entry $entry
    )
    {
        Request::validate([
            'type_id' => ['required'],
            'description' => ['required'],
            'date' => ['required', 'date'],

            'entries.*.account_id' => ['required'],
            'entries.*.debit' => ['required'],
            'entries.*.credit' => ['required'],
        ]);

        $preEntries = Entry::all()->where('document_id', $document->id);


        DB::transaction(function () use ($request, $document, $preEntries) {
            $date = new Carbon($request->date);
            try {

                foreach ($preEntries as $preEntry) {
                    $preEntry->delete();
                }
                // $prefix = \App\Models\DocumentType::where('id', $request->type_id)->first()->prefix;
                // $date = $date->format('Y-m-d');
                // $ref_date_parts = explode("-", $date);
                // $reference = $prefix . "/" . $ref_date_parts[0] . "/" . $ref_date_parts[1] . "/" . $ref_date_parts[2];

                // $doc = Document::create([
                //     'type_id' => Request::input('type_id'),
                //     'company_id' => session('company_id'),
                //     'description' => Request::input('description'),
                //     'ref' => $reference,
                //     'date' => $date,
                //     'year_id' => session('year_id'),
                // ]);
                $date = new carbon(Request::input('date'));

                $document->description = Request::input('description');
                $document->date = $date->format('Y-m-d');

                $document->save();

                $data = $request->entries;
                foreach ($data as $entry) {
                    Entry::create([
                        // 'company_id' => $document->company_id,
                        'company_id' => session('company_id'),
                        'account_id' => $entry['account_id']['id'],
                        'year_id' => session('year_id'),
                        // 'year_id' => $document->year_id,
                        'document_id' => $document->id,
                        'debit' => $entry['debit'],
                        'credit' => $entry['credit'],
                    ]);
                }
            } catch (Exception $e) {
                return $e;
            }
        });


        // $data = $request->entries;
        // foreach ($data as $entry) {
        // Entry::create([
        // 'company_id' => $doc->company_id,
        // 'year_id' => $doc->year_id,
        // 'document_id' => $doc->id,

        // $entry->account_id = $entry->account_id;
        // $entry->debit = $entry->debit;
        // $entry->credit = $entry->credit;

        // $entry->save;

        // 'account_id' => $entry['account_id'],
        // 'debit' => $entry['debit'],
        // 'credit' => $entry['credit'],
        // ]);
        // }

        return Redirect::route('documents')->with('success', 'Transaction updated.');
    }


    public function destroy(Document $document)
    {
        $document->delete();
        return Redirect::back()->with('success', 'Transaction deleted.');
    }

    public function downloadFile()
    {
        // dd(public_path() . "/sales_trial.xlsx");
        return response()->download(public_path() . "/sales_trial.xlsx");
    }

      public function Accountpdf()
    {
        $accounts = Account::where('company_id', session('company_id'))->get()
        ->map(function ($account){
            return[
                'id' => $account->id,
                'number' => $account->number,
                'name' => $account->name . " - " . $account->accountGroup->name,
                // 'acc_grp' => $account->accountGroup->name,
            ];
        });
        // dd($accounts);
        if ($accounts) {

            $year = Year::where('company_id', session('company_id'))
            ->where('id', session('year_id'))->first();
            $end = $year->end ? new Carbon($year->end) : null;

            $names = str_replace(["&"], "&amp;", $year->company->name);
            $endDate = $end->format("F j Y");
        //     //   $a = "hello world";

        //     $confirmation = BankConfirmation::where('company_id', session('company_id'))->get()
        //     ->map(function ($confirm){
        //         return[
        //             'id' => $confirm->id,
        //             'branch' => $confirm->bankBranch->bank->name . " - " . $confirm->bankBranch->address,
        //         ];
        //     });
        //     // dd($confirmation);


        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('accountpdf', compact('names', 'endDate' ,'accounts'));
        return $pdf->stream($names ." ".'Account.pdf');
        }else{
            return Redirect::route('accounts.create')->with('success', 'Create Account first.');

        }
    }
}

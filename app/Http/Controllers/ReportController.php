<?php

namespace App\Http\Controllers;


use App;
use App\Models\Account;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Year;
use App\Models\Setting;
use App\Models\Company;
use App\Models\Document;
use App\Models\Entry;
use App\Models\DocumentType;
use Inertia\Inertia;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;
// use Crypt;
use Illuminate\Support\Facades\Crypt;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        $accounts = \App\Models\Account::all()->map->only('id', 'name');
        $account_first = \App\Models\Account::all('id', 'name')->first();
        return Inertia::render('Reports/Index', [
            'account_first' => $account_first,
            'accounts' => $accounts,
            'companies' => Company::all()
                ->map(function ($com) {
                    return [
                        'id' => $com->id,
                        'name' => $com->name,
                    ];
                }),
        ]);
    }


    // FOR PDF GENERATION -------------------------- --------
    public function pd()
    {
        $data['entry_obj'] = Entry::all()->where('company_id', session('company_id'))->where('year_id', session('year_id'));

        $i = 0;
        foreach ($data['entry_obj'] as $entry) {
            if ($entry) {
                $data['entries'][$i] = $entry;
                $i++;
            }
        }
        $data['doc'] = Document::all()->where('id', $data['entries'][0]->document_id)->first();
        $data['doc_type'] = DocumentType::all()->where('id', $data['doc']->type_id)->first();
        // $a = Company::where('id', session('company_id'))->first();
        $pdf = App::make('dompdf.wrapper');
        // $pdf->loadView('pdf', compact('a'));
        $pdf->loadView('pdf', $data);
        return $pdf->stream('v.pdf');
    }


    // FOR trialbalance GENERATION -------------------------- --------
    public function trialbalance()
    {
        $data['accounts'] = Account::where('company_id', session('company_id'))->get();
        $tb = App::make('dompdf.wrapper');
        // $pdf->loadView('pdf', compact('a'));
        $tb->loadView('trialbalance', $data);
        return $tb->stream('v.pdf');
    }

    public function bs()
    {
        // $pdf = PDF::loadView('balanceSheet');
        $bs = App::make('dompdf.wrapper');
        $bs->loadView('balanceSheet');
        return $bs->stream('bs.pdf');
    }

    public function pl()
    {
        $pl = App::make('dompdf.wrapper');
        $pl->loadView('profitOrLoss');
        return $pl->stream('pl.pdf');
    }

    public function ledger($id)
    {
        $year = Year::where('company_id', session('company_id'))->where('enabled', 1)->first();
        $acc = Account::where('company_id', session('company_id'))->where('id', Crypt::decrypt($id))->first();

        $entries = DB::table('documents')
            ->join('entries', 'documents.id', '=', 'entries.document_id')
            ->whereDate('documents.date', '>=', $year->begin)
            ->whereDate('documents.date', '<=', $year->end)
            ->where('documents.company_id', session('company_id'))
            ->select('entries.account_id', 'entries.debit', 'entries.credit', 'documents.ref', 'documents.date', 'documents.description')
            ->where('entries.account_id', '=', Crypt::decrypt($id))
            ->get();

        $previous = DB::table('documents')
            ->join('entries', 'documents.id', '=', 'entries.document_id')
            ->whereDate('documents.date', '<', $year->begin)
            ->where('documents.company_id', session('company_id'))
            ->select('entries.debit', 'entries.credit')
            ->where('entries.account_id', '=', Crypt::decrypt($id))
            ->get();

        //        $entries = Entry::where('account_id',Crypt::decrypt($id))->where('company_id',session('company_id'))->get();
        $period = "From " . strval($year->begin) . " to " . strval($year->end);
        $pdf = PDF::loadView('led', compact('entries', 'previous', 'year', 'period', 'acc'));
        return $pdf->stream($acc->name . ' - ' . $acc->accountGroup->name . '.pdf');
    }

    public function create()
    {
        return Inertia::render('Years/Create');
    }

    public function store(Req $request)
    {
        Request::validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $year = Year::create([
            'begin' => $request->begin,
            'end' => $request->end,
            'company_id' => session('company_id'),
        ]);

        Setting::create([
            'key' => 'active_year',
            'value' => $year->id,
            'user_id' => Auth::user()->id,
        ]);

        session(['year_id' => $year->id]);
        return Redirect::route('years')->with('success', 'Year created.');
    }

    public function edit(Year $year)
    {
        return Inertia::render('Years/Edit', [
            'year' => [
                'id' => $year->id,
                'begin' => $year->begin,
                'end' => $year->end,
                'company_id' => session('company_id'),
            ],
        ]);
    }

    public function update(Year $year)
    {
        Request::validate([
            'begin' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $begin = new carbon(Request::input('begin'));
        $end = new carbon(Request::input('end'));

        $year->begin = $begin->format('Y-m-d');
        $year->end = $end->format('Y-m-d');
        $year->company_id = session('company_id');
        $year->save();

        return Redirect::route('years')->with('success', 'Year updated.');
    }

    public function destroy(Year $year)
    {
        // if (Document::where('year_id', $year->id)->first()) {
        //     return Redirect::back()->with('success', 'Can\'t DELETE this Year.');
        // } else {
        $year->delete();
        if (Year::where('company_id', session('company_id'))->first()) {
            return Redirect::back()->with('success', 'Year deleted.');
        } else {
            session(['year_id' => null]);
            return Redirect::route('years.create')->with('success', 'YEAR NOT FOUND. Please create an Year for selected Company.');
        }
        // }
    }

    public function yrch($id)
    {

        $active_yr = Setting::where('user_id', Auth::user()->id)->where('key', 'active_year')->first();

        $active_yr->value = $id;
        $active_yr->save();
        session(['year_id' => $id]);

        return Redirect::back();
    }
}

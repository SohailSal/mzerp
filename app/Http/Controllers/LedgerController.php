<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Entry;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use App;
use App\Models\Account;
use Illuminate\Http\Request as Req;
use Illuminate\Support\Facades\Auth;
use App\Models\Year;
use App\Models\Setting;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use PDF;

use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {

        // $transaction = Document::where('company_id', session('company_id'))->where('year_id', session('year_id'))->first();
        // if($transaction){

        $account_first = ['id' => 0];
        $account_first = Account::where('company_id', session('company_id'))->first();
        $accounts = Account::where('company_id', session('company_id'))->get();
        // ->map->only('id', 'name');
        if ($request->account_id) {
            $account_first = Account::all()->where('company_id', session('company_id'))->where('id', $request->account_id)->map->only('id', 'name')->first();
        }
        //  else {
        //     $account_first = \App\Models\Account::all()->where('company_id', session('company_id'))->map->only('id', 'name')->first();
        // }

        if ($request) {
            $start = new Carbon($request->input('date_start'));
            $end = new Carbon($request->input('date_end'));
            $account = $request->input('account_id');

            $start = $start->format('Y-m-d');
            $end = $end->format('Y-m-d');

            $data['start'] = $start;

            $data['entries'] = DB::table('documents')
                ->join('entries', 'documents.id', '=', 'entries.document_id')
                ->whereDate('documents.date', '>=', $start)
                ->whereDate('documents.date', '<=', $end)
                ->where('documents.company_id', session('company_id'))
                ->select('entries.account_id', 'entries.debit', 'entries.credit', 'documents.ref', 'documents.date', 'documents.description')
                ->where('entries.account_id', '=', $account)
                ->get();

            $data['previous'] = DB::table('documents')
                ->join('entries', 'documents.id', '=', 'entries.document_id')
                ->whereDate('documents.date', '<', $start)
                ->where('documents.company_id', session('company_id'))
                ->select('entries.debit', 'entries.credit')
                ->where('entries.account_id', '=', $account)
                ->get();

            $data['acc'] = Account::where('id', '=', $account)->where('company_id', session('company_id'))->first();
            // $data['period'] = "From " . strval($start) . " to " . strval($end);


            //----------------------------------------------- WORKING OF VUE ---------------------------------------------
            // $fmt = new NumberFormatter('en_GB', NumberFormatter::CURRENCY);
            // $amt = new NumberFormatter('en_GB', NumberFormatter::SPELLOUT);
            // $fmt->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
            // $fmt->setSymbol(NumberFormatter::CURRENCY_SYMBOL, '');
            $prebal = 0;
            $data['lastbalance'] = 0;
            $ite = 0;
            $data['debits'] = 0;
            $data['credits'] = 0;
            if ($data['previous']->count()) {
                foreach ($data['previous'] as $value) {
                    $prebal = $data['lastbalance'] + floatval($value->debit) - floatval($value->credit);
                    $data['lastbalance'] = $prebal;
                    $ite++;
                }
            }
            $balance = [];
            $ite = 0;
            foreach ($data['entries'] as $value) {
                $balance[$ite] = $data['lastbalance'] + floatval($value->debit) - floatval($value->credit);
                $data['lastbalance'] = $balance[$ite];
                $ite++;
            }
            // $dt = \Carbon\Carbon::now(new DateTimeZone('Asia/Karachi'))->format('M d, Y - h:m a');
        }


        // @foreach ($entries as $entry)
        foreach ($data['entries'] as $entry) {
            //     {{$entry->ref}}
            //     {{$entry->date}}
            //     {{$entry->description}}
            //     {{str_replace(['Rs.','.00'],'',$fmt->formatCurrency($entry->debit,'Rs.'))}}
            //     {{str_replace(['Rs.','.00'],'',$fmt->formatCurrency($entry->credit,'Rs.'))}}
            //     {{str_replace(['Rs.','.00'],'',$fmt->formatCurrency($balance[$loop->index],'Rs.'))}}

            $data['debits'] = $data['debits'] + $entry->debit;
            $data['credits'] = $data['credits'] + $entry->credit;
        }

        return Inertia::render('Ledgers/Index', [
            'company' => Company::where('id', session("company_id"))->first(),
            'companies' => Auth::user()->companies,
            'account_first' => $account_first,
            'accounts' => $accounts,
            'date_start' => $start,
            'date_end' => $end,
            'entries' => $data['entries'],
            'debits' => $data['debits'],
            'credits' => $data['credits'],
            'balance' => $balance,
            'prebal' => $prebal,
        ]);
    // }else{
    //     return Redirect::route('documents')->with('warning', 'Transaction NOT FOUND, Please create an transaction first.');
    // }
    }


    // To generate ledger report in pdf --------------------------- LEDGER ---------------
    public function rangeLedger(Req $request, $id)
    {
        $start = new Carbon($request->input('date_start'));
        $end = new Carbon($request->input('date_end'));
        // $account = $request->input('account_id');
        $account = $id;

        $start = $start->format('Y-m-d');
        $end = $end->format('Y-m-d');

        $entries = DB::table('documents')
            ->join('entries', 'documents.id', '=', 'entries.document_id')
            ->whereDate('documents.date', '>=', $start)
            ->whereDate('documents.date', '<=', $end)
            ->where('documents.company_id', session('company_id'))
            ->select('entries.account_id', 'entries.debit', 'entries.credit', 'documents.ref', 'documents.date', 'documents.description')
            ->where('entries.account_id', '=', $account)
            ->get();

        $previous = DB::table('documents')
            ->join('entries', 'documents.id', '=', 'entries.document_id')
            ->whereDate('documents.date', '<', $start)
            ->where('documents.company_id', session('company_id'))
            ->select('entries.debit', 'entries.credit')
            ->where('entries.account_id', '=', $account)
            ->get();

        $acc = Account::where('id', '=', $account)->where('company_id', session('company_id'))->first();
        $period = "From " . strval($start) . " to " . strval($end);


        // $data['start'] = $start;
        $data['start'] = $start;

        $data['entries'] = DB::table('documents')
            ->join('entries', 'documents.id', '=', 'entries.document_id')
            ->whereDate('documents.date', '>=', $start)
            ->whereDate('documents.date', '<=', $end)
            ->where('documents.company_id', session('company_id'))
            ->select('entries.account_id', 'entries.debit', 'entries.credit', 'documents.ref', 'documents.date', 'documents.description')
            ->where('entries.account_id', '=', $account)
            ->get();

        $data['previous'] = DB::table('documents')
            ->join('entries', 'documents.id', '=', 'entries.document_id')
            ->whereDate('documents.date', '<', $start)
            ->where('documents.company_id', session('company_id'))
            ->select('entries.debit', 'entries.credit')
            ->where('entries.account_id', '=', $account)
            ->get();

        // dd(session('company_id'));
        // dd($account);
        $data['acc'] = Account::where('id', '=', $account)->where('company_id', session('company_id'))->first();
        $data['period'] = "From " . strval($start) . " to " . strval($end);
        // $pdf = PDF::loadView('range', compact('entries', 'previous', 'acc', 'period', 'start'));
        // dd($data['entries']);
        // dd($acc->accountGroup->name);

        // dd($data['acc']);
        $a = "hello world";
        $pdf = App::make('dompdf.wrapper');
        // $pdf->loadView('ledger', compact('a'));
        // return $pdf->stream('v.pdf');

        $pdf = PDF::loadView('range', $data);
        // $pdf = PDF::loadView('ledger', compact('entries', 'previous', 'acc', 'period', 'start'));

        // $pdf = PDF::loadView('range', $data);


        // $pdf = PDF::loadView('range', compact('entries', 'previous', 'acc', 'period', 'start'));


        return $pdf->stream($acc->name . ' - ' . $acc->accountGroup->name . '.pdf');
        return $pdf->stream('hi.pdf');
    }



    public function getledger($id)
    {
        dd($id);


        if ($id) {
            $account_first = \App\Models\Account::all()->where('account_id', request('account_id'))->map->only('id', 'name')->first();
        } else {
            $account_first = \App\Models\Account::all()->where('company_id', session('company_id'))->map->only('id', 'name')->first();
        }

        $accounts = \App\Models\Account::all()->where('company_id', session('company_id'))->map->only('id', 'name');
        $entries = Entry::all()->where('company_id', session('company_id'))->where('account_id', $id)
            ->map(function ($entry) {
                return [
                    'ref' => $entry->document->ref,
                    'date' => $entry->document->date,
                    'description' => $entry->document->description,
                    'debit' => 'debit',
                    'credit' => 'credit',
                ];
            });

        return Inertia::render('Ledgers/Index', [
            'enteries' => $entries,
            'companies' => Company::all(),
            'accounts' => $accounts,
        ]);
    }


    public function __invoke(Request $request)
    {
    }
}

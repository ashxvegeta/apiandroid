<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use App\Models\Fee;

class ExpenseController extends Controller
{
    //

        public function store(Request $request)
    {


        $request->validate([
            'title' => 'required',
            'amount' => 'required|numeric|min:1'
        ]);
        

        $expense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'expense_date' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Expense added',
            'data' => $expense
        ]);
    }


        public function index()
    {
        $expenses = Expense::latest()->get();

        return response()->json($expenses);
    }


        public function monthlyExpense()
    {
        $data = Expense::select(
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }


        public function profitReport()
    {
        $income = Fee::sum('amount');
        $expense = Expense::sum('amount');

        return response()->json([
            'total_income' => $income,
            'total_expense' => $expense,
            'profit' => $income - $expense
        ]);
    }
}

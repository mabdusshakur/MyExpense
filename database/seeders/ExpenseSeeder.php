<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        $expenses = [
            [
                'description' => 'Grocery Shopping',
                'amount' => 150.50,
                'category' => 'Food',
                'date' => '2025-07-25',
            ],
            [
                'description' => 'Internet Bill',
                'amount' => 75.00,
                'category' => 'Utilities',
                'date' => '2025-07-26',
            ],
            [
                'description' => 'Movie Tickets',
                'amount' => 30.00,
                'category' => 'Entertainment',
                'date' => '2025-07-27',
            ],
            [
                'description' => 'Gas',
                'amount' => 45.50,
                'category' => 'Transportation',
                'date' => '2025-07-28',
            ],
            [
                'description' => 'Phone Bill',
                'amount' => 60.00,
                'category' => 'Utilities',
                'date' => '2025-07-29',
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::create([
                'user_id' => $user->id,
                'description' => $expense['description'],
                'amount' => $expense['amount'],
                'category' => $expense['category'],
                'date' => $expense['date'],
            ]);
        }
    }
}

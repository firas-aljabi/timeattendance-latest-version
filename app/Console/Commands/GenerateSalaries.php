<?php

namespace App\Console\Commands;

use App\Models\Salary;
use App\Models\User;
use App\Statuses\UserTypes;
use Illuminate\Console\Command;

class GenerateSalaries extends Command
{
    protected $signature = 'generate:salaries';
    protected $description = 'Generate new salaries for all employees on the first day of every month';


    public function handle()
    {
        $employees = User::where('type', UserTypes::EMPLOYEE)->get();

        foreach ($employees as $employee) {
            $salary = new Salary();
            $salary->user_id = $employee->id;
            $salary->salary = $employee->basic_salary;
            $salary->rewards = 0;
            $salary->adversaries = 0;
            $salary->date = now()->startOfMonth();
            $salary->save();
        }

        return Command::SUCCESS;
    }
}

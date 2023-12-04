<?php

final class IncomeExpense {
    public const INCOME = "income";
    public const EXPENSE = "expense";
}

class DataHandling {
    protected int $Id = -1;
    protected int $Amount = -1;
    protected string $Category = "";

    public function getInput($income_or_expense) {
        $this->Id = (int) (count(file("{$income_or_expense}.txt"))) + 1;
        $this->Amount = (int) (readline("Enter Amount: "));
        $this->Category = (string) (readline("Enter Category: "));
    }

    public function getInputForUpdate($income_or_expense): array {
        $Id = readline("Enter {$income_or_expense} Id which you want ot update: ");
        $Amount = readline("Enter updated Amount or leave blank: ");
        $Category = readline("Enter updated Category or leave blank: ");

        return [(string)$Id, (string)$Amount, $Category];
    }
}

class AddData extends DataHandling{
    public function addIncome() {
        $this->getInput(IncomeExpense::INCOME);

        $file_pointer = fopen("income.txt", "a");

        fwrite($file_pointer, "{$this->Id} {$this->Amount} {$this->Category}\n");

        fclose($file_pointer);

        echo "Income added successfully.\n\n";
    }

    public function addExpense(int $total) {
        $this->getInput(IncomeExpense::EXPENSE);
        // var_dump($total);
        if ($total >= $this->Amount) {
            $file_pointer = fopen("expense.txt", "a");
            fwrite($file_pointer, "{$this->Id} {$this->Amount} {$this->Category}\n");
            fclose($file_pointer);

            echo "Expense added successfully.\n\n";
        } else {
            echo "You don't have sufficient Amount!!!\n\n";
        }
    }
}

class ReadData {
    // protected int $total = 0;
    protected $incomes = [];
    protected $expenses = [];

    public function readIncome() {
        $file_pointer = fopen("income.txt", "r");
        while (true) {
            $income = fgets($file_pointer);
            if (!$income)break;
            // echo $income;
            array_push($this->incomes, $income);
        }
        fclose($file_pointer);
        // var_dump($this->incomes);
    }

    public function readExpense() {
        $file_pointer = fopen("expense.txt", "r");
        while (true) {
            $expense = fgets($file_pointer);
            if (!$expense)break;
            // echo $income;
            array_push($this->expenses, $expense);
        }
        fclose($file_pointer);
        // var_dump($this->expenses);
    }

    public function readTotal(): int {
        $this->readIncome();
        $this->readExpense();

        $total_income = (int) 0;
        foreach ($this->incomes as $income) {
            $amount = (int) (explode(" ", $income)[1]);
            $total_income += $amount;
        }

        $total_expense = (int) 0;
        foreach ($this->expenses as $expense) {
            $amount = (int) (explode(" ", $expense)[1]);
            $total_expense += $amount;
        }
        // var_dump($total_income, $total_expense);
        return $total_income - $total_expense;
    }
}

class ViewData extends ReadData {
    public function viewIncome() {
        $this->readIncome();
        
        echo "\nAll incomes:\n------------\n";
        echo "Id Amount Category\n";
        foreach ($this->incomes as $income) {
            echo $income;
        }printf("\n\n");
        // var_dump($this->incomes);
    }

    public function viewExpense() {
        $this->readExpense();

        echo "\nAll expenses:\n-------------\n";
        echo "Id Amount Category\n";
        foreach ($this->expenses as $expense) {
            echo $expense;
        }printf("\n\n");
        // var_dump($this->expenses);
    }

    public function viewTotal() {
        echo "\nTotal: {$this->readTotal()}\n\n";
    }

    public function viewCategory() {
        $this->readIncome();
        $this->readExpense();

        echo "\nAll category from incomes:\n--------------------------\n";
        foreach ($this->incomes as $income) {
            echo explode(" ", $income)[2];
        }printf("\n");

        echo "All category from expenses:\n---------------------------\n";
        foreach ($this->expenses as $expense) {
            echo explode(" ", $expense)[2];
        }printf("\n\n");
    }
}

class UpdateData extends ReadData {
    private function reWriteIncomes() {
        $file_pointer = fopen("income.txt", "w");

        foreach ($this->incomes as $income) {
            fwrite($file_pointer, "{$income}");
        }

        fclose($file_pointer);
    }

    private function reWriteExpense() {
        $file_pointer = fopen("expense.txt", "w");

        foreach ($this->expenses as $expense) {
            fwrite($file_pointer, "{$expense}");
        }

        fclose($file_pointer);
    }

    public function updateIncome() {
        $update_income = (new DataHandling())->getInputForUpdate(IncomeExpense::INCOME);

        $this->readIncome();
        // var_dump($update_income);
        $update = false;
        for ($i = 0; $i < count($this->incomes); $i++) {
            $previous_income = explode(" ", $this->incomes[$i]);
            // var_dump($update_income, $previous_income);
            if ($previous_income[0] === $update_income[0]) {
                $Id = $previous_income[0];
                $Amount = ($update_income[1] === "" ? $previous_income[1] : $update_income[1]);
                $Category = ($update_income[2] === "" ? $previous_income[2] : "{$update_income[2]}\n");
                
                $this->incomes[$i] = "{$Id} {$Amount} {$Category}";

                $this->reWriteIncomes();
                
                $update = true;
            }
        }

        if (!$update) echo "Couldn't update!!!\n\n";
        else echo "Updated successfully!\n\n";
    }

    public function updateExpense() {
        $update_expense = (new DataHandling())->getInputForUpdate(IncomeExpense::EXPENSE);

        $this->readExpense();
        // var_dump($update_expense);
        $update = false;
        for ($i = 0; $i < count($this->expenses); $i++) {
            $previous_expense = explode(" ", $this->expenses[$i]);
            // var_dump($update_expense, $previous_expense);
            if ($previous_expense[0] === $update_expense[0]) {
                $Id = $previous_expense[0];
                $Amount = ($update_expense[1] === "" ? $previous_expense[1] : $update_expense[1]);
                $Category = ($update_expense[2] === "" ? $previous_expense[2] : "{$update_expense[2]}\n");
                $this->expenses[$i] = "{$Id} {$Amount} {$Category}";

                $this->reWriteExpense();
                
                $update = true;
            }
        }

        if (!$update) echo "Couldn't update!!!\n\n";
        else echo "Updated successfully!\n\n";
    }
}

while (true) {
    echo "Type a number between 0 - 3 for which option you want...\n";
    echo "1 for add Income/Expense.\n";
    echo "2 for view Income/Expense/Total/Category.\n";
    echo "3 for update Income/Expense.\n";
    echo "0 for Close.\n";
    $primary_option = (int) readline("Enter a number: ");

    if ($primary_option === 0) break;
    else if ($primary_option === 1) {
        echo "\n1 for add an Income.\n";
        echo "2 for add an Expense.\n";
        $secondary_option = (int) readline("Enter a number: ");

        $addData = new AddData();

        if ($secondary_option === 1) $addData->addIncome();
        else if ($secondary_option === 2) {
            $total = (new ReadData())->readTotal();
            // var_dump($total);
            $addData->addExpense($total);
        } else echo "Invalid input!\n\n";
    }
    else if ($primary_option === 2) {
        echo "\n1 for view Income.\n";
        echo "2 for view Expense.\n";
        echo "3 for view Total money.\n";
        echo "4 for view Category.\n";
        $secondary_option = (int) readline("Enter a number: ");

        $viewData = new ViewData();

        if ($secondary_option === 1) $viewData->viewIncome();
        else if ($secondary_option === 2) $viewData->viewExpense();
        else if ($secondary_option === 3) $viewData->viewTotal();
        else if ($secondary_option === 4) $viewData->viewCategory();
        else echo "Invalid input!\n\n";
    }
    else if ($primary_option === 3) {
        echo "\n1 for update an Income.\n";
        echo "2 for update an Expense.\n";
        $secondary_option = (int) readline("Enter a number: ");

        $updateData = new UpdateData();

        if ($secondary_option === 1) $updateData->updateIncome();
        else if ($secondary_option === 2) $updateData->updateExpense();
        else echo "Invalid input!\n\n";
    }
    else {
        echo "Invalid input!\n\n";
    }
}

?>
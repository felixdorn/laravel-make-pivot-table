<?php

namespace Felix\MakePivotTable\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class PivotMakeCommand extends GeneratorCommand
{
    protected $name = 'make:pivot {first_table} {second_table}';

    protected $description = 'Create a new pivot migration';

    protected $type = 'Migration';

    protected function buildClass($name): string
    {
        $stub = $this->files->get($this->getStub());

        $stub = str_replace('{{pivotTableName}}', $this->getPivotTableName(), $stub);
        $stub = str_replace('{{class}}', $this->getClassName(), $stub);

        return str_replace(
            ['{{columnOne}}', '{{columnTwo}}', '{{tableOne}}', '{{tableTwo}}'],
            array_merge(
                $this->getSortedSingularTableNames(),
                $this->getSortedTableNames()
            ),
            $stub
        );
    }

    protected function getStub(): string
    {
        return __DIR__ . '/../../stubs/pivot.stub';
    }

    private function getPivotTableName(): string
    {
        return implode('_', $this->getSortedSingularTableNames());
    }

    protected function getSortedSingularTableNames(): array
    {
        $tables = array_map(fn (string $table) => Str::singular($table), $this->getTableNamesFromInput());

        sort($tables);

        return $tables;
    }

    protected function getTableNamesFromInput(): array
    {
        return [
            /* @phpstan-ignore-next-line */
            strtolower($this->argument('first_table')),
            /* @phpstan-ignore-next-line */
            strtolower($this->argument('second_table')),
        ];
    }

    protected function getClassName(): string
    {
        $name = implode('', array_map('ucwords', $this->getSortedSingularTableNames()));

        $name = preg_replace_callback('/(_)([a-z])/', function ($matches) {
            return Str::studly($matches[0]);
        }, $name);

        return "Create{$name}PivotTable";
    }

    protected function getSortedTableNames(): array
    {
        $tables = $this->getTableNamesFromInput();

        sort($tables);

        return $tables;
    }

    protected function getNameInput()
    {
    }

    protected function getPath($name = null)
    {
        return base_path() . '/database/migrations/' . date('Y_m_d_His') .
            '_create_' . $this->getPivotTableName() . '_pivot_table.php';
    }

    protected function getArguments(): array
    {
        return [
            ['first_table', InputArgument::REQUIRED, 'The name of the first table.'],
            ['second_table', InputArgument::REQUIRED, 'The name of the second table.'],
        ];
    }
}

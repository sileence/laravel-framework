<?php

namespace Illuminate\Foundation\Testing\Constraints;

use PHPUnit_Framework_Constraint;
use Illuminate\Database\Connection;

class HasInDatabase extends PHPUnit_Framework_Constraint
{
    /**
     * Number of records that will be shown in the console in case of failure.
     *
     * @var int
     */
    protected $show = 5;

    /**
     * Database connection.
     *
     * @var \Illuminate\Database\Collection
     */
    protected $database;

    /**
     * Data that will be used to narrow the search in the database table.
     *
     * @var array
     */
    protected $data;

    /**
     * Name of the queried database table.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new constraint instance.
     *
     * @param  array  $data
     * @param  \Illuminate\Database\Collection  $database
     */
    public function __construct(array $data, Connection $database)
    {
        $this->data = $data;

        $this->database = $database;
    }

    /**
     * Check if the data is found in the given table.
     *
     * @param  string  $table
     * @return bool
     */
    public function matches($table)
    {
        $this->table = $table;

        return $this->database->table($table)->where($this->data)->count() > 0;
    }

    /**
     * Get the description of the failure.
     *
     * @param  string  $table
     * @return string
     */
    public function failureDescription($table)
    {
        return sprintf(
            "a row in the table [%s] matches the attributes %s.\n\n%s",
            $table, $this->toString(), $this->getAdditionalInfo()
        );
    }

    /**
     * Get additional info about the records found in the database table.
     *
     * @return string
     */
    protected function getAdditionalInfo()
    {
        $results = $this->database->table($this->table)->get();

        if ($results->isEmpty()) {
            return "The table is empty";
        }

        $description = "Found: " . json_encode($results->take(5));

        if ($results->count() > $this->show) {
            $description .= sprintf(' and %s others', $results->count() - $this->show);
        }

        return $description;
    }

    /**
     * Get a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return json_encode($this->data);
    }
}

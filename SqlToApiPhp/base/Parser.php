<?php

namespace SqlToApiPhp\base;

use PhpMyAdmin\SqlParser\Parser as PhpMyAdminParser;

/**
 * Class Parser
 * @package SqlToApiPhp\base
 */
class Parser {

    /** @var PhpMyAdminParser */
    private $parser;

    /**
     * Parser constructor.
     *
     * @param string $query
     */
    public function __construct(string $query) {
        $this->parser = new PhpMyAdminParser($query);
    }

    /**
     * @return string
     */
    public function getApiRoute(): string {
        $statement = $this->parser->statements[0];
        $fromStatement = $statement->from[0];

        $path = '';

        if ($fromStatement->database !== null) {
            $path .= '/' . $fromStatement->database;
        }
        if ($fromStatement->table !== null) {
            $path .= '/' . $fromStatement->table;
        }

        return $path;
    }

}
<?php

namespace SqlToApiPhp\base;

use PhpMyAdmin\SqlParser\Parser as PhpMyAdminParser;
use SqlToApiPhp\exceptions\OrNotASupportedOperation;
use SqlToApiPhp\exceptions\WhereNotGivenException;

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

    /**
     * @return string
     * @throws WhereNotGivenException
     * @throws OrNotASupportedOperation
     */
    public function getRequestParameters(): string {
        $statement = $this->parser->statements[0];
        $whereStatement = $statement->where;
        if($whereStatement === null){
            throw new WhereNotGivenException('Where must be given in the query to determine the GET parameters');
        }
        $apiParameters = '';
        foreach ($whereStatement as $whereCondition){
            if($whereCondition->isOperator === true){
                if($whereCondition->expr === 'OR') {
                    throw new OrNotASupportedOperation('OR is not supported by this operation');
                }

                continue;
            }
            if($apiParameters !== ''){
                $apiParameters .= '&';
            }
            $apiParameters .= $whereCondition->identifiers[0] . '=' . $whereCondition->identifiers[1];
        }

        return $apiParameters;
    }

}
<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Database\Query\Restriction;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use GeorgRinger\Gdpr\Domain\Model\Dto\Table;
use GeorgRinger\Gdpr\Service\TableInformation;
use TYPO3\CMS\Core\Database\Query\Expression\CompositeExpression;
use TYPO3\CMS\Core\Database\Query\Expression\ExpressionBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\QueryRestrictionInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Restriction to filter records that have been marked as hidden
 */
class GdprRandomizedRestriction implements QueryRestrictionInterface
{
    /**
     * Main method to build expressions for given tables
     * Evaluates the ctrl/enablecolumns/disabled flag of the table and adds the according restriction if set
     *
     * @param array $queriedTables Array of tables, where array key is table alias and value is a table name
     * @param ExpressionBuilder $expressionBuilder Expression builder instance to add restrictions with
     * @return CompositeExpression The result of query builder expression(s)
     */
    public function buildExpression(array $queriedTables, ExpressionBuilder $expressionBuilder): CompositeExpression
    {
        $constraints = [];
        foreach ($queriedTables as $tableAlias => $tableName) {
            if (TableInformation::isTableEnabled($tableName)) {
                $table = Table::getInstance($tableName);
                $restrictionFieldName = $table->getGdprRandomizedField() ?? null;
                if (!empty($restrictionFieldName)) {
                    $constraints[] = $expressionBuilder->eq(
                        $tableAlias . '.' . $restrictionFieldName,
                        0
                    );
                }
            }
        }
        return $expressionBuilder->andX(...$constraints);
    }
}

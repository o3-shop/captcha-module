<?php

/**
 * This file is part of O3-Shop Captcha Module.
 *
 * O3-Shop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * O3-Shop is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with O3-Shop.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

declare(strict_types=1);

namespace O3\SimpleCaptcha\Migrations;

use Doctrine\DBAL\Exception as DoctrineException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\Migrations\AbstractMigration;

final class Version20230417203524 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Simple Captcha hash database table';
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws DoctrineException
     * @throws SchemaException
     */
    public function up(Schema $schema): void
    {
        $this->connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $table = $schema->hasTable('oecaptcha') ?
            $schema->getTable('oecaptcha') :
            $schema->createTable('oecaptcha');

        if (!$table->hasColumn('OXID')) {
            $table->addColumn(
                'OXID',
                (new IntegerType())->getName(),
                ['autoincrement' => true]
            )->setNotnull(true)
            ->setLength(11)
            ->setComment('Captcha Id');
        }

        if (!$table->hasColumn('OXHASH')) {
            $table->addColumn(
                'OXHASH',
                (new StringType())->getName()
            )->setNotnull(true)
            ->setLength(32)
            ->setFixed(true)
            ->setComment('Hash');
        }

        if (!$table->hasColumn('OXTIME')) {
            $table->addColumn(
                'OXTIME',
                (new IntegerType())->getName()
            )->setNotnull(true)
            ->setLength(11)
            ->setComment('Validation time');
        }

        if (!$table->hasColumn('OXTIMESTAMP')) {
            $table->addColumn('OXTIMESTAMP', (new DateTimeType())->getName())
                ->setType(new DateTimeType())
                ->setNotnull(true)
                // can't set default value via default method
                ->setColumnDefinition('timestamp DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP')
                ->setComment('Timestamp');
        }

        if (!$table->hasPrimaryKey()) {
            $table->setPrimaryKey(['OXID']);
        }

        if (!$table->hasIndex('HASH_IDX')) {
            $table->addIndex(['OXID', 'OXHASH'], 'HASH_IDX');
        }

        if (!$table->hasIndex('TIME_IDX')) {
            $table->addIndex(['OXTIME'], 'TIME_IDX');
        }

        $table->setComment('If session is not available, this is where captcha information is stored');
    }

    /**
     * @param Schema $schema
     *
     * @return void
     * @throws DoctrineException
     */
    public function down(Schema $schema): void
    {
        $this->connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        if ($schema->hasTable('oecaptcha')) {
            $schema->dropTable('oecaptcha');
        }
    }
}

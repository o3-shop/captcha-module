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
use Doctrine\Migrations\AbstractMigration;

final class Version20231219103539 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'change captcha tables collation';
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

        $this->addSql('ALTER TABLE o3captcha CONVERT TO CHARACTER SET LATIN1 COLLATE latin1_general_ci');
        $this->addSql('ALTER TABLE o3captcha MODIFY OXID VARCHAR(11) CHARACTER SET LATIN1 COLLATE latin1_general_ci');
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
    }
}

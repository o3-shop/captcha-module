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
 * @copyright  Copyright (c) 2022 OXID eSales AG (https://www.oxid-esales.com)
 * @copyright  Copyright (c) 2022 O3-Shop (https://www.o3-shop.com)
 * @license    https://www.gnu.org/licenses/gpl-3.0  GNU General Public License 3 (GPLv3)
 */

namespace O3\SimpleCaptcha\Application\Core\Setup;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\DbMetaDataHandler;

class Actions
{
    public function migrateUp(): void
    {
        /** @var MigrationsBuilder $migrationsBuilder */
        $migrationsBuilder = oxNew(MigrationsBuilder::class);
        $migrations = $migrationsBuilder->build();
        $migrations->execute('migrations:migrate', 'o3-captcha');
    }

    /**
     * Regenerate views for changed tables
     */
    public function regenerateViews(): void
    {
        $oDbMetaDataHandler = oxNew(DbMetaDataHandler::class);
        $oDbMetaDataHandler->updateViews();
    }


    public function migrateDown(): void
    {
        // at the moment the migration wrapper can't migrate down
        $query = "DROP TABLE IF EXISTS `o3captcha`";
        DatabaseProvider::getDb()->execute($query);
    }
}
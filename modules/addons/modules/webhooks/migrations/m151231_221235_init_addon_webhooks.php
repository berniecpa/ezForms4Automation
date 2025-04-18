<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2024 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

use yii\db\Migration;

class m151231_221235_init_addon_webhooks extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%addon_webhooks}}', [
            'id' => $this->primaryKey(),
            'form_id' => $this->integer(11)->notNull(),
            'url' => $this->text()->notNull(),
            'handshake_key' => $this->text(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'json' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%addon_webhooks}}');
    }
}

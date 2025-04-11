<?php

use yii\db\Migration;

/**
 * Class m240713_175431_upgrade_to_2_2
 */
class m240713_175431_upgrade_to_2_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('{{%form}}');
        if (!isset($table->columns['preferences'])) {
            $this->addColumn('{{%form}}', 'preferences', $this->text()->after('message'));
        }

        $table = Yii::$app->db->schema->getTableSchema('{{%form_confirmation}}');
        if (!isset($table->columns['custom_html'])) {
            $this->addColumn('{{%form_confirmation}}', 'custom_html', $this->tinyInteger()->after('hide_form'));
        }
        if (!isset($table->columns['opt_custom_html'])) {
            $this->addColumn('{{%form_confirmation}}', 'opt_custom_html', $this->tinyInteger()->after('opt_out_url'));
        }

        $table = Yii::$app->db->schema->getTableSchema('{{%form_confirmation_rule}}');
        if (!isset($table->columns['custom_html'])) {
            $this->addColumn('{{%form_confirmation_rule}}', 'custom_html', $this->tinyInteger()->after('seconds'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%form}}', 'preferences');
        $this->dropColumn('{{%form_confirmation}}', 'custom_html');
        $this->dropColumn('{{%form_confirmation}}', 'opt_custom_html');
        $this->dropColumn('{{%form_confirmation_rule}}', 'custom_html');
    }

}

<?php

use yii\db\Migration;

/**
 * Class m240503_162850_upgrade_to_2_1
 */
class m240503_162850_upgrade_to_2_1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%form}}', 'authorized_urls_error_type', $this->tinyInteger()->after('urls'));
        $this->addColumn('{{%form}}', 'authorized_urls_error_message', $this->text()->after('authorized_urls_error_type'));

        $this->addColumn('{{%form_confirmation}}', 'hide_form', $this->tinyInteger()->after('seconds'));

        $this->addColumn('{{%form_ui}}', 'favicon', $this->text()->after('js_file'));
        $this->addColumn('{{%form_ui}}', 'logo', $this->text()->after('favicon'));
        $this->addColumn('{{%form_ui}}', 'logo_link', $this->tinyInteger(1)->defaultValue(1)->after('logo'));
        $this->addColumn('{{%form_ui}}', 'logo_link_url', $this->text()->after('logo_link'));
        $this->addColumn('{{%form_ui}}', 'meta_image', $this->text()->after('logo_link_url'));
        $this->addColumn('{{%form_ui}}', 'meta_title', $this->text()->after('meta_image'));
        $this->addColumn('{{%form_ui}}', 'meta_description', $this->text()->after('meta_title'));
        $this->addColumn('{{%form_ui}}', 'styles', $this->text()->after('meta_description'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%form}}', 'authorized_urls_error_type');
        $this->dropColumn('{{%form}}', 'authorized_urls_error_message');

        $this->dropColumn('{{%form_confirmation}}', 'hide_form');

        $this->dropColumn('{{%form_ui}}', 'favicon');
        $this->dropColumn('{{%form_ui}}', 'logo');
        $this->dropColumn('{{%form_ui}}', 'logo_link');
        $this->dropColumn('{{%form_ui}}', 'logo_link_url');
        $this->dropColumn('{{%form_ui}}', 'meta_image');
        $this->dropColumn('{{%form_ui}}', 'meta_title');
        $this->dropColumn('{{%form_ui}}', 'meta_description');
        $this->dropColumn('{{%form_ui}}', 'styles');
    }
}

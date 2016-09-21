<?php

use yii\db\Schema;

class m160921_195308_create_tables extends yii\db\Migration
{
    public function up()
    {
        $this->createTable('{{%answer}}', [
            'id' => $this->primaryKey()->notNull(),
            'test_id' => $this->integer()->notNull(),
            'question_number' => $this->integer()->notNull(),
            'type' => $this->getDb()->getSchema()->createColumnSchemaBuilder('CHAR(2)')->notNull(),
            'question_word' => $this->string(100)->notNull(),
            'answer_word' => $this->string(100)->notNull(),
            'is_correct' => $this->integer()->notNull(),
        ]);

        $this->createIndex('test_id', '{{%answer}}', 'test_id', false);


        $this->createTable('{{%test}}', [
            'id' => $this->primaryKey()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'question_count' => $this->integer()->notNull(),
        ]);

        $this->createIndex('user_id', '{{%test}}', 'user_id', false);


        $this->createTable('{{%word}}', [
            'en' => $this->string(100)->notNull() . ' PRIMARY KEY',
            'ru' => $this->string(100)->notNull(),
        ]);

        $this->createIndex('ru', '{{%word}}', 'ru', true);


        $this->addForeignKey('FK_answer_test', '{{%answer}}', 'test_id', '{{%test}}', 'id', null, null);

        $this->addForeignKey('FK_test_user', '{{%test}}', 'user_id', '{{%user}}', 'id', null, null);
    }

    public function down()
    {
        $this->execute('SET FOREIGN_KEY_CHECKS = 0');

        $this->dropTable('{{%word}}');
        $this->dropTable('{{%test}}');
        $this->dropTable('{{%answer}}');

        $this->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}

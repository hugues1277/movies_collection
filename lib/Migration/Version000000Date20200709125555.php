<?php

namespace OCA\MoviesCollection\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version000000Date20200709125555 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options)
    {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('movies_collection')) {
            $table = $schema->createTable('movies_collection');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('imdb_id', 'string', [
                'notnull' => true,
                'length' => 20,
            ]);
            $table->addColumn('title', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('original_title', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('director', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('genre', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('year', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('runtime', 'string', [
                'notnull' => true,
                'length' => 10,
            ]);
            $table->addColumn('poster_url', 'string', [
                'notnull' => true,
                'length' => 512,
            ]);
            $table->addColumn('trailer_url', 'string', [
                'notnull' => true,
                'length' => 512,
            ]);
            $table->addColumn('synopsis', 'text', [
                'notnull' => true,
            ]);
            $table->addColumn('cast', 'string', [
                'notnull' => true,
                'length' => 255,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 100,
            ]);
            $table->addColumn('rating', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('comment', 'text', [
                'notnull' => true,
            ]);
            $table->addColumn('listed', 'integer', [
                'notnull' => true,
                'length' => 1,
            ]);
            $table->addColumn('created', 'integer', [
                'notnull' => true,
                'length' => 4,
                'default' => 0,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['title','year','user_id'], 'notes_meta_file_user_index');
            $table->addIndex(['imdb_id'], 'imdb_id_index');
            $table->addIndex(['user_id'], 'user_id_index');
        }
        return $schema;
    }
}
